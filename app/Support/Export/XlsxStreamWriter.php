<?php

namespace App\Support\Export;

use RuntimeException;
use ZipArchive;

/**
 * Minimal streaming XLSX (SpreadsheetML) writer for large, single-sheet exports.
 *
 * Rows are appended to temporary files as soon as they are added (sheet XML plus a
 * shared-strings file) and the workbook is zipped once at the end, so memory use stays
 * flat no matter how many rows are written. PhpSpreadsheet keeps every cell as a PHP object
 * instead, which needed ~870 MB and ~100 s for 5,000 applicants and took the production
 * worker down (502 Bad Gateway).
 *
 * Strings go to the shared-strings table (the encoding Excel itself writes; duplicates are
 * allowed by the spec, so no in-memory de-duplication is needed).
 *
 * Security: text is always written as a string cell (t="s"), never as a formula, so
 * user-controlled values such as "=HYPERLINK(...)" are shown as plain text in Excel instead
 * of being executed (CSV/formula injection, CWE-1236).
 */
final class XlsxStreamWriter
{
    public const STYLE_DEFAULT = 0;
    public const STYLE_HEADER = 1;
    public const STYLE_BODY = 2;

    /** Excel refuses (and "repairs") cells longer than 32,767 UTF-16 code units. */
    private const MAX_CELL_UTF16_UNITS = 32767;

    private const NS_MAIN = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
    private const NS_REL = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';

    /** @var resource|null */
    private $sheetHandle = null;

    /** @var resource|null */
    private $stringsHandle = null;

    /** @var list<string> temp files to delete on close/destruct */
    private array $tempFiles = [];

    private ?string $sheetPath = null;

    private ?string $stringsPath = null;

    private int $stringCount = 0;

    private string $stringsBuffer = '';

    private int $columnCount = 0;

    private int $dataRowCount = 0;

    private bool $closed = false;

    private readonly string $sheetTitle;

    /** @var array<int, string> 1-based column index => column letters */
    private array $letters = [];

    public function __construct(string $sheetTitle = 'Sheet1', private readonly ?string $tempDir = null)
    {
        $this->sheetTitle = self::sanitizeSheetTitle($sheetTitle);
    }

    /**
     * Start the sheet and write the (frozen, filterable) header row.
     *
     * @param  list<string>  $headers
     * @param  array<int, float|int>  $columnWidths  1-based column index => width (characters)
     */
    public function open(array $headers, array $columnWidths = [], float $defaultWidth = 18.0, float $headerHeight = 28.0): void
    {
        if ($this->sheetHandle !== null || $this->closed) {
            throw new RuntimeException('XlsxStreamWriter::open() may only be called once.');
        }

        [$this->sheetPath, $this->sheetHandle] = $this->createTempFile('xlsx_sheet_');
        [$this->stringsPath, $this->stringsHandle] = $this->createTempFile('xlsx_strings_');
        $this->columnCount = count($headers);

        $this->write('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<worksheet xmlns="' . self::NS_MAIN . '" xmlns:r="' . self::NS_REL . '">'
            . '<sheetViews><sheetView tabSelected="1" workbookViewId="0">'
            . '<pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/>'
            . '<selection pane="bottomLeft" activeCell="A2" sqref="A2"/>'
            . '</sheetView></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="15"/>'
            . $this->colsXml($columnWidths, $defaultWidth)
            . '<sheetData>'
            . $this->rowXml(1, $headers, self::STYLE_HEADER, $headerHeight));
    }

    /**
     * Append one data row. Ints/floats become numeric cells; everything else is text.
     *
     * @param  array<int, mixed>  $values
     */
    public function addRow(array $values): void
    {
        if ($this->sheetHandle === null) {
            throw new RuntimeException('Call open() before addRow().');
        }

        $this->dataRowCount++;
        $this->write($this->rowXml($this->dataRowCount + 1, $values, self::STYLE_BODY));
    }

    /** Number of data rows written (header excluded). */
    public function rowCount(): int
    {
        return $this->dataRowCount;
    }

    /**
     * Finish the sheet and write the complete .xlsx package to $targetPath.
     */
    public function close(string $targetPath): void
    {
        if ($this->sheetHandle === null) {
            throw new RuntimeException('Call open() before close().');
        }

        $range = $this->filterRange();

        $this->write('</sheetData>'
            . '<autoFilter ref="' . $range . '"/>'
            . '<pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>'
            . '</worksheet>');
        fclose($this->sheetHandle);
        $this->sheetHandle = null;
        fclose($this->stringsHandle);
        $this->stringsHandle = null;

        // The shared-strings header needs the final count, so prepend it now.
        [$sstPath, $sst] = $this->createTempFile('xlsx_sst_');
        $entries = fopen($this->stringsPath, 'rb');
        fwrite($sst, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<sst xmlns="' . self::NS_MAIN . '" count="' . $this->stringCount . '" uniqueCount="' . $this->stringCount . '">');
        $copied = stream_copy_to_stream($entries, $sst);
        $written = fwrite($sst, '</sst>');
        fclose($entries);
        fclose($sst);
        if ($copied === false || $written === false) {
            throw new RuntimeException('Failed writing shared strings (disk full?).');
        }

        $zip = new ZipArchive();
        $opened = $zip->open($targetPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($opened !== true) {
            throw new RuntimeException("Cannot create XLSX archive [{$targetPath}] (ZipArchive error {$opened}).");
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml($range));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->stylesXml());
        $zip->addFile($sstPath, 'xl/sharedStrings.xml');
        $zip->addFile($this->sheetPath, 'xl/worksheets/sheet1.xml');
        // Deflate level 1: ~5x faster than the default on large parts; the file is only
        // slightly bigger because spreadsheet XML is highly repetitive.
        $zip->setCompressionName('xl/sharedStrings.xml', ZipArchive::CM_DEFLATE, 1);
        $zip->setCompressionName('xl/worksheets/sheet1.xml', ZipArchive::CM_DEFLATE, 1);

        if (! $zip->close()) {
            throw new RuntimeException("Failed to write XLSX archive [{$targetPath}].");
        }

        $this->closed = true;
        $this->cleanup();
    }

    public function __destruct()
    {
        $this->cleanup();
    }

    /**
     * Make arbitrary text safe for an XML 1.0 text node and for Excel's cell limit:
     * repair invalid UTF-8, drop control characters XML forbids, cap the length.
     */
    public static function sanitizeText(string $text): string
    {
        if (! mb_check_encoding($text, 'UTF-8')) {
            $text = mb_scrub($text, 'UTF-8');
        }

        // C0 controls except TAB/LF/CR, plus U+FFFE/U+FFFF, are not allowed in XML 1.0.
        if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]|\xEF\xBF[\xBE\xBF]/', $text)) {
            $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]|\xEF\xBF[\xBE\xBF]/', '', $text);
        }

        if (strlen($text) > self::MAX_CELL_UTF16_UNITS) {
            $utf16 = mb_convert_encoding($text, 'UTF-16LE', 'UTF-8');
            if (strlen($utf16) > self::MAX_CELL_UTF16_UNITS * 2) {
                $utf16 = substr($utf16, 0, self::MAX_CELL_UTF16_UNITS * 2);
                // Do not leave a dangling high surrogate at the cut.
                $last = unpack('v', substr($utf16, -2))[1];
                if ($last >= 0xD800 && $last <= 0xDBFF) {
                    $utf16 = substr($utf16, 0, -2);
                }
                $text = mb_convert_encoding($utf16, 'UTF-8', 'UTF-16LE');
            }
        }

        return $text;
    }

    /** 1 => A, 26 => Z, 27 => AA ... */
    public static function columnLetter(int $index): string
    {
        $letters = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letters = chr(65 + $mod) . $letters;
            $index = intdiv($index - 1, 26);
        }

        return $letters;
    }

    private function rowXml(int $rowNumber, array $values, int $style, ?float $height = null): string
    {
        $xml = $height !== null
            ? '<row r="' . $rowNumber . '" ht="' . $height . '" customHeight="1">'
            : '<row r="' . $rowNumber . '">';

        $index = 0;
        foreach ($values as $value) {
            $index++;
            $xml .= $this->cellXml($this->letter($index) . $rowNumber, $value, $style);
        }

        return $xml . '</row>';
    }

    private function cellXml(string $ref, mixed $value, int $style): string
    {
        $open = '<c r="' . $ref . '" s="' . $style . '"';

        if ($value === null || $value === '') {
            return $open . '/>';
        }

        if (is_int($value) || (is_float($value) && is_finite($value))) {
            return $open . '><v>' . $value . '</v></c>';
        }

        if (is_bool($value)) {
            return $open . ' t="b"><v>' . ($value ? '1' : '0') . '</v></c>';
        }

        $text = is_scalar($value) || $value instanceof \Stringable
            ? (string) $value
            : (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $open . ' t="s"><v>' . $this->addSharedString($text) . '</v></c>';
    }

    private function addSharedString(string $text): int
    {
        $text = self::sanitizeText($text);
        $preserve = ($text !== trim($text)) ? ' xml:space="preserve"' : '';
        $this->stringsBuffer .= '<si><t' . $preserve . '>' . self::escapeText($text) . '</t></si>';

        return $this->stringCount++;
    }

    /** Escape for an XML text node (quotes need no escaping there). Hot path: most cells have nothing to escape. */
    private static function escapeText(string $text): string
    {
        return strpbrk($text, '&<>') === false
            ? $text
            : str_replace(['&', '<', '>'], ['&amp;', '&lt;', '&gt;'], $text);
    }

    private function colsXml(array $columnWidths, float $defaultWidth): string
    {
        if ($this->columnCount === 0) {
            return '';
        }

        // Merge consecutive columns with the same width into one <col> range.
        $xml = '<cols>';
        $start = 1;
        $current = (float) ($columnWidths[1] ?? $defaultWidth);
        for ($i = 2; $i <= $this->columnCount + 1; $i++) {
            $width = $i <= $this->columnCount ? (float) ($columnWidths[$i] ?? $defaultWidth) : null;
            if ($width !== $current) {
                $xml .= '<col min="' . $start . '" max="' . ($i - 1) . '" width="' . $current . '" customWidth="1"/>';
                $start = $i;
                $current = $width;
            }
        }

        return $xml . '</cols>';
    }

    private function filterRange(): string
    {
        return 'A1:' . $this->letter(max(1, $this->columnCount)) . ($this->dataRowCount + 1);
    }

    private function letter(int $index): string
    {
        return $this->letters[$index] ??= self::columnLetter($index);
    }

    /** Write sheet XML plus the shared strings collected for it (one row at a time). */
    private function write(string $sheetXml): void
    {
        if (fwrite($this->sheetHandle, $sheetXml) === false
            || ($this->stringsBuffer !== '' && fwrite($this->stringsHandle, $this->stringsBuffer) === false)) {
            throw new RuntimeException('Failed writing temporary export data (disk full?).');
        }
        $this->stringsBuffer = '';
    }

    /** @return array{0: string, 1: resource} */
    private function createTempFile(string $prefix): array
    {
        $dir = $this->tempDir ?? sys_get_temp_dir();
        $path = tempnam($dir, $prefix);
        $handle = $path === false ? false : fopen($path, 'wb');
        if ($handle === false) {
            throw new RuntimeException("Cannot create temporary file in [{$dir}].");
        }
        $this->tempFiles[] = $path;

        return [$path, $handle];
    }

    private function cleanup(): void
    {
        foreach (['sheetHandle', 'stringsHandle'] as $handle) {
            if (is_resource($this->$handle)) {
                fclose($this->$handle);
            }
            $this->$handle = null;
        }

        foreach ($this->tempFiles as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
        $this->tempFiles = [];
    }

    private static function sanitizeSheetTitle(string $title): string
    {
        // Excel: max 31 chars, none of []:*?/\ and no leading/trailing apostrophe.
        $title = trim(str_replace(['[', ']', ':', '*', '?', '/', '\\', "'"], '', self::sanitizeText($title)));
        $title = mb_substr($title, 0, 31);

        return $title === '' ? 'Sheet1' : $title;
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
            . '</Types>';
    }

    private function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function workbookXml(string $filterRange): string
    {
        [$from, $to] = explode(':', $filterRange);
        $absolute = fn (string $ref) => preg_replace('/^([A-Z]+)(\d+)$/', '\$$1\$$2', $ref);
        $sheetRef = "'" . $this->sheetTitle . "'!" . $absolute($from) . ':' . $absolute($to);

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<workbook xmlns="' . self::NS_MAIN . '" xmlns:r="' . self::NS_REL . '">'
            . '<bookViews><workbookView activeTab="0"/></bookViews>'
            . '<sheets><sheet name="' . htmlspecialchars($this->sheetTitle, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '" sheetId="1" r:id="rId1"/></sheets>'
            . '<definedNames><definedName name="_xlnm._FilterDatabase" localSheetId="0" hidden="1">'
            . htmlspecialchars($sheetRef, ENT_XML1 | ENT_QUOTES, 'UTF-8')
            . '</definedName></definedNames>'
            . '</workbook>';
    }

    private function workbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'
            . '</Relationships>';
    }

    /**
     * Style ids: 0 = default, 1 = header (bold white on navy, centered, wrapped),
     * 2 = body (thin slate borders, vertically centered). Font: Calibri 10.
     */
    private function stylesXml(): string
    {
        $border = '<left style="thin"><color rgb="FFCBD5E1"/></left>'
            . '<right style="thin"><color rgb="FFCBD5E1"/></right>'
            . '<top style="thin"><color rgb="FFCBD5E1"/></top>'
            . '<bottom style="thin"><color rgb="FFCBD5E1"/></bottom><diagonal/>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<styleSheet xmlns="' . self::NS_MAIN . '">'
            . '<fonts count="2">'
            . '<font><sz val="10"/><name val="Calibri"/><family val="2"/></font>'
            . '<font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Calibri"/><family val="2"/></font>'
            . '</fonts>'
            . '<fills count="3">'
            . '<fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF0B1120"/><bgColor indexed="64"/></patternFill></fill>'
            . '</fills>'
            . '<borders count="2">'
            . '<border><left/><right/><top/><bottom/><diagonal/></border>'
            . '<border>' . $border . '</border>'
            . '</borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="3">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1">'
            . '<alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1">'
            . '<alignment vertical="center"/></xf>'
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }
}

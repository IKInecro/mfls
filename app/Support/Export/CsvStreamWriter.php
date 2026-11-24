<?php

namespace App\Support\Export;

use RuntimeException;

/**
 * Streaming CSV writer (UTF-8 with BOM so Excel detects the encoding) that is safe to
 * open in spreadsheet software.
 *
 * - Cells that Excel/LibreOffice would evaluate as a formula (leading = + - @ TAB CR) are
 *   prefixed with an apostrophe, per OWASP "CSV Injection" guidance (CWE-1236).
 * - RFC 4180 quoting only (escape char disabled): PHP's default "\" escape lets a value
 *   such as `x\";=CMD()` break out of its quotes and start a new, unescaped cell.
 */
final class CsvStreamWriter
{
    /** @var resource|null */
    private $handle;

    private int $rowCount = 0;

    public function __construct(string $path, private readonly string $delimiter = ';', bool $withBom = true)
    {
        $handle = fopen($path, 'wb');
        if ($handle === false) {
            throw new RuntimeException("Cannot open [{$path}] for writing.");
        }
        $this->handle = $handle;

        if ($withBom) {
            fwrite($this->handle, "\xEF\xBB\xBF");
        }
    }

    /** @param  array<int, mixed>  $values */
    public function addRow(array $values): void
    {
        if ($this->handle === null) {
            throw new RuntimeException('CSV writer already closed.');
        }

        $cells = array_map(self::neutralizeFormula(...), $values);
        if (fputcsv($this->handle, $cells, $this->delimiter, '"', '') === false) {
            throw new RuntimeException('Failed writing CSV row (disk full?).');
        }
        $this->rowCount++;
    }

    /** Rows written so far (including a header row if one was added). */
    public function rowCount(): int
    {
        return $this->rowCount;
    }

    public function close(): void
    {
        if ($this->handle !== null) {
            fclose($this->handle);
            $this->handle = null;
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * Prefix a value with an apostrophe when a spreadsheet would treat it as a formula.
     * Plain numbers / phone numbers ("+62 812-3456", "-5") are left untouched: without
     * letters they cannot call functions or DDE, and prefixing them would corrupt data.
     */
    public static function neutralizeFormula(mixed $value): mixed
    {
        if (! is_string($value) || $value === '' || $value === '-') {
            return $value;
        }

        if (! in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return $value;
        }

        if (preg_match('/^[+-]?[0-9][0-9 ().-]*$/', $value)) {
            return $value;
        }

        return "'" . $value;
    }
}

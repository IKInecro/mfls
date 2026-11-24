<?php

namespace App\Services\Export;

use App\Models\Akun;
use App\Models\Berkas;
use App\Models\Daftar;
use App\Models\Matpel;
use App\Models\Nilai;
use App\Models\PenilaianAkademik;
use App\Models\PenilaianMentor;
use App\Models\Peserta;
use App\Models\Sertifikat;
use Generator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Data for the "Database Seleksi Administrasi" export: one row per pendaftar account.
 *
 * Rows are produced lazily in id-ordered chunks with only the columns that are needed,
 * so memory stays bounded. The previous controller implementation loaded every pendaftar
 * with all relations at once (~370 MB / 20 s for 5,000 accounts before any Excel work).
 */
class SeleksiAdministrasiExport
{
    public const CHUNK_SIZE = 500;

    private const SEMESTERS = 5;

    private const MIN_SERTIFIKAT_SLOTS = 3;

    private const CORE_MATPELS = ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Informatika'];

    private const JABODETABEK = ['jakarta', 'bogor', 'depok', 'tangerang', 'bekasi'];

    private const IDENTITY_COLUMNS = [
        'No',
        'Status Seleksi Administrasi',
        'Progres Berkas',
        'Tanggal Pendaftaran',
        'Nama Lengkap',
        'Email',
        'Jenis Kelamin',
        'Nomor HP / WhatsApp',
        'NISN',
        'Asal Sekolah',
        'Provinsi',
        'Kabupaten / Kota',
        'Wilayah (Jabodetabek)',
        'Tempat, Tanggal Lahir',
        'Tahun Lulus',
        'Telepon Sekolah',
        'No HP Guru BK',
        'Minat Prodi 1',
        'Minat Prodi 2',
        'Sumber Informasi',
        'Kode Referral',
        'Catatan Dosen',
        'Catatan Mentor',
    ];

    private const BERKAS_COLUMNS = [
        'FOTO',
        'RAPOR S1',
        'RAPOR S2',
        'RAPOR S3',
        'RAPOR S4',
        'RAPOR S5',
        'RAPOR S6',
        'IJAZAH / SKL',
        'PERSONAL STATEMENT',
        'SURAT REKOMENDASI',
        'STUDY PLAN',
        'SURAT BUTA WARNA (DKV)',
        'PORTOFOLIO (DKV)',
        'LINK VIDEO MOTIVASI',
        'LINK VIDEO MOTIVASI TIKTOK',
        'LINK INSTAGRAM',
        'LINK TIKTOK',
        'LINK TWIBBON',
        'BUKTI FOLLOW IG BEASISWA',
        'BUKTI FOLLOW IG MNCU',
        'BUKTI FOLLOW TIKTOK BEASISWA',
        'BUKTI FOLLOW TIKTOK MNCU',
        'BUKTI FOLLOW IG MNC NATION',
        'BUKTI FOLLOW IG THE ARETE GEN',
    ];

    private const STATUS_COLUMNS = [
        'STATUS SELEKSI UJIAN (CBT)',
        'NOMINAL BEASISWA',
        'STATUS WAWANCARA BOD',
    ];

    /** Column widths (1-based index => width) for the identity block; others use the default. */
    private const COLUMN_WIDTHS = [
        1 => 8,    // No
        2 => 26,   // Status Seleksi
        3 => 16,   // Progres Berkas
        4 => 20,   // Tanggal Daftar
        5 => 30,   // Nama Lengkap
        6 => 28,   // Email
        8 => 22,   // No HP/WA
        9 => 18,   // NISN
        10 => 32,  // Asal Sekolah
        11 => 20,  // Provinsi
        12 => 22,  // Kabupaten
        13 => 16,  // Wilayah
        14 => 25,  // TTL
        15 => 14,  // Tahun Lulus
        18 => 24,  // Minat Prodi 1
        19 => 24,  // Minat Prodi 2
        20 => 20,  // Sumber Info
        21 => 18,  // Referral
        22 => 30,  // Catatan Dosen
        23 => 30,  // Catatan Mentor
    ];

    private ?Collection $matpels = null;

    private ?int $sertifikatSlots = null;

    /** @return list<string> */
    public function columns(): array
    {
        $columns = self::IDENTITY_COLUMNS;

        for ($sem = 1; $sem <= self::SEMESTERS; $sem++) {
            foreach ($this->matpels() as $mp) {
                $columns[] = "S{$sem} - {$mp->nama}";
            }
            $columns[] = "Rata Rata S{$sem}";
        }

        $columns[] = 'TOTAL NILAI (S1-S5)';
        $columns[] = 'RATA RATA AKADEMIK (S1-S5)';

        array_push($columns, ...self::BERKAS_COLUMNS);

        for ($i = 1; $i <= $this->sertifikatSlots(); $i++) {
            $columns[] = "SERTIFIKAT {$i} (NAMA)";
            $columns[] = "SERTIFIKAT {$i} (LINK)";
        }

        array_push($columns, ...self::STATUS_COLUMNS);

        return $columns;
    }

    /** @return array<int, int> */
    public function columnWidths(): array
    {
        return self::COLUMN_WIDTHS;
    }

    /**
     * Export rows in akun.id order. Memory is bounded by CHUNK_SIZE, not by the total.
     *
     * @return Generator<int, list<int|float|string>>
     */
    public function rows(): Generator
    {
        $matpelIds = $this->matpels()->pluck('id')->all();
        $slots = $this->sertifikatSlots();
        $storageBase = rtrim(url('storage'), '/') . '/';
        $no = 1;
        $lastId = 0;

        // Keyset pagination on akun.id. Plain arrays instead of Eloquent models: hydrating
        // ~30 models per applicant and reading ~300 attributes through Eloquent cost ~4 ms
        // per row (20 s for 5,000). None of the columns used here have casts/accessors.
        do {
            $akuns = Akun::query()->toBase()
                ->select(['id', 'nama', 'email', 'email_verified_at', 'created_at'])
                ->where('role', 'pendaftar')
                ->where('id', '>', $lastId)
                ->orderBy('id')
                ->limit(self::CHUNK_SIZE)
                ->get();

            if ($akuns->isEmpty()) {
                return;
            }

            $lastId = $akuns->last()->id;
            $related = $this->loadRelated($akuns->pluck('id')->all());

            foreach ($akuns as $akun) {
                yield $this->buildRow((array) $akun, $no++, $related, $matpelIds, $slots, $storageBase);
            }
        } while ($akuns->count() === self::CHUNK_SIZE);
    }

    /**
     * Load everything a chunk of accounts needs, keyed for O(1) lookups.
     * "First" means lowest id, matching the hasOne / ->first() semantics used before.
     *
     * @param  list<int>  $akunIds
     */
    private function loadRelated(array $akunIds): array
    {
        $peserta = self::firstBy(Peserta::query()->toBase()->whereIn('akun_id', $akunIds)->orderBy('id')->get(), 'akun_id');
        $pesertaIds = array_column($peserta, 'id');

        $related = [
            'peserta' => $peserta,
            'daftar' => [],
            'berkas' => [],
            'nilai' => [],
            'sertifikat' => [],
            'catatan_dosen' => [],
            'catatan_mentor' => [],
        ];

        if ($pesertaIds === []) {
            return $related;
        }

        $related['daftar'] = self::firstBy(Daftar::query()->toBase()->whereIn('peserta_id', $pesertaIds)->orderBy('id')->get(), 'peserta_id');
        $related['berkas'] = self::firstBy(Berkas::query()->toBase()->whereIn('peserta_id', $pesertaIds)->orderBy('id')->get(), 'peserta_id');
        $related['nilai'] = self::groupBy(Nilai::query()->toBase()
            ->select(['peserta_id', 'matpel_id', 'semester', 'nilai'])
            ->whereIn('peserta_id', $pesertaIds)->orderBy('id')->get(), 'peserta_id');
        $related['sertifikat'] = self::groupBy(Sertifikat::query()->toBase()
            ->select(['peserta_id', 'nama', 'file'])
            ->whereIn('peserta_id', $pesertaIds)->orderBy('id')->get(), 'peserta_id');
        $related['catatan_dosen'] = array_map(fn ($r) => $r['catatan'], self::firstBy(PenilaianAkademik::query()->toBase()
            ->select(['peserta_id', 'catatan'])
            ->whereIn('peserta_id', $pesertaIds)->orderBy('id')->get(), 'peserta_id'));
        $related['catatan_mentor'] = array_map(fn ($r) => $r['catatan'], self::firstBy(PenilaianMentor::query()->toBase()
            ->select(['peserta_id', 'catatan'])
            ->whereIn('peserta_id', $pesertaIds)->orderBy('id')->get(), 'peserta_id'));

        return $related;
    }

    /** @return array<int|string, array<string, mixed>> first row per key */
    private static function firstBy(iterable $rows, string $key): array
    {
        $out = [];
        foreach ($rows as $row) {
            $row = (array) $row;
            $out[$row[$key]] ??= $row;
        }

        return $out;
    }

    /** @return array<int|string, list<array<string, mixed>>> */
    private static function groupBy(iterable $rows, string $key): array
    {
        $out = [];
        foreach ($rows as $row) {
            $row = (array) $row;
            $out[$row[$key]][] = $row;
        }

        return $out;
    }

    /**
     * Core subjects first (fixed order), then any other subject that has at least one grade.
     */
    private function matpels(): Collection
    {
        if ($this->matpels !== null) {
            return $this->matpels;
        }

        $core = Matpel::query()
            ->whereIn('nama', self::CORE_MATPELS)
            ->orderBy('id')
            ->get()
            ->sortBy(fn ($m) => array_search($m->nama, self::CORE_MATPELS, true))
            ->values();

        $usedMatpelIds = DB::table('nilai')
            ->join('peserta', 'peserta.id', '=', 'nilai.peserta_id')
            ->join('akun', 'akun.id', '=', 'peserta.akun_id')
            ->where('akun.role', 'pendaftar')
            ->distinct()
            ->pluck('nilai.matpel_id');

        $additional = Matpel::query()
            ->whereIn('id', $usedMatpelIds)
            ->whereNotIn('nama', self::CORE_MATPELS)
            ->orderBy('id')
            ->get();

        return $this->matpels = $core->concat($additional)->values();
    }

    /** Number of certificate column pairs: the most any pendaftar has, at least 3. */
    private function sertifikatSlots(): int
    {
        if ($this->sertifikatSlots !== null) {
            return $this->sertifikatSlots;
        }

        $perPeserta = DB::table('sertifikat')
            ->join('peserta', 'peserta.id', '=', 'sertifikat.peserta_id')
            ->join('akun', 'akun.id', '=', 'peserta.akun_id')
            ->where('akun.role', 'pendaftar')
            ->groupBy('sertifikat.peserta_id')
            ->selectRaw('COUNT(*) AS total');

        $max = (int) DB::query()->fromSub($perPeserta, 'per_peserta')->max('total');

        return $this->sertifikatSlots = max(self::MIN_SERTIFIKAT_SLOTS, $max);
    }

    /**
     * One export row. Same values and rules as the former controller code.
     *
     * @param  array<string, mixed>  $akun
     * @param  list<int>  $matpelIds  ordered subject ids (core first)
     * @return list<int|float|string>
     */
    private function buildRow(array $akun, int $no, array $related, array $matpelIds, int $slots, string $storageBase): array
    {
        $peserta = $related['peserta'][$akun['id']] ?? null;
        $pesertaId = $peserta['id'] ?? null;
        $daftar = $pesertaId !== null ? ($related['daftar'][$pesertaId] ?? null) : null;
        $berkas = $pesertaId !== null ? ($related['berkas'][$pesertaId] ?? null) : null;
        $nilaiRows = $pesertaId !== null ? ($related['nilai'][$pesertaId] ?? []) : [];

        $nilaiByKey = [];
        $nilaiCountBySemester = [];
        foreach ($nilaiRows as $n) {
            $nilaiByKey[$n['semester'] . '_' . $n['matpel_id']] = $n['nilai']; // last wins, like keyBy()
            $nilaiCountBySemester[$n['semester']] = ($nilaiCountBySemester[$n['semester']] ?? 0) + 1;
        }

        // Same output as url('storage/' . $path), without ~25 UrlGenerator calls per row.
        $storageUrl = static fn ($path): string => $storageBase . trim((string) $path, '/');
        $fileUrl = static fn ($path, string $empty = '-'): string => $path
            ? (str_starts_with((string) $path, 'http') ? (string) $path : $storageUrl($path))
            : $empty;

        // Status Seleksi Administrasi
        $statusAdmin = match ($daftar['status'] ?? null) {
            'lulus' => 'LULUS',
            'tidak_lulus' => 'TIDAK LULUS',
            default => 'MENUNGGU VERIFIKASI',
        };

        // Progres Berkas
        $progress = $peserta ? Peserta::calculateProgress($peserta, $berkas, $nilaiCountBySemester) : 0;
        if (! $peserta) {
            $progressText = 'BELUM ISI BIODATA (0%)';
        } elseif (! ($akun['email_verified_at'] ?? null)) {
            $progressText = 'BELUM VERIFIKASI EMAIL (OTP)';
        } elseif ($progress < 100) {
            $progressText = "BELUM LENGKAP ({$progress}%)";
        } else {
            $progressText = '100% LENGKAP';
        }

        $tanggalDaftar = self::formatDateTime($daftar['created_at'] ?? null)
            ?? self::formatDateTime($akun['created_at'] ?? null)
            ?? '-';

        // Akun yang dihapus disimpan sebagai "deleted_<ts>_<nilai asli>"
        $cleanEmail = self::stripDeletedPrefix((string) $akun['email']);
        $cleanNisn = self::stripDeletedPrefix((string) ($peserta['nisn'] ?? '-'));

        $kabupaten = ($peserta['kabupaten'] ?? null) ?: (($daftar['kabupaten'] ?? null) ?: '-');
        $provinsi = ($peserta['provinsi'] ?? null) ?: (($daftar['provinsi'] ?? null) ?: '-');
        $kabLower = strtolower($kabupaten);
        $wilayah = 'DI LUAR JABODETABEK';
        foreach (self::JABODETABEK as $city) {
            if (str_contains($kabLower, $city)) {
                $wilayah = 'JABODETABEK';
                break;
            }
        }

        $prodi1 = $peserta['pilihan_prodi'] ?? '-';
        $prodi2 = $peserta['pilihan_prodi2'] ?? '-';
        if (str_contains($prodi1, ' | ')) {
            $parts = explode(' | ', $prodi1);
            $prodi1 = $parts[0] ?? $prodi1;
            if ($prodi2 === '-' && isset($parts[1])) {
                $prodi2 = $parts[1];
            }
        }

        $tahunLulus = ($peserta['tahun_lulus'] ?? null) ?: (($daftar['tahun_lulus'] ?? null) ?: '-');
        if (is_string($tahunLulus) && ctype_digit($tahunLulus)) {
            $tahunLulus = (int) $tahunLulus; // keep it a number in Excel
        }

        $tglLahir = $peserta['tgl_lahir'] ?? null;

        // A. Data Identitas
        $row = [
            $no,
            $statusAdmin,
            $progressText,
            $tanggalDaftar,
            $akun['nama'],
            $cleanEmail,
            $peserta['jenis_kelamin'] ?? ($daftar['jenis_kelamin'] ?? '-'),
            ($peserta['no_whatsapp'] ?? null) ?: (($daftar['no_wa'] ?? null) ?: '-'),
            $cleanNisn,
            ($peserta['nama_sekolah'] ?? null) ?: (($daftar['asal_sekolah'] ?? null) ?: '-'),
            $provinsi,
            $kabupaten,
            $wilayah,
            ($daftar['ttl'] ?? null) ?: ($tglLahir ? date('d/m/Y', strtotime($tglLahir)) : '-'),
            $tahunLulus,
            $peserta['telp_sekolah'] ?? '-',
            $peserta['no_guru_bk'] ?? '-',
            $prodi1,
            $prodi2,
            $daftar['sumber_informasi'] ?? '-',
            $daftar['kode_referral'] ?? '-',
            ($pesertaId !== null ? ($related['catatan_dosen'][$pesertaId] ?? null) : null) ?? '-',
            ($pesertaId !== null ? ($related['catatan_mentor'][$pesertaId] ?? null) : null) ?? '-',
        ];

        // B. Data Akademik Rinci (S1-S5)
        $grandTotalAcademic = 0;
        $totalMatpelCount = 0;

        for ($sem = 1; $sem <= self::SEMESTERS; $sem++) {
            $semSum = 0;
            $semCount = 0;

            foreach ($matpelIds as $matpelId) {
                $val = $nilaiByKey["{$sem}_{$matpelId}"] ?? 0;

                $row[] = $val > 0 ? (float) $val : '-';

                if ($val > 0) {
                    $semSum += $val;
                    $semCount++;
                    $grandTotalAcademic += $val;
                    $totalMatpelCount++;
                }
            }

            // Rata-rata semester: pakai nilai tersimpan di daftar bila ada, selain itu hitung.
            $avgSemester = $daftar["avg_semester_{$sem}"] ?? null;
            if ($daftar && $avgSemester > 0) {
                $row[] = round((float) $avgSemester, 2);
            } else {
                $row[] = $semCount > 0 ? round($semSum / $semCount, 2) : 0;
            }
        }

        // C. Total & Rata-rata Akademik
        $rataRata = $daftar['rata_rata_nilai'] ?? null;
        $row[] = round($grandTotalAcademic, 2);
        $row[] = ($daftar && $rataRata > 0)
            ? round((float) $rataRata, 2)
            : ($totalMatpelCount > 0 ? round($grandTotalAcademic / $totalMatpelCount, 2) : 0);

        // D. Dokumen Berkas & Link
        $row[] = $fileUrl($berkas['foto'] ?? null);
        for ($s = 1; $s <= 6; $s++) {
            $row[] = $fileUrl($berkas["rapor{$s}"] ?? null);
        }
        $row[] = $fileUrl($berkas['ijazah'] ?? null);
        $row[] = $fileUrl($berkas['personal_statement'] ?? null);
        $row[] = $fileUrl($berkas['surat_rekom'] ?? null);
        $row[] = $fileUrl($berkas['study_plan'] ?? null);

        // Khusus DKV: Surat Buta Warna & Portofolio
        if (str_contains($prodi1, 'DKV') || str_contains($prodi2, 'DKV')) {
            $row[] = $fileUrl($berkas['surat_buta_warna'] ?? null, 'BELUM UNGGAH');
            $row[] = $fileUrl($berkas['portfolio'] ?? null, 'BELUM UNGGAH');
        } else {
            $row[] = 'N/A';
            $row[] = 'N/A';
        }

        // Video Motivasi & Sosmed
        $row[] = ($berkas['motivasi_video'] ?? null) ?: '-';
        $row[] = ($berkas['motivasi_video_tiktok'] ?? null) ?: '-';
        $row[] = ($peserta['link_ig'] ?? null) ?: '-';
        $row[] = ($peserta['link_tiktok'] ?? null) ?: '-';
        $row[] = ($peserta['link_twibbon'] ?? null) ?: '-';
        foreach ([
            'bukti_follow_ig_beasiswamncu',
            'bukti_follow_ig_mncu',
            'bukti_follow_tiktok_beasiswamncu',
            'bukti_follow_tiktok_mncu',
            'bukti_follow_ig_mncunation',
            'bukti_follow_ig_thearetegen',
        ] as $field) {
            $row[] = ($berkas[$field] ?? null) ? $storageUrl($berkas[$field]) : '-';
        }

        // Sertifikat Prestasi
        $sertifikats = $pesertaId !== null ? ($related['sertifikat'][$pesertaId] ?? []) : [];
        for ($i = 0; $i < $slots; $i++) {
            $sertifikat = $sertifikats[$i] ?? null;
            $row[] = $sertifikat ? ($sertifikat['nama'] ?? '-') : '-';
            $row[] = $sertifikat ? $fileUrl($sertifikat['file'] ?? null) : '-';
        }

        // Status Ujian & Beasiswa
        $row[] = match ($peserta['status_seleksi_ujian'] ?? null) {
            'lulus' => 'LULUS',
            'tidak_lulus' => 'TIDAK LULUS',
            default => 'MENUNGGU UJIAN / BELUM',
        };
        $row[] = self::formatNominal($daftar['nominal_beasiswa'] ?? null);
        $row[] = ($daftar['status_wawancara_bod'] ?? null) ?: '-';

        return $row;
    }

    /** "Y-m-d H:i:s" from the DB => "d/m/Y H:i" (same result as the Carbon timestamp cast). */
    private static function formatDateTime(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        $timestamp = strtotime((string) $value);

        return $timestamp === false ? (string) $value : date('d/m/Y H:i', $timestamp);
    }

    /**
     * nominal_beasiswa is a free string column: "5000000", but also "75%", "100%" or
     * "Mandiri" (set by the BOD interview flow / batch import). number_format() throws a
     * TypeError on those, which used to abort the whole export, so only format numbers.
     */
    private static function formatNominal(mixed $nominal): string
    {
        if (! $nominal) {
            return '-';
        }

        return is_numeric($nominal)
            ? 'Rp ' . number_format((float) $nominal, 0, ',', '.')
            : (string) $nominal;
    }

    private static function stripDeletedPrefix(string $value): string
    {
        if (str_starts_with($value, 'deleted_')) {
            return explode('_', $value, 3)[2] ?? $value;
        }

        return $value;
    }
}

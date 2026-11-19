<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Daftar;
use App\Models\Peserta;
use App\Models\PenilaianAkademik;
use Illuminate\Http\Request;

class BatchSeleksiController extends Controller
{
    /**
     * Show the batch import form / results.
     */
    public function index()
    {
        if (!in_array(auth()->user()->role, ['admin', 'akademik', 'palugada', 'god'])) {
            return abort(403);
        }
        return view('admin.beasiswa.batch_import');
    }

    /**
     * Parse pasted text and bulk-update all matching candidates.
     */
    public function import(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'akademik', 'palugada', 'god'])) {
            return abort(403);
        }

        $request->validate([
            'data_input' => 'required|string',
        ]);

        $rawText  = $request->data_input;
        $lines    = preg_split('/\r\n|\r|\n/', trim($rawText));

        $candidates = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Split by tab (TSV from Excel/Sheets)
            $cols = preg_split('/\t/', $line);

            // Expect at least 5 columns
            if (count($cols) < 5) continue;

            // Skip header rows
            $first = strtolower(trim($cols[0]));
            if (in_array($first, ['no', 'no.', 'no,']) || !is_numeric(trim($cols[0]))) continue;

            $nama      = trim($cols[1]);
            $sekolah   = trim($cols[2]);
            $prodi     = trim($cols[3]);
            
            // If we have 5 columns, the format is: No | Nama | Sekolah | Prodi | Beasiswa (missing Kelas)
            // If we have 6 columns, the format is: No | Nama | Sekolah | Prodi | Kelas | Beasiswa
            if (count($cols) == 5) {
                $kelas    = 'Reguler';
                $beasiswa = trim($cols[4]);
            } else {
                $kelas    = trim($cols[4]);
                $beasiswa = trim($cols[5]);
            }

            if (empty($nama)) continue;

            // Normalize beasiswa to "75%" or "100%" etc.
            $beasiswa = preg_replace('/[^0-9%]/', '', $beasiswa);
            if (empty($beasiswa)) $beasiswa = '75%';
            if (strpos($beasiswa, '%') === false) $beasiswa .= '%';

            // Normalize kelas
            $kelasNorm = strtolower(trim($kelas));

            $candidates[] = compact('nama', 'sekolah', 'prodi', 'kelas', 'kelasNorm', 'beasiswa');
        }

        if (empty($candidates)) {
            return back()->withInput()->with('error', 'Tidak ada data yang berhasil dibaca. Pastikan format tabel sudah benar (minimal 5 atau 6 kolom dipisah tab).');
        }

        $matched   = [];
        $notFound  = [];

        foreach ($candidates as $c) {
            // Try to find by exact name first, then fuzzy
            $peserta = Peserta::where('nama', $c['nama'])->first()
                ?? Peserta::whereRaw('LOWER(TRIM(nama)) = ?', [strtolower(trim($c['nama']))])->first()
                ?? Peserta::where('nama', 'like', '%' . trim($c['nama']) . '%')->first();

            if (!$peserta) {
                $notFound[] = $c['nama'];
                continue;
            }

            // Determine status_wawancara_bod equivalent label (for record keeping)
            $persen = preg_replace('/[^0-9]/', '', $c['beasiswa']);
            $kelasLabel = ucfirst(strtolower($c['kelas']));
            // Normalize kelas label
            if (stripos($kelasLabel, 'exel') !== false || stripos($kelasLabel, 'excel') !== false) {
                $kelasLabel = 'Exellent';
            } elseif (stripos($kelasLabel, 'eksekutif') !== false || stripos($kelasLabel, 'executive') !== false) {
                $kelasLabel = 'Eksekutif';
            } else {
                $kelasLabel = 'Reguler';
            }

            // Update daftar
            $daftar = $peserta->daftar;
            if ($daftar) {
                $daftar->update([
                    'nominal_beasiswa' => $c['beasiswa'],
                    'status'           => 'lulus',
                ]);
            } else {
                // Create daftar if not exist
                Daftar::create([
                    'peserta_id'       => $peserta->id,
                    'nominal_beasiswa' => $c['beasiswa'],
                    'status'           => 'lulus',
                ]);
            }

            // Update or create penilaian akademik
            PenilaianAkademik::updateOrCreate(
                ['peserta_id' => $peserta->id],
                [
                    'penilai_id'             => auth()->id(),
                    'rekomendasi_prodi_1'    => $c['prodi'],
                    'rekomendasi_kelas'      => $kelasLabel,
                    'rekomendasi_beasiswa'   => 'Beasiswa ' . $c['beasiswa'],
                    'rekomendasi_akhir'      => 'Lolos',
                ]
            );

            $matched[] = [
                'nama'     => $peserta->nama,
                'prodi'    => $c['prodi'],
                'kelas'    => $kelasLabel,
                'beasiswa' => $c['beasiswa'],
            ];
        }

        return back()->withInput()->with([
            'import_matched'  => $matched,
            'import_notfound' => $notFound,
            'import_total'    => count($candidates),
        ]);
    }
}

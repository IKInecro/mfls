<?php

namespace App\Http\Controllers;

use App\Models\DispensasiUjian;
use App\Models\JawabanUjian;
use App\Models\NilaiUjian;
use App\Models\Peserta;
use App\Models\SesiUjian;
use App\Models\Soal;
use App\Models\Ujian;
use Illuminate\Http\Request;

class UjianPesertaController extends Controller
{
    private function peserta(): Peserta
    {
        $peserta = auth()->user()->peserta;
        abort_unless($peserta, 403, 'Profil peserta tidak ditemukan.');

        return $peserta;
    }

    /** Daftar test untuk peserta (kartu responsif). */
    public function index()
    {
        $peserta = $this->peserta();

        $cards = Ujian::orderBy('id')->get()->map(function ($ujian) use ($peserta) {
            $submitted = JawabanUjian::where('ujian_id', $ujian->id)
                ->where('peserta_id', $peserta->id)
                ->exists();

            $dispensasi = DispensasiUjian::where('ujian_id', $ujian->id)
                ->where('peserta_id', $peserta->id)
                ->sum('tambahan_menit') > 0;

            $sesi = SesiUjian::where('ujian_id', $ujian->id)
                ->where('peserta_id', $peserta->id)
                ->first();

            // Sesi lama peserta yang sudah final submit → bersihkan
            if ($submitted && !$dispensasi && $sesi) {
                $sesi->delete();
                $sesi = null;
            }

            $redo = $submitted && $dispensasi;
            $sisa = $sesi ? $ujian->sisaDetik($sesi) : null;

            if ($submitted && !$redo) {
                $nilai = NilaiUjian::where('ujian_id', $ujian->id)
                    ->where('peserta_id', $peserta->id)
                    ->value('skor_rata');
                if ($nilai === null) {
                    $nilai = JawabanUjian::where('ujian_id', $ujian->id)
                        ->where('peserta_id', $peserta->id)
                        ->value('nilai');
                }
                $state = 'selesai';
            } elseif ($sesi && $sesi->terkunci) {
                $state = 'terkunci';
            } elseif (!$ujian->isOpen() && !$sesi) {
                $state = 'tertutup';
            } elseif ($sesi && $sisa <= 0 && !$redo) {
                $state = 'waktu_habis';
            } elseif ($sesi) {
                $state = 'lanjut';
            } else {
                $state = 'mulai';
            }

            return [
                'ujian' => $ujian,
                'state' => $state,
                'nilai' => $nilai ?? null,
                'sisa' => $sisa,
            ];
        });

        return view('pendaftar.ujian.index', compact('cards'));
    }

    /** Halaman pengerjaan CBT — memulai sesi bila belum ada. */
    public function show($id)
    {
        $peserta = $this->peserta();
        $ujian = Ujian::findOrFail($id);

        $submitted = JawabanUjian::where('ujian_id', $ujian->id)
            ->where('peserta_id', $peserta->id)
            ->exists();

        $dispensasi = DispensasiUjian::where('ujian_id', $ujian->id)
            ->where('peserta_id', $peserta->id)
            ->sum('tambahan_menit') > 0;

        $existingSesi = SesiUjian::where('ujian_id', $ujian->id)
            ->where('peserta_id', $peserta->id)
            ->first();

        if ($existingSesi && $existingSesi->terkunci) {
            return redirect()->route('ujian.index')->with('error', "Ujian \"{$ujian->nama}\" Anda TERKUNCI karena terdeteksi melanggar batas fokus layar (3x beralih tab/aplikasi). Silakan hubungi Panitia atau Administrator untuk membuka kunci ujian Anda.");
        }

        $sesi = SesiUjian::firstOrCreate(
            ['ujian_id' => $ujian->id, 'peserta_id' => $peserta->id],
            ['mulai_pada' => now()]
        );

        if ($submitted && !$dispensasi) {
            $sesi->delete(); // final submit sudah → sesi lama gak berguna

            return redirect()->route('ujian.index')->with('info', "Anda sudah mengerjakan \"{$ujian->nama}\".");
        }

        // Belum pernah mulai dan ujian tertutup → gak boleh masuk
        if ($sesi->wasRecentlyCreated && !$ujian->isOpen()) {
            $sesi->delete();

            return redirect()->route('ujian.index')->with('error', 'Ujian ini belum dibuka oleh panitia.');
        }

        $soals = Soal::where('ujian_id', $ujian->id)
            ->orderBy('id')
            ->get([
                'id', 'pertanyaan', 'gambar',
                'opsi_a', 'opsi_a_image', 'opsi_b', 'opsi_b_image',
                'opsi_c', 'opsi_c_image', 'opsi_d', 'opsi_d_image',
                'opsi_e', 'opsi_e_image', 'kategori',
            ])
            ->map(function ($soal) {
                $data = $soal->toArray();
                foreach (['gambar', 'opsi_a_image', 'opsi_b_image', 'opsi_c_image', 'opsi_d_image', 'opsi_e_image'] as $col) {
                    $data[$col] = $soal->{$col} ? asset('storage/'.$soal->{$col}) : null;
                }

                return $data;
            });

        $sisaDetik = $ujian->sisaDetik($sesi);
        if (!$ujian->isOpen()) {
            // Ditutup admin / jadwal lewat di tengah sesi → paksa finalkan saat
            // halaman (dimuat ulang) — draft yang tersimpan jadi jawaban akhir.
            $sisaDetik = 0;
        }

        return view('pendaftar.ujian.kerjakan', [
            'ujian' => $ujian,
            'soals' => $soals,
            'sisaDetik' => $sisaDetik,
            'savedAnswers' => $sesi->draft(),
        ]);
    }

    /**
     * Final submit — delegasi ke Api\SoalController::submit (satu-satunya
     * logika penilaian: skor bobot, narasi AI pemetaan diri, dsb). Endpoint API
     * lama jalan tanpa middleware session, jadi halaman web memanggil jalur
     * session-protected ini; app external tetap pakai /api/soal/submit.
     */
    public function submit(Request $request, $id)
    {
        $request->merge(['ujian_id' => (int) $id]);

        return app(\App\Http\Controllers\Api\SoalController::class)->submit($request);
    }

    /** Autosave draft jawaban (JSON). */
    public function draft(Request $request, $id)
    {
        $peserta = $this->peserta();
        $ujian = Ujian::findOrFail($id);

        $data = $request->validate([
            'jawaban' => 'required|array',
            'jawaban.*' => 'nullable|string|max:10',
        ]);

        $sesi = SesiUjian::where('ujian_id', $ujian->id)
            ->where('peserta_id', $peserta->id)
            ->first();

        if (!$sesi) {
            return response()->json(['status' => 'error', 'message' => 'Sesi belum dimulai.'], 403);
        }

        if ($sesi->terkunci) {
            return response()->json(['status' => 'error', 'message' => 'Sesi ujian Anda sedang terkunci akibat pelanggaran fokus layar.'], 403);
        }

        if (!$ujian->isOpen()) {
            return response()->json(['status' => 'error', 'message' => 'Ujian sudah ditutup panitia.'], 403);
        }

        if ($ujian->sisaDetik($sesi) <= 0) {
            return response()->json(['status' => 'error', 'message' => 'Waktu sudah habis.'], 422);
        }

        $sesi->update(['jawaban' => json_encode($data['jawaban'])]);

        return response()->json(['status' => 'ok']);
    }

    /** Finalisasi sesi yang waktu-nya habis tanpa jawaban apa pun (auto-submit kosong). */
    public function selesai($id)
    {
        $peserta = $this->peserta();
        $ujian = Ujian::findOrFail($id);

        $sesi = SesiUjian::where('ujian_id', $ujian->id)
            ->where('peserta_id', $peserta->id)
            ->first();

        if ($sesi && $ujian->sisaDetik($sesi) <= 0) {
            $sesi->delete();
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Reset ujian akibat pelanggaran fokus (meninggalkan layar ujian / beralih tab).
     * Menghapus sesi aktif agar peserta dapat mengulang kembali dari nol secara mandiri.
     */
    public function resetPelanggaran(Request $request, $id)
    {
        $peserta = $this->peserta();
        $ujian = Ujian::findOrFail($id);

        $sesi = SesiUjian::where('ujian_id', $ujian->id)
            ->where('peserta_id', $peserta->id)
            ->first();

        if ($sesi) {
            $sesi->delete();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'reset',
                'message' => 'Ujian telah di-reset karena terdeteksi meninggalkan layar ujian.',
                'redirect' => route('ujian.index'),
            ]);
        }

        return redirect()->route('ujian.index')->with('error', 'Ujian Anda telah di-reset dari nol karena terdeteksi meninggalkan layar ujian. Pastikan koneksi internet stabil dan tetap fokus pada layar ujian saat mengulang!');
    }

    /**
     * Kunci sesi ujian akibat pelanggaran fokus (meninggalkan layar ujian / beralih tab sebanyak 3 kali).
     * Sesi ditandai terkunci dan peserta tidak bisa masuk lagi sampai dibuka oleh admin.
     */
    public function kunciPelanggaran(Request $request, $id)
    {
        $peserta = $this->peserta();
        $ujian = Ujian::findOrFail($id);

        $sesi = SesiUjian::where('ujian_id', $ujian->id)
            ->where('peserta_id', $peserta->id)
            ->first();

        if ($sesi) {
            $sesi->update([
                'terkunci' => true,
                'alasan_kunci' => 'Pelanggaran fokus layar (meninggalkan layar ujian / beralih tab 3 kali)',
                'pelanggaran_count' => 3,
                'terkunci_pada' => now(),
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'locked',
                'message' => 'Ujian telah dikunci karena terdeteksi meninggalkan layar ujian sebanyak 3 kali.',
                'redirect' => route('ujian.index'),
            ]);
        }

        return redirect()->route('ujian.index')->with('error', "Ujian \"{$ujian->nama}\" Anda telah dikunci karena terdeteksi meninggalkan layar ujian sebanyak 3 kali. Silakan hubungi panitia atau admin untuk membuka kunci ujian.");
    }
}

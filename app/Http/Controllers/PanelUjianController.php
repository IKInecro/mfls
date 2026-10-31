<?php

namespace App\Http\Controllers;

use App\Mail\UjianLinkMail;
use App\Models\JawabanUjian;
use App\Models\Peserta;
use App\Models\Ujian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PanelUjianController extends Controller
{
    public function __construct()
    {
        // Ngikutin aturan Bank Soal: hanya admin & akademik
        abort_unless(in_array(auth()->user()?->role, ['admin', 'akademik'], true), 403);
    }

    private function logAktivitas($aksi, $targetTipe = null, $targetId = null, $deskripsi = null)
    {
        \App\Models\RiwayatAktivitas::create([
            'pelaku_id' => auth()->id(),
            'aksi' => $aksi,
            'target_tipe' => $targetTipe,
            'target_id' => $targetId,
            'deskripsi' => $deskripsi,
        ]);
    }

    public function index()
    {
        $ujians = Ujian::withCount('soals')
            ->withCount('jawabanUjians')
            ->orderBy('id')
            ->get();

        $totalPeserta = Peserta::count();

        return view('admin.panel_ujian.index', compact('ujians', 'totalPeserta'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:191|unique:ujian,nama',
            'durasi' => 'required|integer|min:1|max:600',
            'jadwal_mulai' => 'nullable|date',
            'jadwal_selesai' => 'nullable|date|after:jadwal_mulai',
        ]);

        $data['is_active'] = true;
        $data['override_status'] = 'auto';

        $ujian = Ujian::create($data);

        $this->logAktivitas('Buat Ujian', 'Ujian', $ujian->id, "Membuat test baru: {$ujian->nama}");

        return back()->with('success', "Test \"{$ujian->nama}\" berhasil dibuat. Silakan isi soal di Bank Soal.");
    }

    public function updateJadwal(Request $request, $id)
    {
        $ujian = Ujian::findOrFail($id);

        $data = $request->validate([
            'jadwal_mulai' => 'nullable|date',
            'jadwal_selesai' => 'nullable|date|after:jadwal_mulai',
        ]);

        $ujian->update($data);

        $this->logAktivitas('Atur Jadwal Ujian', 'Ujian', $id, "Mengubah jadwal ujian: {$ujian->nama}");

        return back()->with('success', "Jadwal \"{$ujian->nama}\" diperbarui.");
    }

    public function setOverride(Request $request, $id)
    {
        $request->validate(['override_status' => 'required|in:auto,buka,tutup']);

        $ujian = Ujian::findOrFail($id);
        $ujian->update(['override_status' => $request->override_status]);

        $label = ['auto' => 'otomatis (ikut jadwal)', 'buka' => 'DIBUKA manual', 'tutup' => 'DITUTUP manual'][$request->override_status];
        $this->logAktivitas('Override Ujian', 'Ujian', $id, "Ujian {$ujian->nama}: {$label}");

        return back()->with('success', "Status \"{$ujian->nama}\": {$label}.");
    }

    public function update(Request $request, $id)
    {
        $ujian = Ujian::findOrFail($id);

        $data = $request->validate([
            'nama' => 'required|string|max:191|unique:ujian,nama,'.$id,
            'durasi' => 'required|integer|min:1|max:600',
        ]);

        $ujian->update($data);

        $this->logAktivitas('Edit Ujian', 'Ujian', $id, "Mengubah test: {$ujian->nama}");

        return back()->with('success', "Test \"{$ujian->nama}\" diperbarui.");
    }

    public function destroy($id)
    {
        $ujian = Ujian::findOrFail($id);
        $nama = $ujian->nama;

        $ujian->delete(); // soal, sesi, jawaban, nilai ikut cascade

        $this->logAktivitas('Hapus Ujian', 'Ujian', $id, "Menghapus test: {$nama}");

        return back()->with('success', "Test \"{$nama}\" beserta soal & nilainya dihapus.");
    }

    /** Kirim email undangan ujian (template ujian_link) ke peserta yang lulus seleksi. */
    public function kirimUndangan($id)
    {
        $ujian = Ujian::findOrFail($id);
        $linkUjian = route('ujian.index');

        $pesertas = Peserta::where('status_seleksi_ujian', 'lulus')
            ->with('akun')
            ->get();

        $sukses = 0;
        $gagal = 0;

        foreach ($pesertas as $peserta) {
            $email = $peserta->akun->email ?? null;
            if (!$email) {
                $gagal++;
                continue;
            }
            $nama = $peserta->akun->nama ?? $peserta->nama;

            try {
                Mail::mailer('hostinger')->send(
                    'emails.ujian_link',
                    array_merge(
                        \App\Models\EmailTemplate::viewData('ujian_link', ['nama' => $nama, 'linkUjian' => $linkUjian]),
                        ['templateBody' => \App\Models\EmailTemplate::renderBody('ujian_link', ['nama' => $nama, 'linkUjian' => $linkUjian])]
                    ),
                    function ($message) use ($email, $nama, $linkUjian) {
                        $message->to($email, $nama)
                            ->subject(\App\Models\EmailTemplate::subjectOrDefault('ujian_link', 'Undangan Ujian Online MNCU Future Leader Scholarship 2026', [
                                'nama' => $nama,
                                'linkUjian' => $linkUjian,
                            ]));
                    }
                );
                $sukses++;
            } catch (\Throwable $e) {
                report($e);
                $gagal++;
            }
        }

        $this->logAktivitas('Kirim Undangan Ujian', 'Ujian', $id, "Undangan \"{$ujian->nama}\": {$sukses} terkirim, {$gagal} gagal");

        return back()->with(
            $sukses > 0 ? 'success' : 'error',
            "Undangan ujian \"{$ujian->nama}\": {$sukses} terkirim" . ($gagal ? ", {$gagal} gagal." : ".")
        );
    }
}

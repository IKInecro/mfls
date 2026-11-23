<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\RateLimiter;

class PendaftarController extends Controller
{
    use \App\Traits\ImageCompressor;

    public function index()
    {
        $peserta = Auth::user()->peserta;
        if (!$peserta) {
            Auth::logout();
            return redirect('/login')->with('error', 'Data profil pendaftar tidak ditemukan. Silakan hubungi admin.');
        }
        
        $progress = $peserta->progress;

        // --- Fetch Real Activity Logs ---
        $logs = \App\Models\RiwayatAktivitas::where('pelaku_id', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        $history = [];
        foreach ($logs as $log) {
            $icon = 'user';
            $aksi = strtolower($log->aksi);
            if (str_contains($aksi, 'update') || str_contains($aksi, 'simpan') || str_contains($aksi, 'biodata'))
                $icon = 'edit';
            if (str_contains($aksi, 'upload') || str_contains($aksi, 'berkas') || str_contains($aksi, 'file'))
                $icon = 'upload';
            if (str_contains($aksi, 'nilai') || str_contains($aksi, 'rapor'))
                $icon = 'chart-bar';
            if (str_contains($aksi, 'prodi'))
                $icon = 'graduation-cap';

            $history[] = [
                'title' => $log->aksi,
                'desc' => $log->deskripsi,
                'date' => $log->created_at->diffForHumans(),
                'icon' => $icon,
                'status' => 'completed'
            ];
        }

        // Seed initial history if empty
        if (count($history) == 0) {
            $history[] = [
                'title' => 'Selamat Datang!',
                'desc' => 'Akun Anda berhasil terdaftar di sistem MNCU Future Leader Scholarship.',
                'date' => auth()->user()->created_at->format('d M Y'),
                'icon' => 'user',
                'status' => 'completed'
            ];
        }

        // Check if the user has added supporting subjects (at least 2 custom matpels)
        $coreMatpelIds = \App\Models\Matpel::whereIn('nama', ['Bahasa Indonesia', 'Matematika Wajib', 'Bahasa Inggris'])->pluck('id');
        $supportingCount = \App\Models\Nilai::where('peserta_id', $peserta->id)
            ->whereNotIn('matpel_id', $coreMatpelIds)
            ->distinct('matpel_id')
            ->count('matpel_id');
        $hasSupportingSubject = $supportingCount >= 2;
        $berkas = $peserta->berkas;

        return view('pendaftar.dashboard', compact('peserta', 'berkas', 'progress', 'history', 'hasSupportingSubject'));
    }

    private function logAktivitas($aksi, $targetTipe = null, $targetId = null, $deskripsi = null)
    {
        \App\Models\RiwayatAktivitas::create([
            'pelaku_id' => auth()->id(),
            'aksi' => $aksi,
            'target_tipe' => $targetTipe,
            'target_id' => $targetId,
            'deskripsi' => $deskripsi
        ]);
    }

    public function biodata()
    {
        $peserta = Auth::user()->peserta;
        return view('pendaftar.biodata', compact('peserta'));
    }

    public function storeBiodata(Request $request)
    {
        $peserta = Auth::user()->peserta;
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'nisn' => 'required|string|max:20|unique:peserta,nisn,' . $peserta->id,
            'no_whatsapp' => 'required|string|max:20|unique:peserta,no_whatsapp,' . $peserta->id,
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan,L,P',
            'tgl_lahir' => 'required|date',
            'provinsi' => 'required|string|max:100',
            'kabupaten' => 'required|string|max:100',
            'nama_sekolah' => 'required|string|max:255',
            'tahun_lulus' => 'required|integer',
            'no_guru_bk' => 'nullable|string|max:30',
            'kode_referral' => 'nullable|string|max:50',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nisn.unique' => 'NISN ini sudah terdaftar dalam sistem.',
            'no_whatsapp.unique' => 'Nomor WhatsApp ini sudah digunakan oleh pendaftar lain.',
        ]);

        // Sync name to Akun (Auth User)
        Auth::user()->update(['nama' => $request->nama]);

        $peserta->update($request->only([
            'nama', 'nisn', 'no_whatsapp', 'jenis_kelamin', 'tgl_lahir', 'provinsi', 'kabupaten', 'nama_sekolah', 'tahun_lulus', 'no_guru_bk'
        ]));

        if ($peserta->daftar) {
            $peserta->daftar->update([
                'kode_referral' => $request->kode_referral,
                'tahun_lulus' => $request->tahun_lulus,
                'ttl' => $request->tgl_lahir,
                'provinsi' => $request->provinsi,
                'kabupaten' => $request->kabupaten,
                'asal_sekolah' => $request->nama_sekolah,
                'jenis_kelamin' => $request->jenis_kelamin,
                'no_wa' => $request->no_whatsapp,
            ]);
        }

        $this->logAktivitas('Update Biodata', 'Peserta', $peserta->id, 'Memperbarui data profil dan biodata diri.');

        return back()->with('success', 'Biodata berhasil disimpan!');
    }

    public function berkas()
    {
        $peserta = Auth::user()->peserta;
        if (!$peserta) {
            Auth::logout();
            return redirect('/login')->with('error', 'Data profil pendaftar tidak ditemukan.');
        }

        $berkas = \App\Models\Berkas::where('peserta_id', $peserta->id)->first();
        $sertifikats = \App\Models\Sertifikat::where('peserta_id', $peserta->id)->get();
        
        $tahunLulus = (int) ($peserta->tahun_lulus ?? 2027);
        $maxSemester = ($tahunLulus < 2027) ? 6 : 5;

        return view('pendaftar.berkas', compact('peserta', 'berkas', 'sertifikats', 'maxSemester'));
    }

    public function storeBerkas(Request $request)
    {
        $request->validate([
            'foto' => 'nullable|image|max:10240',
            'kartu_identitas' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'kartu_keluarga' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'rapor1.*' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'rapor2.*' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'rapor3.*' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'rapor4.*' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'rapor5.*' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'rapor6.*' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'ijazah' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'personal_statement' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:10240',
            'study_plan' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:10240',
            'surat_rekomendasi_sekolah' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:10240',
            'motivasi_video' => 'nullable|string|max:1000',
            'motivasi_video_tiktok' => 'nullable|string|max:1000',
            'sertifikat.*' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'prestasi' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'nama_sertifikat' => 'nullable|string|max:255',
            'surat_buta_warna' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'portfolio' => 'nullable|max:10240', // Can be file or string
            'bukti_follow_ig_beasiswamncu' => 'nullable|image|max:10240',
            'bukti_follow_ig_mncu' => 'nullable|image|max:10240',
            'bukti_follow_ig_mncunation' => 'nullable|image|max:10240',
            'bukti_follow_ig_thearetegen' => 'nullable|image|max:10240',
            'bukti_follow_tiktok_beasiswamncu' => 'nullable|image|max:10240',
            'bukti_follow_tiktok_mncu' => 'nullable|image|max:10240',
        ]);

        $peserta = Auth::user()->peserta;
        $berkas = \App\Models\Berkas::firstOrNew(['peserta_id' => $peserta->id]);
        $uploadField = $request->input('upload_field');
        $raporFields = ['rapor1', 'rapor2', 'rapor3', 'rapor4', 'rapor5', 'rapor6'];
        $singleFields = [
            'foto', 'kartu_identitas', 'kartu_keluarga', 'ijazah', 'personal_statement', 'study_plan', 'surat_rekomendasi_sekolah',
            'bukti_follow_ig_beasiswamncu', 'bukti_follow_ig_mncu', 'bukti_follow_ig_mncunation', 'bukti_follow_ig_thearetegen',
            'bukti_follow_tiktok_beasiswamncu', 'bukti_follow_tiktok_mncu',
            'surat_buta_warna'
        ];

        // --- Handle Program Studi Selection (Dual Choice) ---
        if ($uploadField === 'program_studi') {
            $p1 = $request->input('pilihan_prodi1');
            $p2 = $request->input('pilihan_prodi2');
            
            if ($p1 && $p2) {
                if ($p1 === $p2) {
                    return back()->with('error', 'Pilihan 1 dan Pilihan 2 tidak boleh sama.');
                }
                $peserta->update(['pilihan_prodi' => $p1 . ' | ' . $p2]);
                $this->logAktivitas('Pembaruan Program Studi', 'Peserta', $peserta->id, "Mengubah pilihan program studi menjadi: $p1 dan $p2.");
                
                if (!str_contains($p1, 'DKV') && !str_contains($p2, 'DKV')) {
                    if ($berkas->surat_buta_warna) {
                        if (Storage::disk('public')->exists($berkas->surat_buta_warna)) {
                            Storage::disk('public')->delete($berkas->surat_buta_warna);
                        }
                        $berkas->surat_buta_warna = null;
                    }
                    if ($berkas->portfolio) {
                        if (Storage::disk('public')->exists($berkas->portfolio)) {
                            Storage::disk('public')->delete($berkas->portfolio);
                        }
                        $berkas->portfolio = null;
                    }
                }
            }
        }

        // --- Handle Link Video Motivasi ---
        if (!$uploadField || $uploadField === 'motivasi_video') {
            if ($request->has('motivasi_video')) {
                $val = trim((string) $request->input('motivasi_video'));
                if ($val !== '' && !preg_match('~^https?://~i', $val)) {
                    $val = 'https://' . $val;
                }
                $berkas->motivasi_video = $val ?: null;
            }
            if ($request->has('motivasi_video_tiktok')) {
                $val = trim((string) $request->input('motivasi_video_tiktok'));
                if ($val !== '' && !preg_match('~^https?://~i', $val)) {
                    $val = 'https://' . $val;
                }
                $berkas->motivasi_video_tiktok = $val ?: null;
            }
        }

        // --- Handle Portfolio (File or Link) ---
        if (!$uploadField || $uploadField === 'portfolio') {
            if ($request->hasFile('portfolio')) {
                $path = $request->file('portfolio')->store('berkas/' . $peserta->id, 'public');
                $berkas->portfolio = $path;
                // Only compress if it's an image, but portfolio is usually PDF.
                // compressImage has check inside usually, or I can skip it for PDFs.
                if (str_contains($request->file('portfolio')->getMimeType(), 'image')) {
                    $this->compressImage($path);
                }
            } elseif ($request->filled('portfolio')) {
                $berkas->portfolio = $request->portfolio;
            }
        }

        // --- Handle Single File Fields ---
        foreach ($singleFields as $field) {
            if ((!$uploadField || $uploadField === $field) && $request->hasFile($field)) {
                $path = $request->file($field)->store('berkas/' . $peserta->id, 'public');
                $berkas->$field = $path;
                $this->compressImage($path);
            }
        }

        // --- Handle Rapor (Array) ---
        foreach ($raporFields as $field) {
            if ((!$uploadField || $uploadField === $field) && $request->hasFile($field)) {
                $files = $request->file($field);
                $paths = [];
                if (is_array($files)) {
                    foreach ($files as $file) {
                        $p = $file->store('berkas/' . $peserta->id . '/' . $field, 'public');
                        $paths[] = $p;
                        $this->compressImage($p);
                    }
                }
                else {
                    $p = $files->store('berkas/' . $peserta->id . '/' . $field, 'public');
                    $paths[] = $p;
                    $this->compressImage($p);
                }
                $berkas->$field = json_encode($paths);
            }
        }

        $berkas->save();

        // --- Handle Sertifikat / Prestasi ---
        $sertifikatCount = 0;
        if ((!$uploadField || $uploadField === 'sertifikat' || $uploadField === 'prestasi') && ($request->hasFile('sertifikat') || $request->hasFile('prestasi'))) {
            $inputNama = trim((string) $request->input('nama_sertifikat')) ?: trim((string) $request->input('nama'));

            if ($request->hasFile('prestasi')) {
                $file = $request->file('prestasi');
                $path = $file->store('sertifikat/' . $peserta->id, 'public');
                $this->compressImage($path);
                $certName = $inputNama ?: ('Sertifikat ' . date('Y-m-d H:i'));
                \App\Models\Sertifikat::create([
                    'peserta_id' => $peserta->id,
                    'nama' => $certName,
                    'file' => $path,
                    'tahun' => (int) date('Y'),
                ]);
                $sertifikatCount++;
            }

            if ($request->hasFile('sertifikat')) {
                $files = $request->file('sertifikat');
                $sertifikatFiles = is_array($files) ? $files : [$files];
                foreach ($sertifikatFiles as $index => $file) {
                    $path = $file->store('sertifikat/' . $peserta->id, 'public');
                    $this->compressImage($path);
                    $certName = $inputNama ? ($inputNama . (count($sertifikatFiles) > 1 ? ' (' . ($index + 1) . ')' : '')) : ('Sertifikat ' . date('Y-m-d H:i'));
                    \App\Models\Sertifikat::create([
                        'peserta_id' => $peserta->id,
                        'nama' => $certName,
                        'file' => $path,
                        'tahun' => (int) date('Y'),
                    ]);
                    $sertifikatCount++;
                }
            }
        }

        // --- Log Aktivitas ---
        $uploaded = [];
        if ($request->hasFile('foto'))
            $uploaded[] = 'Pas Foto 4x6';
        if ($request->hasFile('ijazah'))
            $uploaded[] = 'Ijazah/SKL';
        if ($request->hasFile('personal_statement'))
            $uploaded[] = 'Personal Statement';
        if ($request->hasFile('study_plan'))
            $uploaded[] = 'Study Plan';
        if ($request->hasFile('surat_rekomendasi_sekolah'))
            $uploaded[] = 'Surat Rekomendasi Sekolah';
        if ($request->filled('motivasi_video'))
            $uploaded[] = 'Link Video Motivasi IG';
        if ($request->filled('motivasi_video_tiktok'))
            $uploaded[] = 'Link Video Motivasi TikTok';
        if ($request->hasFile('bukti_follow_ig_beasiswamncu'))
            $uploaded[] = 'Bukti Follow IG Beasiswa MNCU';
        if ($request->hasFile('bukti_follow_ig_mncu'))
            $uploaded[] = 'Bukti Follow IG MNC University';
        if ($request->hasFile('bukti_follow_tiktok_beasiswamncu'))
            $uploaded[] = 'Bukti Follow TikTok Beasiswa MNCU';
        if ($request->hasFile('bukti_follow_tiktok_mncu'))
            $uploaded[] = 'Bukti Follow TikTok MNC University';
        if ($request->hasFile('surat_buta_warna'))
            $uploaded[] = 'Surat Keterangan Tidak Buta Warna';
        if ($request->hasFile('portfolio') || ($uploadField === 'portfolio' && $request->filled('portfolio')))
            $uploaded[] = 'Portfolio (DKV)';
        for ($i = 1; $i <= 6; $i++) {
            if ($request->hasFile('rapor' . $i))
                $uploaded[] = "Scan Rapor S$i";
        }
        if ($sertifikatCount > 0)
            $uploaded[] = "$sertifikatCount Sertifikat Prestasi";

        if (count($uploaded) > 0) {
            $desc = 'Berhasil memperbarui dokumen: ' . implode(', ', $uploaded);
            $this->logAktivitas('Pembaruan Berkas', 'Berkas', $berkas->id, $desc);
        }

        return redirect()->back()->with('success', 'Berkas berhasil disimpan!');
    }

    public function twibbon()
    {
        $peserta = Auth::user()->peserta;
        if (!$peserta) {
            Auth::logout();
            return redirect('/login')->with('error', 'Data profil pendaftar tidak ditemukan.');
        }
        return view('pendaftar.twibbon', compact('peserta'));
    }

    public function storeTwibbon(Request $request)
    {
        $request->validate([
            'link_ig' => 'nullable|url|max:255',
            'link_tiktok' => 'nullable|url|max:255',
            'link_twibbon' => 'required|url|max:255',
        ]);

        $peserta = Auth::user()->peserta;
        $peserta->update([
            'link_ig' => $request->link_ig,
            'link_tiktok' => $request->link_tiktok,
            'link_twibbon' => $request->link_twibbon,
        ]);

        $this->logAktivitas('Update Media Sosial', 'Peserta', $peserta->id, 'Memperbarui link Twibbon' . ($request->filled('link_ig') || $request->filled('link_tiktok') ? ' dan Media Sosial.' : '.'));

        return back()->with('success', 'Tautan media sosial & Twibbon berhasil disimpan!');
    }

    public function nilai()
    {
        $peserta = Auth::user()->peserta;
        if (!$peserta) {
            Auth::logout();
            return redirect('/login')->with('error', 'Data profil pendaftar tidak ditemukan.');
        }

        $tahunLulus = (int) ($peserta->tahun_lulus ?? 2027);
        $maxSemester = ($tahunLulus < 2027) ? 6 : 5;

        // Ensure core subjects exist before querying them
        $coreSubjects = ['Bahasa Indonesia', 'Matematika Wajib', 'Bahasa Inggris'];
        foreach ($coreSubjects as $subjectName) {
            \App\Models\Matpel::firstOrCreate(['nama' => $subjectName]);
        }

        $matpels = \App\Models\Matpel::whereIn('nama', $coreSubjects)->get();
        // Fetch existing score
        $existingNilai = \App\Models\Nilai::where('peserta_id', $peserta->id)->get()->groupBy('matpel_id');

        // Also get custom matpels IDs that are NOT in the core list but have scores for this user
        $coreIds = $matpels->pluck('id')->toArray();
        $customNilaiBySemester = \App\Models\Nilai::with('matpel')
            ->where('peserta_id', $peserta->id)
            ->whereNotIn('matpel_id', $coreIds)
            ->get()
            ->groupBy('semester');

        $berkas = \App\Models\Berkas::where('peserta_id', $peserta->id)->first();

        return view('pendaftar.nilai', compact('peserta', 'matpels', 'berkas', 'existingNilai', 'customNilaiBySemester', 'maxSemester'));
    }

    public function storeNilai(Request $request)
    {
        $request->validate([
            'surat_buta_warna' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'rapor1.*' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'rapor2.*' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'rapor3.*' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'rapor4.*' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'rapor5.*' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'rapor6.*' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ]);

        $peserta = Auth::user()->peserta;
        if (!$peserta) {
            return back()->with('error', 'Data profil peserta tidak ditemukan.');
        }

        $tahunLulus = (int) ($peserta->tahun_lulus ?? 2027);
        $maxSemester = ($tahunLulus < 2027) ? 6 : 5;

        // 1. Parse & validate Core Subjects ($request->nilai)
        $coreScoresToSave = [];
        $coreScoresToDelete = [];

        if ($request->has('nilai') && is_array($request->nilai)) {
            foreach ($request->nilai as $matpelId => $semesters) {
                if (!is_array($semesters)) continue;
                foreach ($semesters as $sem => $rawVal) {
                    $sem = (int) $sem;
                    if ($sem < 1 || $sem > $maxSemester) continue;

                    $strVal = trim((string) $rawVal);
                    if ($strVal === '') {
                        // Tandai untuk dihapus jika dikosongkan oleh user
                        $coreScoresToDelete[] = [
                            'matpel_id' => (int) $matpelId,
                            'semester' => $sem
                        ];
                        continue;
                    }

                    // Normalisasi koma menjadi titik desimal
                    $cleanVal = str_replace(',', '.', $strVal);
                    if (!is_numeric($cleanVal)) {
                        return back()->withInput()->with('error', "Nilai mata pelajaran wajib pada Semester {$sem} harus berupa angka (skala 0 - 100).");
                    }

                    $numVal = (float) $cleanVal;
                    if ($numVal < 0 || $numVal > 100) {
                        return back()->withInput()->with('error', "Nilai mata pelajaran wajib pada Semester {$sem} harus berada di antara 0 sampai 100.");
                    }

                    $coreScoresToSave[] = [
                        'matpel_id' => (int) $matpelId,
                        'semester' => $sem,
                        'nilai' => round($numVal, 2)
                    ];
                }
            }
        }

        // 2. Parse & validate Custom Subjects ($request->custom_matpel)
        $customScoresToSave = [];
        $customCount = 0;

        if ($request->has('custom_matpel') && is_array($request->custom_matpel)) {
            foreach ($request->custom_matpel as $sem => $slots) {
                $sem = (int) $sem;
                if ($sem < 1 || $sem > $maxSemester) continue;
                if (!is_array($slots)) continue;

                foreach ($slots as $slotIdx => $slot) {
                    $nama = trim((string) ($slot['nama'] ?? ''));
                    $rawNilai = trim((string) ($slot['nilai'] ?? ''));

                    // Jika keduanya kosong, lewati
                    if ($nama === '' && $rawNilai === '') {
                        continue;
                    }

                    // Jika nama diisi tapi nilai kosong
                    if ($nama !== '' && $rawNilai === '') {
                        return back()->withInput()->with('error', "Nilai untuk mata pelajaran pendukung \"{$nama}\" pada Semester {$sem} belum diisi. Harap lengkapi nilai (skala 0 - 100).");
                    }

                    // Jika nilai diisi tapi nama kosong
                    if ($nama === '' && $rawNilai !== '') {
                        return back()->withInput()->with('error', "Nama mata pelajaran pendukung pada Semester {$sem} belum diisi.");
                    }

                    // Validasi angka
                    $cleanNilai = str_replace(',', '.', $rawNilai);
                    if (!is_numeric($cleanNilai)) {
                        return back()->withInput()->with('error', "Nilai untuk mata pelajaran \"{$nama}\" pada Semester {$sem} harus berupa angka yang valid.");
                    }

                    $numNilai = (float) $cleanNilai;
                    if ($numNilai < 0 || $numNilai > 100) {
                        return back()->withInput()->with('error', "Nilai untuk mata pelajaran \"{$nama}\" pada Semester {$sem} harus berada di antara 0 sampai 100.");
                    }

                    $customScoresToSave[] = [
                        'semester' => $sem,
                        'nama' => $nama,
                        'nilai' => round($numNilai, 2),
                    ];
                    $customCount++;
                }
            }
        }

        // Wajib minimal 2 mapel pendukung terisi
        if ($customCount < 2) {
            return back()->withInput()->with('error', 'Anda wajib menambahkan minimal 2 Mata Pelajaran Pendukung (Nama dan Nilai) pada rapor Anda.');
        }

        // 3. Handle File Uploads (Surat Buta Warna & Rapor)
        $berkas = \App\Models\Berkas::firstOrNew(['peserta_id' => $peserta->id]);

        if ($request->hasFile('surat_buta_warna')) {
            $path = $request->file('surat_buta_warna')->store('berkas/' . $peserta->id, 'public');
            $berkas->surat_buta_warna = $path;
            $this->compressImage($path);
        }

        for ($i = 1; $i <= $maxSemester; $i++) {
            $field = 'rapor' . $i;
            if ($request->hasFile($field)) {
                $existing = json_decode($berkas->$field, true) ?: [];
                $files = $request->file($field);
                if (is_array($files)) {
                    foreach ($files as $file) {
                        $p = $file->store('berkas/' . $peserta->id . '/' . $field, 'public');
                        $existing[] = $p;
                        $this->compressImage($p);
                    }
                } else {
                    $p = $files->store('berkas/' . $peserta->id . '/' . $field, 'public');
                    $existing[] = $p;
                    $this->compressImage($p);
                }
                $berkas->$field = json_encode(array_values(array_unique($existing)));
            }
        }
        $berkas->save();

        // 4. Save Core & Custom Nilai (Bypass redundant Google Sheets 50x loops, sync once at end)
        $coreSubjects = ['Bahasa Indonesia', 'Matematika Wajib', 'Bahasa Inggris'];
        $coreIds = \App\Models\Matpel::whereIn('nama', $coreSubjects)->pluck('id')->toArray();

        \App\Models\Nilai::withoutEvents(function () use ($peserta, $coreScoresToSave, $coreScoresToDelete, $customScoresToSave, $coreIds) {
            // Hapus nilai wajib yang dikosongkan
            foreach ($coreScoresToDelete as $del) {
                \App\Models\Nilai::where([
                    'peserta_id' => $peserta->id,
                    'matpel_id' => $del['matpel_id'],
                    'semester' => $del['semester'],
                ])->delete();
            }

            // Simpan / update nilai wajib
            foreach ($coreScoresToSave as $item) {
                \App\Models\Nilai::updateOrCreate(
                    ['peserta_id' => $peserta->id, 'matpel_id' => $item['matpel_id'], 'semester' => $item['semester']],
                    ['nilai' => $item['nilai']]
                );
            }

            // Hapus mapel pendukung lama untuk peserta ini
            \App\Models\Nilai::where('peserta_id', $peserta->id)
                ->whereNotIn('matpel_id', $coreIds)
                ->delete();

            // Simpan mapel pendukung baru
            foreach ($customScoresToSave as $item) {
                $matpel = \App\Models\Matpel::firstOrCreate(['nama' => $item['nama']]);
                \App\Models\Nilai::updateOrCreate(
                    ['peserta_id' => $peserta->id, 'matpel_id' => $matpel->id, 'semester' => $item['semester']],
                    ['nilai' => $item['nilai']]
                );
            }
        });

        // 5. Rekalkulasi rata-rata nilai pada model Daftar sekali secara akurat
        $this->recalculateNilaiPeserta($peserta);

        // 6. Log aktivitas
        $uploadedRapor = [];
        for ($i = 1; $i <= $maxSemester; $i++) {
            if ($request->hasFile('rapor' . $i)) {
                $uploadedRapor[] = "Scan Rerata S{$i}";
            }
        }

        $msg = "Input nilai akademik semester 1-{$maxSemester}" . (count($uploadedRapor) > 0 ? " dan unggah berkas " . implode(', ', $uploadedRapor) : "") . ".";
        $this->logAktivitas('Pengisian Nilai Rapor', 'Nilai', $peserta->id, $msg);

        return back()->with('success', 'Data nilai rapor dan akademik berhasil disimpan dengan sukses!');
    }

    /**
     * Rekalkulasi rata-rata nilai rapor per semester dan total rata-rata pada model Daftar
     */
    protected function recalculateNilaiPeserta($peserta): void
    {
        if (!$peserta || !$peserta->daftar) return;

        $daftar = $peserta->daftar;
        $nilais = $peserta->nilais()->get();

        $updateData = [];
        for ($sem = 1; $sem <= 6; $sem++) {
            $semNilais = $nilais->where('semester', $sem);
            $avgSem = $semNilais->count() > 0 ? $semNilais->avg('nilai') : 0;
            $updateData["avg_semester_{$sem}"] = round($avgSem, 2);
        }

        $realAvg = $nilais->count() > 0 ? $nilais->avg('nilai') : 0;
        $updateData['rata_rata_nilai'] = round($realAvg, 2);

        $daftar->update($updateData);
    }

    /**
     * Hapus satu file dari model Berkas (bisa single path atau item dalam JSON array).
     */
    public function deleteFile(Request $request)
    {
        $request->validate([
            'field' => 'required|string',
            'file_path' => 'nullable|string',
        ]);

        $peserta = Auth::user()->peserta;
        $berkas = \App\Models\Berkas::where('peserta_id', $peserta->id)->first();

        if (!$berkas) {
            return back()->with('error', 'Data berkas tidak ditemukan.');
        }

        $field = $request->field;
        $filePath = $request->file_path; // Only used for multiple files (JSON)

        // Cari tahu apakah field ini JSON array atau single path
        $currentValue = $berkas->$field;
        $decoded = json_decode($currentValue, true);

        if (is_array($decoded)) {
            // Kasus Multiple Files (Rapor)
            if (!$filePath) {
                return back()->with('error', 'Path file tidak ditentukan.');
            }

            // Hapus file dari storage
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // Hapus dari array
            $newArray = array_values(array_filter($decoded, function ($p) use ($filePath) {
                return $p !== $filePath;
            }));

            $berkas->$field = count($newArray) > 0 ? json_encode($newArray) : null;
        } else {
            // Kasus Single File (Foto, Ijazah, dsb)
            if ($currentValue && Storage::disk('public')->exists($currentValue)) {
                Storage::disk('public')->delete($currentValue);
            }
            $berkas->$field = null;
        }

        $berkas->save();
        $this->logAktivitas('Penghapusan File', 'Berkas', $berkas->id, "Menghapus file pada bagian $field.");

        return back()->with('success', 'File berhasil dihapus.');
    }

    /**
     * Hapus Sertifikat Prestasi
     */
    public function destroySertifikat($id)
    {
        $peserta = Auth::user()->peserta;
        $sertifikat = \App\Models\Sertifikat::where('id', $id)
            ->where('peserta_id', $peserta->id)
            ->firstOrFail();

        // Hapus file fisik
        if ($sertifikat->file && Storage::disk('public')->exists($sertifikat->file)) {
            Storage::disk('public')->delete($sertifikat->file);
        }

        $sertifikat->delete();
        $this->logAktivitas('Penghapusan Sertifikat', 'Sertifikat', $id, "Menghapus sertifikat prestasi: $sertifikat->nama.");

        return back()->with('success', 'Sertifikat berhasil dihapus.');
    }

    /**
     * Tampilkan halaman ganti email
     */
    public function showChangeEmail()
    {
        $peserta = Auth::user()->peserta;
        return view('pendaftar.change_email', compact('peserta'));
    }

    /**
     * Request ganti email (Kirim OTP ke email baru)
     */
    public function requestEmailChange(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_email' => 'required|email|unique:akun,email',
        ], [
            'new_email.unique' => 'Email baru sudah terdaftar di sistem. Gunakan email lain.',
        ]);

        // --- Device-based Rate Limiting (Anti-Spam) ---
        $throttleKey = 'otp-limit-' . sha1($request->ip() . $request->userAgent());
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', 'Terlalu banyak permintaan OTP dari perangkat Anda. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.');
        }
        RateLimiter::hit($throttleKey, 3600); // Ban for 1 hour

        $user = Auth::user();

        // Rate limiting ubah email per user
        $throttleKey = 'change-email-' . $user->id;
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return back()->with('error', 'Terlalu banyak permintaan ubah email. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.');
        }
        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 1800);

        // 1. Verifikasi Password Saat Ini
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password saat ini tidak cocok.');
        }

        // 2. Generate OTP menggunakan CSPRNG aman
        $otpCode = random_int(100000, 999999);
        $email = $request->new_email;

        // 3. Simpan OTP & New Email ke Session/DB
        DB::table('otps')->updateOrInsert(
            ['email' => $email],
            [
                'otp' => (string) $otpCode,
                'expires_at' => Carbon::now()->addMinutes(15)
            ]
        );

        // Simpan email baru di session untuk tahap verifikasi
        session(['pending_new_email' => $email]);

        // 4. Kirim Mail
        try {
            Mail::send('emails.email_change_otp', [
                'otp' => $otpCode,
                'user' => $user,
                'nama' => $user->nama,
                'templateBody' => \App\Models\EmailTemplate::renderBody('email_change_otp', [
                    'nama' => $user->nama,
                    'otp' => $otpCode,
                ]),
            ], function($message) use($email, $user, $otpCode){
                $message->to($email);
                $message->subject(\App\Models\EmailTemplate::subjectOrDefault('email_change_otp', 'Kode Verifikasi Ubah Email MNCU Future Leader Scholarship', [
                    'nama' => $user->nama,
                    'otp' => $otpCode,
                ]));
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal kirim OTP Ganti Email: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim kode verifikasi ke email baru. Mohon coba lagi.');
        }

        return redirect()->route('pendaftar.email.verify')->with('success', 'Kode verifikasi telah dikirim ke email baru Anda.');
    }

    /**
     * Tampilkan halaman input OTP ganti email
     */
    public function showVerifyEmailChange()
    {
        $newEmail = session('pending_new_email');
        if (!$newEmail) {
            return redirect()->route('pendaftar.email.change')->with('error', 'Sesi kedaluwarsa. Silakan ulangi permintaan.');
        }
        return view('auth.verify_email_change_otp', compact('newEmail'));
    }

    /**
     * Eksekusi ganti email setelah OTP valid
     */
    public function verifyEmailChange(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $newEmail = session('pending_new_email');
        if (!$newEmail) {
            return redirect()->route('pendaftar.email.change')->with('error', 'Sesi kedaluwarsa.');
        }

        $otpRecord = DB::table('otps')
            ->where('email', $newEmail)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otpRecord) {
            return back()->with('error', 'Kode OTP salah atau sudah kedaluwarsa.');
        }

        // Jalankan Update Email
        $user = Auth::user();
        $oldEmail = $user->email;
        
        DB::transaction(function() use ($user, $newEmail) {
            $user->update(['email' => $newEmail]);
            // Bersihkan OTP
            DB::table('otps')->where('email', $newEmail)->delete();
        });

        session()->forget('pending_new_email');
        
        $this->logAktivitas('Ganti Email', 'Akun', $user->id, "Mengubah email dari $oldEmail menjadi $newEmail.");

        return redirect()->route('pendaftar.dashboard')->with('success', 'Email berhasil diperbarui menjadi ' . $newEmail);
    }
}

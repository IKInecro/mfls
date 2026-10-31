<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BatchSeleksiController;
use App\Http\Controllers\PendaftarController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;

// === TEMPORARY DEBUG ROUTE - HAPUS SETELAH SELESAI ===
Route::get('/debug-beasiswa-100', function () {
    if (!auth()->check() || auth()->user()->role !== 'admin') abort(403);

    $totalPA = \App\Models\PenilaianAkademik::count();
    $pa100 = \App\Models\PenilaianAkademik::where('rekomendasi_beasiswa', 'like', '%100%')->count();
    $distinctRek = \App\Models\PenilaianAkademik::whereNotNull('rekomendasi_beasiswa')
        ->select('rekomendasi_beasiswa')->distinct()->pluck('rekomendasi_beasiswa');
    $totalDaftar100 = \App\Models\Daftar::where('nominal_beasiswa', 'like', '%100%')->count();
    $distinctNominal = \App\Models\Daftar::whereNotNull('nominal_beasiswa')
        ->select('nominal_beasiswa')->distinct()->pluck('nominal_beasiswa');

    // Semua kandidat yang punya rekomendasi 100% dari penilaian akademik
    $kandidatRek100 = \App\Models\PenilaianAkademik::where('rekomendasi_beasiswa', 'like', '%100%')
        ->with('peserta.akun', 'peserta.daftar')
        ->get()
        ->map(fn($pa) => [
            'nama'              => $pa->peserta->akun->nama ?? '?',
            'rekomendasi_beasiswa' => $pa->rekomendasi_beasiswa,
            'nominal_beasiswa'  => $pa->peserta->daftar->nominal_beasiswa ?? null,
            'status_wawancara_bod' => $pa->peserta->daftar->status_wawancara_bod ?? null,
            'status_daftar'     => $pa->peserta->daftar->status ?? null,
        ]);

    // Semua kandidat yang punya nominal_beasiswa 100% di tabel daftar
    $kandidatNominal100 = \App\Models\Daftar::where('nominal_beasiswa', 'like', '%100%')
        ->with('peserta.akun')
        ->get()
        ->map(fn($d) => [
            'nama'              => $d->peserta->akun->nama ?? '?',
            'nominal_beasiswa'  => $d->nominal_beasiswa,
            'status_wawancara_bod' => $d->status_wawancara_bod ?? null,
            'status'            => $d->status,
        ]);

    return response()->json([
        'total_penilaian_akademiks'            => $totalPA,
        'penilaian_rekomendasi_100_percent'    => $pa100,
        'distinct_rekomendasi_beasiswa'        => $distinctRek,
        'total_daftar_nominal_beasiswa_100'    => $totalDaftar100,
        'distinct_nominal_beasiswa'            => $distinctNominal,
        'kandidat_dari_penilaian_akademik_100' => $kandidatRek100,
        'kandidat_dari_nominal_100'            => $kandidatNominal100,
    ], 200, [], JSON_PRETTY_PRINT);
});
// === END DEBUG ROUTE ===



// // Route::get('/', function () {
//     $beritas = \App\Models\Berita::where('is_published', true)->orderBy('created_at', 'desc')->take(3)->get();

//     // Live Counter Data
//     $totalPendaftar = \App\Models\Akun::where('role', 'pendaftar')->has('peserta')->count();

//     // Asumsi ingin menambahkan efek 100 base data agar terlihat ramai di awal
//     // $totalPendaftar += 100; // Un-comment jika ingin tambah data dummy

//     // Dapatkan data agregat pendaftar berdasarkan kabupaten/kota
//     $locationStats = \App\Models\Peserta::select('kabupaten', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
//         ->whereNotNull('kabupaten')
//         ->groupBy('kabupaten')
//         ->orderByDesc('total')
//         ->take(5)
//         ->get()
//         ->map(function ($p) {
//             return [
//                 'total' => $p->total,
//                 'lokasi' => $p->kabupaten,
//             ];
//         });

//     return view('pendaftar.home', compact('beritas', 'totalPendaftar', 'locationStats'));
// });


// Guest Routes
Route::get('/', function () {
    // Original Logic:
    $beritas = \App\Models\Berita::where('is_published', true)->orderBy('created_at', 'desc')->take(3)->get();
    //     // $totalPendaftar = \App\Models\Akun::where('role', 'pendaftar')->has('peserta')->count();
//     // $locationStats = \App\Models\Peserta::select('kabupaten', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
//     //     ->whereNotNull('kabupaten')
//     //     ->groupBy('kabupaten')
//     //     ->orderByDesc('total')
//     //     ->take(5)
//     //     ->get()
//     //     ->map(function ($p) {
//     //         return [
//     //             'total' => $p->total,
//     //             'lokasi' => $p->kabupaten,
//     //         ];
//     //     });
    $pengaturan = \App\Models\PengaturanPendaftaran::instance();

    return view('pendaftar.home', compact('beritas', 'pengaturan'));

});

// Route::get('/', function () {
//     return view('auth.maintance');
// })->name('maintenance');
// Pengumuman Route (Public)
Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman');

// Rekomendasi Prodi (Quiz Matching)
Route::get('/rekomendasi-prodi', [\App\Http\Controllers\MatchingController::class, 'showQuiz'])->name('quiz.show');
Route::post('/rekomendasi-prodi', [\App\Http\Controllers\MatchingController::class, 'submit'])->name('quiz.submit');

// Berita Route (Public)
Route::get('/berita/{slug}', function ($slug) {
    $berita = \App\Models\Berita::where('slug', $slug)->firstOrFail();
    return view('pendaftar.berita_show', compact('berita'));
})->name('berita.show');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/maintance', [AuthController::class, 'maintance'])->name('maintance');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// OTP Verification Routes
Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('verify.otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.otp.post');
Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('resend.otp');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Internal / Staff Routes    
Route::get('/internal/login', [AuthController::class, 'showInternalLogin'])->name('internal.login');
Route::post('/internal/login', [AuthController::class, 'internalLogin']);

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // === ROLE: Admin, Panitia, Akademik, Mentor ===
    Route::middleware(['role:admin,panitia,akademik,mentor,palugada,dosen'])->group(
        function () {
            // Admin / Staff Dashboard
            Route::get('/admin/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
            Route::get('/admin/activity-log', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('admin.activity_log.index');

            // Pengaturan pembukaan & penutupan pendaftaran
            Route::get('/admin/pengaturan/pendaftaran', [App\Http\Controllers\PengaturanController::class, 'edit'])->name('admin.pengaturan.pendaftaran.edit');
            Route::post('/admin/pengaturan/pendaftaran', [App\Http\Controllers\PengaturanController::class, 'update'])->name('admin.pengaturan.pendaftaran.update');

            // Kelola template email — khusus admin (mengubah isi email ke user)
            Route::middleware(['role:admin'])->prefix('admin/email-templates')->name('admin.email-templates.')->group(function () {
                Route::get('/', [App\Http\Controllers\EmailTemplateController::class, 'index'])->name('index');
                Route::get('/{key}/edit', [App\Http\Controllers\EmailTemplateController::class, 'edit'])->name('edit');
                Route::post('/{key}/preview', [App\Http\Controllers\EmailTemplateController::class, 'preview'])->name('preview');
                Route::post('/{key}', [App\Http\Controllers\EmailTemplateController::class, 'update'])->name('update');
                Route::delete('/{key}', [App\Http\Controllers\EmailTemplateController::class, 'destroy'])->name('destroy');
            });

            // Unified Seleksi Administrasi (Profil, Raport, Berkas)
            Route::prefix('admin/pendaftar')->name('admin.pendaftar.')->group(
                function () {
                // Route for Mentor grading
                Route::post('/{id}/mentor-nilai', [App\Http\Controllers\AdminController::class, 'storePenilaianMentor'])->name('mentor_nilai');

                // Routes restricted to Admin, Panitia, Akademik, Palugada
                Route::middleware(['role:admin,panitia,akademik,palugada'])->group(function () {
                    Route::get('/', [App\Http\Controllers\AdminController::class, 'indexPendaftar'])->name('index');
                    Route::get('/{id}', [App\Http\Controllers\AdminController::class, 'detailPendaftar'])->name('show');
                    Route::post('/{id}/verify', [App\Http\Controllers\AdminController::class, 'verifikasi'])->name('verify');
                    Route::get('/{id}/download-zip', [App\Http\Controllers\AdminController::class, 'downloadZip'])->name('download_zip');
                    Route::post('/{id}/upload-berkas', [App\Http\Controllers\AdminController::class, 'uploadBerkas'])->name('upload_berkas');
                    Route::post('/{id}/delete-berkas', [App\Http\Controllers\AdminController::class, 'deleteBerkas'])->name('delete_berkas');
                    Route::post('/{id}/update-email', [App\Http\Controllers\AdminController::class, 'updateEmailPendaftar'])->name('update_email');
                    Route::post('/{id}/update-tahun-lulus', [App\Http\Controllers\AdminController::class, 'updateTahunLulus'])->name('update_tahun_lulus');
                    Route::post('/{id}/dispensasi-ujian', [App\Http\Controllers\AdminController::class, 'storeDispensasiUjian'])->name('dispensasi_ujian');
                    Route::delete('/{id}', [App\Http\Controllers\AdminController::class, 'destroyPendaftar'])->name('destroy');
                });
            }
            );

            // Dedicated Palugada Verification (Double Check)
            Route::get('/admin/palugada/verifikasi', [App\Http\Controllers\AdminController::class, 'indexPalugada'])->name('admin.palugada.index');

            // Seleksi Beasiswa (Email Restricted)
            Route::middleware(['role:admin,akademik,palugada,email:dion@gmail.com|info@beasiswamncu.com'])->group(
                function () {
                Route::get('/admin/beasiswa', [App\Http\Controllers\AdminController::class, 'indexBeasiswa'])->name('admin.beasiswa.index');
                Route::get('/admin/beasiswa/{id}', [App\Http\Controllers\AdminController::class, 'showBeasiswa'])->name('admin.beasiswa.show');
                Route::post('/admin/beasiswa/{id}/update', [App\Http\Controllers\AdminController::class, 'updateBeasiswa'])->name('admin.beasiswa.update');

                // Batch Import Seleksi Beasiswa
                Route::get('/admin/beasiswa-import', [BatchSeleksiController::class, 'index'])->name('admin.beasiswa.import.index');
                Route::post('/admin/beasiswa-import', [BatchSeleksiController::class, 'import'])->name('admin.beasiswa.import.batch');
            }
            );

            Route::get('/admin/hasil-ujian', [App\Http\Controllers\AdminController::class, 'indexHasilUjian'])->name('admin.hasil_ujian.index');

            // Wawancara BoD
            Route::get('/admin/wawancara-bod', [App\Http\Controllers\AdminController::class, 'indexWawancaraBod'])->name('admin.wawancara_bod.index');
            Route::post('/admin/wawancara-bod/{id}/update', [App\Http\Controllers\AdminController::class, 'updateWawancaraBod'])->name('admin.wawancara_bod.update');
            Route::get('/admin/wawancara-bod/download-photos', [App\Http\Controllers\AdminController::class, 'downloadWawancaraBodPhotos'])->name('admin.wawancara_bod.download_photos');

            Route::get('/admin/penilaian-mentor', [App\Http\Controllers\AdminController::class, 'indexPenilaian'])->name('admin.penilaian.index');
            Route::get('/admin/penilaian-akademik', [App\Http\Controllers\AdminController::class, 'indexPenilaianAkademik'])->name('admin.penilaian.akademik.index');
            Route::get('/admin/penilaian-detail/{id}', [App\Http\Controllers\AdminController::class, 'showPenilaian'])->name('admin.penilaian.show');
            Route::post('/admin/penilaian-akademik/{id}', [App\Http\Controllers\AdminController::class, 'storePenilaianAkademik'])->name('admin.penilaian.akademik.store');
            Route::post('/admin/penilaian-akademik/{id}/assign-interviewer', [App\Http\Controllers\AdminController::class, 'assignInterviewer'])->name('admin.penilaian.akademik.assign');

            Route::get('/admin/user', [App\Http\Controllers\AdminController::class, 'indexUser'])->name('admin.user.index');
            Route::post('/admin/user', [App\Http\Controllers\AdminController::class, 'storeUser'])->name('admin.user.store');
            Route::put('/admin/user/{id}', [App\Http\Controllers\AdminController::class, 'updateUser'])->name('admin.user.update');
            Route::put('/admin/user/{id}/reset-password', [App\Http\Controllers\AdminController::class, 'resetPassword'])->name('admin.user.reset_password');
            Route::delete('/admin/user/{id}', [App\Http\Controllers\AdminController::class, 'destroyUser'])->name('admin.user.destroy');

            // Panel Ujian — jadwal, buka/tutup, CRUD test (admin & akademik)
            Route::middleware(['role:admin,akademik'])->prefix('admin/panel-ujian')->name('admin.panel_ujian.')->group(function () {
                Route::get('/', [App\Http\Controllers\PanelUjianController::class, 'index'])->name('index');
                Route::post('/', [App\Http\Controllers\PanelUjianController::class, 'store'])->name('store');
                Route::put('/{id}/jadwal', [App\Http\Controllers\PanelUjianController::class, 'updateJadwal'])->name('jadwal');
                Route::put('/{id}/override', [App\Http\Controllers\PanelUjianController::class, 'setOverride'])->name('override');
                Route::put('/{id}', [App\Http\Controllers\PanelUjianController::class, 'update'])->name('update');
                Route::delete('/{id}', [App\Http\Controllers\PanelUjianController::class, 'destroy'])->name('destroy');
                Route::post('/{id}/kirim-undangan', [App\Http\Controllers\PanelUjianController::class, 'kirimUndangan'])->name('kirim_undangan');
            });

            // Manajemen Soal
            Route::post('/admin/soal/delete-all', [App\Http\Controllers\AdminController::class, 'deleteAllSoal'])->name('admin.soal.deleteAll');
            Route::get('/admin/soal', [App\Http\Controllers\AdminController::class, 'indexSoal'])->name('admin.soal.index');
            Route::post('/admin/soal', [App\Http\Controllers\AdminController::class, 'storeSoal'])->name('admin.soal.store');
            Route::get('/admin/soal/{id}/edit', [App\Http\Controllers\AdminController::class, 'editSoal'])->name('admin.soal.edit');
            Route::put('/admin/soal/{id}', [App\Http\Controllers\AdminController::class, 'updateSoal'])->name('admin.soal.update');
            Route::delete('/admin/soal/{id}', [App\Http\Controllers\AdminController::class, 'destroySoal'])->name('admin.soal.destroy');
            Route::post('/admin/soal/import', [App\Http\Controllers\AdminController::class, 'importSoal'])->name('admin.soal.import');
            Route::put('/admin/soal/ujian/{id}', [App\Http\Controllers\AdminController::class, 'updateUjian'])->name('admin.ujian.update');
            Route::post('/admin/hasil-ujian/{id}/reset', [App\Http\Controllers\AdminController::class, 'resetJawabanUjian'])->name('admin.hasil_ujian.reset');

            // Seleksi Ujian (CBT) Agregat per Peserta
            Route::get('/admin/seleksi-ujian', [App\Http\Controllers\AdminController::class, 'indexSeleksiUjian'])->name('admin.seleksi_ujian.index');
            Route::post('/admin/seleksi-ujian/{id}/update-status', [App\Http\Controllers\AdminController::class, 'updateStatusSeleksiUjianCandidate'])->name('admin.seleksi_ujian.update_status');
            Route::post('/admin/seleksi-ujian/{id}/kirim-email', [App\Http\Controllers\AdminController::class, 'kirimEmailLolosUjianCandidate'])->name('admin.seleksi_ujian.kirim_email');
            Route::post('/admin/seleksi-ujian/kirim-massal', [App\Http\Controllers\AdminController::class, 'kirimEmailLolosMassalCandidate'])->name('admin.seleksi_ujian.kirim_massal');
            Route::get('/admin/seleksi-ujian/kirim-massal/progress', [App\Http\Controllers\AdminController::class, 'getBulkEmailProgress'])->name('admin.seleksi_ujian.kirim_massal_progress');
            Route::post('/admin/seleksi-ujian/bulk-update', [App\Http\Controllers\AdminController::class, 'bulkUpdateStatusSeleksiUjianCandidate'])->name('admin.seleksi_ujian.bulk_update');
            Route::get('/admin/seleksi-ujian/export', [App\Http\Controllers\AdminController::class, 'exportSeleksiUjianCandidate'])->name('admin.seleksi_ujian.export');

            // Manajemen Berita
            Route::middleware(['role:admin,panitia,palugada'])->group(
                function () {
                Route::resource('admin/berita', \App\Http\Controllers\BeritaController::class, ['as' => 'admin']);
            }
            );

            Route::get('/admin/export', [App\Http\Controllers\AdminController::class, 'exportExcel'])->name('admin.export');
            Route::get('/admin/export-wawancara', [App\Http\Controllers\AdminController::class, 'exportExcelWawancara'])->name('admin.export_wawancara');
            Route::get('/admin/sync-wawancara-sheet', [App\Http\Controllers\AdminController::class, 'syncWawancaraSheet'])->name('admin.sync_wawancara_sheet');

            // Certificate Generation Route
            Route::get('/admin/pendaftar/{id}/certificate', [App\Http\Controllers\AdminController::class, 'generateCertificate'])->name('admin.pendaftar.certificate');
            Route::post('/admin/pendaftar/{id}/send-certificate', [App\Http\Controllers\AdminController::class, 'sendCertificateEmail'])->name('admin.pendaftar.send_certificate');
            Route::post('/admin/pendaftar/bulk-send-certificate', [App\Http\Controllers\AdminController::class, 'bulkSendCertificate'])->name('admin.pendaftar.bulk_send_certificate');
            Route::get('/admin/pendaftar/bulk-send/progress', [App\Http\Controllers\AdminController::class, 'getBulkProgress'])->name('admin.pendaftar.bulk_send_progress');
        }
    );

    // === ROLE: Pendaftar ===
    Route::middleware(['role:pendaftar'])->group(
        function () {
            Route::prefix('pendaftar')->group(
                function () {
                    Route::get('/dashboard', [PendaftarController::class, 'index'])->name('pendaftar.dashboard');
                    Route::get('/biodata', [PendaftarController::class, 'biodata'])->name('pendaftar.biodata');
                    Route::get('/berkas', [PendaftarController::class, 'berkas'])->name('pendaftar.berkas');
                    Route::get('/twibbon', [PendaftarController::class, 'twibbon'])->name('pendaftar.twibbon');
                    Route::get('/nilai', [PendaftarController::class, 'nilai'])->name('pendaftar.nilai');
                    Route::get('/change-email', [PendaftarController::class, 'showChangeEmail'])->name('pendaftar.email.change');
                    Route::get('/change-email/verify', [PendaftarController::class, 'showVerifyEmailChange'])->name('pendaftar.email.verify');

                    // Ujian online (CBT) — daftar test, pengerjaan, autosave draft
                    Route::get('/ujian', [\App\Http\Controllers\UjianPesertaController::class, 'index'])->name('ujian.index');
                    Route::get('/ujian/{id}', [\App\Http\Controllers\UjianPesertaController::class, 'show'])->name('ujian.show');
                    Route::post('/ujian/{id}/draft', [\App\Http\Controllers\UjianPesertaController::class, 'draft'])->name('ujian.draft');
                    Route::post('/ujian/{id}/selesai', [\App\Http\Controllers\UjianPesertaController::class, 'selesai'])->name('ujian.selesai');
                    Route::post('/ujian/{id}/submit', [\App\Http\Controllers\UjianPesertaController::class, 'submit'])->name('ujian.submit');


                    // Aksi tulis pendaftar: jalan hanya saat pendaftaran terbuka (lihat PengaturanPendaftaran::aktif)
                    $gate = function (string $method) {
                        return function (...$routeParams) use ($method) {
                            if (!\App\Models\PengaturanPendaftaran::aktif()) {
                                return back()->with('error', 'Maaf, pendaftaran sudah ditutup. Anda tidak dapat mengubah data.');
                            }
                            $controller = app(\App\Http\Controllers\PendaftarController::class);
                            $firstParam = (new \ReflectionMethod($controller, $method))->getParameters()[0] ?? null;
                            $args = $firstParam?->getType()?->getName() === \Illuminate\Http\Request::class
                                ? [request(), ...$routeParams]
                                : $routeParams;

                            return $controller->{$method}(...$args);
                        };
                    };
                    Route::post('/biodata', $gate('storeBiodata'))->name('pendaftar.biodata.store');
                    Route::post('/berkas', $gate('storeBerkas'))->name('pendaftar.berkas.store');
                    Route::post('/twibbon', $gate('storeTwibbon'))->name('pendaftar.twibbon.store');
                    Route::post('/nilai', $gate('storeNilai'))->name('pendaftar.nilai.store');
                    Route::post('/berkas/delete-file', $gate('deleteFile'))->name('pendaftar.berkas.delete_file');
                    Route::post('/berkas/delete-sertifikat/{id}', $gate('destroySertifikat'))->name('pendaftar.sertifikat.destroy');
                    Route::post('/change-email/request', $gate('requestEmailChange'))->name('pendaftar.email.request');
                    Route::post('/change-email/verify', $gate('verifyEmailChange'))->name('pendaftar.email.verify.post');
                }
            );
        }
    );
});
// Temporary Route to run migrations on Server
Route::get('/debug-migrate', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ["--force" => true]);
        return "<pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::get('/debug-candidates', function () {
    $alfans = \App\Models\Akun::where('nama', 'like', '%alfan%')->with(['peserta.daftar', 'peserta.penilaianAkademiks'])->get();
    $raihans = \App\Models\Akun::where('nama', 'like', '%raihan%')->with(['peserta.daftar', 'peserta.penilaianAkademiks'])->get();
    return response()->json([
        'alfans' => $alfans->map(fn($a) => [
            'id' => $a->id,
            'nama' => $a->nama,
            'role' => $a->role,
            'has_peserta' => !!$a->peserta,
            'has_daftar' => $a->peserta ? !!$a->peserta->daftar : false,
            'nominal_beasiswa' => $a->peserta?->daftar?->nominal_beasiswa,
            'status_wawancara_bod' => $a->peserta?->daftar?->status_wawancara_bod,
            'rekomendasi_beasiswa' => $a->peserta?->penilaianAkademiks->first()?->rekomendasi_beasiswa,
            'status_daftar' => $a->peserta?->daftar?->status,
        ]),
        'raihans' => $raihans->map(fn($a) => [
            'id' => $a->id,
            'nama' => $a->nama,
            'role' => $a->role,
            'has_peserta' => !!$a->peserta,
            'has_daftar' => $a->peserta ? !!$a->peserta->daftar : false,
            'nominal_beasiswa' => $a->peserta?->daftar?->nominal_beasiswa,
            'status_wawancara_bod' => $a->peserta?->daftar?->status_wawancara_bod,
            'rekomendasi_beasiswa' => $a->peserta?->penilaianAkademiks->first()?->rekomendasi_beasiswa,
            'status_daftar' => $a->peserta?->daftar?->status,
        ])
    ]);
});

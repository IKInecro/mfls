<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Daftar;
use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    use \App\Traits\ImageCompressor;

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

    public function dashboard()
    {
        if (auth()->user()->role === 'dosen') {
            return redirect()->route('admin.penilaian.akademik.index');
        }

        $riwayatQuery = \App\Models\RiwayatAktivitas::with('pelaku');
        if (auth()->user()->role !== 'god') {
            $riwayatQuery->whereDoesntHave('pelaku', function($q) {
                $q->where('role', 'god');
            });
        }
        $riwayats = $riwayatQuery->latest()->take(10)->get();

        // Stats untuk Dashboard Real-time
        $totalPendaftar = Akun::where('role', 'pendaftar')->has('peserta')->count();
        $todayPendaftar = Akun::where('role', 'pendaftar')->has('peserta')->whereDate('created_at', \Carbon\Carbon::today())->count();
        $yesterdayPendaftar = Akun::where('role', 'pendaftar')->has('peserta')->whereDate('created_at', \Carbon\Carbon::yesterday())->count();

        // Hitung kenaikan (growth)
        $growth = 0;
        if ($yesterdayPendaftar > 0) {
            $growth = (($todayPendaftar - $yesterdayPendaftar) / $yesterdayPendaftar) * 100;
        }
        elseif ($todayPendaftar > 0) {
            $growth = 100;
        }

        // Daily trend (last 7 days)
        $dailyTrend = Akun::where('role', 'pendaftar')
            ->has('peserta')
            ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(6))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Website Analytics
        $totalViews = \App\Models\PageView::count();
        $todayViews = \App\Models\PageView::whereDate('viewed_at', \Carbon\Carbon::today())->count();
        $yesterdayViews = \App\Models\PageView::whereDate('viewed_at', \Carbon\Carbon::yesterday())->count();
        
        // Views growth
        $viewsGrowth = 0;
        if ($yesterdayViews > 0) {
            $viewsGrowth = (($todayViews - $yesterdayViews) / $yesterdayViews) * 100;
        } elseif ($todayViews > 0) {
            $viewsGrowth = 100;
        }
        
        // Daily views trend (last 7 days)
        $dailyViewsTrend = \App\Models\PageView::where('viewed_at', '>=', \Carbon\Carbon::now()->subDays(6))
            ->select(DB::raw('DATE(viewed_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();
        
        // Top pages
        $topPages = \App\Models\PageView::select('url', DB::raw('count(*) as views'))
            ->groupBy('url')
            ->orderBy('views', 'DESC')
            ->limit(5)
            ->get();
        
        // Unique visitors today
        $uniqueVisitorsToday = \App\Models\PageView::whereDate('viewed_at', \Carbon\Carbon::today())
            ->distinct('ip_address')
            ->count('ip_address');

        // Tambahan: Hitung Pendaftar dengan Dokument Lengkap (Progres 100%)
        $pendaftars = \App\Models\Akun::where('role', 'pendaftar')->with('peserta')->get();
        $totalLengkap = $pendaftars->filter(fn($akun) => ($akun->peserta->progress ?? 0) == 100)->count();

        return view('admin.dashboard', compact(
            'riwayats', 
            'totalPendaftar', 
            'todayPendaftar', 
            'growth', 
            'dailyTrend',
            'totalViews',
            'todayViews',
            'viewsGrowth',
            'dailyViewsTrend',
            'topPages',
            'uniqueVisitorsToday',
            'totalLengkap'
        ));
    }

    public function storePenilaianMentor(Request $request, $id)
    {
        if (auth()->user()->role !== 'mentor' && auth()->user()->role !== 'admin' && auth()->user()->role !== 'palugada' && auth()->user()->role !== 'god') {
            return back()->with('loginError', 'Hanya Mentor yang dapat memberikan penilaian.');
        }

        $request->validate([
            'nilai_kepemimpinan' => 'required|numeric|min:0|max:100',
            'nilai_kepribadian' => 'required|numeric|min:0|max:100',
            'nilai_keaktifan' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string'
        ]);

        $peserta = \App\Models\Peserta::where('akun_id', $id)->firstOrFail();

        // Cek apakah sudah lulus administrasi
        if ($peserta->daftar->status !== 'lulus') {
            return back()->with('loginError', 'Peserta ini belum lulus tahap administrasi.');
        }

        // Cek apakah sudah lulus seleksi ujian
        if ($peserta->status_seleksi_ujian !== 'lulus') {
            return back()->with('loginError', 'Peserta ini belum dinyatakan Lulus Seleksi Ujian CBT.');
        }

        // Kalkulasi nilai berbobot
        // 1. Kepemimpinan - 35%
        // 2. Kepribadian - 35%
        // 3. Keaktifan - 30%
        $totalNilai = ($request->nilai_kepemimpinan * 0.35) +
            ($request->nilai_kepribadian * 0.35) +
            ($request->nilai_keaktifan * 0.30);

        \App\Models\PenilaianMentor::updateOrCreate(
        ['peserta_id' => $peserta->id, 'mentor_id' => auth()->id()],
        [
            'nilai' => $totalNilai,
            'nilai_kepemimpinan' => $request->nilai_kepemimpinan,
            'nilai_kepribadian' => $request->nilai_kepribadian,
            'nilai_keaktifan' => $request->nilai_keaktifan,
            'catatan' => $request->catatan
        ]
        );

        $this->logAktivitas('Menilai Peserta', 'Peserta', $peserta->id, "Memberikan nilai mentor (" . number_format($totalNilai, 2) . ") kepada " . ($peserta->akun->nama ?? 'Peserta'));

        return back()->with('success', 'Penilaian mentor berhasil disimpan!');
    }

    // --- DATA PENDAFTAR: UNIFIED SELEKSI ADMINISTRASI (PROFIL, RAPORT, BERKAS) ---
    public function indexPendaftar(Request $request)
    {
        if (auth()->user()->role === 'mentor')
            return abort(403);

        $baseQuery = Akun::where('role', 'pendaftar')
            ->with(['peserta.daftar', 'peserta.nilais', 'peserta.berkas', 'peserta.sertifikats']);

        // Hitung statistik global untuk badge filter cepat
        $allAkuns = (clone $baseQuery)->get();
        $counts = [
            'total' => $allAkuns->count(),
            'lengkap' => $allAkuns->filter(fn($p) => ($p->peserta->progress ?? 0) >= 100)->count(),
            'belum_lengkap' => $allAkuns->filter(fn($p) => ($p->peserta->progress ?? 0) < 100)->count(),
            'menunggu' => $allAkuns->filter(fn($p) => in_array($p->peserta?->daftar?->status, ['menunggu', null, '']))->count(),
            'lulus' => $allAkuns->filter(fn($p) => ($p->peserta?->daftar?->status ?? '') === 'lulus')->count(),
            'palugada' => $allAkuns->filter(fn($p) => ($p->peserta?->daftar?->status ?? '') === 'diajukan_palugada')->count(),
            'tidak_lulus' => $allAkuns->filter(fn($p) => ($p->peserta?->daftar?->status ?? '') === 'tidak_lulus')->count(),
        ];

        $query = clone $baseQuery;

        // 1. Filter Pencarian Cepat
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('peserta', function ($q2) use ($search) {
                        $q2->where('nisn', 'like', "%{$search}%")
                            ->orWhere('no_whatsapp', 'like', "%{$search}%")
                            ->orWhere('nama_sekolah', 'like', "%{$search}%")
                            ->orWhere('kabupaten', 'like', "%{$search}%")
                            ->orWhere('provinsi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('peserta.daftar', function ($q2) use ($search) {
                        $q2->where('asal_sekolah', 'like', "%{$search}%")
                            ->orWhere('kode_referral', 'like', "%{$search}%")
                            ->orWhere('no_wa', 'like', "%{$search}%");
                    });
            });
        }

        // 2. Filter Status Seleksi Administrasi
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'menunggu') {
                $query->whereHas('peserta.daftar', function ($q) {
                    $q->whereIn('status', ['menunggu', ''])->orWhereNull('status');
                });
            } else {
                $query->whereHas('peserta.daftar', function ($q) use ($status) {
                    $q->where('status', $status);
                });
            }
        }

        // 3. Filter Program Studi
        if ($request->filled('prodi')) {
            $prodi = $request->prodi;
            $query->whereHas('peserta', function ($q) use ($prodi) {
                $q->where('pilihan_prodi', 'like', "%{$prodi}%")
                  ->orWhere('pilihan_prodi2', 'like', "%{$prodi}%");
            });
        }

        // 4. Filter Tanggal Pendaftaran
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // 5. Filter Nilai Minimum
        if ($request->filled('filter_nilai')) {
            $query->whereHas('peserta.daftar', function ($q) use ($request) {
                $q->where('rata_rata_nilai', '>=', $request->filter_nilai);
            });
        }

        $pendaftars = $query->get();

        // 6. Filter Progress Dokumen (Collection filtering karena progress adalah accessor dinamis)
        if ($request->filled('progress')) {
            if ($request->progress === '100') {
                $pendaftars = $pendaftars->filter(fn($p) => ($p->peserta->progress ?? 0) >= 100);
            } elseif ($request->progress === 'under100') {
                $pendaftars = $pendaftars->filter(fn($p) => ($p->peserta->progress ?? 0) < 100);
            }
        }

        // 7. Fleksibilitas Pengurutan (Sorting)
        $sort = $request->get('sort', 'priority');
        if ($sort === 'nilai_high') {
            $pendaftars = $pendaftars->sortByDesc(fn($p) => $p->peserta?->daftar?->rata_rata_nilai ?? 0);
        } elseif ($sort === 'nilai_low') {
            $pendaftars = $pendaftars->sortBy(fn($p) => $p->peserta?->daftar?->rata_rata_nilai ?? 0);
        } elseif ($sort === 'name_asc') {
            $pendaftars = $pendaftars->sortBy(fn($p) => strtolower($p->nama));
        } elseif ($sort === 'name_desc') {
            $pendaftars = $pendaftars->sortByDesc(fn($p) => strtolower($p->nama));
        } elseif ($sort === 'latest') {
            $pendaftars = $pendaftars->sortByDesc(fn($p) => $p->created_at);
        } elseif ($sort === 'oldest') {
            $pendaftars = $pendaftars->sortBy(fn($p) => $p->created_at);
        } elseif ($sort === 'progress_high') {
            $pendaftars = $pendaftars->sortByDesc(fn($p) => $p->peserta->progress ?? 0);
        } else {
            // Default: Prioritas (Progress 100% di atas, lalu Rata-rata Nilai Tertinggi)
            $pendaftars = $pendaftars->sort(function ($a, $b) {
                $progA = $a->peserta->progress ?? 0;
                $progB = $b->peserta->progress ?? 0;

                if ($progA == $progB) {
                    $nilaiA = $a->peserta && $a->peserta->daftar ? $a->peserta->daftar->rata_rata_nilai : 0;
                    $nilaiB = $b->peserta && $b->peserta->daftar ? $b->peserta->daftar->rata_rata_nilai : 0;
                    return $nilaiB <=> $nilaiA;
                }

                return $progB <=> $progA;
            });
        }

        return view('admin.pendaftar.index', compact('pendaftars', 'counts'));
    }

    public function indexPalugada(Request $request)
    {
        if (auth()->user()->role !== 'palugada' && auth()->user()->role !== 'admin' && auth()->user()->role !== 'god') {
            return abort(403);
        }

        $query = Akun::where('role', 'pendaftar')
            ->whereHas('peserta.daftar', function ($q) {
                $q->where('status', 'diajukan_palugada');
            })
            ->with(['peserta.daftar', 'peserta.nilais', 'peserta.berkas']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $pendaftars = $query->latest()->get();

        return view('admin.palugada.index', compact('pendaftars'));
    }

    /**
     * Tampilkan List Video Motivasi Pendaftar
     */
    public function indexVideoMotivasi(Request $request)
    {
        if (in_array(auth()->user()->role, ['mentor', 'dosen'])) {
            return abort(403);
        }

        $query = Akun::where('role', 'pendaftar')
            ->with(['peserta.daftar', 'peserta.berkas']);

        // Search Keyword
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('peserta', function ($qp) use ($search) {
                        $qp->where('no_hp', 'like', "%{$search}%")
                            ->orWhere('no_whatsapp', 'like', "%{$search}%")
                            ->orWhere('nisn', 'like', "%{$search}%")
                            ->orWhere('pilihan_prodi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('peserta.daftar', function ($qd) use ($search) {
                        $qd->where('asal_sekolah', 'like', "%{$search}%");
                    });
            });
        }

        // Filter Program Studi
        if ($request->filled('prodi')) {
            $prodi = $request->prodi;
            $query->whereHas('peserta', function ($q) use ($prodi) {
                $q->where('pilihan_prodi', 'like', "%{$prodi}%");
            });
        }

        // Filter Status Submission Video
        $status = $request->get('status', 'all');
        if ($status === 'sudah') {
            $query->whereHas('peserta.berkas', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNotNull('motivasi_video')
                        ->where('motivasi_video', '!=', '')
                        ->orWhere(function ($sub2) {
                            $sub2->whereNotNull('motivasi_video_tiktok')
                                ->where('motivasi_video_tiktok', '!=', '');
                        });
                });
            });
        } elseif ($status === 'lengkap') {
            $query->whereHas('peserta.berkas', function ($q) {
                $q->whereNotNull('motivasi_video')
                    ->where('motivasi_video', '!=', '')
                    ->whereNotNull('motivasi_video_tiktok')
                    ->where('motivasi_video_tiktok', '!=', '');
            });
        } elseif ($status === 'video_utama') {
            $query->whereHas('peserta.berkas', function ($q) {
                $q->whereNotNull('motivasi_video')
                    ->where('motivasi_video', '!=', '');
            });
        } elseif ($status === 'video_tiktok') {
            $query->whereHas('peserta.berkas', function ($q) {
                $q->whereNotNull('motivasi_video_tiktok')
                    ->where('motivasi_video_tiktok', '!=', '');
            });
        } elseif ($status === 'belum') {
            $query->where(function ($q) {
                $q->whereDoesntHave('peserta.berkas')
                    ->orWhereHas('peserta.berkas', function ($qb) {
                        $qb->where(function ($sub) {
                            $sub->whereNull('motivasi_video')->orWhere('motivasi_video', '');
                        })->where(function ($sub) {
                            $sub->whereNull('motivasi_video_tiktok')->orWhere('motivasi_video_tiktok', '');
                        });
                    });
            });
        }

        // Filter Platform Video Utama
        if ($request->filled('platform')) {
            $platform = $request->platform;
            $query->whereHas('peserta.berkas', function ($q) use ($platform) {
                if ($platform === 'youtube') {
                    $q->where(function ($sub) {
                        $sub->where('motivasi_video', 'like', '%youtube.com%')
                            ->orWhere('motivasi_video', 'like', '%youtu.be%');
                    });
                } elseif ($platform === 'tiktok') {
                    $q->where(function ($sub) {
                        $sub->where('motivasi_video', 'like', '%tiktok.com%')
                            ->orWhere('motivasi_video_tiktok', 'like', '%tiktok.com%');
                    });
                } elseif ($platform === 'drive') {
                    $q->where('motivasi_video', 'like', '%drive.google.com%');
                } elseif ($platform === 'instagram') {
                    $q->where('motivasi_video', 'like', '%instagram.com%');
                }
            });
        }

        // Stats calculation
        $totalPendaftar = Akun::where('role', 'pendaftar')->count();
        $totalSubmitVideo = Akun::where('role', 'pendaftar')
            ->whereHas('peserta.berkas', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNotNull('motivasi_video')
                        ->where('motivasi_video', '!=', '')
                        ->orWhere(function ($sub2) {
                            $sub2->whereNotNull('motivasi_video_tiktok')
                                ->where('motivasi_video_tiktok', '!=', '');
                        });
                });
            })->count();

        $totalSubmitUtama = Akun::where('role', 'pendaftar')
            ->whereHas('peserta.berkas', function ($q) {
                $q->whereNotNull('motivasi_video')->where('motivasi_video', '!=', '');
            })->count();

        $totalSubmitTiktok = Akun::where('role', 'pendaftar')
            ->whereHas('peserta.berkas', function ($q) {
                $q->whereNotNull('motivasi_video_tiktok')->where('motivasi_video_tiktok', '!=', '');
            })->count();

        $totalBelumSubmit = $totalPendaftar - $totalSubmitVideo;

        $pendaftars = $query->latest()->paginate(20)->withQueryString();

        $prodiList = [
            'Sains Komunikasi',
            'Desain Komunikasi Visual (DKV)',
            'Manajemen',
            'Akuntansi',
            'Sistem Informasi',
            'Pendidikan Bahasa Inggris',
            'Pendidikan Matematika',
            'Ilmu Komputer'
        ];

        return view('admin.video_motivasi.index', compact(
            'pendaftars',
            'totalPendaftar',
            'totalSubmitVideo',
            'totalSubmitUtama',
            'totalSubmitTiktok',
            'totalBelumSubmit',
            'prodiList'
        ));
    }

    /**
     * Tampilkan List & Monitoring Berkas Pendaftar Lengkap
     */
    public function indexBerkasPendaftar(Request $request)
    {
        if (in_array(auth()->user()->role, ['mentor', 'dosen'])) {
            return abort(403);
        }

        $query = Akun::where('role', 'pendaftar')
            ->with(['peserta.daftar', 'peserta.berkas', 'peserta.sertifikats']);

        // Search Keyword
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('peserta', function ($qp) use ($search) {
                        $qp->where('no_hp', 'like', "%{$search}%")
                            ->orWhere('no_whatsapp', 'like', "%{$search}%")
                            ->orWhere('nisn', 'like', "%{$search}%")
                            ->orWhere('nama_sekolah', 'like', "%{$search}%")
                            ->orWhere('pilihan_prodi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('peserta.daftar', function ($qd) use ($search) {
                        $qd->where('asal_sekolah', 'like', "%{$search}%");
                    });
            });
        }

        // Filter Program Studi
        if ($request->filled('prodi')) {
            $prodi = $request->prodi;
            $query->whereHas('peserta', function ($q) use ($prodi) {
                $q->where('pilihan_prodi', 'like', "%{$prodi}%");
            });
        }

        // Filter Status Kelengkapan Berkas
        $status = $request->get('status', 'all');
        if ($status === 'lengkap') {
            $query->whereHas('peserta.berkas', function ($q) {
                $q->whereNotNull('foto')->where('foto', '!=', '')
                    ->whereNotNull('rapor1')->where('rapor1', '!=', '')
                    ->whereNotNull('rapor2')->where('rapor2', '!=', '')
                    ->whereNotNull('rapor3')->where('rapor3', '!=', '')
                    ->whereNotNull('rapor4')->where('rapor4', '!=', '')
                    ->whereNotNull('rapor5')->where('rapor5', '!=', '')
                    ->whereNotNull('ijazah')->where('ijazah', '!=', '')
                    ->whereNotNull('personal_statement')->where('personal_statement', '!=', '')
                    ->whereNotNull('study_plan')->where('study_plan', '!=', '');
            });
        } elseif ($status === 'belum_lengkap') {
            $query->where(function ($q) {
                $q->whereDoesntHave('peserta.berkas')
                    ->orWhereHas('peserta.berkas', function ($qb) {
                        $qb->whereNull('foto')->orWhere('foto', '')
                            ->orWhereNull('rapor1')->orWhere('rapor1', '')
                            ->orWhereNull('rapor2')->orWhere('rapor2', '')
                            ->orWhereNull('rapor3')->orWhere('rapor3', '')
                            ->orWhereNull('rapor4')->orWhere('rapor4', '')
                            ->orWhereNull('rapor5')->orWhere('rapor5', '')
                            ->orWhereNull('ijazah')->orWhere('ijazah', '')
                            ->orWhereNull('personal_statement')->orWhere('personal_statement', '')
                            ->orWhereNull('study_plan')->orWhere('study_plan', '');
                    });
            });
        } elseif ($status === 'ada_sertifikat') {
            $query->whereHas('peserta.sertifikats');
        } elseif ($status === 'tanpa_sertifikat') {
            $query->whereDoesntHave('peserta.sertifikats');
        } elseif ($status === 'dkv_khusus') {
            $query->whereHas('peserta', function ($q) {
                $q->where('pilihan_prodi', 'like', '%DKV%')
                    ->orWhere('pilihan_prodi', 'like', '%Desain Komunikasi Visual%');
            });
        } elseif ($status === 'dkv_lengkap') {
            $query->whereHas('peserta', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('pilihan_prodi', 'like', '%DKV%')
                        ->orWhere('pilihan_prodi', 'like', '%Desain Komunikasi Visual%');
                })->whereHas('berkas', function ($qb) {
                    $qb->whereNotNull('surat_buta_warna')->where('surat_buta_warna', '!=', '')
                        ->whereNotNull('portfolio')->where('portfolio', '!=', '');
                });
            });
        } elseif ($status === 'dkv_belum_lengkap') {
            $query->whereHas('peserta', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('pilihan_prodi', 'like', '%DKV%')
                        ->orWhere('pilihan_prodi', 'like', '%Desain Komunikasi Visual%');
                })->where(function ($sub2) {
                    $sub2->whereDoesntHave('berkas')
                        ->orWhereHas('berkas', function ($qb) {
                            $qb->whereNull('surat_buta_warna')->orWhere('surat_buta_warna', '')
                                ->orWhereNull('portfolio')->orWhere('portfolio', '');
                        });
                });
            });
        }

        // Filter Spesifik Dokumen
        if ($request->filled('dokumen')) {
            $doc = $request->dokumen;
            if ($doc === 'foto') {
                $query->whereHas('peserta.berkas', fn($q) => $q->whereNotNull('foto')->where('foto', '!=', ''));
            } elseif ($doc === 'rapor') {
                $query->whereHas('peserta.berkas', fn($q) => $q->whereNotNull('rapor1')->where('rapor1', '!=', ''));
            } elseif ($doc === 'ijazah') {
                $query->whereHas('peserta.berkas', fn($q) => $q->whereNotNull('ijazah')->where('ijazah', '!=', ''));
            } elseif ($doc === 'personal_statement') {
                $query->whereHas('peserta.berkas', fn($q) => $q->whereNotNull('personal_statement')->where('personal_statement', '!=', ''));
            } elseif ($doc === 'study_plan') {
                $query->whereHas('peserta.berkas', fn($q) => $q->whereNotNull('study_plan')->where('study_plan', '!=', ''));
            } elseif ($doc === 'buta_warna') {
                $query->whereHas('peserta.berkas', fn($q) => $q->whereNotNull('surat_buta_warna')->where('surat_buta_warna', '!=', ''));
            } elseif ($doc === 'portfolio') {
                $query->whereHas('peserta.berkas', fn($q) => $q->whereNotNull('portfolio')->where('portfolio', '!=', ''));
            } elseif ($doc === 'sertifikat') {
                $query->whereHas('peserta.sertifikats');
            } elseif ($doc === 'rekomendasi') {
                $query->whereHas('peserta.berkas', fn($q) => $q->whereNotNull('surat_rekomendasi_sekolah')->where('surat_rekomendasi_sekolah', '!=', ''));
            }
        }

        // Stats calculation
        $totalPendaftar = Akun::where('role', 'pendaftar')->count();
        $totalLengkap = Akun::where('role', 'pendaftar')
            ->whereHas('peserta.berkas', function ($q) {
                $q->whereNotNull('foto')->where('foto', '!=', '')
                    ->whereNotNull('rapor1')->where('rapor1', '!=', '')
                    ->whereNotNull('rapor2')->where('rapor2', '!=', '')
                    ->whereNotNull('rapor3')->where('rapor3', '!=', '')
                    ->whereNotNull('rapor4')->where('rapor4', '!=', '')
                    ->whereNotNull('rapor5')->where('rapor5', '!=', '')
                    ->whereNotNull('ijazah')->where('ijazah', '!=', '')
                    ->whereNotNull('personal_statement')->where('personal_statement', '!=', '')
                    ->whereNotNull('study_plan')->where('study_plan', '!=', '');
            })->count();
        $totalBelumLengkap = $totalPendaftar - $totalLengkap;
        $totalAdaSertifikat = Akun::where('role', 'pendaftar')->whereHas('peserta.sertifikats')->count();
        $totalDkv = Akun::where('role', 'pendaftar')
            ->whereHas('peserta', function ($q) {
                $q->where('pilihan_prodi', 'like', '%DKV%')
                    ->orWhere('pilihan_prodi', 'like', '%Desain Komunikasi Visual%');
            })->count();
        $totalDkvLengkap = Akun::where('role', 'pendaftar')
            ->whereHas('peserta', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('pilihan_prodi', 'like', '%DKV%')
                        ->orWhere('pilihan_prodi', 'like', '%Desain Komunikasi Visual%');
                })->whereHas('berkas', function ($qb) {
                    $qb->whereNotNull('surat_buta_warna')->where('surat_buta_warna', '!=', '')
                        ->whereNotNull('portfolio')->where('portfolio', '!=', '');
                });
            })->count();

        // Sorting
        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'nama_asc') {
            $query->orderBy('nama', 'asc');
        } elseif ($sort === 'nama_desc') {
            $query->orderBy('nama', 'desc');
        } else {
            $query->latest();
        }

        $perPage = (int) $request->get('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        $pendaftars = $query->paginate($perPage)->withQueryString();

        $prodiList = [
            'Sains Komunikasi',
            'Desain Komunikasi Visual (DKV)',
            'Manajemen',
            'Akuntansi',
            'Sistem Informasi',
            'Pendidikan Bahasa Inggris',
            'Pendidikan Matematika',
            'Ilmu Komputer'
        ];

        return view('admin.berkas.index', compact(
            'pendaftars',
            'totalPendaftar',
            'totalLengkap',
            'totalBelumLengkap',
            'totalAdaSertifikat',
            'totalDkv',
            'totalDkvLengkap',
            'prodiList'
        ));
    }

    // --- SISTEM DATABASE TERPUSAT: BEASISWA ---
    public function indexBeasiswa(Request $request)
    {
        if (auth()->user()->role === 'mentor')
            return abort(403);

        $excludedNames = [
            'syifa atikah putri', 'fernando kevin pratama', 'nanda tri hafsyari', 'ayu suryaningsih',
            'violin azzahra riyona', 'putra izhar gumilar', 'vivi aulivia', 'anggun zahra julina',
            "adinda sofa syai'rillah", 'adlin ni’mah', 'maryana cecilia sitorus', 'amelia kania dewi',
            'shireen tsamrotul puadah', 'jelita rihandini', 'hayaa', 'affan farid kurniawan',
            'alya putri sulaiman', 'sulthan syauqi azhar', 'nazwa salsabila', 'devi rahma syahna',
            'rizki ramadan harahap', 'sinaga,eka chrsitina octaviani sinaga', 'salwa azariah', 'muhammad fauzi',
            'raditya', 'cristy stefany putri', 'muhammad farid firdaus', 'aini salsabila',
            'bunga sabiila agitsni', 'marchelina selviani', 'dea puspita sari', 'sri ambar wati',
            'rafiqa kaylani', 'khairunnisa darlis', 'neyna fakhirah abdiel', 'olivia ramadhani',
            'muhammad rusli sajidan', 'muhammad alvaro', 'desriama pasaribu', 'dame roma uli harahap',
            'mei sheilla azizah', 'alvin saifin nuha', 'alysa chairani', "syari'ah fathul aulia",
            'jessi tiara putri', 'nurdin', 'siti nur fadillah', 'rahmania agustin', 'desila dwi sartika',
            'azka syafwa', 'keyla aulya rizky', 'rahma auliya', 'muda agustian', 'felisha savinnatunasah',
            'rifdah zahraani'
        ];

        $query = Akun::where('role', 'pendaftar')
            ->whereNotIn(DB::raw('LOWER(nama)'), $excludedNames)
            ->where(function ($q) {
                // Normal path: Passed admin and passed CBT
                $q->where(function ($q2) {
                    $q2->whereHas('peserta.daftar', function ($q3) {
                        $q3->where('status', 'lulus');
                    })
                    ->whereHas('peserta', function ($q3) {
                        $q3->where('status_seleksi_ujian', 'lulus');
                    });
                })
                // BoD path: Has status_wawancara_bod and passed admin
                ->orWhereHas('peserta.daftar', function ($q2) {
                    $q2->where('status', 'lulus')
                       ->whereNotNull('status_wawancara_bod')
                       ->where('status_wawancara_bod', '!=', '');
                });
            })
            ->with(['peserta.daftar', 'peserta.nilais', 'peserta.berkas', 'peserta.penilaianMentors.mentor', 'peserta.jawabanUjians.ujian']);

        // filters ...
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhereHas('peserta.daftar', function ($q2) use ($search) {
                    $q2->where('asal_sekolah', 'like', "%{$search}%")
                        ->orWhere('kode_referral', 'like', "%{$search}%");
                }
                );
            });
        }

        if ($request->filled('sekolah')) {
            $query->whereHas('peserta.daftar', function ($q) use ($request) {
                $q->where('asal_sekolah', 'like', "%{$request->sekolah}%");
            });
        }

        if ($request->filled('min_nilai')) {
            $query->whereHas('peserta.daftar', function ($q) use ($request) {
                $q->where('rata_rata_nilai', '>=', $request->min_nilai);
            });
        }

        if ($request->filled('beasiswa')) {
            $query->whereHas('peserta.daftar', function ($q) use ($request) {
                $q->where('nominal_beasiswa', 'like', "%{$request->beasiswa}%");
            });
        }

        if ($request->filled('kelas')) {
            $query->whereHas('peserta.penilaianAkademiks', function ($q) use ($request) {
                $q->where('rekomendasi_kelas', 'like', "%{$request->kelas}%");
            });
        }

        if ($request->filled('prodi')) {
            $query->whereHas('peserta', function ($q) use ($request) {
                $q->where('pilihan_prodi', 'like', "%{$request->prodi}%")
                  ->orWhereHas('penilaianAkademiks', function ($q2) use ($request) {
                      $q2->where('rekomendasi_prodi_1', 'like', "%{$request->prodi}%");
                  });
            });
        }

        if ($request->filled('berkas_status')) {
            if ($request->berkas_status == 'lengkap') {
                $query->whereHas('peserta', function ($q) {
                    // Cek kelengkapan dasar, bisa pakai where(progress, 100) jika ada di table,
                    // atau cukup pastikan punya foto dan ijazah dll
                    $q->whereHas('berkas', function ($q2) {
                        $q2->whereNotNull('foto')
                           ->where(function ($sub) {
                               $sub->whereNotNull('ijazah')
                                   ->orWhereNotNull('kartu_identitas');
                           })
                           ->whereNotNull('motivasi_video');
                    });
                });
            } elseif ($request->berkas_status == 'belum') {
                $query->whereHas('peserta', function ($q) {
                    $q->whereDoesntHave('berkas')
                      ->orWhereHas('berkas', function ($q2) {
                          $q2->whereNull('foto')
                             ->orWhere(function ($sub) {
                                 $sub->whereNull('ijazah')
                                     ->whereNull('kartu_identitas');
                             })
                             ->orWhereNull('motivasi_video');
                      });
                });
            }
        }

        $pendaftars = $query->get();

        // Urutkan berdasarkan nilai rata-rata dari tertinggi ke terendah
        $pendaftars = $pendaftars->sortByDesc(function ($akun) {
            return $akun->peserta && $akun->peserta->daftar
            ? $akun->peserta->daftar->rata_rata_nilai
            : 0;
        });

        return view('admin.beasiswa.index', compact('pendaftars'));
    }

    public function showBeasiswa($id)
    {
        if (auth()->user()->role === 'mentor')
            return abort(403);

        $user = Akun::with([
            'peserta.daftar',
            'peserta.berkas',
            'peserta.nilais.matpel',
            'peserta.penilaianMentors.mentor',
            'peserta.penilaianAkademiks.penilai',
            'peserta.nilaiUjians.ujian'
        ])->findOrFail($id);

        $rataRataAkademik = $user->peserta->nilais->avg('nilai') ?? 0;
        $rataRataMentor = $user->peserta->penilaianMentors->avg('nilai') ?? 0;
        $rataRataAkademikFinal = $user->peserta->penilaianAkademiks->avg('total_akhir') ?? 0;

        // Calculate CBT Score (Exclude Pemetaan Diri)
        $cbtUjians = $user->peserta->nilaiUjians->filter(function($n) {
            $nama = strtolower($n->ujian->nama ?? '');
            return !str_contains($nama, 'pemetaan diri');
        });
        if ($cbtUjians->isNotEmpty()) {
            $rataRataCbt = $cbtUjians->avg('skor_rata') ?? 0;
        } else {
            $cbtJawaban = $user->peserta->jawabanUjians->filter(function($j) {
                $nama = strtolower($j->ujian->nama ?? '');
                return !str_contains($nama, 'pemetaan diri');
            });
            $rataRataCbt = $cbtJawaban->avg('nilai') ?? 0;
        }

        return view('admin.beasiswa.show', compact('user', 'rataRataAkademik', 'rataRataMentor', 'rataRataAkademikFinal', 'rataRataCbt'));
    }

    public function updateBeasiswa(Request $request, $id)
    {
        $request->validate([
            'nominal_beasiswa' => 'nullable|string|max:255',
            'kelas' => 'nullable|string|max:255',
            'prodi' => 'nullable|string|max:255',
            'status' => 'required|in:lulus,tidak_lulus,menunggu'
        ]);

        $daftar = Daftar::where('peserta_id', function ($query) use ($id) {
            $query->select('id')->from('peserta')->where('akun_id', $id);
        })->firstOrFail();

        $daftar->update([
            'status' => $request->status,
            'nominal_beasiswa' => $request->nominal_beasiswa
        ]);

        // Save kelas and prodi to PenilaianAkademik if provided
        if ($request->has('kelas') || $request->has('prodi')) {
            $penilaian = \App\Models\PenilaianAkademik::firstOrNew(['peserta_id' => $daftar->peserta_id]);
            if ($request->has('kelas')) $penilaian->rekomendasi_kelas = $request->kelas;
            if ($request->has('prodi')) $penilaian->rekomendasi_prodi_1 = $request->prodi;
            
            // set penilai_id if it's new
            if (!$penilaian->exists || !$penilaian->penilai_id) {
                $penilaian->penilai_id = auth()->id();
            }
            $penilaian->save();
        }

        $akun = Akun::with('peserta')->findOrFail($id);
        $this->logAktivitas('Update Beasiswa', 'Peserta', $akun->peserta->id, "Menetapkan beasiswa {$request->nominal_beasiswa} untuk {$akun->nama}");

        return back()->with('success', 'Keputusan beasiswa berhasil disimpan!');
    }

    public function indexHasilUjian(Request $request)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'akademik' && auth()->user()->role !== 'palugada' && auth()->user()->role !== 'god')
            return abort(403);

        $query = \App\Models\JawabanUjian::with(['peserta.akun', 'ujian']);

        // Search pendaftar
        if ($request->filled('search')) {
            $query->whereHas('peserta.akun', function($q) use ($request) {
                $q->where('nama', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Filter Type (Pemetaan Diri, TBA, TBI)
        if ($request->filled('type')) {
            if ($request->type === 'pemetaan_diri') {
                $query->whereHas('ujian', function($q) {
                    $q->where('nama', 'LIKE', '%Pemetaan Diri%');
                });
            } elseif ($request->type === 'tba' || $request->type === 'tpa') {
                $query->whereHas('ujian', function($q) {
                    $q->where('nama', 'LIKE', '%Potensi Akademik%')
                      ->orWhere('nama', 'LIKE', '%TPA%')
                      ->orWhere('nama', 'LIKE', '%TBA%')
                      ->orWhere('nama', 'LIKE', '%Akademik%');
                });
            } elseif ($request->type === 'tbi') {
                $query->whereHas('ujian', function($q) {
                    $q->where('nama', 'LIKE', '%Inggris%')
                      ->orWhere('nama', 'LIKE', '%TBI%')
                      ->orWhere('nama', 'LIKE', '%English%');
                });
            }
        }

        $hasilUjians = $query->with('peserta.nilaiUjians')->latest()->get()->map(function($hasil) {
            // Override nilai dengan skor_rata dari NilaiUjian (skala 100) jika ada
            $nilaiAsli = $hasil->peserta->nilaiUjians->firstWhere('ujian_id', $hasil->ujian_id);
            if ($nilaiAsli && $nilaiAsli->skor_rata !== null) {
                $hasil->nilai = (float) $nilaiAsli->skor_rata;
            }
            return $hasil;
        });

        return view('admin.hasil_ujian.index', compact('hasilUjians'));
    }

    public function resetJawabanUjian($id)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'akademik' && auth()->user()->role !== 'palugada' && auth()->user()->role !== 'god')
            return abort(403);

        $jawaban = \App\Models\JawabanUjian::findOrFail($id);
        
        // Hapus juga nilai_ujian jika ada
        \App\Models\NilaiUjian::where('ujian_id', $jawaban->ujian_id)
            ->where('peserta_id', $jawaban->peserta_id)
            ->delete();

        $namaUjian = $jawaban->ujian->nama ?? 'Unknown';
        $namaPeserta = $jawaban->peserta->akun->nama ?? 'Unknown';
        
        $this->logAktivitas('Reset Ujian', 'Ujian', $jawaban->ujian_id, "Mereset jawaban ujian {$namaUjian} untuk peserta {$namaPeserta}");
        
        $jawaban->delete();

        return back()->with('success', "Jawaban ujian peserta {$namaPeserta} berhasil dihapus. Peserta sekarang dapat mengikuti ujian kembali.");
    }

    /**
     * Tampilkan halaman Seleksi Ujian (CBT) secara agregat per peserta
     */
    public function indexSeleksiUjian(Request $request)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'akademik' && auth()->user()->role !== 'palugada' && auth()->user()->role !== 'god')
            return abort(403);

        $query = \App\Models\Peserta::whereHas('daftar', function ($q) {
            $q->where('status', 'lulus');
        })->with(['akun', 'daftar', 'jawabanUjians.ujian', 'nilaiUjians.ujian']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nama_sekolah', 'LIKE', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'a-z');
        if ($sort === 'z-a') {
            $query->orderBy('nama', 'desc');
        } elseif ($sort === 'latest') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('nama', 'asc');
        }

        $pesertas = $query->get()->map(function ($peserta) {
            // Temukan nilai TBA / TPA
            $tbaRecord = $peserta->nilaiUjians->filter(function ($n) {
                $nama = strtolower($n->ujian->nama ?? '');
                return str_contains($nama, 'tpa') || str_contains($nama, 'tba') || str_contains($nama, 'potensi') || str_contains($nama, 'akademik');
            })->first();
            
            if (!$tbaRecord) {
                $tbaJawaban = $peserta->jawabanUjians->filter(function ($j) {
                    $nama = strtolower($j->ujian->nama ?? '');
                    return str_contains($nama, 'tpa') || str_contains($nama, 'tba') || str_contains($nama, 'potensi') || str_contains($nama, 'akademik');
                })->first();
                $peserta->score_tba = $tbaJawaban ? ($tbaJawaban->nilai ?? 0) : null;
            } else {
                $peserta->score_tba = $tbaRecord->skor_rata;
            }

            // Temukan nilai TBI
            $tbiRecord = $peserta->nilaiUjians->filter(function ($n) {
                $nama = strtolower($n->ujian->nama ?? '');
                return str_contains($nama, 'tbi') || str_contains($nama, 'inggris') || str_contains($nama, 'english');
            })->first();
            
            if (!$tbiRecord) {
                $tbiJawaban = $peserta->jawabanUjians->filter(function ($j) {
                    $nama = strtolower($j->ujian->nama ?? '');
                    return str_contains($nama, 'tbi') || str_contains($nama, 'inggris') || str_contains($nama, 'english');
                })->first();
                $peserta->score_tbi = $tbiJawaban ? ($tbiJawaban->nilai ?? 0) : null;
            } else {
                $peserta->score_tbi = $tbiRecord->skor_rata;
            }

            // Temukan Kesimpulan AI Pemetaan Diri
            $pdJawaban = $peserta->jawabanUjians->filter(function ($j) {
                $nama = strtolower($j->ujian->nama ?? '');
                return str_contains($nama, 'pemetaan diri');
            })->first();
            
            $peserta->analisis_pemetaan = $pdJawaban ? $pdJawaban->kesimpulan_ai : null;
            $peserta->pemetaan_id = $pdJawaban ? $pdJawaban->id : null;

            return $peserta;
        });

        return view('admin.seleksi_ujian.index', compact('pesertas'));
    }

    /**
     * Update status seleksi ujian peserta secara manual
     */
    public function updateStatusSeleksiUjianCandidate(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'akademik' && auth()->user()->role !== 'palugada' && auth()->user()->role !== 'god')
            return abort(403);

        $request->validate([
            'status_seleksi_ujian' => 'required|in:menunggu,lulus,tidak_lulus'
        ]);

        $peserta = \App\Models\Peserta::findOrFail($id);
        $peserta->update([
            'status_seleksi_ujian' => $request->status_seleksi_ujian,
            'is_email_dikirim' => false
        ]);

        $statusText = $request->status_seleksi_ujian === 'lulus' ? 'LULUS' : ($request->status_seleksi_ujian === 'tidak_lulus' ? 'TIDAK LULUS' : 'MENUNGGU');

        $this->logAktivitas('Update Status Seleksi Ujian', 'Peserta', $peserta->id, 
            "Mengubah status seleksi ujian {$peserta->nama} menjadi {$statusText}");

        return back()->with('success', "Status seleksi ujian peserta {$peserta->nama} berhasil diperbarui menjadi {$statusText}!");
    }

    /**
     * Update status seleksi ujian peserta secara massal/bulk
     */
    public function bulkUpdateStatusSeleksiUjianCandidate(Request $request)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'akademik' && auth()->user()->role !== 'palugada' && auth()->user()->role !== 'god')
            return abort(403);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|exists:peserta,id',
            'status_seleksi_ujian' => 'required|in:menunggu,lulus,tidak_lulus'
        ]);

        $ids = $request->ids;
        $status = $request->status_seleksi_ujian;
        $statusText = $status === 'lulus' ? 'LULUS' : ($status === 'tidak_lulus' ? 'TIDAK LULUS' : 'MENUNGGU');

        \App\Models\Peserta::whereIn('id', $ids)->update([
            'status_seleksi_ujian' => $status,
            'is_email_dikirim' => false
        ]);

        $count = count($ids);
        $this->logAktivitas('Bulk Update Status Seleksi Ujian', 'Peserta', null, 
            "Mengubah status seleksi ujian {$count} peserta secara massal menjadi {$statusText}");

        return back()->with('success', "Status seleksi ujian {$count} peserta berhasil diperbarui menjadi {$statusText} secara massal!");
    }

    /**
     * Ekspor data Seleksi Ujian (CBT) ke file CSV/Excel
     */
    public function exportSeleksiUjianCandidate(Request $request)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'akademik' && auth()->user()->role !== 'palugada' && auth()->user()->role !== 'god')
            return abort(403);

        $query = \App\Models\Peserta::whereHas('daftar', function ($q) {
            $q->where('status', 'lulus');
        })->with(['akun', 'daftar', 'jawabanUjians.ujian', 'nilaiUjians.ujian']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nama_sekolah', 'LIKE', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'a-z');
        if ($sort === 'z-a') {
            $query->orderBy('nama', 'desc');
        } elseif ($sort === 'latest') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('nama', 'asc');
        }

        $pesertas = $query->get()->map(function ($peserta) {
            // Temukan nilai TBA / TPA
            $tbaRecord = $peserta->nilaiUjians->filter(function ($n) {
                $nama = strtolower($n->ujian->nama ?? '');
                return str_contains($nama, 'tpa') || str_contains($nama, 'tba') || str_contains($nama, 'potensi') || str_contains($nama, 'akademik');
            })->first();
            
            if (!$tbaRecord) {
                $tbaJawaban = $peserta->jawabanUjians->filter(function ($j) {
                    $nama = strtolower($j->ujian->nama ?? '');
                    return str_contains($nama, 'tpa') || str_contains($nama, 'tba') || str_contains($nama, 'potensi') || str_contains($nama, 'akademik');
                })->first();
                $peserta->score_tba = $tbaJawaban ? ($tbaJawaban->nilai ?? 0) : null;
            } else {
                $peserta->score_tba = $tbaRecord->skor_rata;
            }

            // Temukan nilai TBI
            $tbiRecord = $peserta->nilaiUjians->filter(function ($n) {
                $nama = strtolower($n->ujian->nama ?? '');
                return str_contains($nama, 'tbi') || str_contains($nama, 'inggris') || str_contains($nama, 'english');
            })->first();
            
            if (!$tbiRecord) {
                $tbiJawaban = $peserta->jawabanUjians->filter(function ($j) {
                    $nama = strtolower($j->ujian->nama ?? '');
                    return str_contains($nama, 'tbi') || str_contains($nama, 'inggris') || str_contains($nama, 'english');
                })->first();
                $peserta->score_tbi = $tbiJawaban ? ($tbiJawaban->nilai ?? 0) : null;
            } else {
                $peserta->score_tbi = $tbiRecord->skor_rata;
            }

            // Temukan Kesimpulan AI Pemetaan Diri
            $pdJawaban = $peserta->jawabanUjians->filter(function ($j) {
                $nama = strtolower($j->ujian->nama ?? '');
                return str_contains($nama, 'pemetaan diri');
            })->first();
            
            $peserta->analisis_pemetaan = $pdJawaban ? $pdJawaban->kesimpulan_ai : null;

            return $peserta;
        });

        $headers = [
            'Content-type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=MFLS_Seleksi_Ujian_CBT_2026.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $columns = ['No', 'Nama Lengkap', 'NISN', 'Asal Sekolah', 'Nilai TPA', 'Nilai TBI', 'Rata-rata CBT', 'Status Seleksi'];

        $callback = function() use ($pesertas, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $columns, ';');

            $no = 1;
            foreach ($pesertas as $peserta) {
                // Tidy up Status Seleksi without emojis
                $statusText = 'Menunggu Konfirmasi';
                if ($peserta->status_seleksi_ujian === 'lulus') {
                    $statusText = 'Lulus Seleksi';
                } elseif ($peserta->status_seleksi_ujian === 'tidak_lulus') {
                    $statusText = 'Gagal Seleksi';
                }

                // Tidy up Nama Lengkap (proper title casing and space collapsing)
                $namaLengkap = trim($peserta->nama ?? ($peserta->akun->nama ?? ''));
                $namaLengkap = preg_replace('/\s+/', ' ', $namaLengkap);
                $namaLengkap = ucwords(strtolower($namaLengkap));

                // Tidy up Asal Sekolah (proper capitalization & standardizing abbreviations like SMA, SMK, Negeri)
                $asalSekolahRaw = $peserta->daftar->asal_sekolah ?? ($peserta->nama_sekolah ?? '-');
                $asalSekolah = '-';
                if (!empty($asalSekolahRaw) && $asalSekolahRaw !== '-') {
                    $asalSekolah = preg_replace('/\s+/', ' ', trim($asalSekolahRaw));
                    $asalSekolah = ucwords(strtolower($asalSekolah));
                    
                    // Replace common abbreviations to make them look professional
                    $replacements = [
                        'Sma ' => 'SMA ',
                        'Smk ' => 'SMK ',
                        'Ma ' => 'MA ',
                        'Smp ' => 'SMP ',
                        'Sd ' => 'SD ',
                        ' Sma' => ' SMA',
                        ' Smk' => ' SMK',
                        ' Smp' => ' SMP',
                        ' Sd' => ' SD',
                        ' Negeri' => ' Negeri',
                        ' Swasta' => ' Swasta',
                        'Mts ' => 'MTs ',
                        ' Man ' => ' MAN ',
                        'Ipa' => 'IPA',
                        'Ips' => 'IPS',
                        'Tk ' => 'TK ',
                    ];
                    foreach ($replacements as $search => $replace) {
                        $asalSekolah = str_ireplace($search, $replace, $asalSekolah);
                    }
                    $asalSekolah = trim($asalSekolah);
                }

                $scoreTbaFormatted = $peserta->score_tba !== null ? number_format($peserta->score_tba, 2) : '-';
                $scoreTbiFormatted = $peserta->score_tbi !== null ? number_format($peserta->score_tbi, 2) : '-';
                $cbtScores = array_filter([$peserta->score_tba, $peserta->score_tbi], fn($v) => $v !== null);
                $rataCbt = count($cbtScores) > 0 ? number_format(array_sum($cbtScores) / count($cbtScores), 2) : '-';

                fputcsv($file, [
                    $no++,
                    $namaLengkap,
                    $peserta->nisn ?? '-',
                    $asalSekolah,
                    $scoreTbaFormatted,
                    $scoreTbiFormatted,
                    $rataCbt,
                    $statusText
                ], ';');
            }
            fclose($file);
        };

        $this->logAktivitas('Ekspor Excel Seleksi Ujian', 'Peserta', null, 
            "Mengekspor data seleksi ujian CBT peserta ke Excel");

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Kirim email notifikasi lolos seleksi ujian ke 1 peserta
     */
    public function kirimEmailLolosUjianCandidate($id)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'akademik' && auth()->user()->role !== 'palugada' && auth()->user()->role !== 'god')
            return abort(403);

        $peserta = \App\Models\Peserta::with(['akun', 'daftar'])->findOrFail($id);

        if ($peserta->status_seleksi_ujian !== 'lulus') {
            return back()->with('error', 'Peserta ini belum dinyatakan Lulus Seleksi Ujian CBT.');
        }

        $emailTujuan = $peserta->akun->email ?? null;
        if (!$emailTujuan) {
            return back()->with('error', 'Email peserta tidak ditemukan.');
        }

        $nama       = $peserta->akun->nama ?? $peserta->nama;
        $asalSekolah = $peserta->daftar->asal_sekolah ?? ($peserta->nama_sekolah ?? '-');

        try {
            \Illuminate\Support\Facades\Mail::mailer('hostinger')
                ->send('emails.lolos_seleksi_ujian',
                    array_merge(compact('nama', 'asalSekolah'), [
                        'templateBody' => \App\Models\EmailTemplate::renderBody('lolos_seleksi_ujian', compact('nama', 'asalSekolah')),
                    ]),
                    function ($message) use ($emailTujuan, $nama, $asalSekolah) {
                        $message->to($emailTujuan, $nama)
                                ->subject(\App\Models\EmailTemplate::subjectOrDefault('lolos_seleksi_ujian', '🎉 Selamat! Anda Lolos Seleksi Ujian MNCU Future Leader Scholarship 2026', compact('nama', 'asalSekolah')));
                    }
                );

            // Mark email as successfully sent
            $peserta->update(['is_email_dikirim' => true]);

            $this->logAktivitas('Kirim Email Lolos Ujian', 'Peserta', $peserta->id,
                "Mengirim email lolos seleksi ujian ke {$nama} ({$emailTujuan})");

            return back()->with('success', "Email notifikasi lolos seleksi ujian berhasil dikirim ke {$nama} ({$emailTujuan})!");
        } catch (\Exception $e) {
            Log::error('Gagal kirim email lolos ujian: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }

    /**
     * Kirim email massal ke semua peserta yang lulus seleksi ujian (via Queue)
     */
    public function kirimEmailLolosMassalCandidate(\Illuminate\Http\Request $request)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'akademik' && auth()->user()->role !== 'palugada' && auth()->user()->role !== 'god') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return abort(403);
        }

        $semuaPeserta = \App\Models\Peserta::where('status_seleksi_ujian', 'lulus')
            ->where('is_email_dikirim', false)
            ->get();

        if ($semuaPeserta->isEmpty()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Tidak ada peserta dengan status Lulus Seleksi Ujian yang belum dikirimi email.'], 400);
            }
            return back()->with('error', 'Tidak ada peserta dengan status Lulus Seleksi Ujian yang belum dikirimi email.');
        }

        $batchId = (string) \Illuminate\Support\Str::uuid();
        $total = $semuaPeserta->count();

        // Store progress in Cache (expires in 1 hour)
        \Illuminate\Support\Facades\Cache::put("ujian_email_batch_{$batchId}_total", $total, 3600);
        \Illuminate\Support\Facades\Cache::put("ujian_email_batch_{$batchId}_current", 0, 3600);
        \Illuminate\Support\Facades\Cache::put("ujian_email_batch_{$batchId}_status", 'processing', 3600);

        foreach ($semuaPeserta as $peserta) {
            \App\Jobs\SendLolosUjianEmailJob::dispatch($peserta->id, $batchId);
        }

        $this->logAktivitas('Kirim Email Massal Lolos Ujian', 'Bulk', null,
            "Memulai pengiriman massal email lolos ujian untuk {$total} peserta (Batch: {$batchId}).");

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'batch_id' => $batchId,
                'total' => $total,
                'message' => 'Proses pengiriman email massal seleksi ujian dimulai.'
            ]);
        }

        return back()->with('success', "Proses pengiriman massal untuk {$total} peserta telah dimulai di antrean latar belakang.");
    }

    /**
     * Get real-time progress of bulk exam email sending
     */
    public function getBulkEmailProgress(\Illuminate\Http\Request $request)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'akademik' && auth()->user()->role !== 'palugada' && auth()->user()->role !== 'god') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $batchId = $request->query('batch_id');
        if (!$batchId || !\Illuminate\Support\Facades\Cache::has("ujian_email_batch_{$batchId}_total")) {
            return response()->json(['status' => 'not_found'], 404);
        }

        $total = \Illuminate\Support\Facades\Cache::get("ujian_email_batch_{$batchId}_total", 0);
        $current = \Illuminate\Support\Facades\Cache::get("ujian_email_batch_{$batchId}_current", 0);
        
        $percentage = $total > 0 ? round(($current / $total) * 100) : 0;
        
        $status = 'processing';
        if ($current >= $total && $total > 0) {
            $status = 'completed';
            \Illuminate\Support\Facades\Cache::put("ujian_email_batch_{$batchId}_status", 'completed', 3600);
        }

        return response()->json([
            'batch_id' => $batchId,
            'total' => $total,
            'current' => $current,
            'percentage' => $percentage,
            'status' => $status
        ]);
    }


    // --- DATA PENDAFTAR: PENILAIAN MENTOR ---
    public function indexPenilaian(Request $request)
    {
        if (auth()->user()->role !== 'mentor' && auth()->user()->role !== 'admin' && auth()->user()->role !== 'akademik' && auth()->user()->role !== 'palugada' && auth()->user()->role !== 'god')
            return abort(403);

        $query = Akun::where('role', 'pendaftar')
            ->whereHas('peserta.daftar', function ($q) {
                $q->where('status', 'lulus');
            })
            ->whereHas('peserta', function ($q) {
                $q->where('status_seleksi_ujian', 'lulus');
            })
            ->with(['peserta.daftar', 'peserta.penilaianMentors']);

        return $this->processPenilaianIndex($query, $request, 'mentor');
    }

    // --- DATA PENDAFTAR: PENILAIAN AKADEMIK ---
    public function indexPenilaianAkademik(Request $request)
    {
        if (auth()->user()->role !== 'akademik' && auth()->user()->role !== 'admin' && auth()->user()->role !== 'mentor' && auth()->user()->role !== 'palugada' && auth()->user()->role !== 'dosen' && auth()->user()->role !== 'god')
            return abort(403);

        $query = Akun::where('role', 'pendaftar')
            ->whereHas('peserta.daftar', function ($q) {
                $q->where('status', 'lulus');
            })
            ->whereHas('peserta', function ($q) {
                $q->where('status_seleksi_ujian', 'lulus');
            });

        if (auth()->user()->role === 'dosen') {
            if (strtolower(auth()->user()->email) !== 'dendi.pratama@mncu.ac.id') {
                $query->whereHas('peserta', function ($q) {
                    $q->where('interviewer_id', auth()->id());
                });
            }
        }

        $query->with(['peserta.daftar', 'peserta.penilaianAkademiks']);

        return $this->processPenilaianIndex($query, $request, 'akademik');
    }

    private function processPenilaianIndex($query, $request, $type)
    {

        // Search logic
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                    ->orWhereHas('peserta', function ($pq) use ($search) {
                    $pq->where('nama_sekolah', 'LIKE', "%{$search}%")
                        ->orWhere('kabupaten', 'LIKE', "%{$search}%")
                        ->orWhere('provinsi', 'LIKE', "%{$search}%");
                }
                );
            });
        }

        // Sorting logic
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');

        if ($sort == 'nama') {
            $query->orderBy('nama', $order);
        }
        elseif (in_array($sort, ['sekolah', 'kabupaten', 'kota'])) {
            $field = [
                'sekolah' => 'nama_sekolah',
                'kabupaten' => 'kabupaten',
                'kota' => 'provinsi'
            ][$sort];

            $query->join('peserta', 'akun.id', '=', 'peserta.akun_id')
                ->orderBy('peserta.' . $field, $order)
                ->select('akun.*');
        }
        else {
            $query->latest();
        }

        $pendaftars = $query->get();

        $dosens = [];
        if (in_array(auth()->user()->role, ['admin', 'palugada', 'akademik', 'god'])) {
            $dosens = Akun::where('role', 'dosen')->orderBy('nama', 'asc')->get();
        }

        return view('admin.penilaian.index', compact('pendaftars', 'type', 'dosens'));
    }

    public function showPenilaian($id, Request $request)
    {
        if (auth()->user()->role !== 'mentor' && auth()->user()->role !== 'admin' && auth()->user()->role !== 'akademik' && auth()->user()->role !== 'palugada' && auth()->user()->role !== 'dosen' && auth()->user()->role !== 'god')
            return abort(403);

        $type = $request->get('type', 'mentor'); // Default to mentor if not specified

        // If user is dosen, force the type to 'akademik'
        if (auth()->user()->role === 'dosen') {
            $type = 'akademik';
        }

        $user = Akun::with(['peserta.daftar', 'peserta.penilaianMentors.mentor', 'peserta.penilaianAkademiks.penilai'])->findOrFail($id);

        // Security check for dosen
        if (auth()->user()->role === 'dosen' && strtolower(auth()->user()->email) !== 'dendi.pratama@mncu.ac.id' && ($user->peserta->interviewer_id ?? null) !== auth()->id()) {
            return abort(403, 'Anda tidak ditugaskan untuk mewawancarai peserta ini.');
        }

        return view('admin.penilaian.show', compact('user', 'type'));
    }

    public function storePenilaianAkademik(Request $request, $id)
    {
        if (auth()->user()->role !== 'akademik' && auth()->user()->role !== 'admin' && auth()->user()->role !== 'mentor' && auth()->user()->role !== 'palugada' && auth()->user()->role !== 'dosen' && auth()->user()->role !== 'god') {
            return back()->with('loginError', 'Hanya bagian Akademik, Mentor, dan Dosen yang dapat memberikan penilaian ini.');
        }

        $peserta = \App\Models\Peserta::where('akun_id', $id)->firstOrFail();

        // Security check for dosen: must be assigned to this candidate
        if (auth()->user()->role === 'dosen' && strtolower(auth()->user()->email) !== 'dendi.pratama@mncu.ac.id' && $peserta->interviewer_id !== auth()->id()) {
            return abort(403, 'Anda tidak ditugaskan untuk mewawancarai peserta ini.');
        }

        // Cek apakah sudah lulus seleksi ujian CBT
        // Accept either: JawabanUjian with status_seleksi='lulus' OR peserta.status_seleksi_ujian='lulus'
        $lulusUjianFromJawaban = \App\Models\JawabanUjian::where('peserta_id', $peserta->id)
            ->where('status_seleksi', 'lulus')
            ->whereHas('ujian', function ($q) {
                $q->where('nama', 'NOT LIKE', '%Pemetaan Diri%');
            })
            ->exists();

        $lulusUjian = $lulusUjianFromJawaban || $peserta->status_seleksi_ujian === 'lulus';

        if (!$lulusUjian) {
            return back()->with('loginError', 'Peserta ini belum dinyatakan Lulus Seleksi Ujian CBT. Pastikan status seleksi ujian sudah diperbarui menjadi "lulus" terlebih dahulu.');
        }

        if (auth()->user()->role === 'dosen') {
                    $request->validate([
            'wawancara_motivasi' => 'required|integer|min:1|max:5',
            'wawancara_prestasi' => 'required|integer|min:1|max:5',
            'wawancara_karakter' => 'required|integer|min:1|max:5',
            'wawancara_kontribusi' => 'required|integer|min:1|max:5',
            'wawancara_komunikasi' => 'required|integer|min:1|max:5',

            'catatan' => 'nullable|string',

            'rekomendasi_akhir' => 'required|string',
            'rekomendasi_beasiswa' => 'required|string',
            'rekomendasi_kelas' => 'required|string',
            'rekomendasi_prodi_1' => 'required|string',
            'rekomendasi_prodi_2' => 'required|string',
        ]);

            $wawancaraMotivasi = (float) $request->wawancara_motivasi;
            $wawancaraPrestasi = (float) $request->wawancara_prestasi;
            $wawancaraKarakter = (float) $request->wawancara_karakter;
            $wawancaraKontribusi = (float) $request->wawancara_kontribusi;
            $wawancaraKomunikasi = (float) $request->wawancara_komunikasi;

            // Calculate weighted final score: (25%, 20%, 20%, 20%, 15%)
            // Scale from 1-5 to 0-100 by multiplying the final average by 20
            $totalAkhir = (($wawancaraMotivasi * 0.25) + 
                          ($wawancaraPrestasi * 0.20) + 
                          ($wawancaraKarakter * 0.20) + 
                          ($wawancaraKontribusi * 0.20) + 
                          ($wawancaraKomunikasi * 0.15)) * 20;

            \App\Models\PenilaianAkademik::updateOrCreate(
                ['peserta_id' => $peserta->id, 'penilai_id' => auth()->id()],
                [
                    'wawancara_motivasi' => round($wawancaraMotivasi, 2),
                    'wawancara_prestasi' => round($wawancaraPrestasi, 2),
                    'wawancara_karakter' => round($wawancaraKarakter, 2),
                    'wawancara_kontribusi' => round($wawancaraKontribusi, 2),
                    'wawancara_komunikasi' => round($wawancaraKomunikasi, 2),

                    'wawancara_motivasi_catatan' => null,
                    'wawancara_prestasi_catatan' => null,
                    'wawancara_karakter_catatan' => null,
                    'wawancara_kontribusi_catatan' => null,
                    'wawancara_komunikasi_catatan' => null,

                    'rekomendasi_akhir' => $request->rekomendasi_akhir,
                    'rekomendasi_beasiswa' => $request->rekomendasi_beasiswa,
                    'catatan' => $request->catatan,
                    'catatan_rekomendasi_beasiswa' => $request->catatan,
                    'rekomendasi_kelas' => $request->rekomendasi_kelas,
                    'rekomendasi_prodi_1' => $request->rekomendasi_prodi_1,
                    'rekomendasi_prodi_2' => $request->rekomendasi_prodi_2,

                    'total_akhir' => round($totalAkhir, 2),
                ]
            );

            $this->logAktivitas('Input Penilaian Wawancara Dosen', 'Peserta', $peserta->id, "Memberikan penilaian wawancara dosen untuk {$peserta->nama}");

            return back()->with('success', 'Penilaian wawancara dosen berhasil disimpan!');
        }

        // Standard academic / admin form validation and saving
        $request->validate([
            'dosen_kompetensi' => 'required|numeric|min:0|max:100',
            'dosen_motivasi' => 'required|numeric|min:0|max:100',
            'dosen_wawasan' => 'required|numeric|min:0|max:100',
            'dosen_karir' => 'required|numeric|min:0|max:100',
            'dosen_integritas' => 'required|numeric|min:0|max:100',

            'mhs_leadership' => 'required|numeric|min:0|max:100',
            'mhs_organisasi' => 'required|numeric|min:0|max:100',
            'mhs_etika' => 'required|numeric|min:0|max:100',
            'mhs_adaptasi' => 'required|numeric|min:0|max:100',
            'mhs_komitmen' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string'
        ]);

        $totalDosen = ($request->dosen_kompetensi + $request->dosen_motivasi + $request->dosen_wawasan +
            $request->dosen_karir + $request->dosen_integritas) / 5;

        $totalMhs = ($request->mhs_leadership + $request->mhs_organisasi + $request->mhs_etika +
            $request->mhs_adaptasi + $request->mhs_komitmen) / 5;

        $totalAkhir = ($totalDosen + $totalMhs) / 2;

        \App\Models\PenilaianAkademik::updateOrCreate(
            ['peserta_id' => $peserta->id, 'penilai_id' => auth()->id()],
            [
                'dosen_kompetensi' => $request->dosen_kompetensi,
                'dosen_motivasi' => $request->dosen_motivasi,
                'dosen_wawasan' => $request->dosen_wawasan,
                'dosen_karir' => $request->dosen_karir,
                'dosen_integritas' => $request->dosen_integritas,
                'total_dosen' => round($totalDosen, 2),

                'mhs_leadership' => $request->mhs_leadership,
                'mhs_organisasi' => $request->mhs_organisasi,
                'mhs_etika' => $request->mhs_etika,
                'mhs_adaptasi' => $request->mhs_adaptasi,
                'mhs_komitmen' => $request->mhs_komitmen,
                'total_mhs' => round($totalMhs, 2),

                'total_akhir' => round($totalAkhir, 2),
                'catatan' => $request->catatan,
            ]
        );

        $this->logAktivitas('Input Penilaian Akademik', 'Peserta', $peserta->id, "Memberikan penilaian akademik untuk {$peserta->nama}");

        return back()->with('success', 'Penilaian akademik berhasil disimpan!');
    }

    public function assignInterviewer(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'akademik' && auth()->user()->role !== 'palugada' && auth()->user()->role !== 'god') {
            return abort(403);
        }

        if (auth()->user()->email === 'dendi.pratama@mncu.ac.id') {
            return abort(403, 'Anda tidak diizinkan untuk memilih dosen.');
        }

        $request->validate([
            'interviewer_id' => 'nullable|exists:akun,id',
            'ruangan' => 'nullable|string|max:255'
        ]);

        $peserta = \App\Models\Peserta::where('akun_id', $id)->firstOrFail();
        $peserta->update([
            'interviewer_id' => $request->interviewer_id,
            'ruangan' => $request->ruangan
        ]);

        $dosenName = 'Belum Ditentukan';
        if ($request->interviewer_id) {
            $dosen = \App\Models\Akun::find($request->interviewer_id);
            if ($dosen) $dosenName = $dosen->nama;
        }

        $this->logAktivitas('Assign Interviewer', 'Peserta', $peserta->id, "Mengatur pewawancara {$dosenName} di ruangan {$request->ruangan} untuk {$peserta->nama}");

        return back()->with('success', 'Pewawancara dan ruangan berhasil diperbarui!');
    }

    // --- PENGATURAN: MANAJEMEN USER ---
    public function indexUser(Request $request)
    {
        $authUser = auth()->user();
        if ($authUser->role !== 'admin' && $authUser->role !== 'palugada' && $authUser->role !== 'god' && $authUser->email !== 'info@beasiswamncu.com')
            return abort(403);

        $query = Akun::with(['peserta.daftar']);

        // Akun GOD disembunyikan dari siapapun KECUALI GOD sendiri
        if ($authUser->role !== 'god') {
            $query->where('role', '!=', 'god');
        }

        // Search by nama, email, nisn, atau asal sekolah
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('peserta', function ($qp) use ($search) {
                      $qp->where('nisn', 'like', "%{$search}%")
                         ->orWhere('nama_sekolah', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            if ($request->role === 'god' && $authUser->role !== 'god') {
                $query->whereRaw('1 = 0');
            } else {
                $query->where('role', $request->role);
            }
        }

        $users = $query->latest('id')->paginate(15)->withQueryString();

        // Statistics ringkasan (sembunyikan akun god jika dilihat non-god)
        $statQuery = Akun::query();
        if ($authUser->role !== 'god') {
            $statQuery->where('role', '!=', 'god');
        }
        $totalUsers = (clone $statQuery)->count();
        $totalPendaftar = (clone $statQuery)->where('role', 'pendaftar')->count();
        $totalStaff = (clone $statQuery)->where('role', '!=', 'pendaftar')->count();
        $totalVerified = (clone $statQuery)->whereNotNull('email_verified_at')->count();

        return view('admin.user.index', compact('users', 'totalUsers', 'totalPendaftar', 'totalStaff', 'totalVerified'));
    }

    public function destroyPendaftar($id)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->role !== 'palugada' && $user->role !== 'god' && !in_array($user->email, ['dept.adminis@mfls.com', 'info@beasiswamncu.com']))
            return abort(403);

        $akun = Akun::with('peserta.berkas')->findOrFail($id);

        // Pastikan yang dihapus hanya pendaftar
        if ($akun->role !== 'pendaftar') {
            return back()->with('error', 'Hanya akun pendaftar yang dapat dihapus melalui fitur ini.');
        }

        // Hapus file-file berkas fisik jika ada
        if ($akun->peserta && $akun->peserta->berkas) {
            $berkas = $akun->peserta->berkas;
            $filesToDelete = [
                $berkas->foto,
                $berkas->kartu_identitas,
                $berkas->kartu_keluarga,
                $berkas->ijazah,
                $berkas->surat_buta_warna,
                $berkas->portfolio,
                $berkas->surat_rekomendasi_sekolah,
                $berkas->motivasi_video, // asumsikan video ada yang diupload juga walau db bilang text URL
                $berkas->personal_statement,
                $berkas->study_plan
            ];

            // Hapus rapor JSON
            for ($i = 1; $i <= 5; $i++) {
                $field = "rapor$i";
                if ($berkas->$field) {
                    $raporFiles = json_decode($berkas->$field, true);
                    if (is_array($raporFiles)) {
                        $filesToDelete = array_merge($filesToDelete, $raporFiles);
                    }
                    else {
                        $filesToDelete[] = $berkas->$field;
                    }
                }
            }

            foreach ($filesToDelete as $file) {
                if ($file && \Illuminate\Support\Facades\Storage::disk('public')->exists($file)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
                }
            }
        }

        $this->logAktivitas('Hapus Pendaftar', 'Akun', $id, "Menghapus akun pendaftar beserta data dan berkasnya: " . $akun->nama);

        $timestamp = time();

        // Prefix email and NISN to free them up for new registration
        $akun->email = 'deleted_' . $timestamp . '_' . $akun->email;
        $akun->save();

        if ($akun->peserta) {
            $peserta = $akun->peserta;
            
            if ($peserta->daftar) {
                $peserta->daftar->forceDelete();
            }
            
            $peserta->forceDelete();
        }

        $akun->forceDelete();
        
        // Google Sheet Sync is now handled automatically by Observers in the background!

        return back()->with('success', 'Akun pendaftar berhasil dihapus! Email dan NISN sekarang tersedia kembali untuk pendaftaran baru.');
    }

    public function storeUser(Request $request)
    {
        $authUser = auth()->user();
        if ($authUser->role !== 'admin' && $authUser->role !== 'palugada' && $authUser->role !== 'god' && $authUser->email !== 'info@beasiswamncu.com')
            return abort(403);

        $allowedRoles = ['admin', 'panitia', 'akademik', 'mentor', 'palugada', 'dosen', 'pendaftar'];
        if ($authUser->role === 'god') {
            $allowedRoles[] = 'god';
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:akun,email',
            'password' => 'required|min:6',
            'role' => 'required|in:' . implode(',', $allowedRoles),
            'nisn' => 'nullable|string|max:20|unique:peserta,nisn',
            'nama_sekolah' => 'nullable|string|max:255',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $user = Akun::create([
                'nama' => $request->nama,
                'email' => strtolower(trim($request->email)),
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'role' => $request->role,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Jika role pendaftar, inisialisasi record peserta & daftar
            if ($request->role === 'pendaftar') {
                $nisn = $request->nisn ?: ('GEN' . time() . rand(100, 999));
                $noWa = $request->no_whatsapp ?: ('08' . rand(100000000, 999999999));
                $peserta = \App\Models\Peserta::create([
                    'akun_id' => $user->id,
                    'nama' => $user->nama,
                    'nisn' => $nisn,
                    'no_whatsapp' => $noWa,
                    'nama_sekolah' => $request->nama_sekolah ?: '-',
                    'tahun_lulus' => date('Y'),
                    'jenis_kelamin' => 'Laki-laki',
                    'tgl_lahir' => '2008-01-01',
                    'provinsi' => '-',
                    'kabupaten' => '-',
                ]);

                \App\Models\Daftar::create([
                    'peserta_id' => $peserta->id,
                    'no_wa' => $noWa,
                    'asal_sekolah' => $peserta->nama_sekolah,
                    'tahun_lulus' => date('Y'),
                    'jenis_kelamin' => 'Laki-laki',
                    'ttl' => '2008-01-01',
                    'provinsi' => '-',
                    'kabupaten' => '-',
                ]);
            }

            $this->logAktivitas('Tambah User Admin', 'Akun', $user->id, "Admin membuat akun baru: {$user->nama} ({$user->email}) role {$user->role}");
        });

        return back()->with('success', 'User berhasil ditambahkan!');
    }

    public function updateUser(Request $request, $id)
    {
        $authUser = auth()->user();
        if ($authUser->role !== 'admin' && $authUser->role !== 'palugada' && $authUser->role !== 'god' && $authUser->email !== 'info@beasiswamncu.com')
            return abort(403);
        $user = Akun::findOrFail($id);

        // Akun GOD tidak dapat diubah oleh siapapun kecuali GOD sendiri
        if ($user->role === 'god' && $authUser->role !== 'god') {
            return abort(403);
        }

        $allowedRoles = ['admin', 'panitia', 'akademik', 'mentor', 'palugada', 'dosen', 'pendaftar'];
        if ($authUser->role === 'god') {
            $allowedRoles[] = 'god';
        }

        $rules = [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:akun,email,' . $id,
            'role' => 'required|in:' . implode(',', $allowedRoles),
            'nisn' => 'nullable|string|max:20',
            'nama_sekolah' => 'nullable|string|max:255',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'min:6';
        }

        $request->validate($rules);

        $user->nama = $request->nama;
        $user->email = strtolower(trim($request->email));
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->save();

        if ($user->peserta) {
            $updatePeserta = ['nama' => $user->nama];
            if ($request->filled('nisn')) {
                $updatePeserta['nisn'] = $request->nisn;
            }
            if ($request->filled('nama_sekolah')) {
                $updatePeserta['nama_sekolah'] = $request->nama_sekolah;
                if ($user->peserta->daftar) {
                    $user->peserta->daftar->update(['asal_sekolah' => $request->nama_sekolah]);
                }
            }
            $user->peserta->update($updatePeserta);
        }

        $this->logAktivitas('Update User Admin', 'Akun', $user->id, "Admin memperbarui data user: {$user->nama} ({$user->email})");

        return back()->with('success', 'Data user berhasil diperbarui!');
    }

    public function updateTahunLulus(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'palugada' && auth()->user()->role !== 'god')
            return abort(403);
        
        $request->validate([
            'tahun_lulus' => 'required|integer|min:2000|max:2030'
        ]);

        $akun = Akun::with('peserta.daftar')->findOrFail($id);
        if ($akun->peserta) {
            $akun->peserta->update(['tahun_lulus' => $request->tahun_lulus]);
            if ($akun->peserta->daftar) {
                $akun->peserta->daftar->update(['tahun_lulus' => $request->tahun_lulus]);
            }
        }

        $this->logAktivitas('Update Tahun Lulus Admin', 'Peserta', $akun->peserta->id ?? null, "Admin mengubah tahun lulus {$akun->nama} menjadi {$request->tahun_lulus}");

        return back()->with('success', 'Tahun lulus berhasil diperbarui!');
    }

    public function storeDispensasiUjian(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'palugada' && auth()->user()->role !== 'god')
            return abort(403);

        $request->validate([
            'ujian_id' => 'required|exists:ujian,id',
            'tambahan_menit' => 'required|integer|min:0'
        ]);

        $akun = Akun::with('peserta')->findOrFail($id);
        
        if (!$akun->peserta) {
            return back()->with('error', 'Profil peserta tidak ditemukan.');
        }

        $dispensasi = \App\Models\DispensasiUjian::updateOrCreate(
            ['peserta_id' => $akun->peserta->id, 'ujian_id' => $request->ujian_id],
            ['tambahan_menit' => $request->tambahan_menit]
        );

        $this->logAktivitas('Dispensasi Waktu Ujian', 'Peserta', $akun->peserta->id, "Admin memberikan tambahan waktu {$request->tambahan_menit} menit untuk ujian {$request->ujian_id} ke {$akun->nama}");

        return back()->with('success', "Dispensasi waktu ujian berhasil diberikan sebesar {$request->tambahan_menit} menit.");
    }

    public function toggleStatus($id)
    {
        $currentUser = auth()->user();
        if ($currentUser->role !== 'god' && $currentUser->role !== 'admin' && $currentUser->role !== 'palugada' && $currentUser->email !== 'info@beasiswamncu.com') {
            return abort(403);
        }

        $user = Akun::findOrFail($id);

        // Hanya GOD yang boleh menonaktifkan/mengaktifkan akun Super Admin ('admin', 'palugada') atau sesama GOD
        // Non-god TIDAK boleh menyentuh GOD, dan non-god tidak boleh menonaktifkan Super Admin
        if ($user->role === 'god' && $currentUser->role !== 'god') {
            return abort(403);
        }

        if (in_array($user->role, ['admin', 'palugada']) && $currentUser->role !== 'god') {
            return back()->with('error', 'Hanya GOD Admin yang memiliki wewenang untuk menonaktifkan Super Admin.');
        }

        if ($user->id === $currentUser->id) {
            return back()->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->logAktivitas('Ubah Status User', 'Akun', $user->id, "User {$user->nama} ({$user->email}, Role: {$user->role}) {$statusText}.");

        return back()->with('success', "Akun {$user->nama} ({$user->email}) berhasil {$statusText}.");
    }

    public function destroyUser($id)
    {
        $authUser = auth()->user();
        if ($authUser->role !== 'admin' && $authUser->role !== 'palugada' && $authUser->role !== 'god' && $authUser->email !== 'info@beasiswamncu.com')
            return abort(403);
        $user = Akun::with(['peserta.berkas', 'peserta.sertifikats'])->findOrFail($id);

        if ($user->role === 'god' && $authUser->role !== 'god') {
            return abort(403);
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun yang sedang Anda gunakan saat ini!');
        }

        $userName = $user->nama;
        $userEmail = $user->email;
        $userRole = $user->role;

        \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
            // 1. Bersihkan seluruh data peserta dan berkas fisik jika akun pendaftar
            if ($user->peserta) {
                $peserta = $user->peserta;

                // Hapus berkas fisik di storage
                if ($peserta->berkas) {
                    $berkas = $peserta->berkas;
                    $singleFiles = [
                        $berkas->foto,
                        $berkas->kartu_identitas,
                        $berkas->kartu_keluarga,
                        $berkas->ijazah,
                        $berkas->kartu_pelajar,
                        $berkas->surat_rekom,
                        $berkas->personal_statement,
                        $berkas->study_plan,
                        $berkas->surat_rekomendasi_sekolah,
                        $berkas->motivasi_video,
                        $berkas->motivasi_video_tiktok,
                        $berkas->surat_buta_warna,
                        $berkas->bukti_follow_ig_beasiswamncu,
                        $berkas->bukti_follow_ig_mncu,
                        $berkas->bukti_follow_ig_mncunation,
                        $berkas->bukti_follow_ig_thearetegen,
                        $berkas->bukti_follow_tiktok_beasiswamncu,
                        $berkas->bukti_follow_tiktok_mncu,
                        $berkas->portfolio,
                    ];

                    foreach ($singleFiles as $file) {
                        if ($file && \Illuminate\Support\Facades\Storage::disk('public')->exists($file)) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
                        }
                    }

                    // Hapus file rapor
                    for ($i = 1; $i <= 6; $i++) {
                        $fld = "rapor{$i}";
                        if (!empty($berkas->$fld)) {
                            $decoded = json_decode($berkas->$fld, true);
                            if (is_array($decoded)) {
                                foreach ($decoded as $rf) {
                                    if ($rf && \Illuminate\Support\Facades\Storage::disk('public')->exists($rf)) {
                                        \Illuminate\Support\Facades\Storage::disk('public')->delete($rf);
                                    }
                                }
                            } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($berkas->$fld)) {
                                \Illuminate\Support\Facades\Storage::disk('public')->delete($berkas->$fld);
                            }
                        }
                    }

                    $berkas->forceDelete();
                }

                // Hapus berkas & record sertifikat
                if ($peserta->sertifikats && $peserta->sertifikats->count() > 0) {
                    foreach ($peserta->sertifikats as $sertifikat) {
                        if (!empty($sertifikat->file) && \Illuminate\Support\Facades\Storage::disk('public')->exists($sertifikat->file)) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($sertifikat->file);
                        }
                        $sertifikat->forceDelete();
                    }
                }

                // Hapus riwayat relasi seleksi, ujian, nilai
                $peserta->nilais()->forceDelete();
                $peserta->jawabanUjians()->forceDelete();
                $peserta->nilaiUjians()->forceDelete();
                $peserta->penilaianMentors()->forceDelete();
                $peserta->penilaianAkademiks()->forceDelete();
                if ($peserta->keputusan) {
                    $peserta->keputusan->forceDelete();
                }
                \App\Models\SesiUjian::where('peserta_id', $peserta->id)->delete();
                \App\Models\DispensasiUjian::where('peserta_id', $peserta->id)->delete();

                if ($peserta->daftar) {
                    $peserta->daftar->forceDelete();
                }

                $peserta->forceDelete();
            }

            // 2. Jika akun adalah staf/mentor/dosen/pewawancara, lepaskan ketergantungan relasi
            \App\Models\Peserta::where('interviewer_id', $user->id)->update(['interviewer_id' => null, 'ruangan' => null]);
            \App\Models\PenilaianMentor::where('mentor_id', $user->id)->delete();
            \App\Models\PenilaianAkademik::where('penilai_id', $user->id)->delete();
            \App\Models\Keputusan::where('akademik_id', $user->id)->delete();

            // 3. Bersihkan OTP, riwayat aktivitas, session, token
            \App\Models\Otp::where('email', $user->email)->delete();
            \App\Models\RiwayatAktivitas::where('pelaku_id', $user->id)->delete();
            \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $user->id)->delete();
            \Illuminate\Support\Facades\DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();

            // 4. Hapus akun user
            $user->forceDelete();
        });

        $this->logAktivitas('Hapus User Total', 'Akun', $id, "Menghapus akun {$userName} ({$userEmail}, Role: {$userRole}) beserta seluruh data & berkasnya sampai ke akar-akarnya.");

        return back()->with('success', "Akun user {$userName} ({$userEmail}) beserta seluruh data & berkasnya berhasil dihapus sampai ke akar-akarnya!");
    }

    public function resetPassword(Request $request, $id)
    {
        $authUser = auth()->user();
        if ($authUser->role !== 'admin' && $authUser->role !== 'palugada' && $authUser->role !== 'god' && !in_array($authUser->email, ['dept.adminis@mfls.com', 'info@beasiswamncu.com']))
            return abort(403);
        $user = Akun::findOrFail($id);

        if ($user->role === 'god' && $authUser->role !== 'god') {
            return abort(403);
        }

        $request->validate([
            'password' => 'required|min:6'
        ]);

        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password user ' . $user->nama . ' berhasil direset!');
    }

    public function updateEmailPendaftar(Request $request, $id)
    {
        $authUser = auth()->user();
        if ($authUser->role !== 'admin' && $authUser->role !== 'palugada' && $authUser->role !== 'god' && !in_array($authUser->email, ['dept.adminis@mfls.com', 'info@beasiswamncu.com'])) {
            return abort(403);
        }

        $request->validate([
            'email' => 'required|email|unique:akun,email,' . $id
        ]);

        $akun = Akun::findOrFail($id);
        
        // Pastikan hanya pendaftar yang bisa diubah emailnya melalui rute ini
        if ($akun->role !== 'pendaftar') {
            return back()->with('error', 'Hanya email pendaftar yang dapat diubah melalui fitur ini.');
        }

        $oldEmail = $akun->email;
        $akun->email = $request->email;
        $akun->save();

        // --- Kirim Notifikasi Email (Security & Info) ---
        try {
            $nama = $akun->nama;
            $newEmail = $request->email;
            
            // Kirim ke email BARU
            \Illuminate\Support\Facades\Mail::send('emails.admin_changed_email', 
                array_merge(['nama' => $nama, 'oldEmail' => $oldEmail, 'newEmail' => $newEmail, 'emailLama' => $oldEmail, 'emailBaru' => $newEmail], [
                    'templateBody' => \App\Models\EmailTemplate::renderBody('admin_changed_email', [
                        'nama' => $nama, 'emailLama' => $oldEmail, 'emailBaru' => $newEmail,
                    ]),
                ]), 
                function($message) use($newEmail, $nama, $oldEmail) {
                    $message->to($newEmail);
                    $message->subject(\App\Models\EmailTemplate::subjectOrDefault('admin_changed_email', 'Pembaruan Alamat Email Akun - MNCU Future Leader Scholarship', [
                        'nama' => $nama, 'emailLama' => $oldEmail, 'emailBaru' => $newEmail,
                    ]));
                }
            );

            // Kirim ke email LAMA (sebagai notifikasi keamanan)
            \Illuminate\Support\Facades\Mail::send('emails.admin_changed_email', 
                array_merge(['nama' => $nama, 'oldEmail' => $oldEmail, 'newEmail' => $newEmail, 'emailLama' => $oldEmail, 'emailBaru' => $newEmail], [
                    'templateBody' => \App\Models\EmailTemplate::renderBody('admin_changed_email', [
                        'nama' => $nama, 'emailLama' => $oldEmail, 'emailBaru' => $newEmail,
                    ]),
                ]), 
                function($message) use($oldEmail, $nama, $newEmail) {
                    $message->to($oldEmail);
                    $message->subject(\App\Models\EmailTemplate::subjectOrDefault('admin_changed_email', 'Pemberitahuan Perubahan Email Akun - MNCU Future Leader Scholarship', [
                        'nama' => $nama, 'emailLama' => $oldEmail, 'emailBaru' => $newEmail,
                    ]));
                }
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal kirim notifikasi ganti email admin: " . $e->getMessage());
        }

        $this->logAktivitas('Ganti Email Pendaftar', 'Akun', $id, "Mengubah email pendaftar {$akun->nama} dari [{$oldEmail}] menjadi [{$request->email}]");

        return back()->with('success', 'Email pendaftar berhasil diperbarui! Notifikasi telah dikirim ke pendaftar.');
    }

    public function detailPendaftar($id)
    {
        $user = Akun::with(['peserta.daftar', 'peserta.berkas', 'peserta.nilais.matpel', 'peserta.jawabanUjians.ujian'])->findOrFail($id);
        $ujians = \App\Models\Ujian::where('is_active', true)->get();
        $dispensasiUjian = \App\Models\DispensasiUjian::where('peserta_id', $user->peserta->id ?? 0)->get();
        $sesiTerkunci = \App\Models\SesiUjian::where('peserta_id', $user->peserta->id ?? 0)
            ->where('terkunci', true)
            ->with('ujian')
            ->get();
        return view('admin.pendaftar.show', compact('user', 'ujians', 'dispensasiUjian', 'sesiTerkunci'));
    }

    public function bukaKunciCbt($id, $sesiId)
    {
        $sesi = \App\Models\SesiUjian::with(['peserta.akun', 'ujian'])->findOrFail($sesiId);
        $namaPeserta = $sesi->peserta?->nama ?? $sesi->peserta?->akun?->nama ?? 'Peserta';
        $namaUjian = $sesi->ujian?->nama ?? 'Ujian';

        $sesi->update([
            'terkunci' => false,
            'pelanggaran_count' => 0,
            'dibuka_pada' => now(),
            'dibuka_oleh' => auth()->user()->nama ?? auth()->user()->name ?? 'Administrator',
        ]);

        $this->logAktivitas('Buka Kunci CBT', 'Peserta', $sesi->peserta_id, "Membuka kunci CBT {$namaUjian} untuk {$namaPeserta}");

        return back()->with('success', "Kunci CBT \"{$namaUjian}\" untuk {$namaPeserta} berhasil dibuka. Peserta dapat kembali melanjutkan ujian.");
    }

    public function resetSesiCbt($id, $sesiId)
    {
        $sesi = \App\Models\SesiUjian::with(['peserta.akun', 'ujian'])->findOrFail($sesiId);
        $namaPeserta = $sesi->peserta?->nama ?? $sesi->peserta?->akun?->nama ?? 'Peserta';
        $namaUjian = $sesi->ujian?->nama ?? 'Ujian';
        $pesertaId = $sesi->peserta_id;

        $sesi->delete();

        $this->logAktivitas('Reset Sesi CBT', 'Peserta', $pesertaId, "Mereset sesi CBT {$namaUjian} untuk {$namaPeserta}");

        return back()->with('success', "Sesi CBT \"{$namaUjian}\" untuk {$namaPeserta} telah di-reset dari awal.");
    }

    public function generateCertificate($id, \App\Services\CertificateService $certificateService)
    {
        $user = Akun::with('peserta')->findOrFail($id);
        $nama = $user->nama;

        return $certificateService->generatePdf($id)->stream("Sertifikat_{$nama}.pdf");
    }

    public function sendCertificateEmail(Request $request, $id, \App\Services\CertificateService $certificateService)
    {
        $success = $certificateService->sendEmail($id);

        if ($success) {
            return back()->with('success', "Sertifikat berhasil dikirim.");
        } else {
            return back()->with('error', "Gagal mengirim sertifikat.");
        }
    }

    public function bulkSendCertificate(Request $request)
    {
        $ids = $request->input('selected_ids');

        if (!$ids || !is_array($ids)) {
            return response()->json(['error' => 'Pilih minimal satu pendaftar.'], 400);
        }

        $batchId = (string) Str::uuid();
        $total = count($ids);

        // Store progress in Cache (expires in 1 hour)
        Cache::put("cert_batch_{$batchId}_total", $total, 3600);
        Cache::put("cert_batch_{$batchId}_current", 0, 3600);
        Cache::put("cert_batch_{$batchId}_status", 'processing', 3600);

        foreach ($ids as $id) {
            \App\Jobs\SendCertificateJob::dispatch($id, $batchId);
        }

        $this->logAktivitas('Kirim Sertifikat Massal', 'Bulk', null, "Memulai pengiriman $total sertifikat (Batch: $batchId).");

        return response()->json([
            'success' => true,
            'batch_id' => $batchId,
            'total' => $total,
            'message' => 'Proses pengiriman sertifikat dimulai.'
        ]);
    }

    public function getBulkProgress(Request $request)
    {
        $batchId = $request->query('batch_id');
        if (!$batchId || !Cache::has("cert_batch_{$batchId}_total")) {
            return response()->json(['status' => 'not_found'], 404);
        }

        $total = Cache::get("cert_batch_{$batchId}_total", 0);
        $current = Cache::get("cert_batch_{$batchId}_current", 0);
        
        $percentage = $total > 0 ? round(($current / $total) * 100) : 0;
        
        $status = 'processing';
        if ($current >= $total && $total > 0) {
            $status = 'completed';
            Cache::put("cert_batch_{$batchId}_status", 'completed', 3600);
        }

        return response()->json([
            'batch_id' => $batchId,
            'total' => $total,
            'current' => $current,
            'percentage' => $percentage,
            'status' => $status
        ]);
    }

    public function uploadBerkas(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->role !== 'palugada' && $user->role !== 'god' && !in_array($user->email, ['dept.adminis@mfls.com', 'info@beasiswamncu.com']))
            return abort(403);

        $request->validate([
            'field' => 'required|string',
            'file' => 'required|file|max:10240' // max 10MB
        ]);

        $akun = Akun::with('peserta')->findOrFail($id);
        $peserta = $akun->peserta;
        if (!$peserta) return back()->with('error', 'Peserta tidak ditemukan.');

        $berkas = \App\Models\Berkas::firstOrNew(['peserta_id' => $peserta->id]);
        
        $field = $request->field;
        // Upload
        $path = $request->file('file')->store('berkas/' . $peserta->id . (str_starts_with($field, 'rapor') ? '/' . $field : ''), 'public');
        $this->compressImage($path);

        if (str_starts_with($field, 'rapor')) {
            $existing = $berkas->$field ? json_decode($berkas->$field, true) : [];
            if (!is_array($existing)) $existing = $berkas->$field ? [$berkas->$field] : [];
            $existing[] = $path;
            $berkas->$field = json_encode($existing);
        } else {
            if ($berkas->$field) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($berkas->$field);
            }
            $berkas->$field = $path;
        }
        
        $berkas->save();
        
        $this->logAktivitas('Upload Berkas Admin', 'Berkas', $berkas->id, "Admin mengunggah berkas $field untuk " . $akun->nama);
        return back()->with('success', "Berkas $field berhasil diunggah!");
    }

    public function deleteBerkas(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->role !== 'god' && !in_array($user->email, ['dept.adminis@mfls.com', 'info@beasiswamncu.com']))
            return abort(403);

        $request->validate([
            'field' => 'required|string',
            'path' => 'nullable|string'
        ]);

        $akun = Akun::with('peserta.berkas')->findOrFail($id);
        $berkas = $akun->peserta->berkas;
        if (!$berkas) return back()->with('error', 'Berkas tidak ditemukan.');

        $field = $request->field;
        $pathToDelete = $request->path;

        if (str_starts_with($field, 'rapor')) {
            $existing = $berkas->$field ? json_decode($berkas->$field, true) : [];
            if (!is_array($existing)) $existing = $berkas->$field ? [$berkas->$field] : [];

            if ($pathToDelete) {
                $existing = array_values(array_filter($existing, fn($p) => $p !== $pathToDelete));
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pathToDelete);
            } else {
                foreach($existing as $p) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($p);
                }
                $existing = [];
            }
            $berkas->$field = count($existing) > 0 ? json_encode($existing) : null;
        } else {
            if ($berkas->$field) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($berkas->$field);
            }
            $berkas->$field = null;
        }

        $berkas->save();

        $this->logAktivitas('Hapus Berkas Admin', 'Berkas', $berkas->id, "Admin menghapus berkas $field untuk " . $akun->nama);
        return back()->with('success', "Berkas $field berhasil dihapus!");
    }

    public function indexSoal(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'akademik'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses ke Bank Soal.');
        }

        $query = \App\Models\Soal::with('ujian')->latest();

        // Fitur Filter by Kategori Ujian
        if ($request->filled('ujian_id')) {
            $query->where('ujian_id', $request->ujian_id);
        }

        $soals = $query->get();
        $ujians = \App\Models\Ujian::all();

        return view('admin.soal.index', compact('soals', 'ujians'));
    }

    public function storeSoal(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'akademik']))
            return abort(403);
        $request->validate([
            'ujian_id' => 'required|exists:ujian,id',
            'pertanyaan' => 'required',
            'gambar' => 'nullable|image|max:2048',
            'opsi_a' => 'required',
            'opsi_a_image' => 'nullable|image|max:2048',
            'opsi_b' => 'required',
            'opsi_b_image' => 'nullable|image|max:2048',
            'opsi_c' => 'required',
            'opsi_c_image' => 'nullable|image|max:2048',
            'opsi_d' => 'required',
            'opsi_d_image' => 'nullable|image|max:2048',
            'opsi_e' => 'nullable|string',
            'opsi_e_image' => 'nullable|image|max:2048',
            'kunci_jawaban' => 'nullable|in:a,b,c,d,e',
            'bobot' => 'required|integer',
            'kategori' => 'nullable|string'
        ]);

        $data = $request->all();
        if (empty($data['kunci_jawaban'])) $data['kunci_jawaban'] = null;

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('soal_images', 'public');
            $data['gambar'] = $path;
            $this->compressImage($path);
        }

        // Handle option images
        foreach (['a', 'b', 'c', 'd', 'e'] as $option) {
            $fieldName = "opsi_{$option}_image";
            if ($request->hasFile($fieldName)) {
                $path = $request->file($fieldName)->store('soal_images', 'public');
                $data[$fieldName] = $path;
                $this->compressImage($path);
            }
        }

        \App\Models\Soal::create($data);
        $this->logAktivitas('Tambah Soal', 'Soal', null, "Menambahkan soal baru ke kategori ID: " . $data['ujian_id']);

        return back()->with('success', 'Soal berhasil ditambahkan!');
    }

    public function editSoal($id)
    {
        if (!in_array(auth()->user()->role, ['admin', 'akademik']))
            return abort(403);
        $soal = \App\Models\Soal::findOrFail($id);
        $ujians = \App\Models\Ujian::all();
        return view('admin.soal.edit', compact('soal', 'ujians'));
    }

    public function updateSoal(Request $request, $id)
    {
        if (!in_array(auth()->user()->role, ['admin', 'akademik']))
            return abort(403);
        $request->validate([
            'ujian_id' => 'required|exists:ujian,id',
            'pertanyaan' => 'required',
            'gambar' => 'nullable|image|max:2048',
            'opsi_a' => 'required',
            'opsi_a_image' => 'nullable|image|max:2048',
            'opsi_b' => 'required',
            'opsi_b_image' => 'nullable|image|max:2048',
            'opsi_c' => 'required',
            'opsi_c_image' => 'nullable|image|max:2048',
            'opsi_d' => 'required',
            'opsi_d_image' => 'nullable|image|max:2048',
            'opsi_e' => 'nullable|string',
            'opsi_e_image' => 'nullable|image|max:2048',
            'kunci_jawaban' => 'nullable|in:a,b,c,d,e',
            'bobot' => 'required|integer',
            'kategori' => 'nullable|string'
        ]);

        $soal = \App\Models\Soal::findOrFail($id);
        $data = $request->all();
        if (empty($data['kunci_jawaban'])) $data['kunci_jawaban'] = null;

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($soal->gambar) {
                Storage::disk('public')->delete($soal->gambar);
            }
            $path = $request->file('gambar')->store('soal_images', 'public');
            $data['gambar'] = $path;
            $this->compressImage($path);
        }

        // Handle option images
        foreach (['a', 'b', 'c', 'd', 'e'] as $option) {
            $fieldName = "opsi_{$option}_image";
            if ($request->hasFile($fieldName)) {
                // Delete old image if exists
                if ($soal->$fieldName) {
                    Storage::disk('public')->delete($soal->$fieldName);
                }
                $path = $request->file($fieldName)->store('soal_images', 'public');
                $data[$fieldName] = $path;
                $this->compressImage($path);
            }
        }

        $soal->update($data);

        return redirect()->route('admin.soal.index', ['ujian_id' => $soal->ujian_id])->with('success', 'Soal berhasil diperbarui!');
    }

    public function destroySoal($id)
    {
        if (!in_array(auth()->user()->role, ['admin', 'akademik']))
            return abort(403);
        $soal = \App\Models\Soal::findOrFail($id);

        // Delete main question image
        if ($soal->gambar) {
            Storage::disk('public')->delete($soal->gambar);
        }

        // Delete option images
        foreach (['a', 'b', 'c', 'd', 'e'] as $option) {
            $fieldName = "opsi_{$option}_image";
            if ($soal->$fieldName) {
                Storage::disk('public')->delete($soal->$fieldName);
            }
        }

        $this->logAktivitas('Hapus Soal', 'Soal', $id, "Menghapus soal: " . substr($soal->pertanyaan, 0, 30));
        $soal->delete();

        return back()->with('success', 'Soal berhasil dihapus!');
    }

    public function updateUjian(Request $request, $id)
    {
        if (!in_array(auth()->user()->role, ['admin', 'akademik']))
            return abort(403);

        $request->validate([
            'durasi' => 'required|integer|min:1',
            'is_active' => 'required|boolean'
        ]);

        $ujian = \App\Models\Ujian::findOrFail($id);
        $ujian->update([
            'durasi' => $request->durasi,
            'is_active' => $request->is_active
        ]);

        $this->logAktivitas('Update Ujian', 'Ujian', $id, "Mengubah pengaturan ujian: " . $ujian->nama);
        return back()->with('success', 'Pengaturan ujian berhasil diperbarui!');
    }

    public function deleteAllSoal(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'akademik']))
            return abort(403);
        
        $query = \App\Models\Soal::query();

        // Jika ada filter kategori, hanya hapus yang di kategori tersebut
        if ($request->filled('ujian_id')) {
            $query->where('ujian_id', $request->ujian_id);
            $namaUjian = \App\Models\Ujian::find($request->ujian_id)->nama ?? 'Kategori Terpilih';
            $logMsg = "Menghapus semua soal di kategori: " . $namaUjian;
        } else {
            $logMsg = "Menghapus SELURUH bank soal";
        }

        $soals = $query->get();
        $count = $soals->count();

        foreach ($soals as $soal) {
            // Delete images
            if ($soal->gambar) Storage::disk('public')->delete($soal->gambar);
            foreach (['a', 'b', 'c', 'd', 'e'] as $opt) {
                $f = "opsi_{$opt}_image";
                if ($soal->$f) Storage::disk('public')->delete($soal->$f);
            }
            $soal->delete();
        }

        $this->logAktivitas('Hapus Masal Soal', 'Soal', null, $logMsg);

        return back()->with('success', "Berhasil menghapus $count soal!");
    }

    public function importSoal(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'akademik']))
            return abort(403);
        $request->validate([
            'ujian_id' => 'required|exists:ujian,id',
            'file_soal' => 'required|mimes:csv,txt,docx,pdf'
        ]);

        $ujianId = $request->ujian_id;
        $file = $request->file('file_soal');
        $ext = $file->getClientOriginalExtension();

        if ($ext === 'docx') {
            return $this->importWord($file, $ujianId);
        }

        if ($ext === 'pdf') {
            return $this->importPdf($file, $ujianId);
        }

        // ... Logic CSV lama ...
        $handle = fopen($file->getRealPath(), "r");
        fgetcsv($handle); // Skip header

        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($row) >= 8) {
                \App\Models\Soal::create([
                    'ujian_id' => $ujianId,
                    'pertanyaan' => $row[0],
                    'gambar' => null,
                    'opsi_a' => $row[1],
                    'opsi_b' => $row[2],
                    'opsi_c' => $row[3],
                    'opsi_d' => $row[4],
                    'opsi_e' => $row[5],
                    'kunci_jawaban' => !empty($row[6]) ? strtolower($row[6]) : null,
                    'bobot' => (int)$row[7]
                ]);
            }
        }
        fclose($handle);

        return back()->with('success', 'Import soal berhasil!');
    }

    private function importPdf($file, $ujianId)
    {
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($file->getRealPath());
            $text = $pdf->getText();

            // Jika teks tidak mengandung kata 'Kunci' atau 'Jawaban', kemungkinan jawaban ada di format BOLD.
            // Kita gunakan bantuan Gemini AI untuk mendeteksi soal & kunci jawaban dari PDF tersebut.
            if (!preg_match('/(Kunci|Jawaban)\s*:/i', $text)) {
                return $this->importPdfWithAi($file, $ujianId);
            }
            
            // Split by lines
            $lines = explode("\n", $text);
            $currentSoal = [];
            $count = 0;

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                // Deteksi Soal Baru: (1) PG : Pertanyaan... atau 1. Pertanyaan...
                if (preg_match('/^(?:\((\d+)\)\s*PG\s*:\s*|\d+\.\s*)(.*)/i', $line, $matches)) {
                    // Simpan soal sebelumnya jika ada pertanyaan dan kunci
                    if (isset($currentSoal['pertanyaan']) && isset($currentSoal['kunci_jawaban'])) {
                        $currentSoal['ujian_id'] = $ujianId;
                        $currentSoal['bobot'] = 5;
                        \App\Models\Soal::create($currentSoal);
                        $count++;
                    }
                    
                    $currentSoal = [];
                    $currentSoal['pertanyaan'] = $matches[2];
                }
                // Deteksi Opsi (a. ..., b. ..., dst - Mendukung a-e)
                elseif (preg_match('/^([a-e])\.\s*(.*)/i', $line, $matches)) {
                    $optKey = strtolower($matches[1]);
                    $currentSoal['opsi_' . $optKey] = $matches[2];
                }
                // Deteksi Kunci Jawaban (Kunci: A atau Jawaban: A)
                elseif (preg_match('/^(Kunci|Jawaban)\s*:\s*([A-E])/i', $line, $matches)) {
                    $currentSoal['kunci_jawaban'] = strtolower($matches[2]);
                }
                // Jika masih dalam pertanyaan (melanjutkan baris sebelumnya)
                elseif (isset($currentSoal['pertanyaan']) && !isset($currentSoal['opsi_a'])) {
                    $currentSoal['pertanyaan'] .= " " . $line;
                }
            }

            // Simpan soal terakhir
            if (isset($currentSoal['pertanyaan']) && isset($currentSoal['kunci_jawaban'])) {
                $currentSoal['ujian_id'] = $ujianId;
                $currentSoal['bobot'] = 5;
                \App\Models\Soal::create($currentSoal);
                $count++;
            }

            return back()->with('success', "Import PDF berhasil! Total $count soal ditambahkan.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('PDF Import failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal membaca file PDF: ' . $e->getMessage());
        }
    }

    private function importPdfWithAi($file, $ujianId)
    {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return back()->with('error', 'Gagal mendeteksi kunci jawaban (format BOLD). Silakan tambahkan tulisan "Kunci: A" di file atau masukkan Gemini API Key di .env.');
        }

        try {
            $pdfBase64 = base64_encode(file_get_contents($file->getRealPath()));
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite-preview:generateContent?key=" . $apiKey;

            $prompt = "Tolong ekstrak SELURUH soal dari PDF ini tanpa ada yang terlewat (ekstrak semua soal dari awal hingga akhir). Deteksi jawaban yang benar berdasarkan teks yang DI-BOLD (tebal). 
            Berikan output dalam format JSON array of objects dengan struktur kunci singkat untuk menghemat token: 
            [{\"q\": \"pertanyaan\", \"a\": \"opsi a\", \"b\": \"opsi b\", \"c\": \"opsi c\", \"d\": \"opsi d\", \"e\": \"opsi e\", \"k\": \"a/b/c/d/e\"}].
            Pastikan hanya mengembalikan JSON saja tanpa markdown atau penjelasan apapun. PENTING: Ekstrak SEMUA soal, jangan berhenti di tengah.";

            $data = [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $prompt],
                            [
                                "inline_data" => [
                                    "mime_type" => "application/pdf",
                                    "data" => $pdfBase64
                                ]
                            ]
                        ]
                    ]
                ],
                "generationConfig" => [
                    "maxOutputTokens" => 8192,
                    "temperature" => 0.1
                ]
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if ($err) {
                throw new \Exception("Curl Error: " . $err);
            }

            $result = json_decode($response, true);
            
            // Log respon mentah untuk debugging jika gagal
            if (isset($result['error'])) {
                \Illuminate\Support\Facades\Log::error('Gemini API Error: ' . json_encode($result['error']));
                throw new \Exception("Gemini API Error: " . ($result['error']['message'] ?? 'Unknown error'));
            }

            $jsonText = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$jsonText) {
                \Illuminate\Support\Facades\Log::error('Gemini Raw Response: ' . $response);
                throw new \Exception("AI tidak memberikan respon teks. Silakan cek Log Laravel.");
            }

            // Bersihkan markdown jika ada (misal ```json ... ```)
            $jsonText = preg_replace('/^```json\s*|```\s*$/i', '', trim($jsonText));

            $soals = json_decode($jsonText, true);
            if (!is_array($soals)) {
                \Illuminate\Support\Facades\Log::error('AI JSON Decode Failed. Raw Text: ' . $jsonText);
                throw new \Exception("Format respon AI tidak valid.");
            }

            \Illuminate\Support\Facades\Log::info('Parsed AI Soals: ' . json_encode($soals));

            $count = 0;
            foreach ($soals as $s) {
                // Konversi semua key ke lowercase untuk menghindari masalah case-sensitivity
                $s = array_change_key_case($s, CASE_LOWER);

                $pertanyaan = $s['pertanyaan'] ?? $s['q'] ?? null;
                $kunci = $s['kunci_jawaban'] ?? $s['k'] ?? null;

                if ($pertanyaan && $kunci) {
                    \App\Models\Soal::create([
                        'ujian_id' => $ujianId,
                        'pertanyaan' => $pertanyaan,
                        'opsi_a' => $s['opsi_a'] ?? $s['a'] ?? '-',
                        'opsi_b' => $s['opsi_b'] ?? $s['b'] ?? '-',
                        'opsi_c' => $s['opsi_c'] ?? $s['c'] ?? '-',
                        'opsi_d' => $s['opsi_d'] ?? $s['d'] ?? '-',
                        'opsi_e' => $s['opsi_e'] ?? $s['e'] ?? '-',
                        'kunci_jawaban' => strtolower($kunci),
                        'bobot' => 5
                    ]);
                    $count++;
                } else {
                    \Illuminate\Support\Facades\Log::warning('Soal skipped due to missing keys: ' . json_encode($s));
                }
            }

            return back()->with('success', "Import via AI Berhasil! Berhasil mendeteksi $count soal beserta kunci jawabannya (BOLD).");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AI PDF Import failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal menggunakan AI untuk membaca PDF: ' . $e->getMessage());
        }
    }
    private function importWord($file, $ujianId)
    {
        $zip = new \ZipArchive;
        $xmlContent = '';

        if ($zip->open($file->getRealPath()) === TRUE) {
            $xmlContent = $zip->getFromName('word/document.xml');
            $zip->close();
        }
        else {
            return back()->with('error', 'Gagal membaca file Word.');
        }

        // Parsing XML sederhana
        $dom = new \DOMDocument();
        $dom->loadXML($xmlContent);
        $paragraphs = $dom->getElementsByTagName('p'); // Paragraph tag in Word XML

        $currentSoal = [];
        $count = 0;

        foreach ($paragraphs as $p) {
            $text = $p->textContent;
            $text = trim($text);
            if (empty($text))
                continue;

            // Deteksi Opsi (A., B., C., D., E.)
            if (preg_match('/^([A-E])\.\s*(.*)/i', $text, $matches)) {
                $optKey = strtolower($matches[1]); // a, b, c, d, e
                $currentSoal['opsi_' . $optKey] = $matches[2];
            }
            // Deteksi Kunci Jawaban (Kunci: A)
            elseif (preg_match('/^Kunci\s*:\s*([A-E])/i', $text, $matches)) {
                $currentSoal['kunci_jawaban'] = strtolower($matches[1]);

                // Kunci biasanya baris terakhir per soal, jadi simpan
                if (isset($currentSoal['pertanyaan'])) {
                    $currentSoal['ujian_id'] = $ujianId; // Set Ujian ID
                    $currentSoal['bobot'] = 5;
                    \App\Models\Soal::create($currentSoal);
                    $currentSoal = []; // Reset
                    $count++;
                }
            }
            // Deteksi Soal Baru (1. Pertanyaan...)
            elseif (preg_match('/^\d+\.\s*(.*)/', $text, $matches)) {
                $currentSoal = [];
                $currentSoal['pertanyaan'] = $matches[1];
            }
            else {
                if (isset($currentSoal['pertanyaan']) && !isset($currentSoal['opsi_a'])) {
                    $currentSoal['pertanyaan'] .= " " . $text;
                }
            }
        }

        return back()->with('success', "Import Word berhasil! Total $count soal ditambahkan.");
    }



    public function verifikasi(Request $request, $id)
    {
        if (auth()->user()->role === 'mentor') {
            return back()->with('error', 'Mentor tidak memiliki izin verifikasi kelulusan.');
        }

        $request->validate([
            'status' => 'required|in:lulus,tidak_lulus,menunggu,diajukan_palugada'
        ]);

        $daftar = Daftar::where('peserta_id', function ($query) use ($id) {
            $query->select('id')->from('peserta')->where('akun_id', $id);
        })->firstOrFail();

        $statusToSet = $request->status;
        $currentUserRole = auth()->user()->role;

        // Workflow Palugada: Admin/Panitia setujui -> diajukan_palugada (untuk double check)
        // Admin/Panitia tolak -> juga masuk diajukan_palugada (palugada yang finalisasi penolakan)
        if ($currentUserRole !== 'palugada' && in_array($statusToSet, ['lulus', 'tidak_lulus'])) {
            $statusToSet = 'diajukan_palugada';
        }

        $daftar->update([
            'status' => $statusToSet
        ]);

        $akun = Akun::with('peserta')->findOrFail($id);
        $this->logAktivitas('Verifikasi Status', 'Peserta', $akun->peserta->id, "Mengubah status {$akun->nama} menjadi " . strtoupper($statusToSet));

        $msg = 'Status verifikasi berhasil diperbarui!';
        
        // Khusus jika direset ke Menunggu, selalu kembalikan ke halaman ini
        if ($statusToSet === 'menunggu') {
            return back()->with('success', 'Keputusan berhasil dibatalkan. Status kembali ke Menunggu.');
        }

        if ($statusToSet === 'diajukan_palugada') {
            $msg = 'Berkas diteruskan ke Palugada untuk verifikasi akhir (Double Check).';
            // Jika yang melakukan adalah role admin/panitia, arahkan ke daftar palugada untuk melihat antrean
            if (auth()->user()->role === 'admin' || auth()->user()->role === 'panitia') {
                return redirect()->route('admin.palugada.index')->with('success', $msg);
            }
        }

        // Jika palugada yang memutuskan tidak_lulus, arahkan kembali ke daftar palugada
        if ($currentUserRole === 'palugada' && $statusToSet === 'tidak_lulus') {
            return redirect()->route('admin.palugada.index')->with('success', 'Peserta dinyatakan TIDAK LULUS dan pengumuman telah diperbarui.');
        }

        return back()->with('success', $msg);
    }


    public function updateNilaiDummy(Request $request, $id)
    {
        // Ini method helper untuk testing update nilai rata-rata
        $daftar = Daftar::where('peserta_id', function ($query) use ($id) {
            $query->select('id')->from('peserta')->where('akun_id', $id);
        })->firstOrFail();

        $daftar->update(['rata_rata_nilai' => $request->nilai]);
        return back();
    }

    public function downloadZip($id)
    {
        $user = Akun::with(['peserta.berkas', 'peserta.sertifikats'])->findOrFail($id);
        $peserta = $user->peserta;

        if (!$peserta) {
            return back()->with('error', 'Data peserta tidak ditemukan.');
        }

        $berkas = $peserta->berkas;

        $zipName = 'Dokumen_' . str_replace(' ', '_', $user->nama) . '.zip';
        $zipPath = storage_path('app/public/temp/' . $zipName);

        // Ensure temp directory exists
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {

            // Add Main Berkas
            if ($berkas) {
                $fields = [
                    'foto' => 'Pas_Foto',
                    'rapor1' => 'Rapor_S1',
                    'rapor2' => 'Rapor_S2',
                    'rapor3' => 'Rapor_S3',
                    'rapor4' => 'Rapor_S4',
                    'rapor5' => 'Rapor_S5',
                    'ijazah' => 'Ijazah',
                    'personal_statement' => 'Essay_Motivasi',
                    'study_plan' => 'Study_Plan',
                    'surat_rekomendasi_sekolah' => 'Surat_Rekomendasi_Sekolah'
                ];

                foreach ($fields as $field => $name) {
                    if ($berkas->$field) {
                        $val = $berkas->$field;
                        $decoded = json_decode($val, true);

                        if (is_array($decoded)) {
                            foreach ($decoded as $idx => $path) {
                                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                                    $ext = pathinfo($path, PATHINFO_EXTENSION);
                                    $zip->addFile(storage_path('app/public/' . $path), $name . '_' . ($idx + 1) . '.' . $ext);
                                }
                            }
                        }
                        else {
                            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($val)) {
                                $ext = pathinfo($val, PATHINFO_EXTENSION);
                                $zip->addFile(storage_path('app/public/' . $val), $name . '.' . $ext);
                            }
                        }
                    }
                }
            }

            // Add Sertifikats
            if ($peserta->sertifikats) {
                foreach ($peserta->sertifikats as $index => $sertifikat) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($sertifikat->file)) {
                        $ext = pathinfo($sertifikat->file, PATHINFO_EXTENSION);
                        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $sertifikat->nama);
                        $zip->addFile(storage_path('app/public/' . $sertifikat->file), 'Sertifikat_' . $safeName . '_' . ($index + 1) . '.' . $ext);
                    }
                }
            }

            $zip->close();

            if (file_exists($zipPath)) {
                return response()->download($zipPath)->deleteFileAfterSend(true);
            }
            else {
                return back()->with('error', 'Gagal membuat file ZIP (File kosong).');
            }
        }
        else {
            return back()->with('error', 'Gagal membuka file ZIP.');
        }
    }

    public function exportExcel(\Illuminate\Http\Request $request, \App\Services\GoogleSheetService $sheetService)
    {
        // 1. Prevent timeouts & memory crashes
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');
        \Illuminate\Support\Facades\DB::disableQueryLog();

        // 2. Trigger Google Sheet sync asynchronously in background so download starts immediately
        try {
            if (class_exists(\App\Jobs\SyncGoogleSheetJob::class)) {
                \App\Jobs\SyncGoogleSheetJob::dispatch();
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Async GSheet Sync dispatch failed: ' . $e->getMessage());
        }

        $format = strtolower($request->get('format', 'xlsx'));
        if (!in_array($format, ['xlsx', 'csv'])) {
            $format = 'xlsx';
        }

        // 3. Query all applicants with required relationships
        $pendaftars = \App\Models\Akun::where('role', 'pendaftar')
            ->with([
                'peserta.daftar',
                'peserta.nilais.matpel',
                'peserta.berkas',
                'peserta.sertifikats',
                'peserta.penilaianAkademiks',
                'peserta.penilaianMentors',
            ])
            ->get();

        // 4. Definisikan Mapel Core & Cari Mapel Tambahan yang ada nilainya
        $coreNames = ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Informatika'];
        $coreMatpels = \App\Models\Matpel::whereIn('nama', $coreNames)->get()->sortBy(function ($m) use ($coreNames) {
            return array_search($m->nama, $coreNames);
        });

        $allUsedMatpelIds = \App\Models\Nilai::whereIn('peserta_id', $pendaftars->pluck('peserta.id')->filter())->pluck('matpel_id')->unique();
        $additionalMatpels = \App\Models\Matpel::whereIn('id', $allUsedMatpelIds)
            ->whereNotIn('nama', $coreNames)
            ->get();

        $orderedMatpels = $coreMatpels->concat($additionalMatpels);

        // 5. Definisikan Column Headers Lengkap
        $columns = [
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

        // Detail Nilai Semester 1-5 + Rata-rata
        for ($sem = 1; $sem <= 5; $sem++) {
            foreach ($orderedMatpels as $mp) {
                $columns[] = "S{$sem} - {$mp->nama}";
            }
            $columns[] = "Rata Rata S{$sem}";
        }

        $columns[] = "TOTAL NILAI (S1-S5)";
        $columns[] = "RATA RATA AKADEMIK (S1-S5)";

        // Berkas Utama
        $berkasColumns = [
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

        // Sertifikat Prestasi (Dinamis sesuai data terbanyak peserta)
        $maxSertifikatCount = 0;
        foreach ($pendaftars as $user) {
            $peserta = $user->peserta;
            if ($peserta && $peserta->sertifikats) {
                $count = $peserta->sertifikats->count();
                if ($count > $maxSertifikatCount) {
                    $maxSertifikatCount = $count;
                }
            }
        }
        $maxSertifikatCount = max(3, $maxSertifikatCount);

        $sertifikatColumns = [];
        for ($i = 1; $i <= $maxSertifikatCount; $i++) {
            $sertifikatColumns[] = "SERTIFIKAT {$i} (NAMA)";
            $sertifikatColumns[] = "SERTIFIKAT {$i} (LINK)";
        }

        // Status Ujian & Beasiswa
        $extraColumns = [
            'STATUS SELEKSI UJIAN (CBT)',
            'NOMINAL BEASISWA',
            'STATUS WAWANCARA BOD',
        ];

        $columns = array_merge($columns, $berkasColumns, $sertifikatColumns, $extraColumns);

        // 6. Siapkan Data Rows untuk Export (Rapi & Terstruktur)
        $rows = [];
        $no = 1;
        foreach ($pendaftars as $user) {
            $peserta = $user->peserta;
            $daftar = $peserta?->daftar;
            $berkas = $peserta?->berkas;
            $nilais = $peserta?->nilais ?? collect();

            // Status Seleksi Administrasi
            $statusAdmin = match($daftar?->status) {
                'lulus' => 'LULUS',
                'tidak_lulus' => 'TIDAK LULUS',
                default => 'MENUNGGU VERIFIKASI',
            };

            // Progres Berkas
            $progress = $peserta?->progress ?? 0;
            if (!$peserta) {
                $progressText = 'BELUM ISI BIODATA (0%)';
            } elseif (!$user->email_verified_at) {
                $progressText = 'BELUM VERIFIKASI EMAIL (OTP)';
            } elseif ($progress < 100) {
                $progressText = "BELUM LENGKAP ({$progress}%)";
            } else {
                $progressText = '100% LENGKAP';
            }

            // Tanggal Pendaftaran
            $tanggalDaftar = $daftar?->created_at ? $daftar->created_at->format('d/m/Y H:i') : ($user->created_at ? $user->created_at->format('d/m/Y H:i') : '-');

            // Format Email & NISN
            $cleanEmail = $user->email;
            if (str_starts_with($cleanEmail, 'deleted_')) {
                $parts = explode('_', $cleanEmail, 3);
                $cleanEmail = $parts[2] ?? $cleanEmail;
            }

            $cleanNisn = $peserta?->nisn ?? '-';
            if (str_starts_with($cleanNisn, 'deleted_')) {
                $parts = explode('_', $cleanNisn, 3);
                $cleanNisn = $parts[2] ?? $cleanNisn;
            }

            // Wilayah Jabodetabek
            $kabupaten = $peserta?->kabupaten ?: ($daftar?->kabupaten ?: '-');
            $provinsi = $peserta?->provinsi ?: ($daftar?->provinsi ?: '-');
            $kabLower = strtolower($kabupaten);
            $cities = ['jakarta', 'bogor', 'depok', 'tangerang', 'bekasi'];
            $wilayah = 'DI LUAR JABODETABEK';
            foreach ($cities as $c) {
                if (str_contains($kabLower, $c)) {
                    $wilayah = 'JABODETABEK';
                    break;
                }
            }

            // Program Studi
            $prodi1 = $peserta?->pilihan_prodi ?? '-';
            $prodi2 = $peserta?->pilihan_prodi2 ?? '-';
            if (str_contains($prodi1, ' | ')) {
                $parts = explode(' | ', $prodi1);
                $prodi1 = $parts[0] ?? $prodi1;
                if ($prodi2 === '-' && isset($parts[1])) {
                    $prodi2 = $parts[1];
                }
            }

            // A. Data Identitas
            $row = [
                $no++,
                $statusAdmin,
                $progressText,
                $tanggalDaftar,
                $user->nama,
                $cleanEmail,
                $peserta?->jenis_kelamin ?? ($daftar?->jenis_kelamin ?? '-'),
                $peserta?->no_whatsapp ?: ($daftar?->no_wa ?: '-'),
                $cleanNisn,
                $peserta?->nama_sekolah ?: ($daftar?->asal_sekolah ?: '-'),
                $provinsi,
                $kabupaten,
                $wilayah,
                $daftar?->ttl ?: ($peserta?->tgl_lahir ? date('d/m/Y', strtotime($peserta->tgl_lahir)) : '-'),
                $peserta?->tahun_lulus ?: ($daftar?->tahun_lulus ?: '-'),
                $peserta?->telp_sekolah ?? '-',
                $peserta?->no_guru_bk ?? '-',
                $prodi1,
                $prodi2,
                $daftar?->sumber_informasi ?? '-',
                $daftar?->kode_referral ?? '-',
                $peserta?->penilaianAkademiks->first()?->catatan ?? '-',
                $peserta?->penilaianMentors->first()?->catatan ?? '-',
            ];

            // B. Data Akademik Rinci (S1-S5)
            $grandTotalAcademic = 0;
            $totalMatpelCount = 0;

            for ($sem = 1; $sem <= 5; $sem++) {
                $semSum = 0;
                $semCount = 0;

                foreach ($orderedMatpels as $mp) {
                    $nilaiObj = $nilais->where('semester', $sem)->where('matpel_id', $mp->id)->first();
                    $val = $nilaiObj ? $nilaiObj->nilai : 0;

                    $row[] = $val > 0 ? (float) $val : '-';

                    if ($val > 0) {
                        $semSum += $val;
                        $semCount++;
                        $grandTotalAcademic += $val;
                        $totalMatpelCount++;
                    }
                }
                
                // Rata-rata semester (ambil dari daftar jika ada, atau hitung)
                $avgSemField = "avg_semester_{$sem}";
                if ($daftar && $daftar->$avgSemField > 0) {
                    $row[] = round((float) $daftar->$avgSemField, 2);
                } else {
                    $row[] = $semCount > 0 ? round($semSum / $semCount, 2) : 0;
                }
            }

            // C. Total & Rata-rata Akademik
            $row[] = round($grandTotalAcademic, 2);
            $totalAvg = ($daftar && $daftar->rata_rata_nilai > 0) 
                ? round((float) $daftar->rata_rata_nilai, 2) 
                : ($totalMatpelCount > 0 ? round($grandTotalAcademic / $totalMatpelCount, 2) : 0);
            $row[] = $totalAvg;

            // D. Dokumen Berkas & Link
            $row[] = ($berkas && $berkas->foto) ? (str_starts_with($berkas->foto, 'http') ? $berkas->foto : url('storage/' . $berkas->foto)) : '-';
            for ($s = 1; $s <= 6; $s++) {
                $field = "rapor{$s}";
                $row[] = ($berkas && $berkas->$field) ? (str_starts_with($berkas->$field, 'http') ? $berkas->$field : url('storage/' . $berkas->$field)) : '-';
            }
            $row[] = ($berkas && $berkas->ijazah) ? (str_starts_with($berkas->ijazah, 'http') ? $berkas->ijazah : url('storage/' . $berkas->ijazah)) : '-';
            $row[] = ($berkas && $berkas->personal_statement) ? (str_starts_with($berkas->personal_statement, 'http') ? $berkas->personal_statement : url('storage/' . $berkas->personal_statement)) : '-';
            $row[] = ($berkas && $berkas->surat_rekom) ? (str_starts_with($berkas->surat_rekom, 'http') ? $berkas->surat_rekom : url('storage/' . $berkas->surat_rekom)) : '-';
            $row[] = ($berkas && $berkas->study_plan) ? (str_starts_with($berkas->study_plan, 'http') ? $berkas->study_plan : url('storage/' . $berkas->study_plan)) : '-';

            // Khusus DKV: Surat Buta Warna & Portofolio
            $isPesertaDkv = str_contains($prodi1, 'DKV') || str_contains($prodi2, 'DKV');
            if ($isPesertaDkv) {
                $row[] = ($berkas && $berkas->surat_buta_warna) ? (str_starts_with($berkas->surat_buta_warna, 'http') ? $berkas->surat_buta_warna : url('storage/' . $berkas->surat_buta_warna)) : 'BELUM UNGGAH';
                $row[] = ($berkas && $berkas->portfolio) ? (str_starts_with($berkas->portfolio, 'http') ? $berkas->portfolio : url('storage/' . $berkas->portfolio)) : 'BELUM UNGGAH';
            } else {
                $row[] = 'N/A';
                $row[] = 'N/A';
            }

            // Video Motivasi & Sosmed
            $row[] = $berkas?->motivasi_video ?: '-';
            $row[] = $berkas?->motivasi_video_tiktok ?: '-';
            $row[] = $peserta?->link_ig ?: '-';
            $row[] = $peserta?->link_tiktok ?: '-';
            $row[] = $peserta?->link_twibbon ?: '-';
            $row[] = ($berkas && $berkas->bukti_follow_ig_beasiswamncu) ? url('storage/' . $berkas->bukti_follow_ig_beasiswamncu) : '-';
            $row[] = ($berkas && $berkas->bukti_follow_ig_mncu) ? url('storage/' . $berkas->bukti_follow_ig_mncu) : '-';
            $row[] = ($berkas && $berkas->bukti_follow_tiktok_beasiswamncu) ? url('storage/' . $berkas->bukti_follow_tiktok_beasiswamncu) : '-';
            $row[] = ($berkas && $berkas->bukti_follow_tiktok_mncu) ? url('storage/' . $berkas->bukti_follow_tiktok_mncu) : '-';
            $row[] = ($berkas && $berkas->bukti_follow_ig_mncunation) ? url('storage/' . $berkas->bukti_follow_ig_mncunation) : '-';
            $row[] = ($berkas && $berkas->bukti_follow_ig_thearetegen) ? url('storage/' . $berkas->bukti_follow_ig_thearetegen) : '-';

            // Sertifikat Prestasi
            $sertifikats = $peserta?->sertifikats ?? collect();
            for ($i = 0; $i < $maxSertifikatCount; $i++) {
                $sertifikatObj = $sertifikats->get($i);
                if ($sertifikatObj) {
                    $row[] = $sertifikatObj->nama ?? '-';
                    $row[] = $sertifikatObj->file ? (str_starts_with($sertifikatObj->file, 'http') ? $sertifikatObj->file : url('storage/' . $sertifikatObj->file)) : '-';
                } else {
                    $row[] = '-';
                    $row[] = '-';
                }
            }

            // Status Ujian & Beasiswa
            $row[] = match($peserta?->status_seleksi_ujian) {
                'lulus' => 'LULUS',
                'tidak_lulus' => 'TIDAK LULUS',
                default => 'MENUNGGU UJIAN / BELUM',
            };
            $row[] = $daftar?->nominal_beasiswa ? 'Rp ' . number_format($daftar->nominal_beasiswa, 0, ',', '.') : '-';
            $row[] = $daftar?->status_wawancara_bod ?: '-';

            $rows[] = $row;
        }

        // ==========================================
        // OPSI 1: FORMAT EXCEL ASLI (.XLSX) - RAPI, BERSIH & AUTO-FILTER
        // ==========================================
        if ($format === 'xlsx') {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Seleksi Administrasi');

            // Set Header Columns
            foreach ($columns as $colIndex => $colName) {
                $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $sheet->setCellValueExplicit("{$colStr}1", $colName, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }

            // Set Data Rows
            $rowIndex = 2;
            foreach ($rows as $rowData) {
                foreach ($rowData as $colIndex => $val) {
                    $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                    $cellCoordinate = "{$colStr}{$rowIndex}";
                    $colName = $columns[$colIndex] ?? '';

                    // Pertahankan leading zero untuk No HP, NISN, Guru BK, Referral
                    if (in_array($colName, ['Nomor HP / WhatsApp', 'NISN', 'Telepon Sekolah', 'No HP Guru BK', 'Kode Referral'])) {
                        $sheet->setCellValueExplicit($cellCoordinate, (string) $val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    } elseif (is_numeric($val) && !str_starts_with((string)$val, '0')) {
                        $sheet->setCellValue($cellCoordinate, $val);
                    } else {
                        $sheet->setCellValueExplicit($cellCoordinate, (string) $val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }

                    // Buat link yang bisa diklik langsung di Excel
                    if (is_string($val) && (str_starts_with($val, 'http://') || str_starts_with($val, 'https://'))) {
                        $sheet->getCell($cellCoordinate)->getHyperlink()->setUrl($val);
                        $sheet->getStyle($cellCoordinate)->getFont()->getColor()->setRGB('0870B8');
                        $sheet->getStyle($cellCoordinate)->getFont()->setUnderline(true);
                    }
                }
                $rowIndex++;
            }

            $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($columns));
            $lastRow = max(1, $rowIndex - 1);

            // Styling Header: Navy Blue MNC, Teks Putih Tebal, Row Height 28pt
            $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 10,
                    'name' => 'Calibri',
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0B1120'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
            $sheet->getRowDimension(1)->setRowHeight(28);

            // Styling Data Rows: Zebra striping, borders, font bersih
            if ($lastRow >= 2) {
                $sheet->getStyle("A2:{$lastColumn}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'CBD5E1'],
                        ],
                    ],
                    'font' => [
                        'size' => 10,
                        'name' => 'Calibri',
                    ],
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Zebra striping untuk mempermudah membaca data 1000+ peserta
                for ($r = 2; $r <= $lastRow; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(20);
                    if ($r % 2 === 1) {
                        $sheet->getStyle("A{$r}:{$lastColumn}{$r}")->getFill()->applyFromArray([
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F8FAFC'],
                        ]);
                    }
                }
            }

            // Freeze Panes (Header baris 1 tetap beku di atas saat scroll 1000 orang)
            $sheet->freezePane('A2');

            // Aktifkan AutoFilter Excel di semua kolom
            $sheet->setAutoFilter("A1:{$lastColumn}{$lastRow}");

            // Auto-size lebar kolom agar teks tidak terpotong
            for ($c = 1; $c <= count($columns); $c++) {
                $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $sheet->getColumnDimension($colStr)->setAutoSize(true);
            }

            $fileName = 'Database_Seleksi_Administrasi_' . date('Y-m-d_H-i') . '.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), 'export_admin_xlsx_');
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($tempFile);

            $fileSize = filesize($tempFile);

            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Length' => (string) $fileSize,
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            return response()->download($tempFile, $fileName, $headers)->deleteFileAfterSend(true);
        }

        // ==========================================
        // OPSI 2: FORMAT CSV (.CSV) DENGAN UTF-8 BOM
        // ==========================================
        $fileName = 'Database_Seleksi_Administrasi_' . date('Y-m-d_H-i') . '.csv';
        $tempFile = tempnam(sys_get_temp_dir(), 'export_seleksi_admin_');
        $file = fopen($tempFile, 'w+');

        // Add UTF-8 BOM so Microsoft Excel renders UTF-8 characters seamlessly
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        $delimiter = $request->get('delimiter', ';');
        if (!in_array($delimiter, [';', ','])) {
            $delimiter = ';';
        }

        fputcsv($file, $columns, $delimiter);

        foreach ($rows as $rowData) {
            fputcsv($file, $rowData, $delimiter);
        }

        fclose($file);

        $fileSize = filesize($tempFile);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Length' => (string) $fileSize,
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->download($tempFile, $fileName, $headers)->deleteFileAfterSend(true);
    }

    public function exportExcelWawancara(\App\Services\GoogleSheetService $sheetService)
    {
        // Sync to Google Sheet as requested
        try {
            $sheetService->syncWawancara();
        } catch(\Exception $e) {
            \Illuminate\Support\Facades\Log::error('GSheet Sync Wawancara failed during export: '.$e->getMessage());
        }

        $fileName = 'Database_Hasil_Wawancara_' . date('Y-m-d_H-i') . '.csv';
        
        $pendaftars = \App\Models\Akun::where('role', 'pendaftar')
            ->whereHas('peserta.daftar', function ($q) {
                $q->where('status', 'lulus');
            })
            ->whereHas('peserta', function ($q) {
                $q->where('status_seleksi_ujian', 'lulus');
            })
            ->with(['peserta.daftar', 'peserta.penilaianAkademiks', 'peserta.penilaianMentors'])
            ->get();

        $columns = [
            'No', 'Nama Lengkap', 'Asal Sekolah', 'Email', 'No HP', 'Minat Prodi 1', 'Minat Prodi 2', 
            'Total Nilai Akhir', 'Wawancara Motivasi', 'Wawancara Prestasi', 'Wawancara Karakter', 'Wawancara Kontribusi', 'Wawancara Komunikasi', 
            'Rekomendasi Akhir', 'Rekomendasi Beasiswa', 'Catatan Dosen', 'Catatan Mentor', 'Catatan Rekomendasi', 'Status Wawancara'
        ];

        $headers = [
            "Content-type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($pendaftars, $columns) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $columns, ';');

            foreach ($pendaftars as $index => $user) {
                $peserta = $user->peserta;
                $daftar = $peserta?->daftar;
                $penilaian = $peserta?->penilaianAkademiks->first();

                // Format Email
                $email = $user->email;
                if (strpos($email, 'deleted_') === 0) {
                    $parts = explode('_', $email, 3);
                    $email = $parts[2] ?? $email;
                }

                $row = [
                    $index + 1,
                    $user->nama,
                    $daftar?->asal_sekolah ?? $peserta?->nama_sekolah ?? '-',
                    $email,
                    $peserta?->no_whatsapp ?? '-',
                    explode(' | ', $peserta?->pilihan_prodi ?? '')[0] ?? '-',
                    explode(' | ', $peserta?->pilihan_prodi ?? '')[1] ?? '-',
                    $penilaian ? $penilaian->total_akhir : '-',
                    $penilaian ? $penilaian->wawancara_motivasi : '-',
                    $penilaian ? $penilaian->wawancara_prestasi : '-',
                    $penilaian ? $penilaian->wawancara_karakter : '-',
                    $penilaian ? $penilaian->wawancara_kontribusi : '-',
                    $penilaian ? $penilaian->wawancara_komunikasi : '-',
                    $penilaian ? $penilaian->rekomendasi_akhir : '-',
                    $penilaian ? $penilaian->rekomendasi_beasiswa : '-',
                    $penilaian ? $penilaian->catatan : '-',
                    $peserta?->penilaianMentors->first()?->catatan ?? '-',
                    $penilaian ? $penilaian->catatan_rekomendasi_beasiswa : '-',
                    $penilaian ? 'Sudah Dinilai' : 'Belum Dinilai'
                ];
                
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function syncWawancaraSheet(\App\Services\GoogleSheetService $sheetService)
    {
        $user = auth()->user();
        if ($user->email !== 'noval.adi@mncu.ac.id' && !in_array($user->role, ['admin', 'palugada'])) {
            return response()->json(['success' => false, 'error' => 'Akses ditolak.'], 403);
        }

        try {
            $sheetService->syncWawancara();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('GSheet Manual Sync Wawancara failed: '.$e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function indexWawancaraBod()
    {
        $user = auth()->user();
        $isAuthorized = in_array($user->role, ['admin', 'god']) || in_array($user->email, ['dendi.pratama@mncu.ac.id', 'muhammad.rezki@mncu.ac.id', 'noval.adi@mncu.ac.id']);

        if (!$isAuthorized) {
            return abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $pesertas = \App\Models\Akun::where('role', 'pendaftar')
            ->whereHas('peserta.daftar')
            ->where(function ($query) {
                $query->whereHas('peserta.daftar', function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('nominal_beasiswa', 'like', '%100%')
                            ->orWhere(function ($sub2) {
                                $sub2->whereNotNull('status_wawancara_bod')
                                     ->where('status_wawancara_bod', '!=', '');
                            });
                    });
                })->orWhereHas('peserta.penilaianAkademiks', function ($q) {
                    $q->where('rekomendasi_beasiswa', 'like', '%100%');
                });
            })->with(['peserta.daftar', 'peserta.penilaianAkademiks'])->get();

        return view('admin.wawancara_bod.index', compact('pesertas'));
    }

    public function updateWawancaraBod(Request $request, $id)
    {
        $user = auth()->user();
        $isAuthorized = in_array($user->role, ['admin', 'god']) || in_array($user->email, ['dendi.pratama@mncu.ac.id', 'muhammad.rezki@mncu.ac.id', 'noval.adi@mncu.ac.id']);

        if (!$isAuthorized) {
            return abort(403, 'Anda tidak memiliki akses.');
        }

        $request->validate([
            'status_wawancara_bod' => 'nullable|in:Layak (100%),Layak (75%),Layak (50%),Layak (25%),Layak (100% Reguler),Layak (75% Reguler),Layak (50% Reguler),Layak (25% Reguler),Layak (100% Eksekutif),Layak (75% Eksekutif),Layak (50% Eksekutif),Layak (25% Eksekutif),Tidak Layak',
            'rekomendasi_prodi_1' => 'nullable|string|max:255',
        ]);

        $daftar = \App\Models\Daftar::findOrFail($id);
        $rekomendasiBeasiswa = $daftar->peserta->penilaianAkademiks->first()?->rekomendasi_beasiswa ?? '';
        
        $has100Or75 = str_contains($daftar->nominal_beasiswa ?? '', '100') || 
                      str_contains($daftar->nominal_beasiswa ?? '', '75') || 
                      str_contains($rekomendasiBeasiswa, '100') || 
                      str_contains($rekomendasiBeasiswa, '75');

        if (!$has100Or75 && empty($daftar->status_wawancara_bod)) {
            return back()->with('error', 'Hanya peserta dengan Beasiswa 100% atau 75% (atau rekomendasi) yang dapat diubah.');
        }

        // Auto-map status_wawancara_bod to nominal_beasiswa and status
        $nominal = null;
        $status = null;
        $statusBod = $request->status_wawancara_bod;

        if ($statusBod) {
            if (str_contains($statusBod, '100%')) {
                $nominal = '100%';
            } elseif (str_contains($statusBod, '75%')) {
                $nominal = '75%';
            } elseif (str_contains($statusBod, '50%')) {
                $nominal = '50%';
            } elseif (str_contains($statusBod, '25%')) {
                $nominal = '25%';
            } elseif ($statusBod === 'Tidak Layak') {
                $nominal = 'Mandiri';
            }

            if (str_starts_with($statusBod, 'Layak')) {
                $status = 'lulus';
            } elseif ($statusBod === 'Tidak Layak') {
                $status = 'tidak_lulus';
            }
        }

        $updateData = [
            'status_wawancara_bod' => $statusBod,
        ];

        if ($nominal !== null) {
            $updateData['nominal_beasiswa'] = $nominal;
        }
        if ($status !== null) {
            $updateData['status'] = $status;
        }

        $daftar->update($updateData);

        // Update Rekomendasi Prodi 1 di Penilaian Akademik
        $penilaian = $daftar->peserta->penilaianAkademiks()->first();
        if (!$penilaian) {
            $penilaian = new \App\Models\PenilaianAkademik();
            $penilaian->peserta_id = $daftar->peserta_id;
            $penilaian->penilai_id = auth()->id();
        }
        $penilaian->rekomendasi_prodi_1 = $request->rekomendasi_prodi_1;
        $penilaian->save();

        return back()->with('success', 'Status Wawancara BoD dan keputusan beasiswa berhasil diperbarui.');
    }

    public function downloadWawancaraBodPhotos()
    {
        $user = auth()->user();
        $isAuthorized = in_array($user->role, ['admin', 'god']) || in_array($user->email, ['dendi.pratama@mncu.ac.id', 'muhammad.rezki@mncu.ac.id', 'noval.adi@mncu.ac.id']);

        if (!$isAuthorized) {
            return abort(403, 'Anda tidak memiliki akses.');
        }

        $pesertas = \App\Models\Akun::where('role', 'pendaftar')
            ->whereHas('peserta.daftar')
            ->where(function ($query) {
                $query->whereHas('peserta.daftar', function ($q) {
                    $q->where(function ($sub) {
                        $sub->where(function ($subNominal) {
                            $subNominal->where('nominal_beasiswa', 'like', '%100%')
                                       ->orWhere('nominal_beasiswa', 'like', '%75%');
                        })
                        ->orWhere(function ($sub2) {
                            $sub2->whereNotNull('status_wawancara_bod')
                                 ->where('status_wawancara_bod', '!=', '');
                        });
                    });
                })->orWhereHas('peserta.penilaianAkademiks', function ($q) {
                    $q->where('rekomendasi_beasiswa', 'like', '%100%')
                      ->orWhere('rekomendasi_beasiswa', 'like', '%75%');
                });
            })->with(['peserta.berkas'])->get();

        $zipName = 'Foto_Kandidat_Wawancara_BoD_' . date('Ymd_His') . '.zip';
        $zipPath = storage_path('app/public/temp/' . $zipName);

        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $count = 0;
            foreach ($pesertas as $index => $akun) {
                $berkas = $akun->peserta?->berkas;
                if ($berkas && $berkas->foto) {
                    $val = $berkas->foto;
                    $decoded = json_decode($val, true);
                    $path = is_array($decoded) ? ($decoded[0] ?? null) : $val;

                    if ($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                        $ext = pathinfo($path, PATHINFO_EXTENSION);
                        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $akun->nama);
                        $filename = ($index + 1) . '_' . $safeName . '.' . $ext;
                        $zip->addFile(storage_path('app/public/' . $path), $filename);
                        $count++;
                    }
                }
            }
            $zip->close();

            if ($count > 0 && file_exists($zipPath)) {
                return response()->download($zipPath)->deleteFileAfterSend(true);
            }

            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
            return back()->with('error', 'Tidak ada foto kandidat yang ditemukan atau berhasil diekspor.');
        }

        return back()->with('error', 'Gagal membuat file ZIP.');
    }
}

@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-500/10 text-blue-700 border border-blue-200">
                    Inspeksi & Berkas
                </span>
                <span class="text-xs text-slate-400 font-bold">•</span>
                <span class="text-xs text-slate-500 font-semibold">MFLS Batch 3</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-600/20 shrink-0">
                    <span class="iconify text-xl" data-icon="solar:folder-with-files-bold-duotone"></span>
                </span>
                <span>Berkas & Dokumen Pendaftar</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                Inspeksi berkas pendaftar (PDF, scan raport, ijazah, portofolio & sertifikat) secara instan di browser tanpa perlu mengunduh ZIP satu per satu.
            </p>
        </div>

        <div class="flex items-center flex-wrap gap-2 shrink-0">
            <a href="{{ route('admin.berkas.index') }}" 
               class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 hover:border-slate-300 text-slate-700 font-bold text-xs shadow-xs hover:shadow-sm transition-all flex items-center gap-2">
                <span class="iconify text-base text-slate-500" data-icon="solar:restart-bold"></span>
                <span>Refresh</span>
            </a>
            <a href="{{ route('admin.pendaftar.index') }}" 
               class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white font-bold text-xs shadow-xs hover:shadow-sm transition-all flex items-center gap-2">
                <span class="iconify text-base" data-icon="solar:users-group-rounded-bold"></span>
                <span>Seleksi Administrasi</span>
            </a>
            <a href="{{ route('admin.video_motivasi.index') }}" 
               class="px-4 py-2.5 rounded-xl bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 text-indigo-700 font-bold text-xs shadow-xs hover:shadow-sm transition-all flex items-center gap-2">
                <span class="iconify text-base" data-icon="solar:videocamera-record-bold"></span>
                <span>Video Motivasi</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3.5 sm:gap-4">
        <!-- Total Pendaftar -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Pendaftar</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <span class="iconify text-base" data-icon="solar:users-group-two-rounded-bold"></span>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-black text-slate-900">{{ number_format($totalPendaftar) }}</span>
                <span class="text-xs text-slate-400 font-semibold">peserta</span>
            </div>
            <p class="text-[10px] text-slate-500 font-medium">Seluruh pelamar terdaftar</p>
        </div>

        <!-- Berkas Lengkap -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-emerald-200/80 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Berkas Lengkap</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="iconify text-base" data-icon="solar:check-circle-bold"></span>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-black text-emerald-700">{{ number_format($totalLengkap) }}</span>
                <span class="text-xs text-emerald-600 font-semibold">
                    ({{ $totalPendaftar > 0 ? round(($totalLengkap / $totalPendaftar) * 100) : 0 }}%)
                </span>
            </div>
            <p class="text-[10px] text-slate-500 font-medium">Rapor 1-5, ijazah, esai lengkap</p>
        </div>

        <!-- Berkas Belum Lengkap -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-amber-200/80 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Belum Lengkap</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="iconify text-base" data-icon="solar:clock-circle-bold"></span>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-black text-amber-700">{{ number_format($totalBelumLengkap) }}</span>
                <span class="text-xs text-amber-600 font-semibold">
                    ({{ $totalPendaftar > 0 ? round(($totalBelumLengkap / $totalPendaftar) * 100) : 0 }}%)
                </span>
            </div>
            <p class="text-[10px] text-slate-500 font-medium">Masih ada berkas yang kosong</p>
        </div>

        <!-- Ada Sertifikat -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-indigo-200/80 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Ada Sertifikat</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <span class="iconify text-base" data-icon="solar:medal-star-bold"></span>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-black text-indigo-700">{{ number_format($totalAdaSertifikat) }}</span>
                <span class="text-xs text-indigo-600 font-semibold">peserta</span>
            </div>
            <p class="text-[10px] text-slate-500 font-medium">Melampirkan prestasi/sertifikat</p>
        </div>

        <!-- Khusus DKV -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-purple-200/80 shadow-xs space-y-2 col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-purple-600">Khusus DKV</span>
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <span class="iconify text-base" data-icon="solar:pallete-2-bold"></span>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-black text-purple-700">{{ number_format($totalDkvLengkap) }}</span>
                <span class="text-xs text-purple-500 font-semibold">/ {{ number_format($totalDkv) }} DKV</span>
            </div>
            <p class="text-[10px] text-slate-500 font-medium">Buta warna & Portofolio lengkap</p>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
        <form method="GET" action="{{ route('admin.berkas.index') }}" id="filter-berkas-form" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                <!-- Search Input -->
                <div class="sm:col-span-2 lg:col-span-4 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <span class="iconify text-lg" data-icon="solar:magnifer-linear"></span>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama, email, NISN, sekolah, no WA..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-xs text-slate-800 placeholder-slate-400 transition-all">
                </div>

                <!-- Program Studi Filter -->
                <div class="lg:col-span-3">
                    <select name="prodi" onchange="this.form.submit()"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-xs text-slate-800 transition-all">
                        <option value="">Semua Program Studi</option>
                        @foreach($prodiList as $prodi)
                            <option value="{{ $prodi }}" {{ request('prodi') == $prodi ? 'selected' : '' }}>
                                {{ $prodi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kelengkapan Filter -->
                <div class="lg:col-span-3">
                    <select name="status" onchange="this.form.submit()"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-xs text-slate-800 transition-all font-semibold">
                        <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>Semua Status Berkas</option>
                        <option value="lengkap" {{ request('status') == 'lengkap' ? 'selected' : '' }}>✓ Berkas 100% Lengkap</option>
                        <option value="belum_lengkap" {{ request('status') == 'belum_lengkap' ? 'selected' : '' }}>⏳ Berkas Belum Lengkap</option>
                        <option value="ada_sertifikat" {{ request('status') == 'ada_sertifikat' ? 'selected' : '' }}>★ Ada Sertifikat Prestasi</option>
                        <option value="tanpa_sertifikat" {{ request('status') == 'tanpa_sertifikat' ? 'selected' : '' }}>Tanpa Sertifikat</option>
                        <option value="dkv_khusus" {{ request('status') == 'dkv_khusus' ? 'selected' : '' }}>🎨 Semua Pendaftar DKV</option>
                        <option value="dkv_lengkap" {{ request('status') == 'dkv_lengkap' ? 'selected' : '' }}>🎨 DKV: Buta Warna & Portofolio Lengkap</option>
                        <option value="dkv_belum_lengkap" {{ request('status') == 'dkv_belum_lengkap' ? 'selected' : '' }}>⚠️ DKV: Belum Lengkap Khusus</option>
                    </select>
                </div>

                <!-- Fokus Dokumen Filter -->
                <div class="lg:col-span-2">
                    <select name="dokumen" onchange="this.form.submit()"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-xs text-slate-800 transition-all">
                        <option value="">Fokus Dokumen: Bebas</option>
                        <option value="foto" {{ request('dokumen') == 'foto' ? 'selected' : '' }}>Ada Foto 3x4</option>
                        <option value="rapor" {{ request('dokumen') == 'rapor' ? 'selected' : '' }}>Ada Scan Rapor</option>
                        <option value="ijazah" {{ request('dokumen') == 'ijazah' ? 'selected' : '' }}>Ada Ijazah/SKL</option>
                        <option value="personal_statement" {{ request('dokumen') == 'personal_statement' ? 'selected' : '' }}>Ada Personal Statement</option>
                        <option value="study_plan" {{ request('dokumen') == 'study_plan' ? 'selected' : '' }}>Ada Study Plan</option>
                        <option value="rekomendasi" {{ request('dokumen') == 'rekomendasi' ? 'selected' : '' }}>Ada Rekomendasi Sekolah</option>
                        <option value="buta_warna" {{ request('dokumen') == 'buta_warna' ? 'selected' : '' }}>Ada Surat Buta Warna</option>
                        <option value="portfolio" {{ request('dokumen') == 'portfolio' ? 'selected' : '' }}>Ada Portofolio DKV</option>
                        <option value="sertifikat" {{ request('dokumen') == 'sertifikat' ? 'selected' : '' }}>Ada Sertifikat</option>
                    </select>
                </div>
            </div>

            <!-- Second Row Filters: Sort, Per-page, and Reset -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100 text-xs">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Urutan:</span>
                    <select name="sort" onchange="this.form.submit()"
                            class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs text-slate-700 bg-slate-50 focus:bg-white font-medium">
                        <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Terbaru Daftar</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama Daftar</option>
                        <option value="nama_asc" {{ request('sort') == 'nama_asc' ? 'selected' : '' }}>Nama A - Z</option>
                        <option value="nama_desc" {{ request('sort') == 'nama_desc' ? 'selected' : '' }}>Nama Z - A</option>
                    </select>

                    <span class="text-slate-300">|</span>

                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Per Halaman:</span>
                    <select name="per_page" onchange="this.form.submit()"
                            class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs text-slate-700 bg-slate-50 focus:bg-white font-medium">
                        <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>

                    @if(request()->anyFilled(['search', 'prodi', 'status', 'dokumen', 'sort']))
                        <a href="{{ route('admin.berkas.index') }}"
                           class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs transition-all flex items-center gap-1">
                            <span class="iconify text-sm" data-icon="solar:close-circle-bold"></span>
                            <span>Reset Filter</span>
                        </a>
                    @endif
                </div>

                <!-- View Switcher -->
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl">
                    <button type="button" id="btn-view-cards" onclick="switchBerkasView('cards')"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 bg-white text-slate-800 shadow-xs">
                        <span class="iconify text-sm" data-icon="solar:widget-5-bold"></span>
                        <span>Kartu Berkas</span>
                    </button>
                    <button type="button" id="btn-view-matrix" onclick="switchBerkasView('matrix')"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 text-slate-500 hover:text-slate-800">
                        <span class="iconify text-sm" data-icon="solar:list-bold"></span>
                        <span>Matriks Tabel</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Pendaftar List Container -->
    @if($pendaftars->isEmpty())
        <div class="p-12 text-center bg-white rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-50 text-slate-300 flex items-center justify-center">
                <span class="iconify text-3xl" data-icon="solar:folder-error-bold-duotone"></span>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Tidak ada data pendaftar yang cocok</h3>
                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter status kelengkapan berkas.</p>
            </div>
            <a href="{{ route('admin.berkas.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition-all">
                <span class="iconify text-base" data-icon="solar:restart-bold"></span>
                <span>Reset Semua Filter</span>
            </a>
        </div>
    @else
        <!-- Helper function for resolving files array & type -->
        @php
            $parseDocFiles = function($rawValue) {
                if (empty($rawValue)) return [];
                $decoded = json_decode($rawValue, true);
                $files = is_array($decoded) ? $decoded : [$rawValue];
                $result = [];
                foreach ($files as $file) {
                    if (empty($file)) continue;
                    $isExternal = str_starts_with($file, 'http://') || str_starts_with($file, 'https://');
                    $lower = strtolower($file);
                    $isPdf = str_ends_with($lower, '.pdf');
                    $isImage = str_ends_with($lower, '.jpg') || str_ends_with($lower, '.jpeg') || str_ends_with($lower, '.png') || str_ends_with($lower, '.webp');
                    $url = $isExternal ? $file : asset('storage/' . $file);
                    $result[] = [
                        'raw' => $file,
                        'url' => $url,
                        'is_external' => $isExternal,
                        'is_pdf' => $isPdf,
                        'is_image' => $isImage,
                        'type' => $isPdf ? 'pdf' : ($isImage ? 'image' : ($isExternal ? 'external' : 'file'))
                    ];
                }
                return $result;
            };
        @endphp

        <!-- 1. CARDS VIEW -->
        <div id="berkas-cards-view" class="space-y-4">
            @foreach($pendaftars as $user)
                @php
                    $peserta = $user->peserta;
                    $berkas = $peserta->berkas ?? null;
                    $daftar = $peserta->daftar ?? null;
                    $tahunLulus = (int) ($daftar->tahun_lulus ?? $peserta->tahun_lulus ?? 2027);
                    $isDkv = str_contains($peserta->pilihan_prodi ?? '', 'DKV') || str_contains($peserta->pilihan_prodi ?? '', 'Desain Komunikasi Visual');

                    // Compute document inventory
                    $docFoto = $parseDocFiles($berkas->foto ?? null);
                    $docRapor1 = $parseDocFiles($berkas->rapor1 ?? null);
                    $docRapor2 = $parseDocFiles($berkas->rapor2 ?? null);
                    $docRapor3 = $parseDocFiles($berkas->rapor3 ?? null);
                    $docRapor4 = $parseDocFiles($berkas->rapor4 ?? null);
                    $docRapor5 = $parseDocFiles($berkas->rapor5 ?? null);
                    $docRapor6 = $parseDocFiles($berkas->rapor6 ?? null);
                    $docIjazah = $parseDocFiles($berkas->ijazah ?? null);
                    $docPersonalStatement = $parseDocFiles($berkas->personal_statement ?? null);
                    $docStudyPlan = $parseDocFiles($berkas->study_plan ?? null);
                    $docRekomendasi = $parseDocFiles($berkas->surat_rekomendasi_sekolah ?? null);
                    $docButaWarna = $parseDocFiles($berkas->surat_buta_warna ?? null);
                    $docPortfolio = $parseDocFiles($berkas->portfolio ?? null);

                    // Sertifikats
                    $sertifikatsList = [];
                    if ($peserta && $peserta->sertifikats) {
                        foreach ($peserta->sertifikats as $sIdx => $ser) {
                            $serFiles = $parseDocFiles($ser->file);
                            foreach ($serFiles as $sf) {
                                $sertifikatsList[] = [
                                    'title' => $ser->nama ?? ('Sertifikat ' . ($sIdx + 1)),
                                    'tahun' => $ser->tahun ?? null,
                                    'file' => $sf
                                ];
                            }
                        }
                    }

                    // Mandatory checklist score
                    $mandatoryCount = 8; // foto, r1..r5, ijazah, ps, sp
                    $mandatoryFilled = 0;
                    if (!empty($docFoto)) $mandatoryFilled++;
                    if (!empty($docRapor1)) $mandatoryFilled++;
                    if (!empty($docRapor2)) $mandatoryFilled++;
                    if (!empty($docRapor3)) $mandatoryFilled++;
                    if (!empty($docRapor4)) $mandatoryFilled++;
                    if (!empty($docRapor5)) $mandatoryFilled++;
                    if (!empty($docIjazah)) $mandatoryFilled++;
                    if (!empty($docPersonalStatement)) $mandatoryFilled++;
                    if (!empty($docStudyPlan)) $mandatoryFilled++;
                    $mandatoryCount = 9; // base: foto + 5 rapor + ijazah + ps + sp = 9

                    if ($tahunLulus < 2027) {
                        $mandatoryCount++;
                        if (!empty($docRapor6)) $mandatoryFilled++;
                    } else {
                        $mandatoryCount++;
                        if (!empty($docRekomendasi)) $mandatoryFilled++;
                    }

                    if ($isDkv) {
                        $mandatoryCount += 2;
                        if (!empty($docButaWarna)) $mandatoryFilled++;
                        if (!empty($docPortfolio)) $mandatoryFilled++;
                    }

                    $percentLengkap = $mandatoryCount > 0 ? round(($mandatoryFilled / $mandatoryCount) * 100) : 0;
                    $isLengkap100 = $mandatoryFilled >= $mandatoryCount;

                    // Collect all valid files for JavaScript modal inspection
                    $candidateDocumentsPayload = [
                        'candidate_id' => $user->id,
                        'nama' => $user->nama,
                        'email' => $user->email,
                        'nisn' => $peserta->nisn ?? '-',
                        'sekolah' => $daftar->asal_sekolah ?? $peserta->nama_sekolah ?? '-',
                        'prodi' => $peserta->pilihan_prodi ?? '-',
                        'tahun_lulus' => $tahunLulus,
                        'status' => $daftar->status ?? 'menunggu',
                        'detail_url' => route('admin.pendaftar.show', $user->id),
                        'docs' => []
                    ];

                    $addDocToPayload = function($key, $label, $badge, $filesList, $isMandatory = false) use (&$candidateDocumentsPayload) {
                        if (!empty($filesList)) {
                            $candidateDocumentsPayload['docs'][] = [
                                'key' => $key,
                                'label' => $label,
                                'badge' => $badge,
                                'is_mandatory' => $isMandatory,
                                'files' => $filesList
                            ];
                        }
                    };

                    $addDocToPayload('foto', 'Pas Foto 3x4', 'Foto', $docFoto, true);
                    $addDocToPayload('rapor1', 'Raport Semester 1', 'Rapor S1', $docRapor1, true);
                    $addDocToPayload('rapor2', 'Raport Semester 2', 'Rapor S2', $docRapor2, true);
                    $addDocToPayload('rapor3', 'Raport Semester 3', 'Rapor S3', $docRapor3, true);
                    $addDocToPayload('rapor4', 'Raport Semester 4', 'Rapor S4', $docRapor4, true);
                    $addDocToPayload('rapor5', 'Raport Semester 5', 'Rapor S5', $docRapor5, true);
                    if ($tahunLulus < 2027) {
                        $addDocToPayload('rapor6', 'Raport Semester 6', 'Rapor S6', $docRapor6, true);
                    }
                    $addDocToPayload('ijazah', 'Ijazah / SKL / Kartu Pelajar', 'Ijazah', $docIjazah, true);
                    $addDocToPayload('personal_statement', 'Personal Statement', 'Esai PS', $docPersonalStatement, true);
                    $addDocToPayload('study_plan', 'Study Plan', 'Study Plan', $docStudyPlan, true);
                    if ($tahunLulus >= 2027) {
                        $addDocToPayload('rekomendasi', 'Surat Rekomendasi Sekolah', 'Rekomendasi', $docRekomendasi, true);
                    }
                    if ($isDkv) {
                        $addDocToPayload('buta_warna', 'Surat Keterangan Bebas Buta Warna', 'Buta Warna', $docButaWarna, true);
                        $addDocToPayload('portfolio', 'Portofolio Karya DKV', 'Portofolio', $docPortfolio, true);
                    }

                    // Add certificates
                    if (!empty($sertifikatsList)) {
                        $serFilesPayload = array_map(function($item) {
                            $f = $item['file'];
                            $f['title'] = $item['title'];
                            return $f;
                        }, $sertifikatsList);
                        $addDocToPayload('sertifikat', 'Sertifikat Prestasi (' . count($sertifikatsList) . ')', 'Sertifikat', $serFilesPayload, false);
                    }

                    // Add medsos proofs
                    $docIgMfls = $parseDocFiles($berkas->bukti_follow_ig_beasiswamncu ?? null);
                    $docIgMncu = $parseDocFiles($berkas->bukti_follow_ig_mncu ?? null);
                    $docTtMfls = $parseDocFiles($berkas->bukti_follow_tiktok_beasiswamncu ?? null);
                    $docTtMncu = $parseDocFiles($berkas->bukti_follow_tiktok_mncu ?? null);
                    $medsosFiles = array_merge($docIgMfls, $docIgMncu, $docTtMfls, $docTtMncu);
                    if (!empty($medsosFiles)) {
                        $addDocToPayload('medsos', 'Bukti Follow Media Sosial', 'Follow Medsos', $medsosFiles, false);
                    }

                    $totalUploadedDocs = count($candidateDocumentsPayload['docs']);
                @endphp

                <!-- Applicant Card -->
                <div class="bg-white rounded-3xl border {{ $isLengkap100 ? 'border-emerald-200/90 shadow-emerald-500/5' : 'border-slate-200/90 shadow-slate-500/5' }} p-5 sm:p-6 shadow-sm hover:shadow-md transition-all duration-200 relative group overflow-hidden"
                     id="card-applicant-{{ $user->id }}"
                     data-json="{{ htmlspecialchars(json_encode($candidateDocumentsPayload), ENT_QUOTES, 'UTF-8') }}">
                    
                    <!-- Top Ribbon Accent -->
                    <div class="absolute top-0 inset-x-0 h-1.5 {{ $isLengkap100 ? 'bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-600' : 'bg-gradient-to-r from-amber-400 via-orange-400 to-amber-500' }}"></div>

                    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-5">
                        <!-- Left Info: Avatar + Details -->
                        <div class="flex items-start gap-4">
                            <!-- Avatar / Foto 3x4 Thumbnail -->
                            <div class="relative shrink-0">
                                @if(!empty($docFoto))
                                    <button type="button" 
                                            onclick="openSingleDocPreview({{ $user->id }}, 'foto')"
                                            class="w-16 h-20 sm:w-20 sm:h-24 rounded-2xl overflow-hidden border-2 border-emerald-300 shadow-sm hover:scale-105 transition-transform group/img relative bg-slate-100 block">
                                        <img src="{{ $docFoto[0]['url'] }}" alt="{{ $user->nama }}" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center text-white">
                                            <span class="iconify text-xl" data-icon="solar:eye-bold"></span>
                                        </div>
                                    </button>
                                @else
                                    <div class="w-16 h-20 sm:w-20 sm:h-24 rounded-2xl bg-slate-100 border-2 border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400 p-2 text-center">
                                        <span class="iconify text-2xl mb-1" data-icon="solar:user-bold"></span>
                                        <span class="text-[9px] font-bold uppercase leading-tight">Foto Kosong</span>
                                    </div>
                                @endif
                                @if($isLengkap100)
                                    <span class="absolute -top-1.5 -right-1.5 w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-sm" title="100% Berkas Lengkap">
                                        <span class="iconify text-sm" data-icon="solar:check-circle-bold"></span>
                                    </span>
                                @endif
                            </div>

                            <!-- Name, School, Prodi & Registration Status -->
                            <div class="space-y-1.5 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-snug">
                                        {{ $user->nama }}
                                    </h3>
                                    <!-- Status Seleksi Tag -->
                                    @php
                                        $statusDaftar = $daftar->status ?? 'menunggu';
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider
                                        {{ $statusDaftar === 'diverifikasi' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 
                                          ($statusDaftar === 'diajukan_palugada' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 
                                          ($statusDaftar === 'ditolak' ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-slate-100 text-slate-700 border border-slate-200')) }}">
                                        {{ str_replace('_', ' ', $statusDaftar) }}
                                    </span>

                                    @if($isDkv)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-purple-100 text-purple-700 border border-purple-200">
                                            DKV
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-3 text-xs text-slate-500 flex-wrap">
                                    <span class="flex items-center gap-1 font-semibold text-slate-700">
                                        <span class="iconify text-sm text-slate-400" data-icon="solar:buildings-bold"></span>
                                        {{ $daftar->asal_sekolah ?? $peserta->nama_sekolah ?? 'Asal Sekolah Belum Diisi' }}
                                    </span>
                                    <span class="text-slate-300">•</span>
                                    <span class="flex items-center gap-1">
                                        <span class="iconify text-sm text-slate-400" data-icon="solar:diploma-bold"></span>
                                        {{ $peserta->pilihan_prodi ?? 'Prodi Belum Dipilih' }}
                                    </span>
                                    <span class="text-slate-300">•</span>
                                    <span class="flex items-center gap-1 text-slate-600">
                                        <span class="iconify text-sm text-slate-400" data-icon="solar:calendar-bold"></span>
                                        Lulus: <b>{{ $tahunLulus }}</b>
                                    </span>
                                </div>

                                <div class="flex items-center gap-4 text-xs text-slate-400 pt-0.5 flex-wrap">
                                    <span>NISN: <b class="text-slate-700">{{ $peserta->nisn ?? '-' }}</b></span>
                                    <span>Email: <b class="text-slate-700">{{ $user->email }}</b></span>
                                    @if(!empty($peserta->no_whatsapp ?? $peserta->no_hp))
                                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $peserta->no_whatsapp ?? $peserta->no_hp)) }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 font-bold flex items-center gap-1">
                                            <span class="iconify" data-icon="solar:phone-bold"></span>
                                            {{ $peserta->no_whatsapp ?? $peserta->no_hp }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Right Stats: Progress Bar & Direct Action -->
                        <div class="lg:w-64 shrink-0 flex flex-col justify-between space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1.5">
                                    <span class="font-bold text-slate-600 uppercase text-[10px] tracking-wider">Kelengkapan Berkas</span>
                                    <span class="font-black {{ $isLengkap100 ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $mandatoryFilled }}/{{ $mandatoryCount }} ({{ $percentLengkap }}%)
                                    </span>
                                </div>
                                <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-300 {{ $isLengkap100 ? 'bg-emerald-500' : ($percentLengkap >= 60 ? 'bg-amber-500' : 'bg-red-500') }}"
                                         style="width: {{ $percentLengkap }}%"></div>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1">
                                    @if($isLengkap100)
                                        ✓ Seluruh berkas wajib telah terunggah
                                    @else
                                        Kurang {{ $mandatoryCount - $mandatoryFilled }} berkas wajib
                                    @endif
                                </p>
                            </div>

                            <div class="flex items-center gap-2 pt-1">
                                <button type="button" onclick="openCandidateInspectionModal({{ $user->id }})"
                                        class="flex-1 px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs hover:shadow transition-all flex items-center justify-center gap-1.5">
                                    <span class="iconify text-base" data-icon="solar:eye-bold"></span>
                                    <span>Inspeksi Dokumen</span>
                                </button>
                                <a href="{{ route('admin.pendaftar.show', $user->id) }}" target="_blank"
                                   class="p-2 rounded-xl bg-white border border-slate-200 hover:border-slate-300 text-slate-700 hover:text-slate-900 transition-all shadow-xs"
                                   title="Buka Halaman Detail Verifikasi">
                                    <span class="iconify text-base" data-icon="solar:arrow-right-up-linear"></span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Middle: Interactive Document Badges Grid -->
                    <div class="mt-5 pt-4 border-t border-slate-100">
                        <div class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-2.5 flex items-center justify-between">
                            <span>Status Dokumen Terunggah (Klik untuk Preview Langsung):</span>
                            <span class="text-slate-400 font-semibold">{{ $totalUploadedDocs }} Kategori Berkas Tersedia</span>
                        </div>

                        <div class="flex flex-wrap gap-2 items-center">
                            <!-- Foto -->
                            @if(!empty($docFoto))
                                <button type="button" onclick="openSingleDocPreview({{ $user->id }}, 'foto')"
                                        class="px-2.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold hover:bg-emerald-100 hover:scale-105 transition-all flex items-center gap-1.5 shadow-2xs">
                                    <span class="iconify text-emerald-600" data-icon="solar:camera-bold"></span>
                                    <span>Foto 3x4</span>
                                </button>
                            @else
                                <span class="px-2.5 py-1.5 rounded-xl bg-slate-100 text-slate-400 border border-slate-200 text-xs font-medium flex items-center gap-1.5 opacity-60">
                                    <span class="iconify" data-icon="solar:close-circle-bold"></span>
                                    <span>Foto 3x4 (Kosong)</span>
                                </span>
                            @endif

                            <!-- Rapor 1 s/d 5 (dan 6) -->
                            @for($i = 1; $i <= ($tahunLulus < 2027 ? 6 : 5); $i++)
                                @php
                                    $varName = 'docRapor' . $i;
                                    $currentRapor = $$varName;
                                    $raporFilled = !empty($currentRapor);
                                @endphp
                                @if($raporFilled)
                                    <button type="button" onclick="openSingleDocPreview({{ $user->id }}, 'rapor{{ $i }}')"
                                            class="px-2.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold hover:bg-emerald-100 hover:scale-105 transition-all flex items-center gap-1.5 shadow-2xs">
                                        <span class="iconify text-emerald-600" data-icon="solar:document-text-bold"></span>
                                        <span>Rapor S{{ $i }}</span>
                                        @if(count($currentRapor) > 1)
                                            <span class="px-1.5 py-0.2 rounded-md bg-emerald-200 text-emerald-800 text-[9px] font-black">{{ count($currentRapor) }}</span>
                                        @endif
                                    </button>
                                @else
                                    <span class="px-2 py-1.5 rounded-xl bg-slate-100 text-slate-400 border border-slate-200 text-xs font-medium flex items-center gap-1 opacity-60">
                                        <span class="iconify text-xs" data-icon="solar:close-circle-bold"></span>
                                        <span>Rapor S{{ $i }}</span>
                                    </span>
                                @endif
                            @endfor

                            <!-- Ijazah / SKL -->
                            @if(!empty($docIjazah))
                                <button type="button" onclick="openSingleDocPreview({{ $user->id }}, 'ijazah')"
                                        class="px-2.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold hover:bg-emerald-100 hover:scale-105 transition-all flex items-center gap-1.5 shadow-2xs">
                                    <span class="iconify text-emerald-600" data-icon="solar:diploma-verified-bold"></span>
                                    <span>Ijazah/SKL</span>
                                </button>
                            @else
                                <span class="px-2.5 py-1.5 rounded-xl bg-slate-100 text-slate-400 border border-slate-200 text-xs font-medium flex items-center gap-1.5 opacity-60">
                                    <span class="iconify" data-icon="solar:close-circle-bold"></span>
                                    <span>Ijazah/SKL</span>
                                </span>
                            @endif

                            <!-- Personal Statement -->
                            @if(!empty($docPersonalStatement))
                                <button type="button" onclick="openSingleDocPreview({{ $user->id }}, 'personal_statement')"
                                        class="px-2.5 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200 text-xs font-bold hover:bg-indigo-100 hover:scale-105 transition-all flex items-center gap-1.5 shadow-2xs">
                                    <span class="iconify text-indigo-600" data-icon="solar:notebook-bold"></span>
                                    <span>Personal Statement</span>
                                </button>
                            @else
                                <span class="px-2.5 py-1.5 rounded-xl bg-slate-100 text-slate-400 border border-slate-200 text-xs font-medium flex items-center gap-1.5 opacity-60">
                                    <span class="iconify" data-icon="solar:close-circle-bold"></span>
                                    <span>Personal Statement</span>
                                </span>
                            @endif

                            <!-- Study Plan -->
                            @if(!empty($docStudyPlan))
                                <button type="button" onclick="openSingleDocPreview({{ $user->id }}, 'study_plan')"
                                        class="px-2.5 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200 text-xs font-bold hover:bg-indigo-100 hover:scale-105 transition-all flex items-center gap-1.5 shadow-2xs">
                                    <span class="iconify text-indigo-600" data-icon="solar:book-bookmark-bold"></span>
                                    <span>Study Plan</span>
                                </button>
                            @else
                                <span class="px-2.5 py-1.5 rounded-xl bg-slate-100 text-slate-400 border border-slate-200 text-xs font-medium flex items-center gap-1.5 opacity-60">
                                    <span class="iconify" data-icon="solar:close-circle-bold"></span>
                                    <span>Study Plan</span>
                                </span>
                            @endif

                            <!-- Surat Rekomendasi Sekolah (Wajib lulusan 2027) -->
                            @if($tahunLulus >= 2027)
                                @if(!empty($docRekomendasi))
                                    <button type="button" onclick="openSingleDocPreview({{ $user->id }}, 'rekomendasi')"
                                            class="px-2.5 py-1.5 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold hover:bg-blue-100 hover:scale-105 transition-all flex items-center gap-1.5 shadow-2xs">
                                        <span class="iconify text-blue-600" data-icon="solar:letter-bold"></span>
                                        <span>Rekomendasi Sekolah</span>
                                    </button>
                                @else
                                    <span class="px-2.5 py-1.5 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 text-xs font-medium flex items-center gap-1.5">
                                        <span class="iconify text-amber-500" data-icon="solar:danger-triangle-bold"></span>
                                        <span>Rekomendasi (Belum Ada)</span>
                                    </span>
                                @endif
                            @endif

                            <!-- Khusus DKV: Surat Buta Warna & Portofolio -->
                            @if($isDkv)
                                @if(!empty($docButaWarna))
                                    <button type="button" onclick="openSingleDocPreview({{ $user->id }}, 'buta_warna')"
                                            class="px-2.5 py-1.5 rounded-xl bg-purple-50 text-purple-700 border border-purple-200 text-xs font-bold hover:bg-purple-100 hover:scale-105 transition-all flex items-center gap-1.5 shadow-2xs">
                                        <span class="iconify text-purple-600" data-icon="solar:eye-closed-bold"></span>
                                        <span>Surat Bebas Buta Warna</span>
                                    </button>
                                @else
                                    <span class="px-2.5 py-1.5 rounded-xl bg-red-50 text-red-700 border border-red-200 text-xs font-bold flex items-center gap-1.5">
                                        <span class="iconify text-red-500" data-icon="solar:close-circle-bold"></span>
                                        <span>Buta Warna (Wajib DKV)</span>
                                    </span>
                                @endif

                                @if(!empty($docPortfolio))
                                    <button type="button" onclick="openSingleDocPreview({{ $user->id }}, 'portfolio')"
                                            class="px-2.5 py-1.5 rounded-xl bg-purple-50 text-purple-700 border border-purple-200 text-xs font-bold hover:bg-purple-100 hover:scale-105 transition-all flex items-center gap-1.5 shadow-2xs">
                                        <span class="iconify text-purple-600" data-icon="solar:pallete-2-bold"></span>
                                        <span>Portofolio Karya</span>
                                    </button>
                                @else
                                    <span class="px-2.5 py-1.5 rounded-xl bg-red-50 text-red-700 border border-red-200 text-xs font-bold flex items-center gap-1.5">
                                        <span class="iconify text-red-500" data-icon="solar:close-circle-bold"></span>
                                        <span>Portofolio (Wajib DKV)</span>
                                    </span>
                                @endif
                            @endif

                            <!-- Sertifikat Prestasi -->
                            @if(!empty($sertifikatsList))
                                <button type="button" onclick="openSingleDocPreview({{ $user->id }}, 'sertifikat')"
                                        class="px-2.5 py-1.5 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold hover:bg-amber-100 hover:scale-105 transition-all flex items-center gap-1.5 shadow-2xs">
                                    <span class="iconify text-amber-600" data-icon="solar:medal-star-bold"></span>
                                    <span>{{ count($sertifikatsList) }} Sertifikat Prestasi</span>
                                </button>
                            @endif

                            <!-- Follow Medsos -->
                            @if(!empty($medsosFiles))
                                <button type="button" onclick="openSingleDocPreview({{ $user->id }}, 'medsos')"
                                        class="px-2.5 py-1.5 rounded-xl bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold hover:bg-slate-100 hover:scale-105 transition-all flex items-center gap-1.5">
                                    <span class="iconify text-pink-500" data-icon="solar:instagram-bold"></span>
                                    <span>Bukti Medsos</span>
                                </button>
                            @endif

                            <!-- Video Motivasi (External Link) -->
                            @if(!empty($berkas->motivasi_video) || !empty($berkas->motivasi_video_tiktok))
                                <a href="{{ route('admin.video_motivasi.index', ['search' => $user->nama]) }}" target="_blank"
                                   class="px-2.5 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200 text-xs font-semibold hover:bg-indigo-100 hover:scale-105 transition-all flex items-center gap-1.5">
                                    <span class="iconify text-indigo-600" data-icon="solar:videocamera-record-bold"></span>
                                    <span>Video Motivasi</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- 2. MATRIX TABLE VIEW (Compact Dense View) -->
        <div id="berkas-matrix-view" class="hidden bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-black uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="py-3 px-4">Pendaftar & Sekolah</th>
                            <th class="py-3 px-3">Prodi</th>
                            <th class="py-3 px-3 text-center">Kelengkapan</th>
                            <th class="py-3 px-2 text-center">Foto</th>
                            <th class="py-3 px-2 text-center">R1</th>
                            <th class="py-3 px-2 text-center">R2</th>
                            <th class="py-3 px-2 text-center">R3</th>
                            <th class="py-3 px-2 text-center">R4</th>
                            <th class="py-3 px-2 text-center">R5</th>
                            <th class="py-3 px-2 text-center">Ijazah</th>
                            <th class="py-3 px-2 text-center">PS</th>
                            <th class="py-3 px-2 text-center">SP</th>
                            <th class="py-3 px-2 text-center">DKV</th>
                            <th class="py-3 px-2 text-center">Sertifikat</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pendaftars as $user)
                            @php
                                $peserta = $user->peserta;
                                $berkas = $peserta->berkas ?? null;
                                $daftar = $peserta->daftar ?? null;
                                $tahunLulus = (int) ($daftar->tahun_lulus ?? $peserta->tahun_lulus ?? 2027);
                                $isDkv = str_contains($peserta->pilihan_prodi ?? '', 'DKV') || str_contains($peserta->pilihan_prodi ?? '', 'Desain Komunikasi Visual');

                                $docFoto = $parseDocFiles($berkas->foto ?? null);
                                $docRapor1 = $parseDocFiles($berkas->rapor1 ?? null);
                                $docRapor2 = $parseDocFiles($berkas->rapor2 ?? null);
                                $docRapor3 = $parseDocFiles($berkas->rapor3 ?? null);
                                $docRapor4 = $parseDocFiles($berkas->rapor4 ?? null);
                                $docRapor5 = $parseDocFiles($berkas->rapor5 ?? null);
                                $docIjazah = $parseDocFiles($berkas->ijazah ?? null);
                                $docPersonalStatement = $parseDocFiles($berkas->personal_statement ?? null);
                                $docStudyPlan = $parseDocFiles($berkas->study_plan ?? null);
                                $docButaWarna = $parseDocFiles($berkas->surat_buta_warna ?? null);
                                $docPortfolio = $parseDocFiles($berkas->portfolio ?? null);

                                $serCount = $peserta && $peserta->sertifikats ? $peserta->sertifikats->count() : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="font-black text-slate-900 leading-tight">{{ $user->nama }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 truncate max-w-[200px]">
                                        {{ $daftar->asal_sekolah ?? $peserta->nama_sekolah ?? '-' }}
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $isDkv ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $isDkv ? 'DKV' : Str::limit($peserta->pilihan_prodi ?? '-', 15) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @php
                                        $baseMandatory = (!empty($docFoto)?1:0) + (!empty($docRapor1)?1:0) + (!empty($docRapor2)?1:0) + (!empty($docRapor3)?1:0) + (!empty($docRapor4)?1:0) + (!empty($docRapor5)?1:0) + (!empty($docIjazah)?1:0) + (!empty($docPersonalStatement)?1:0) + (!empty($docStudyPlan)?1:0);
                                        $isFull = $baseMandatory >= 9;
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $isFull ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $baseMandatory }}/9
                                    </span>
                                </td>
                                <!-- Foto -->
                                <td class="py-3 px-2 text-center">
                                    @if(!empty($docFoto))
                                        <button type="button" onclick="openSingleDocPreview({{ $user->id }}, 'foto')" class="text-emerald-600 hover:scale-125 transition-transform" title="Preview Foto">
                                            <span class="iconify text-base" data-icon="solar:check-circle-bold"></span>
                                        </button>
                                    @else
                                        <span class="text-slate-300" title="Kosong"><span class="iconify text-base" data-icon="solar:close-circle-bold"></span></span>
                                    @endif
                                </td>
                                <!-- Rapor 1-5 -->
                                @foreach([1, 2, 3, 4, 5] as $rIdx)
                                    @php
                                        $rVar = 'docRapor' . $rIdx;
                                        $rFiles = $$rVar;
                                    @endphp
                                    <td class="py-3 px-2 text-center">
                                        @if(!empty($rFiles))
                                            <button type="button" onclick="openSingleDocPreview({{ $user->id }}, 'rapor{{ $rIdx }}')" class="text-emerald-600 hover:scale-125 transition-transform" title="Rapor S{{ $rIdx }} ({{ count($rFiles) }} file)">
                                                <span class="iconify text-base" data-icon="solar:check-circle-bold"></span>
                                            </button>
                                        @else
                                            <span class="text-slate-300" title="Kosong"><span class="iconify text-base" data-icon="solar:close-circle-bold"></span></span>
                                        @endif
                                    </td>
                                @endforeach
                                <!-- Ijazah -->
                                <td class="py-3 px-2 text-center">
                                    @if(!empty($docIjazah))
                                        <button type="button" onclick="openSingleDocPreview({{ $user->id }}, 'ijazah')" class="text-emerald-600 hover:scale-125 transition-transform" title="Preview Ijazah">
                                            <span class="iconify text-base" data-icon="solar:check-circle-bold"></span>
                                        </button>
                                    @else
                                        <span class="text-slate-300"><span class="iconify text-base" data-icon="solar:close-circle-bold"></span></span>
                                    @endif
                                </td>
                                <!-- PS -->
                                <td class="py-3 px-2 text-center">
                                    @if(!empty($docPersonalStatement))
                                        <button type="button" onclick="openSingleDocPreview({{ $user->id }}, 'personal_statement')" class="text-indigo-600 hover:scale-125 transition-transform" title="Preview PS">
                                            <span class="iconify text-base" data-icon="solar:check-circle-bold"></span>
                                        </button>
                                    @else
                                        <span class="text-slate-300"><span class="iconify text-base" data-icon="solar:close-circle-bold"></span></span>
                                    @endif
                                </td>
                                <!-- SP -->
                                <td class="py-3 px-2 text-center">
                                    @if(!empty($docStudyPlan))
                                        <button type="button" onclick="openSingleDocPreview({{ $user->id }}, 'study_plan')" class="text-indigo-600 hover:scale-125 transition-transform" title="Preview SP">
                                            <span class="iconify text-base" data-icon="solar:check-circle-bold"></span>
                                        </button>
                                    @else
                                        <span class="text-slate-300"><span class="iconify text-base" data-icon="solar:close-circle-bold"></span></span>
                                    @endif
                                </td>
                                <!-- DKV -->
                                <td class="py-3 px-2 text-center">
                                    @if($isDkv)
                                        @if(!empty($docButaWarna) && !empty($docPortfolio))
                                            <span class="text-purple-600 font-bold" title="Buta Warna & Portfolio Lengkap">✓ 2/2</span>
                                        @else
                                            <span class="text-red-500 font-bold" title="Berkas DKV Belum Lengkap">⚠️</span>
                                        @endif
                                    @else
                                        <span class="text-slate-200">-</span>
                                    @endif
                                </td>
                                <!-- Sertifikat -->
                                <td class="py-3 px-2 text-center">
                                    @if($serCount > 0)
                                        <button type="button" onclick="openSingleDocPreview({{ $user->id }}, 'sertifikat')" class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 font-bold text-[10px] hover:bg-amber-200">
                                            {{ $serCount }}
                                        </button>
                                    @else
                                        <span class="text-slate-300">0</span>
                                    @endif
                                </td>
                                <!-- Aksi -->
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" onclick="openCandidateInspectionModal({{ $user->id }})"
                                                class="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all" title="Buka Semua Dokumen">
                                            <span class="iconify text-base" data-icon="solar:eye-bold"></span>
                                        </button>
                                        <a href="{{ route('admin.pendaftar.show', $user->id) }}" target="_blank"
                                           class="p-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all" title="Verifikasi">
                                            <span class="iconify text-base" data-icon="solar:arrow-right-up-linear"></span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs text-slate-500">
                Menampilkan <span class="font-bold text-slate-700">{{ $pendaftars->firstItem() ?? 0 }}</span> - <span class="font-bold text-slate-700">{{ $pendaftars->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-700">{{ $pendaftars->total() }}</span> pendaftar
            </div>
            <div>
                {{ $pendaftars->links() }}
            </div>
        </div>
    @endif

    <!-- IN-BROWSER INTERACTIVE DOCUMENT PREVIEW MODAL -->
    <div id="berkas-preview-modal" class="fixed inset-0 z-50 hidden transition-opacity duration-300">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" onclick="closeBerkasPreviewModal()"></div>

        <!-- Modal Dialog Box -->
        <div class="fixed inset-3 sm:inset-6 md:inset-10 z-10 flex flex-col bg-slate-900 border border-slate-700/80 rounded-3xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="px-5 py-3.5 bg-slate-900/95 border-b border-slate-800 flex items-center justify-between gap-4 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0">
                        <span class="iconify text-xl" data-icon="solar:folder-with-files-bold-duotone"></span>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-sm sm:text-base font-black text-white truncate" id="modal-candidate-name">
                                Nama Pendaftar
                            </h2>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-500/20 text-blue-300 border border-blue-500/30" id="modal-candidate-prodi">
                                Prodi
                            </span>
                            <span class="text-xs text-slate-500 hidden sm:inline">•</span>
                            <span class="text-xs text-slate-400 hidden sm:inline" id="modal-candidate-school">Asal Sekolah</span>
                        </div>
                        <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                            <span id="modal-active-doc-title" class="font-bold text-white">Memuat Dokumen...</span>
                            <span class="text-slate-600">•</span>
                            <span id="modal-file-counter" class="text-slate-400">File 1 dari 1</span>
                        </div>
                    </div>
                </div>

                <!-- Modal Actions Toolbar -->
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" id="btn-modal-open-new-tab" onclick="openCurrentDocInNewTab()"
                            class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700 transition-all flex items-center gap-1.5 shadow-xs" title="Buka di Tab Baru">
                        <span class="iconify text-base" data-icon="solar:square-top-down-linear"></span>
                        <span class="hidden sm:inline">Tab Baru</span>
                    </button>
                    <a id="btn-modal-download-file" href="#" download
                       class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700 transition-all flex items-center gap-1.5 shadow-xs" title="Unduh File Ini">
                        <span class="iconify text-base" data-icon="solar:download-bold"></span>
                        <span class="hidden sm:inline">Unduh</span>
                    </a>
                    <a id="btn-modal-verify-pendaftar" href="#" target="_blank"
                       class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition-all flex items-center gap-1.5">
                        <span class="iconify text-base" data-icon="solar:shield-check-bold"></span>
                        <span class="hidden md:inline">Verifikasi Pendaftar</span>
                    </a>
                    <button type="button" onclick="closeBerkasPreviewModal()"
                            class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-red-500/20 hover:text-red-400 text-slate-400 border border-slate-700 transition-all flex items-center justify-center">
                        <span class="iconify text-xl" data-icon="solar:close-circle-bold"></span>
                    </button>
                </div>
            </div>

            <!-- Horizontal Document Tabs (Filter/Switch between all documents of this applicant) -->
            <div class="px-5 py-2.5 bg-slate-950/90 border-b border-slate-800 flex items-center gap-2 overflow-x-auto shrink-0 scrollbar-thin scrollbar-thumb-slate-700" id="modal-doc-tabs-bar">
                <!-- Dynamically generated document tab chips -->
            </div>

            <!-- Viewer Body Stage -->
            <div class="flex-1 bg-slate-950 p-4 sm:p-6 overflow-hidden flex flex-col items-center justify-center relative">
                <!-- 1. PDF Viewer Iframe -->
                <div id="viewer-pdf-container" class="w-full h-full hidden rounded-2xl overflow-hidden bg-slate-900 border border-slate-800">
                    <iframe id="viewer-pdf-iframe" src="" class="w-full h-full border-0"></iframe>
                </div>

                <!-- 2. Image Viewer with Zoom Controls -->
                <div id="viewer-image-container" class="w-full h-full hidden flex flex-col items-center justify-center relative overflow-hidden select-none">
                    <!-- Zoom Controls Floating Bar -->
                    <div class="absolute bottom-4 z-20 flex items-center gap-1.5 px-3 py-1.5 rounded-2xl bg-slate-900/90 border border-slate-700/80 backdrop-blur-md shadow-lg text-white text-xs">
                        <button type="button" onclick="zoomImage(-0.25)" class="p-1.5 hover:bg-slate-800 rounded-lg transition-colors" title="Zoom Out">
                            <span class="iconify text-base" data-icon="solar:magnifer-zoom-out-bold"></span>
                        </button>
                        <span id="image-zoom-level" class="font-mono text-[11px] px-2 font-bold text-slate-300">100%</span>
                        <button type="button" onclick="zoomImage(0.25)" class="p-1.5 hover:bg-slate-800 rounded-lg transition-colors" title="Zoom In">
                            <span class="iconify text-base" data-icon="solar:magnifer-zoom-in-bold"></span>
                        </button>
                        <button type="button" onclick="resetImageZoom()" class="p-1.5 hover:bg-slate-800 rounded-lg transition-colors text-slate-400 hover:text-white" title="Reset Zoom">
                            <span class="iconify text-base" data-icon="solar:restart-bold"></span>
                        </button>
                    </div>
                    <!-- Zoomable Image -->
                    <div class="w-full h-full flex items-center justify-center overflow-auto p-4 cursor-grab" id="image-scroll-wrapper">
                        <img id="viewer-image-element" src="" alt="Preview Berkas" class="max-h-full max-w-full object-contain rounded-xl shadow-2xl transition-transform duration-150 origin-center">
                    </div>
                </div>

                <!-- 3. External / Fallback URL Viewer -->
                <div id="viewer-external-container" class="w-full max-w-lg p-8 rounded-3xl bg-slate-900 border border-slate-800 text-center space-y-5 hidden shadow-2xl">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-blue-600/20 text-blue-400 flex items-center justify-center">
                        <span class="iconify text-3xl" data-icon="solar:link-bold-duotone"></span>
                    </div>
                    <div>
                        <h4 class="text-lg font-black text-white" id="external-doc-title">Tautan Eksternal</h4>
                        <p class="text-xs text-slate-400 mt-1" id="external-doc-url">https://drive.google.com/...</p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-300 font-mono text-left break-all">
                        <span class="text-slate-500 font-bold uppercase text-[10px] block mb-1">Target URL:</span>
                        <span id="external-doc-url-text">-</span>
                    </div>
                    <div class="flex items-center justify-center gap-3">
                        <a id="btn-open-external-url" href="#" target="_blank"
                           class="px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-600/30 transition-all flex items-center gap-2">
                            <span class="iconify text-base" data-icon="solar:square-top-down-linear"></span>
                            <span>Buka di Halaman Baru</span>
                        </a>
                    </div>
                </div>

                <!-- 4. Empty State / No File in Document -->
                <div id="viewer-empty-container" class="text-center p-8 space-y-3 hidden">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-900 border border-slate-800 text-slate-500 flex items-center justify-center">
                        <span class="iconify text-3xl" data-icon="solar:document-cross-bold"></span>
                    </div>
                    <h4 class="text-base font-bold text-white">Tidak ada file yang terlampir</h4>
                    <p class="text-xs text-slate-400 max-w-sm">Pendaftar ini belum mengunggah dokumen pada kategori ini.</p>
                </div>
            </div>

            <!-- Modal Footer Navigation (Previous / Next Doc Buttons) -->
            <div class="px-5 py-3 bg-slate-900/95 border-t border-slate-800 flex items-center justify-between text-xs shrink-0">
                <div class="flex items-center gap-2">
                    <button type="button" id="btn-modal-prev-doc" onclick="navigateModalDoc(-1)"
                            class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold transition-all flex items-center gap-1.5 border border-slate-700">
                        <span class="iconify text-base" data-icon="solar:arrow-left-linear"></span>
                        <span>Dokumen Sebelumnya</span>
                    </button>
                    <button type="button" id="btn-modal-next-doc" onclick="navigateModalDoc(1)"
                            class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold transition-all flex items-center gap-1.5 border border-slate-700">
                        <span>Dokumen Selanjutnya</span>
                        <span class="iconify text-base" data-icon="solar:arrow-right-linear"></span>
                    </button>
                </div>

                <div class="hidden sm:flex items-center gap-4 text-slate-500 text-[11px]">
                    <span>Navigasi: <kbd class="px-1.5 py-0.5 rounded bg-slate-800 text-slate-300 font-mono text-[10px]">←</kbd> <kbd class="px-1.5 py-0.5 rounded bg-slate-800 text-slate-300 font-mono text-[10px]">→</kbd></span>
                    <span>Tutup: <kbd class="px-1.5 py-0.5 rounded bg-slate-800 text-slate-300 font-mono text-[10px]">ESC</kbd></span>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- INLINE JAVASCRIPT FOR SPA COMPATIBILITY & PREVIEWER MODAL -->
<script>
(function() {
    // Current Active Inspection State
    let activeCandidateData = null;
    let activeDocIndex = 0;
    let activeFileIndex = 0;
    let currentImageScale = 1.0;

    // View Switching: Cards vs Matrix Table
    window.switchBerkasView = function(viewMode) {
        const cardsEl = document.getElementById('berkas-cards-view');
        const matrixEl = document.getElementById('berkas-matrix-view');
        const btnCards = document.getElementById('btn-view-cards');
        const btnMatrix = document.getElementById('btn-view-matrix');

        if (!cardsEl || !matrixEl) return;

        if (viewMode === 'matrix') {
            cardsEl.classList.add('hidden');
            matrixEl.classList.remove('hidden');
            btnMatrix.className = "px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 bg-white text-slate-800 shadow-xs";
            btnCards.className = "px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 text-slate-500 hover:text-slate-800";
            localStorage.setItem('mfls_berkas_view', 'matrix');
        } else {
            matrixEl.classList.add('hidden');
            cardsEl.classList.remove('hidden');
            btnCards.className = "px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 bg-white text-slate-800 shadow-xs";
            btnMatrix.className = "px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 text-slate-500 hover:text-slate-800";
            localStorage.setItem('mfls_berkas_view', 'cards');
        }
    };

    // Restore saved view mode on load
    const savedView = localStorage.getItem('mfls_berkas_view');
    if (savedView === 'matrix') {
        window.switchBerkasView('matrix');
    }

    // Open Full Inspection Modal for a Candidate
    window.openCandidateInspectionModal = function(candidateId, initialDocKey) {
        const cardElem = document.getElementById('card-applicant-' + candidateId);
        if (!cardElem) return;

        try {
            const data = JSON.parse(cardElem.getAttribute('data-json'));
            if (!data || !data.docs || data.docs.length === 0) {
                alert('Pendaftar ini belum mengunggah dokumen apapun untuk diinspeksi.');
                return;
            }

            activeCandidateData = data;
            
            // Set initial document index
            activeDocIndex = 0;
            if (initialDocKey) {
                const foundIndex = activeCandidateData.docs.findIndex(d => d.key === initialDocKey);
                if (foundIndex !== -1) {
                    activeDocIndex = foundIndex;
                }
            }
            activeFileIndex = 0;

            renderCandidateModal();
            const modal = document.getElementById('berkas-preview-modal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        } catch (e) {
            console.error('Failed to parse candidate JSON payload', e);
        }
    };

    // Open single document preview directly from badge click
    window.openSingleDocPreview = function(candidateId, docKey) {
        window.openCandidateInspectionModal(candidateId, docKey);
    };

    // Close preview modal
    window.closeBerkasPreviewModal = function() {
        const modal = document.getElementById('berkas-preview-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
        // Reset iframe src to stop background loading
        const iframe = document.getElementById('viewer-pdf-iframe');
        if (iframe) iframe.src = '';
        const img = document.getElementById('viewer-image-element');
        if (img) img.src = '';
    };

    // Render candidate modal info, document tabs, and current active document
    function renderCandidateModal() {
        if (!activeCandidateData) return;

        document.getElementById('modal-candidate-name').textContent = activeCandidateData.nama;
        document.getElementById('modal-candidate-prodi').textContent = activeCandidateData.prodi;
        document.getElementById('modal-candidate-school').textContent = activeCandidateData.sekolah;
        
        const verifyBtn = document.getElementById('btn-modal-verify-pendaftar');
        if (verifyBtn) verifyBtn.href = activeCandidateData.detail_url;

        // Render Tabs
        const tabsBar = document.getElementById('modal-doc-tabs-bar');
        tabsBar.innerHTML = '';

        activeCandidateData.docs.forEach((doc, idx) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            const isActive = idx === activeDocIndex;
            btn.className = `px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shrink-0 ${
                isActive ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700'
            }`;
            
            const fileCount = doc.files ? doc.files.length : 0;
            btn.innerHTML = `
                <span>${doc.badge || doc.label}</span>
                ${fileCount > 1 ? `<span class="px-1.5 py-0.2 rounded-md ${isActive ? 'bg-blue-400 text-slate-950' : 'bg-slate-700 text-slate-300'} text-[9px] font-black">${fileCount}</span>` : ''}
            `;
            btn.onclick = function() {
                activeDocIndex = idx;
                activeFileIndex = 0;
                renderCandidateModal();
            };
            tabsBar.appendChild(btn);
        });

        // Render Active Document File
        renderActiveDocFile();
    }

    // Render active document viewer
    function renderActiveDocFile() {
        if (!activeCandidateData || !activeCandidateData.docs || !activeCandidateData.docs[activeDocIndex]) return;

        const currentDoc = activeCandidateData.docs[activeDocIndex];
        const files = currentDoc.files || [];

        // Title and file counter
        document.getElementById('modal-active-doc-title').textContent = currentDoc.label;
        const counterEl = document.getElementById('modal-file-counter');
        if (files.length > 1) {
            counterEl.textContent = `File ${activeFileIndex + 1} dari ${files.length}`;
        } else {
            counterEl.textContent = `${files.length} File`;
        }

        // Hide all viewer containers first
        const pdfContainer = document.getElementById('viewer-pdf-container');
        const imgContainer = document.getElementById('viewer-image-container');
        const extContainer = document.getElementById('viewer-external-container');
        const emptyContainer = document.getElementById('viewer-empty-container');
        const iframe = document.getElementById('viewer-pdf-iframe');
        const imgElem = document.getElementById('viewer-image-element');

        pdfContainer.classList.add('hidden');
        imgContainer.classList.add('hidden');
        extContainer.classList.add('hidden');
        emptyContainer.classList.add('hidden');

        if (files.length === 0) {
            emptyContainer.classList.remove('hidden');
            return;
        }

        const currentFile = files[activeFileIndex] || files[0];
        const fileUrl = currentFile.url;

        // Update Download button & Open in new tab button
        const dlBtn = document.getElementById('btn-modal-download-file');
        if (dlBtn) {
            dlBtn.href = fileUrl;
            dlBtn.download = currentDoc.key + '_' + (activeFileIndex + 1);
        }

        if (currentFile.type === 'pdf') {
            pdfContainer.classList.remove('hidden');
            iframe.src = fileUrl;
        } else if (currentFile.type === 'image') {
            imgContainer.classList.remove('hidden');
            imgElem.src = fileUrl;
            resetImageZoom();
        } else {
            // External URL or other file
            extContainer.classList.remove('hidden');
            document.getElementById('external-doc-title').textContent = currentDoc.label;
            document.getElementById('external-doc-url').textContent = fileUrl;
            document.getElementById('external-doc-url-text').textContent = fileUrl;
            document.getElementById('btn-open-external-url').href = fileUrl;
        }
    }

    // Open current active document in new tab
    window.openCurrentDocInNewTab = function() {
        if (!activeCandidateData || !activeCandidateData.docs[activeDocIndex]) return;
        const currentDoc = activeCandidateData.docs[activeDocIndex];
        const files = currentDoc.files || [];
        const currentFile = files[activeFileIndex] || files[0];
        if (currentFile && currentFile.url) {
            window.open(currentFile.url, '_blank');
        }
    };

    // Navigate to previous (-1) or next (+1) document in modal
    window.navigateModalDoc = function(direction) {
        if (!activeCandidateData || !activeCandidateData.docs) return;
        const totalDocs = activeCandidateData.docs.length;
        if (totalDocs === 0) return;

        activeDocIndex = (activeDocIndex + direction + totalDocs) % totalDocs;
        activeFileIndex = 0;
        renderCandidateModal();
    };

    // Zoom Image logic
    window.zoomImage = function(delta) {
        currentImageScale = Math.max(0.5, Math.min(3.0, currentImageScale + delta));
        const imgElem = document.getElementById('viewer-image-element');
        const zoomLevelEl = document.getElementById('image-zoom-level');
        if (imgElem) {
            imgElem.style.transform = `scale(${currentImageScale})`;
        }
        if (zoomLevelEl) {
            zoomLevelEl.textContent = Math.round(currentImageScale * 100) + '%';
        }
    };

    window.resetImageZoom = function() {
        currentImageScale = 1.0;
        const imgElem = document.getElementById('viewer-image-element');
        const zoomLevelEl = document.getElementById('image-zoom-level');
        if (imgElem) {
            imgElem.style.transform = 'scale(1)';
        }
        if (zoomLevelEl) {
            zoomLevelEl.textContent = '100%';
        }
    };

    // Keyboard Shortcuts (Arrow Left, Arrow Right, ESC)
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('berkas-preview-modal');
        if (!modal || modal.classList.contains('hidden')) return;

        if (e.key === 'Escape') {
            closeBerkasPreviewModal();
        } else if (e.key === 'ArrowLeft') {
            navigateModalDoc(-1);
        } else if (e.key === 'ArrowRight') {
            navigateModalDoc(1);
        }
    });

})();
</script>
@endsection

@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-indigo-500/10 text-indigo-700 border border-indigo-200">
                    Seleksi & Tahapan
                </span>
                <span class="text-xs text-slate-400 font-bold">•</span>
                <span class="text-xs text-slate-500 font-semibold">MFLS Batch 3</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-600/20 shrink-0">
                    <span class="iconify text-xl" data-icon="solar:videocamera-record-bold-duotone"></span>
                </span>
                <span>Submission Video Motivasi</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                Daftar terpusat tautan (link) video motivasi dan video TikTok yang telah diserahkan oleh calon penerima beasiswa MFLS.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('admin.video_motivasi.index') }}" 
               class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 hover:border-slate-300 text-slate-700 font-bold text-xs shadow-xs hover:shadow-sm transition-all flex items-center gap-2">
                <span class="iconify text-base text-slate-500" data-icon="solar:restart-bold"></span>
                <span>Refresh Data</span>
            </a>
            <a href="{{ route('admin.pendaftar.index') }}" 
               class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white font-bold text-xs shadow-xs hover:shadow-sm transition-all flex items-center gap-2">
                <span class="iconify text-base" data-icon="solar:users-group-rounded-bold"></span>
                <span>Seleksi Administrasi</span>
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

        <!-- Sudah Submit Video -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-emerald-200/80 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Sudah Submit</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="iconify text-base" data-icon="solar:check-circle-bold"></span>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-black text-emerald-700">{{ number_format($totalSubmitVideo) }}</span>
                <span class="text-xs text-emerald-600 font-semibold">
                    ({{ $totalPendaftar > 0 ? round(($totalSubmitVideo / $totalPendaftar) * 100) : 0 }}%)
                </span>
            </div>
            <p class="text-[10px] text-slate-500 font-medium">Minimal 1 video terisi</p>
        </div>

        <!-- Video Motivasi Utama -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-indigo-200/80 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Video Utama</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <span class="iconify text-base" data-icon="solar:play-circle-bold"></span>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-black text-indigo-700">{{ number_format($totalSubmitUtama) }}</span>
                <span class="text-xs text-slate-400 font-semibold">link</span>
            </div>
            <p class="text-[10px] text-slate-500 font-medium">YouTube, Drive, atau Reels</p>
        </div>

        <!-- Video TikTok -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-rose-200/80 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600">Video TikTok</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <span class="iconify text-base" data-icon="logos:tiktok-icon"></span>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-black text-rose-700">{{ number_format($totalSubmitTiktok) }}</span>
                <span class="text-xs text-slate-400 font-semibold">link</span>
            </div>
            <p class="text-[10px] text-slate-500 font-medium">Konten kreatif di TikTok</p>
        </div>

        <!-- Belum Submit -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-amber-200/80 shadow-xs space-y-2 col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Belum Submit</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="iconify text-base" data-icon="solar:clock-circle-bold"></span>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-black text-amber-700">{{ number_format($totalBelumSubmit) }}</span>
                <span class="text-xs text-amber-600 font-semibold">
                    ({{ $totalPendaftar > 0 ? round(($totalBelumSubmit / $totalPendaftar) * 100) : 0 }}%)
                </span>
            </div>
            <p class="text-[10px] text-slate-500 font-medium">Belum mengisi tautan</p>
        </div>
    </div>

    <!-- Filters & Search Form -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs p-4 sm:p-6 space-y-4">
        <form method="GET" action="{{ route('admin.video_motivasi.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                <!-- Keyword Search -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-500">Pencarian Cepat</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <span class="iconify text-base" data-icon="solar:magnifer-linear"></span>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Nama / Email / Sekolah / No HP..." 
                               class="w-full bg-slate-50 text-slate-800 font-semibold text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/15 pl-9 pr-3.5 py-2.5 outline-none transition-all">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-500">Status Submission</label>
                    <select name="status" 
                            class="w-full bg-slate-50 text-slate-800 font-semibold text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/15 px-3.5 py-2.5 outline-none transition-all cursor-pointer">
                        <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>Semua Pendaftar</option>
                        <option value="sudah" {{ request('status') == 'sudah' ? 'selected' : '' }}>Sudah Submit Video (Min. 1)</option>
                        <option value="lengkap" {{ request('status') == 'lengkap' ? 'selected' : '' }}>Lengkap (Utama & TikTok)</option>
                        <option value="video_utama" {{ request('status') == 'video_utama' ? 'selected' : '' }}>Hanya Video Utama</option>
                        <option value="video_tiktok" {{ request('status') == 'video_tiktok' ? 'selected' : '' }}>Hanya Video TikTok</option>
                        <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Submit Sama Sekali</option>
                    </select>
                </div>

                <!-- Platform Filter -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-500">Platform Link</label>
                    <select name="platform" 
                            class="w-full bg-slate-50 text-slate-800 font-semibold text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/15 px-3.5 py-2.5 outline-none transition-all cursor-pointer">
                        <option value="" {{ !request('platform') ? 'selected' : '' }}>Semua Platform</option>
                        <option value="youtube" {{ request('platform') == 'youtube' ? 'selected' : '' }}>YouTube</option>
                        <option value="tiktok" {{ request('platform') == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                        <option value="drive" {{ request('platform') == 'drive' ? 'selected' : '' }}>Google Drive</option>
                        <option value="instagram" {{ request('platform') == 'instagram' ? 'selected' : '' }}>Instagram</option>
                    </select>
                </div>

                <!-- Prodi Filter -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-500">Program Studi</label>
                    <select name="prodi" 
                            class="w-full bg-slate-50 text-slate-800 font-semibold text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/15 px-3.5 py-2.5 outline-none transition-all cursor-pointer">
                        <option value="" {{ !request('prodi') ? 'selected' : '' }}>Semua Program Studi</option>
                        @foreach($prodiList as $p)
                            <option value="{{ $p }}" {{ request('prodi') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-100">
                <a href="{{ route('admin.video_motivasi.index') }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-all">
                    Reset Filter
                </a>
                <button type="submit" 
                        class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition-all active:scale-[0.98] cursor-pointer">
                    <span class="iconify text-base" data-icon="solar:filter-bold"></span>
                    <span>Terapkan Filter</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200/70 bg-slate-50/70 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                <h2 class="text-xs sm:text-sm font-extrabold text-slate-900 tracking-tight">Daftar Pengajuan Video Pendaftar</h2>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-indigo-100 text-indigo-800">
                    {{ $pendaftars->total() }} Data Ditemukan
                </span>
            </div>
            <span class="text-[11px] text-slate-500 font-medium">
                Halaman {{ $pendaftars->currentPage() }} dari {{ $pendaftars->lastPage() }}
            </span>
        </div>

        @if($pendaftars->isEmpty())
            <div class="py-16 text-center space-y-3 px-4">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center mx-auto shadow-inner">
                    <span class="iconify text-3xl" data-icon="solar:videocamera-record-bold"></span>
                </div>
                <h3 class="text-base font-extrabold text-slate-800">Tidak Ada Data Ditemukan</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    Tidak ditemukan pendaftar yang cocok dengan filter atau kata kunci pencarian Anda. Silakan coba atur ulang filter.
                </p>
                <div class="pt-2">
                    <a href="{{ route('admin.video_motivasi.index') }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-black transition-all">
                        <span>Reset Semua Filter</span>
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100/70 text-slate-600 border-b border-slate-200 text-[10px] font-black uppercase tracking-wider">
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4">Calon Mahasiswa</th>
                            <th class="py-3.5 px-4">Sekolah & Pilihan Prodi</th>
                            <th class="py-3.5 px-4">Video Motivasi Utama</th>
                            <th class="py-3.5 px-4">Video TikTok</th>
                            <th class="py-3.5 px-4 text-center">Kelengkapan</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($pendaftars as $index => $akun)
                            @php
                                $peserta = $akun->peserta;
                                $berkas = $peserta ? $peserta->berkas : null;
                                $daftar = $peserta ? $peserta->daftar : null;
                                
                                $vUtama = $berkas ? trim((string) $berkas->motivasi_video) : '';
                                $vTiktok = $berkas ? trim((string) $berkas->motivasi_video_tiktok) : '';
                                
                                $isUtamaYoutube = (bool) preg_match('/(youtube\.com|youtu\.be)/i', $vUtama);
                                $isUtamaDrive = (bool) preg_match('/drive\.google\.com/i', $vUtama);
                                $isUtamaIg = (bool) preg_match('/instagram\.com/i', $vUtama);
                                $isUtamaTiktok = (bool) preg_match('/tiktok\.com/i', $vUtama);

                                $noWa = $peserta ? ($peserta->no_whatsapp ?: $peserta->no_hp) : null;
                                if ($noWa) {
                                    $waClean = preg_replace('/[^0-9]/', '', $noWa);
                                    if (str_starts_with($waClean, '0')) {
                                        $waClean = '62' . substr($waClean, 1);
                                    }
                                } else {
                                    $waClean = null;
                                }
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- No -->
                                <td class="py-3.5 px-4 text-center font-bold text-slate-400">
                                    {{ $pendaftars->firstItem() + $index }}
                                </td>

                                <!-- Calon Mahasiswa -->
                                <td class="py-3.5 px-4 min-w-[200px]">
                                    <div class="space-y-0.5">
                                        <p class="font-extrabold text-slate-900 text-xs sm:text-sm hover:text-indigo-600 transition-colors">
                                            {{ $akun->nama }}
                                        </p>
                                        <div class="flex items-center gap-1.5 text-slate-500 text-[11px]">
                                            <span class="iconify text-xs text-slate-400 shrink-0" data-icon="solar:letter-bold"></span>
                                            <span class="truncate">{{ $akun->email }}</span>
                                        </div>
                                        @if($noWa)
                                            <div class="flex items-center gap-1 text-[11px] text-emerald-700 font-semibold">
                                                <span class="iconify text-xs shrink-0" data-icon="logos:whatsapp-icon"></span>
                                                <a href="https://wa.me/{{ $waClean }}" target="_blank" rel="noopener noreferrer" class="hover:underline">
                                                    {{ $noWa }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Asal Sekolah & Prodi -->
                                <td class="py-3.5 px-4 min-w-[200px]">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1 text-slate-800 font-bold text-xs">
                                            <span class="iconify text-sm text-slate-400 shrink-0" data-icon="solar:buildings-2-bold"></span>
                                            <span class="truncate">{{ $daftar->asal_sekolah ?? '-' }}</span>
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-medium">
                                            @if($peserta && $peserta->pilihan_prodi)
                                                <span class="inline-block px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-[10px]">
                                                    {{ $peserta->pilihan_prodi }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 italic">Belum memilih prodi</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Video Motivasi Utama -->
                                <td class="py-3.5 px-4 min-w-[220px]">
                                    @if($vUtama)
                                        <div class="space-y-1.5">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                @if($isUtamaYoutube)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-black">
                                                        <span class="iconify text-xs" data-icon="logos:youtube-icon"></span>
                                                        <span>YouTube</span>
                                                    </span>
                                                @elseif($isUtamaDrive)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-black">
                                                        <span class="iconify text-xs" data-icon="logos:google-drive"></span>
                                                        <span>Google Drive</span>
                                                    </span>
                                                @elseif($isUtamaIg)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 border border-purple-200 text-[10px] font-black">
                                                        <span class="iconify text-xs" data-icon="logos:instagram-icon"></span>
                                                        <span>Instagram</span>
                                                    </span>
                                                @elseif($isUtamaTiktok)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-800 border border-slate-300 text-[10px] font-black">
                                                        <span class="iconify text-xs" data-icon="logos:tiktok-icon"></span>
                                                        <span>TikTok</span>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-black">
                                                        <span class="iconify text-xs" data-icon="solar:link-bold"></span>
                                                        <span>Web Link</span>
                                                    </span>
                                                @endif

                                                <span class="text-[9px] text-emerald-600 font-bold flex items-center gap-0.5">
                                                    <span class="iconify text-xs" data-icon="solar:check-circle-bold"></span>
                                                    <span>Tersedia</span>
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-1.5">
                                                <a href="{{ $vUtama }}" target="_blank" rel="noopener noreferrer" 
                                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] transition-colors">
                                                    <span class="iconify text-xs" data-icon="solar:arrow-right-up-linear"></span>
                                                    <span>Buka Link</span>
                                                </a>
                                                
                                                <button type="button" 
                                                        onclick="copyTextToClipboard('{{ addslashes($vUtama) }}')" 
                                                        title="Salin Link"
                                                        class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                                                    <span class="iconify text-sm" data-icon="solar:copy-bold"></span>
                                                </button>

                                                @if($isUtamaYoutube)
                                                    <button type="button" 
                                                            onclick="previewYouTubeModal('{{ addslashes($vUtama) }}', '{{ addslashes($akun->nama) }}')"
                                                            title="Tonton Preview YouTube"
                                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-rose-500 hover:bg-rose-600 text-white font-bold text-[10px] transition-all cursor-pointer shadow-xs">
                                                        <span class="iconify text-xs" data-icon="solar:play-bold"></span>
                                                        <span>Preview</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-400 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                            <span>Belum Ada</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Video TikTok -->
                                <td class="py-3.5 px-4 min-w-[200px]">
                                    @if($vTiktok)
                                        <div class="space-y-1.5">
                                            <div class="flex items-center gap-1.5">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-900 text-white text-[10px] font-black">
                                                    <span class="iconify text-xs" data-icon="logos:tiktok-icon"></span>
                                                    <span>TikTok</span>
                                                </span>
                                                <span class="text-[9px] text-emerald-600 font-bold flex items-center gap-0.5">
                                                    <span class="iconify text-xs" data-icon="solar:check-circle-bold"></span>
                                                    <span>Tersedia</span>
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-1.5">
                                                <a href="{{ $vTiktok }}" target="_blank" rel="noopener noreferrer" 
                                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-[11px] transition-colors">
                                                    <span class="iconify text-xs" data-icon="solar:arrow-right-up-linear"></span>
                                                    <span>Buka TikTok</span>
                                                </a>

                                                <button type="button" 
                                                        onclick="copyTextToClipboard('{{ addslashes($vTiktok) }}')" 
                                                        title="Salin Link TikTok"
                                                        class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                                                    <span class="iconify text-sm" data-icon="solar:copy-bold"></span>
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-400 text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                            <span>Belum Ada</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Status Kelengkapan -->
                                <td class="py-3.5 px-4 text-center">
                                    @if($vUtama && $vTiktok)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black border border-emerald-200">
                                            <span class="iconify text-xs text-emerald-600" data-icon="solar:check-circle-bold"></span>
                                            <span>Lengkap (2 Video)</span>
                                        </span>
                                    @elseif($vUtama)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 text-[10px] font-black border border-indigo-200">
                                            <span>Video Utama Saja</span>
                                        </span>
                                    @elseif($vTiktok)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 text-[10px] font-black border border-rose-200">
                                            <span>TikTok Saja</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold">
                                            Belum Mengisi
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('admin.pendaftar.show', $akun->id) }}" 
                                       title="Lihat Profil Lengkap"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition-all">
                                        <span>Detail</span>
                                        <span class="iconify text-xs" data-icon="solar:alt-arrow-right-linear"></span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-5 py-4 border-t border-slate-200 bg-slate-50/50">
                {{ $pendaftars->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Preview YouTube -->
<div id="youtubeModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-3xl w-full overflow-hidden shadow-2xl border border-slate-700 animate-in fade-in zoom-in-95 duration-200">
        <div class="p-4 sm:p-5 bg-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="iconify text-2xl text-rose-500 shrink-0" data-icon="logos:youtube-icon"></span>
                <div class="min-w-0">
                    <h3 id="youtubeModalTitle" class="text-sm font-extrabold truncate">Preview Video Motivasi</h3>
                    <p class="text-[10px] text-slate-400">Putar langsung video motivasi peserta beasiswa</p>
                </div>
            </div>
            <button onclick="closeYouTubeModal()" 
                    class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition-colors cursor-pointer">
                <span class="iconify text-lg" data-icon="solar:close-circle-bold"></span>
            </button>
        </div>
        
        <div class="relative w-full pb-[56.25%] bg-black">
            <iframe id="youtubeIframe" 
                    class="absolute inset-0 w-full h-full" 
                    src="" 
                    title="YouTube video player" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    allowfullscreen>
            </iframe>
        </div>

        <div class="p-3.5 bg-slate-50 flex items-center justify-between text-xs border-t border-slate-200">
            <span class="text-slate-500 text-[11px]">Bermasalah saat memutar? Silakan buka link langsung di tab baru.</span>
            <a id="youtubeDirectLink" href="#" target="_blank" rel="noopener noreferrer" 
               class="inline-flex items-center gap-1 font-bold text-indigo-600 hover:underline text-xs">
                <span>Buka di YouTube</span>
                <span class="iconify text-xs" data-icon="solar:arrow-right-up-linear"></span>
            </a>
        </div>
    </div>
</div>

<script>
function copyTextToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Tersalin!', 'Link berhasil disalin ke clipboard.');
        }).catch(() => fallbackCopy(text));
    } else {
        fallbackCopy(text);
    }
}

function fallbackCopy(text) {
    const el = document.createElement('textarea');
    el.value = text;
    el.style.position = 'fixed';
    el.style.left = '-999999px';
    document.body.appendChild(el);
    el.select();
    try {
        document.execCommand('copy');
        showToast('Tersalin!', 'Link berhasil disalin ke clipboard.');
    } catch (e) {
        prompt('Salin link berikut:', text);
    }
    document.body.removeChild(el);
}

function showToast(title, message) {
    if (window.Swal) {
        Swal.fire({
            icon: 'success',
            title: title,
            text: message,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } else {
        alert(message);
    }
}

function extractYouTubeId(url) {
    if (!url) return null;
    const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|shorts\/))([\w-]{11})/);
    return match ? match[1] : null;
}

function previewYouTubeModal(url, namaPeserta) {
    const videoId = extractYouTubeId(url);
    if (!videoId) {
        window.open(url, '_blank');
        return;
    }
    document.getElementById('youtubeModalTitle').textContent = 'Video Motivasi: ' + namaPeserta;
    document.getElementById('youtubeIframe').src = 'https://www.youtube-nocookie.com/embed/' + videoId + '?autoplay=1&rel=0';
    document.getElementById('youtubeDirectLink').href = url;
    document.getElementById('youtubeModal').classList.remove('hidden');
}

function closeYouTubeModal() {
    document.getElementById('youtubeModal').classList.add('hidden');
    document.getElementById('youtubeIframe').src = '';
}

document.getElementById('youtubeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeYouTubeModal();
    }
});
</script>
@endsection

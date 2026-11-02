@extends('pendaftar.layout')

@section('content')
<div class="space-y-4 sm:space-y-6">

    <!-- Conditional Notice 1: Pendaftaran Ditutup Banner (Hanya muncul jika memang ditutup) -->
    @if(!\App\Models\PengaturanPendaftaran::aktif())
    <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-900 border border-slate-800 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-400/10 border border-amber-400/20 text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <div>
                <h2 class="text-[11px] sm:text-xs font-black uppercase tracking-wider text-amber-400">Periode Pendaftaran Telah Berakhir</h2>
                <p class="text-[11px] sm:text-xs text-slate-300 font-medium mt-0.5">Sistem penerimaan berkas baru telah ditutup. Silakan tunggu informasi jadwal ujian.</p>
            </div>
        </div>
        <a href="#activity-center" class="shrink-0 text-xs font-bold text-slate-300 hover:text-white underline underline-offset-4 transition-colors">
            Pantau Aktivitas &rarr;
        </a>
    </div>
    @endif

    <!-- Conditional Notice 2: Mapel Pendukung Belum Lengkap -->
    @if(!$hasSupportingSubject)
    <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-blue-50/80 border border-blue-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-600/10 border border-blue-600/20 text-blue-700 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h2 class="text-[11px] sm:text-xs font-black uppercase tracking-wider text-slate-900">Mata Pelajaran Pendukung Belum Lengkap</h2>
                <p class="text-[11px] sm:text-xs text-slate-600 font-medium mt-0.5">Silakan lengkapi minimal 2 mapel pendukung wajib pada menu Nilai Rapor.</p>
            </div>
        </div>
        <a href="{{ route('pendaftar.nilai') }}" 
           class="shrink-0 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all shadow-xs active:scale-[0.98]">
            <span>Lengkapi Nilai</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
    @endif

    <!-- Hero Candidate Status Card (Grand Navy & Royal Blue Theme - Mobile Friendly) -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br from-[#0B1120] via-[#0F172A] to-[#1E293B] text-white p-5 sm:p-7 md:p-8 border border-slate-800 shadow-xl">
        <!-- Subtle Ambient Lighting -->
        <div class="absolute -top-32 -left-32 w-80 h-80 rounded-full bg-blue-600/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 rounded-full bg-amber-400/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6 lg:gap-8">
            
            <!-- Left Info Area -->
            <div class="space-y-3 max-w-2xl text-center lg:text-left">
                <!-- Status Pill -->
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-[10px] sm:text-[11px] font-bold text-slate-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span class="tracking-wide">PORTAL RESMI MFLS 2026</span>
                </div>

                <div class="space-y-1">
                    <h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight">
                        Selamat Datang, {{ $peserta->nama }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-300 font-medium leading-relaxed max-w-xl">
                        Pantau seluruh perkembangan pendaftaran, kelengkapan berkas seleksi, dan akses ujian online Anda di sini.
                    </p>
                </div>

                <!-- Profile Meta Badges -->
                <div class="pt-1 flex flex-wrap items-center justify-center lg:justify-start gap-2 text-xs font-semibold">
                    <div class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-slate-300 text-[11px]">
                        NISN: <span class="font-bold text-white">{{ $peserta->nisn }}</span>
                    </div>
                    <div class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-slate-300 text-[11px] max-w-[200px] truncate">
                        Sekolah: <span class="font-bold text-white">{{ $peserta->nama_sekolah }}</span>
                    </div>
                    <div class="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-slate-300 text-[11px]">
                        Lulusan: <span class="font-bold text-white">{{ $peserta->tahun_lulus }}</span>
                    </div>

                    @if(($peserta->daftar->status ?? '') == 'lulus')
                        <div class="px-2.5 py-1 rounded-lg bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 flex items-center gap-1 text-[11px]">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Lolos Berkas</span>
                        </div>
                    @else
                        <div class="px-2.5 py-1 rounded-lg bg-blue-500/15 border border-blue-500/30 text-blue-300 flex items-center gap-1 text-[11px]">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                            <span>Proses Seleksi</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Circular Readiness Telemetry -->
            <div class="shrink-0 flex flex-col items-center">
                <div class="relative w-28 h-28 sm:w-36 sm:h-36 flex items-center justify-center">
                    <!-- SVG Circular Meter -->
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                        <!-- Background Circle Track -->
                        <circle cx="50" cy="50" r="42" 
                                fill="transparent" 
                                stroke="#1e293b" 
                                stroke-width="7" />
                        <!-- Active Progress Track -->
                        <circle cx="50" cy="50" r="42" 
                                fill="transparent" 
                                stroke="#0870B8" 
                                stroke-width="7"
                                stroke-dasharray="264"
                                stroke-dashoffset="{{ 264 - (264 * $progress / 100) }}"
                                stroke-linecap="round"
                                class="transition-all duration-1000 ease-out" />
                    </svg>

                    <!-- Center Label -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                        <span class="text-2xl sm:text-3xl font-black text-white tracking-tight tabular-nums">
                            {{ $progress }}%
                        </span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                            Kesiapan
                        </span>
                    </div>
                </div>
                <span class="text-[11px] font-semibold text-slate-400 mt-1 text-center">
                    Kelengkapan Berkas
                </span>
            </div>

        </div>
    </div>

    <!-- Quick Access Card to Ujian Online (CBT) -->
    <a href="{{ route('ujian.index') }}" 
       class="group block p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 hover:border-blue-400 hover:shadow-md transition-all duration-200">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-blue-50 border border-blue-200/70 text-blue-600 flex items-center justify-center shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors">
                            Ujian Seleksi Online (CBT)
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[9px] font-black uppercase tracking-wider border border-blue-200">
                            CBT Mandiri
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                        Akses modul ujian seleksi beasiswa dan status pengerjaan soal.
                    </p>
                </div>
            </div>
            <div class="shrink-0 flex items-center gap-1.5 text-xs font-extrabold text-blue-600 group-hover:text-blue-700">
                <span>Buka Ruang Ujian</span>
                <svg class="w-3.5 h-3.5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </div>
    </a>

    <!-- 4 Quick Stats / Navigation Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
        @php
            $navCards = [
                [
                    'route' => 'pendaftar.biodata',
                    'title' => 'Biodata Diri',
                    'desc' => 'Profil & Sekolah',
                    'icon' => 'solar:user-circle-bold-duotone',
                ],
                [
                    'route' => 'pendaftar.nilai',
                    'title' => 'Nilai Rapor',
                    'desc' => 'Semester 1 s/d 5',
                    'icon' => 'solar:chart-square-bold-duotone',
                ],
                [
                    'route' => 'pendaftar.berkas',
                    'title' => 'Upload Berkas',
                    'desc' => 'Esai & Sertifikat',
                    'icon' => 'solar:cloud-upload-bold-duotone',
                ],
                [
                    'route' => 'pendaftar.twibbon',
                    'title' => 'Twibbon',
                    'desc' => 'Kampanye Sosial',
                    'icon' => 'solar:gallery-wide-bold-duotone',
                ],
            ];
        @endphp

        @foreach($navCards as $c)
            <a href="{{ route($c['route']) }}" 
               class="group p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-white border border-slate-200/80 hover:border-blue-400 hover:shadow-sm transition-all duration-200 flex flex-col justify-between min-h-[95px] sm:min-h-[110px]">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <span class="iconify text-base sm:text-lg" data-icon="{{ $c['icon'] }}"></span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors truncate">
                        {{ $c['title'] }}
                    </h3>
                    <p class="text-[10px] sm:text-[11px] font-semibold text-slate-400 mt-0.5 truncate">
                        {{ $c['desc'] }}
                    </p>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Two-Column Layout: Activity Timeline & Resources Hub -->
    <div id="activity-center" class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 pt-1">
        
        <!-- Left: Activity Timeline (2 Cols) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-white border border-slate-200/80 shadow-xs">
                
                <!-- Section Header -->
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4 sm:mb-5">
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">
                            Pusat Aktivitas Akun
                        </h2>
                        <p class="text-[11px] text-slate-500 font-medium">
                            Riwayat pembaruan berkas dan status akun pendaftar Anda.
                        </p>
                    </div>
                    <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600 text-[11px] font-bold">
                        {{ count($history) }} Aktivitas
                    </span>
                </div>

                <!-- Timeline Logs -->
                <div class="relative pl-5 sm:pl-6 space-y-4 sm:space-y-5 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @forelse($history as $item)
                        <div class="relative group">
                            <!-- Bullet Marker -->
                            <div class="absolute -left-[17px] sm:-left-[19px] top-1.5 w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-white border-2 border-blue-600 shadow-xs group-hover:scale-125 transition-transform"></div>

                            <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100 hover:bg-white hover:border-slate-200 transition-all">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-0.5 sm:gap-2">
                                    <h3 class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                                        {{ $item['title'] }}
                                    </h3>
                                    <span class="text-[9px] sm:text-[10px] font-semibold text-slate-400 shrink-0">
                                        {{ $item['date'] }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                                    {{ $item['desc'] }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <p class="text-xs font-semibold text-slate-400">Belum ada riwayat aktivitas yang tercatat.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>

        <!-- Right: Resources & Official Support (1 Col) -->
        <div class="space-y-4">
            
            <!-- Community & Official Channels -->
            <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-white border border-slate-200/80 shadow-xs space-y-3">
                <div>
                    <h2 class="text-xs sm:text-sm font-extrabold text-slate-900 tracking-tight">
                        Kanal Informasi Resmi
                    </h2>
                    <p class="text-[11px] text-slate-500 font-medium">
                        Bergabung ke saluran resmi pengumuman jadwal seleksi.
                    </p>
                </div>

                <div class="space-y-2 pt-1">
                    <!-- WhatsApp Group Link -->
                    <a href="https://whatsapp.com/channel/0029VbC0L7I6hENrmgrr9D3D" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50/60 border border-slate-200/60 hover:border-blue-200 transition-all group">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                            <span class="iconify text-base" data-icon="solar:chat-round-dots-bold"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition-colors truncate">Saluran WhatsApp MFLS</p>
                            <p class="text-[9px] text-slate-400 font-medium truncate">Update pengumuman resmi</p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>

                    <!-- Instagram Link -->
                    <a href="https://instagram.com/beasiswamncu" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50/60 border border-slate-200/60 hover:border-blue-200 transition-all group">
                        <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center shrink-0">
                            <span class="iconify text-base" data-icon="solar:camera-bold"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition-colors truncate">Instagram @beasiswamncu</p>
                            <p class="text-[9px] text-slate-400 font-medium truncate">Dokumentasi para awardee</p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Helpdesk Card -->
            <div class="p-4 sm:p-5 rounded-2xl sm:rounded-3xl bg-slate-900 text-white space-y-3 shadow-xs border border-slate-800">
                <div class="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div class="space-y-0.5">
                    <h3 class="text-xs sm:text-sm font-extrabold text-white">Butuh Bantuan Teknis?</h3>
                    <p class="text-[11px] text-slate-300 font-medium leading-relaxed">
                        Tim panitia seleksi siap membantu kendala formulir dan berkas Anda.
                    </p>
                </div>
                <a href="https://wa.me/6285880059189" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="inline-flex items-center justify-center gap-1.5 w-full py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all shadow-xs active:scale-[0.98]">
                    <span>Hubungi via WhatsApp</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>

        </div>

    </div>

</div>
@endsection

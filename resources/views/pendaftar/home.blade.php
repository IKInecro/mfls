@extends('layouts.user')

@section('content')
@php
    $buka = \App\Models\PengaturanPendaftaran::aktif();
    $deadlineDate = $pengaturan->pendaftaran_selesai 
        ? $pengaturan->pendaftaran_selesai->format('Y-m-d\TH:i:s') . '+07:00' 
        : '2026-05-04T23:59:59+07:00';
@endphp

<style>
/* Smooth Floating & Parallax Keyframes */
@keyframes floatSlow {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-12px) rotate(1deg); }
}
@keyframes floatDelayed {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(10px) rotate(-1deg); }
}
@keyframes pulseGlow {
    0%, 100% { opacity: 0.35; transform: scale(1); }
    50% { opacity: 0.65; transform: scale(1.08); }
}
.animate-float-slow {
    animation: floatSlow 6s ease-in-out infinite;
}
.animate-float-delayed {
    animation: floatDelayed 7s ease-in-out infinite;
}
.animate-pulse-glow {
    animation: pulseGlow 8s ease-in-out infinite;
}

/* Perspective 3D Tilt Card */
.perspective-container {
    perspective: 1200px;
}
.tilt-card {
    transition: transform 0.25s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.3s ease;
    transform-style: preserve-3d;
}

/* Reveal on Scroll Elements */
.reveal-init {
    opacity: 0;
    transform: translateY(32px);
    transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
}
.reveal-init.revealed {
    opacity: 1;
    transform: translateY(0);
}
</style>

<!-- Hero Section with Mouse Parallax & 3D Artwork Showcase -->
<section id="home" class="relative overflow-hidden bg-gradient-to-b from-slate-50 via-white to-slate-50/60 pt-6 pb-16 lg:pt-14 lg:pb-28 border-b border-slate-100">
    <!-- Ambient Parallax Glows -->
    <div id="ambient-glow-1" class="absolute -top-32 -left-28 w-96 h-96 sm:w-[32rem] sm:h-[32rem] bg-blue-400/15 rounded-full blur-3xl pointer-events-none animate-pulse-glow"></div>
    <div id="ambient-glow-2" class="absolute top-1/3 -right-28 w-80 h-80 sm:w-[28rem] sm:h-[28rem] bg-amber-400/15 rounded-full blur-3xl pointer-events-none animate-pulse-glow" style="animation-delay: 3s;"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
            
            <!-- Left Column: Primary Copy & Action (7 Cols) -->
            <div class="lg:col-span-7 text-center lg:text-left space-y-6">
                
                <!-- Status Pill -->
                <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full text-xs sm:text-sm font-semibold tracking-wide shadow-sm {{ $buka ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                    <span class="w-2.5 h-2.5 rounded-full {{ $buka ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                    @if($buka)
                        <span>Pendaftaran Dibuka (MFLS Ajaran 2027) @if($pengaturan->pendaftaran_selesai) s/d {{ $pengaturan->pendaftaran_selesai->format('d M Y, H:i') }} WIB @endif</span>
                    @else
                        <span>Pendaftaran Periode Ini Telah Ditutup</span>
                    @endif
                </div>

                <!-- Main Headline -->
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.12]">
                    MNCU <span class="text-primary-blue">Future Leader</span> <br class="hidden sm:inline">
                    <span class="text-amber-500 relative inline-block">
                        Scholarship Ajaran 2027
                        <svg class="absolute -bottom-1.5 left-0 w-full h-2.5 text-amber-300/40" viewBox="0 0 100 10" preserveAspectRatio="none"><path d="M0 5 Q 50 10 100 5" stroke="currentColor" stroke-width="4" fill="none"/></svg>
                    </span>
                </h1>

                <!-- Subheadline -->
                <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-normal">
                    Program beasiswa kuliah unggulan jenjang sarjana (S1) dengan dukungan biaya pendidikan <strong class="text-slate-900 font-semibold">hingga 100% sampai 8 semester</strong> di Media Nusantara Citra University. Dibina langsung praktisi industri dalam ekosistem <strong class="text-primary-blue font-semibold">MNC Group</strong> untuk mencetak calon pemimpin bangsa yang berdaya saing global.
                </p>

                <!-- Action CTAs -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 sm:gap-4 pt-2">
                    @if($buka)
                        <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl text-base font-bold text-white bg-primary-blue hover:bg-primary-blue-hover shadow-lg shadow-blue-900/20 hover:shadow-xl hover:shadow-blue-900/30 transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0">
                            <span>Daftar Beasiswa Sekarang</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @else
                        <a href="#timeline" onclick="showPendaftaranTutupModal(event)" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl text-base font-bold text-slate-600 bg-slate-200 hover:bg-slate-300 transition-colors cursor-pointer shadow-xs">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Pendaftaran Ditutup</span>
                        </a>
                    @endif

                    <a href="https://whatsapp.com/channel/0029VbC0L7I6hENrmgrr9D3D" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-base font-semibold text-slate-700 bg-white border border-slate-200 hover:border-slate-300 hover:bg-slate-50 shadow-sm transition-all duration-200">
                        <span class="iconify text-emerald-600 text-lg" data-icon="solar:chat-round-dots-bold"></span>
                        <span>Gabung Saluran WhatsApp</span>
                    </a>
                </div>

                <!-- Fast Highlights -->
                <div class="pt-4 border-t border-slate-200/80 grid grid-cols-3 gap-4 max-w-xl mx-auto lg:mx-0 text-left">
                    <div>
                        <div class="text-xl sm:text-2xl font-black text-slate-900">100%</div>
                        <div class="text-xs text-slate-500 font-medium leading-snug">Beasiswa Hingga Lulus</div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-black text-slate-900">8 Prodi</div>
                        <div class="text-xs text-slate-500 font-medium leading-snug">Pilihan Jurusan S1</div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-black text-slate-900">MNC Group</div>
                        <div class="text-xs text-slate-500 font-medium leading-snug">Jaringan Industri & Karir</div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Original Artwork (newlogo.webp) with 3D Parallax & Floating Glass Badges -->
            <div class="lg:col-span-5 relative perspective-container" id="hero-artwork-area">
                <div class="relative max-w-md mx-auto tilt-card" id="hero-tilt-card">
                    
                    <!-- Decorative Radial Halo -->
                    <div class="absolute -inset-4 bg-gradient-to-tr from-primary-blue/20 via-amber-400/20 to-blue-500/10 rounded-[2.5rem] blur-2xl opacity-75"></div>

                    <!-- Main Artwork Container using the exact newlogo.webp requested by user -->
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white/90 bg-white group">
                        <img id="hero-main-img" src="{{ asset('icon/newlogo.webp') }}" alt="MNCU Future Leader Scholarship Poster" class="w-full h-auto object-cover transform group-hover:scale-[1.03] transition-transform duration-700 ease-out">
                    </div>

                    <!-- Floating Parallax Badge 1 (Top Left) -->
                    <div class="absolute -top-5 -left-4 sm:-left-8 bg-white/95 backdrop-blur-md rounded-2xl p-3 sm:p-3.5 shadow-xl border border-slate-100 flex items-center gap-3 animate-float-slow z-20">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                            <span class="iconify text-2xl" data-icon="solar:medal-ribbons-star-bold"></span>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-500 font-medium">Kategori Beasiswa</div>
                            <div class="text-xs sm:text-sm font-bold text-slate-900">Penuh Up To 100%</div>
                        </div>
                    </div>

                    <!-- Floating Parallax Badge 2 (Bottom Right) -->
                    <div class="absolute -bottom-5 -right-4 sm:-right-8 bg-white/95 backdrop-blur-md rounded-2xl p-3 sm:p-3.5 shadow-xl border border-slate-100 flex items-center gap-3 animate-float-delayed z-20">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-primary-blue flex items-center justify-center shrink-0">
                            <span class="iconify text-2xl" data-icon="solar:users-group-rounded-bold"></span>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-500 font-medium">Praktisi & Eksekutif</div>
                            <div class="text-xs sm:text-sm font-bold text-slate-900">Mentorship BoD MNC</div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- Countdown & Urgency Telemetry Panel -->
<section class="relative bg-gradient-to-r from-[#021b44] via-[#022868] to-[#011638] text-white py-8 border-y border-blue-950/50 shadow-inner">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
            
            <!-- Left Info -->
            <div class="text-center lg:text-left space-y-1">
                <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-amber-400">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                    <span>Tenggat Waktu Pendaftaran TA 2027</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                    Pendaftaran Ditutup Dalam Waktu:
                </h2>
                <p class="text-xs sm:text-sm text-blue-200">
                    Lengkapi pengisian data dan berkas persyaratan sebelum sistem ditutup.
                </p>
            </div>

            <!-- Tabular Digits Countdown -->
            <div class="grid grid-cols-4 gap-2 sm:gap-4 text-center">
                <div class="bg-white/10 border border-white/15 rounded-xl px-3 sm:px-5 py-2.5 backdrop-blur-md min-w-[70px] sm:min-w-[85px] shadow-lg">
                    <div id="days" class="text-2xl sm:text-4xl font-black font-mono text-amber-400 tabular-nums">00</div>
                    <div class="text-[10px] sm:text-xs font-semibold text-blue-200 uppercase tracking-wider mt-0.5">Hari</div>
                </div>
                <div class="bg-white/10 border border-white/15 rounded-xl px-3 sm:px-5 py-2.5 backdrop-blur-md min-w-[70px] sm:min-w-[85px] shadow-lg">
                    <div id="hours" class="text-2xl sm:text-4xl font-black font-mono text-amber-400 tabular-nums">00</div>
                    <div class="text-[10px] sm:text-xs font-semibold text-blue-200 uppercase tracking-wider mt-0.5">Jam</div>
                </div>
                <div class="bg-white/10 border border-white/15 rounded-xl px-3 sm:px-5 py-2.5 backdrop-blur-md min-w-[70px] sm:min-w-[85px] shadow-lg">
                    <div id="minutes" class="text-2xl sm:text-4xl font-black font-mono text-amber-400 tabular-nums">00</div>
                    <div class="text-[10px] sm:text-xs font-semibold text-blue-200 uppercase tracking-wider mt-0.5">Menit</div>
                </div>
                <div class="bg-white/10 border border-white/15 rounded-xl px-3 sm:px-5 py-2.5 backdrop-blur-md min-w-[70px] sm:min-w-[85px] shadow-lg">
                    <div id="seconds" class="text-2xl sm:text-4xl font-black font-mono text-amber-400 tabular-nums">00</div>
                    <div class="text-[10px] sm:text-xs font-semibold text-blue-200 uppercase tracking-wider mt-0.5">Detik</div>
                </div>
            </div>

            <!-- Fast Action Button -->
            <div>
                @if($buka)
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold text-slate-900 bg-amber-400 hover:bg-amber-300 shadow-lg shadow-amber-400/20 transition-all hover:scale-105 active:scale-95">
                        <span>Lengkapi Berkas Sekarang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <a href="#timeline" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold text-slate-300 bg-blue-950/60 border border-blue-400/20">
                        <span>Lihat Jadwal Seleksi</span>
                    </a>
                @endif
            </div>

        </div>
    </div>
</section>

<!-- About Section: Leadership Ecosystem & Campus Life -->
<section id="about" class="py-20 lg:py-28 bg-white reveal-init">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
            
            <!-- Left: Text Content & Value Proposition (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">
                <span class="inline-block text-xs font-bold uppercase tracking-wider text-primary-blue bg-blue-50 border border-blue-100 px-3.5 py-1.5 rounded-full">
                    Tentang Program MFLS
                </span>
                
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Mencetak Generasi Emas Pemimpin Masa Depan di Bawah Naungan MNC Group
                </h2>

                <div class="space-y-4 text-slate-600 text-base sm:text-lg leading-relaxed">
                    <p>
                        <strong class="text-slate-900">MNCU Future Leader Scholarship (MFLS)</strong> adalah program beasiswa unggulan resmi yang diselenggarakan oleh MNC Group untuk calon mahasiswa berprestasi yang melanjutkan studi sarjana di Media Nusantara Citra University (MNC University).
                    </p>
                    <p>
                        Melalui beasiswa ini, penerima beasiswa tidak hanya mendapatkan dukungan finansial kuliah hingga 100%, tetapi juga akses ke lingkungan pendidikan terintegrasi langsung dengan ekosistem industri, khususnya di bidang bisnis, keuangan, media, teknologi informasi, dan industri kreatif.
                    </p>
                </div>

                <!-- 4 Institutional Pillars -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-primary-blue/30 transition-colors">
                        <div class="text-3xl font-black text-primary-blue">2</div>
                        <div class="text-xs font-semibold text-slate-700 uppercase tracking-wide mt-1">Kampus Strategis</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Menteng & Kebon Jeruk</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-primary-blue/30 transition-colors">
                        <div class="text-3xl font-black text-primary-blue">2</div>
                        <div class="text-xs font-semibold text-slate-700 uppercase tracking-wide mt-1">Fakultas Unggulan</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Bisnis & Industri Kreatif</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-primary-blue/30 transition-colors">
                        <div class="text-3xl font-black text-primary-blue">8</div>
                        <div class="text-xs font-semibold text-slate-700 uppercase tracking-wide mt-1">Program Studi</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Jenjang Sarjana (S1)</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-primary-blue/30 transition-colors">
                        <div class="text-3xl font-black text-primary-blue">90+</div>
                        <div class="text-xs font-semibold text-slate-700 uppercase tracking-wide mt-1">Mitra Industri</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Nasional & Global</div>
                    </div>
                </div>

                <!-- Link to Program Studi -->
                <div class="pt-2">
                    <a href="#program" class="inline-flex items-center gap-2 font-bold text-primary-blue hover:text-blue-800 transition-colors">
                        <span>Lihat 8 Program Studi Pilihan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                </div>
            </div>

            <!-- Right: Campus Life & Seminar Photo Mosaic with Parallax Depth -->
            <div class="lg:col-span-5">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div class="rounded-2xl overflow-hidden shadow-md border border-slate-100 group">
                            <img src="{{ asset('icon/Student1.webp') }}" alt="Aktivitas Mahasiswa MNC University" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                        <div class="rounded-2xl overflow-hidden shadow-md border border-slate-100 group">
                            <img src="{{ asset('icon/seminar2.webp') }}" alt="Seminar Kepemimpinan MNC Group" class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                    </div>
                    <div class="space-y-4 pt-6">
                        <div class="rounded-2xl overflow-hidden shadow-md border border-slate-100 group">
                            <img src="{{ asset('icon/student4.webp') }}" alt="Fasilitas Kampus MNC University" class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                        <div class="rounded-2xl overflow-hidden shadow-md border border-slate-100 group">
                            <img src="{{ asset('icon/group3.webp') }}" alt="Awardee MNC University" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Program Studi Section: Direct Interactive Filter (No Hidden Modals) -->
<section id="program" class="py-20 lg:py-28 bg-slate-50 border-t border-slate-200/80 reveal-init">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="inline-block text-xs font-bold uppercase tracking-wider text-primary-blue bg-blue-50 border border-blue-100 px-3.5 py-1.5 rounded-full mb-3">
                Pilihan Program Studi S1
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Pilih Program Studi Impian Anda
            </h2>
            <p class="text-base sm:text-lg text-slate-600 mt-3">
                Seluruh 8 program studi jenjang S1 terbuka untuk beasiswa penuh hingga 100%. Kurikulum relevan industri dan berbasis teknologi masa depan.
            </p>

            <!-- Quiz Recommendation Banner -->
            <div class="mt-6 inline-flex flex-wrap items-center justify-center gap-3 p-2 pl-4 bg-white rounded-full border border-slate-200 shadow-sm text-sm">
                <span class="text-slate-600 font-medium">Masih ragu menentukan pilihan jurusan?</span>
                <a href="{{ route('quiz.show') }}" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full font-bold text-xs uppercase tracking-wider text-white bg-primary-blue hover:bg-primary-blue-hover transition-colors shadow-sm">
                    <span class="iconify" data-icon="solar:magic-stick-3-bold"></span>
                    <span>Ikuti Quiz Rekomendasi Prodi</span>
                </a>
            </div>
        </div>

        <!-- Faculty Tabs -->
        <div class="flex justify-center mb-8">
            <div class="inline-flex p-1.5 bg-slate-200/80 rounded-2xl gap-1 text-sm font-semibold" id="prodi-filter-tabs">
                <button type="button" onclick="filterProdi('all', event)" class="prodi-tab-btn active px-5 py-2.5 rounded-xl transition-all duration-200 bg-white text-slate-900 shadow-sm">
                    Semua Program Studi (8)
                </button>
                <button type="button" onclick="filterProdi('fbk', event)" class="prodi-tab-btn px-5 py-2.5 rounded-xl transition-all duration-200 text-slate-600 hover:text-slate-900">
                    Bisnis & Keuangan (3)
                </button>
                <button type="button" onclick="filterProdi('fik', event)" class="prodi-tab-btn px-5 py-2.5 rounded-xl transition-all duration-200 text-slate-600 hover:text-slate-900">
                    Industri & Kreatif (5)
                </button>
            </div>
        </div>

        <!-- Program Studi Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="prodi-grid">
            
            <!-- 1. Manajemen -->
            <div class="prodi-card fbk bg-white rounded-2xl p-6 border border-slate-200/90 hover:border-primary-blue hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-primary-blue flex items-center justify-center group-hover:bg-primary-blue group-hover:text-white transition-colors">
                            <span class="iconify text-2xl" data-icon="solar:chart-square-bold"></span>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider bg-slate-100 px-2.5 py-1 rounded-md">FBK</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-primary-blue transition-colors">S1 Manajemen</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-4">
                        Mempelajari strategi kepemimpinan korporasi, manajemen finansial, serta kewirausahaan modern yang adaptif di era digital.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Fakultas Bisnis & Keuangan</span>
                    <span class="font-bold text-primary-blue">Beasiswa s/d 100%</span>
                </div>
            </div>

            <!-- 2. Akuntansi -->
            <div class="prodi-card fbk bg-white rounded-2xl p-6 border border-slate-200/90 hover:border-primary-blue hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-primary-blue flex items-center justify-center group-hover:bg-primary-blue group-hover:text-white transition-colors">
                            <span class="iconify text-2xl" data-icon="solar:wallet-money-bold"></span>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider bg-slate-100 px-2.5 py-1 rounded-md">FBK</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-primary-blue transition-colors">S1 Akuntansi</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-4">
                        Fokus pada analisis laporan keuangan, perpajakan, audit korporat, dan sistem informasi akuntansi berbasis automasi data.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Fakultas Bisnis & Keuangan</span>
                    <span class="font-bold text-primary-blue">Beasiswa s/d 100%</span>
                </div>
            </div>

            <!-- 3. Pendidikan Matematika -->
            <div class="prodi-card fbk bg-white rounded-2xl p-6 border border-slate-200/90 hover:border-primary-blue hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-primary-blue flex items-center justify-center group-hover:bg-primary-blue group-hover:text-white transition-colors">
                            <span class="iconify text-2xl" data-icon="solar:calculator-bold"></span>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider bg-slate-100 px-2.5 py-1 rounded-md">FBK</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-primary-blue transition-colors">S1 Pendidikan Matematika</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-4">
                        Mencetak pendidik matematika inovatif, analis kuantitatif, dan problem solver berdaya saing internasional.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Fakultas Bisnis & Keuangan</span>
                    <span class="font-bold text-primary-blue">Beasiswa s/d 100%</span>
                </div>
            </div>

            <!-- 4. Sains Komunikasi -->
            <div class="prodi-card fik bg-white rounded-2xl p-6 border border-slate-200/90 hover:border-primary-blue hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-slate-900 transition-colors">
                            <span class="iconify text-2xl" data-icon="solar:videocamera-record-bold"></span>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider bg-slate-100 px-2.5 py-1 rounded-md">FIK</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-primary-blue transition-colors">S1 Sains Komunikasi</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-4">
                        Kurikulum komprehensif penyiaran media (broadcasting), jurnalisme digital, dan public relations terhubung langsung ke MNC Media.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Fakultas Industri & Kreatif</span>
                    <span class="font-bold text-primary-blue">Beasiswa s/d 100%</span>
                </div>
            </div>

            <!-- 5. Desain Komunikasi Visual -->
            <div class="prodi-card fik bg-white rounded-2xl p-6 border border-slate-200/90 hover:border-primary-blue hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-slate-900 transition-colors">
                            <span class="iconify text-2xl" data-icon="solar:palette-round-bold"></span>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider bg-slate-100 px-2.5 py-1 rounded-md">FIK</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-primary-blue transition-colors">S1 Desain Komunikasi Visual</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-4">
                        Pengembangan talenta branding, animasi, visual storytelling, UI/UX, dan creative directing dalam ekosistem media nasional.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Fakultas Industri & Kreatif</span>
                    <span class="font-bold text-primary-blue">Beasiswa s/d 100%</span>
                </div>
            </div>

            <!-- 6. Ilmu Komputer -->
            <div class="prodi-card fik bg-white rounded-2xl p-6 border border-slate-200/90 hover:border-primary-blue hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-slate-900 transition-colors">
                            <span class="iconify text-2xl" data-icon="solar:code-square-bold"></span>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider bg-slate-100 px-2.5 py-1 rounded-md">FIK</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-primary-blue transition-colors">S1 Ilmu Komputer</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-4">
                        Fokus pada rekayasa perangkat lunak (software engineering), kecerdasan buatan (AI), data science, dan komputasi awan.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Fakultas Industri & Kreatif</span>
                    <span class="font-bold text-primary-blue">Beasiswa s/d 100%</span>
                </div>
            </div>

            <!-- 7. Sistem Informasi -->
            <div class="prodi-card fik bg-white rounded-2xl p-6 border border-slate-200/90 hover:border-primary-blue hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-slate-900 transition-colors">
                            <span class="iconify text-2xl" data-icon="solar:server-square-bold"></span>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider bg-slate-100 px-2.5 py-1 rounded-md">FIK</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-primary-blue transition-colors">S1 Sistem Informasi</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-4">
                        Mengintegrasikan teknologi informasi dengan arsitektur bisnis korporat, analitik data, dan transformasi digital.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Fakultas Industri & Kreatif</span>
                    <span class="font-bold text-primary-blue">Beasiswa s/d 100%</span>
                </div>
            </div>

            <!-- 8. Pendidikan Bahasa Inggris -->
            <div class="prodi-card fik bg-white rounded-2xl p-6 border border-slate-200/90 hover:border-primary-blue hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-slate-900 transition-colors">
                            <span class="iconify text-2xl" data-icon="solar:global-bold"></span>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider bg-slate-100 px-2.5 py-1 rounded-md">FIK</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-primary-blue transition-colors">S1 Pendidikan Bahasa Inggris</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-4">
                        Mempersiapkan komunikator dan pendidik bahasa Inggris profesional yang kompeten di tingkat global dan industri komunikasi.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Fakultas Industri & Kreatif</span>
                    <span class="font-bold text-primary-blue">Beasiswa s/d 100%</span>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- Exclusive Benefits Section (With Original 'keuntungan.webp' Asset) -->
<section id="benefit" class="py-20 lg:py-28 bg-white border-t border-slate-100 reveal-init">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-block text-xs font-bold uppercase tracking-wider text-primary-blue bg-blue-50 border border-blue-100 px-3.5 py-1.5 rounded-full mb-3">
                Benefit Eksklusif
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                Melangkah Lebih Jauh Bersama MNC University
            </h2>
            <p class="text-base sm:text-lg text-slate-600 mt-3">
                Keuntungan eksklusif bagi penerima beasiswa MFLS dalam mengembangkan potensi akademik dan kepemimpinan.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left: 5 Concrete Benefits (7 Cols) -->
            <div class="lg:col-span-7 space-y-4">
                
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 hover:border-primary-blue/30 hover:shadow-md transition-all flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-400 text-slate-900 flex items-center justify-center shrink-0 shadow-sm">
                        <span class="iconify text-2xl" data-icon="solar:wallet-money-bold"></span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-base">Dukungan Biaya Kuliah Hingga 100%</h4>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1">Mendapat keringanan biaya kuliah hingga potongan penuh (100%) sesuai kategori beasiswa selama masa studi sarjana.</p>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 hover:border-primary-blue/30 hover:shadow-md transition-all flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-primary-blue text-white flex items-center justify-center shrink-0 shadow-sm">
                        <span class="iconify text-2xl" data-icon="solar:users-group-rounded-bold"></span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-base">Program Kepemimpinan & Mentorship BoD MNC</h4>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1">Pelatihan leadership, personal branding, serta pembinaan langsung dari para eksekutif dan pimpinan praktisi MNC Group.</p>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 hover:border-primary-blue/30 hover:shadow-md transition-all flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-400 text-slate-900 flex items-center justify-center shrink-0 shadow-sm">
                        <span class="iconify text-2xl" data-icon="solar:buildings-bold"></span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-base">Sit In Class & Company Visit MNC Group</h4>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1">Belajar langsung di studio penyiaran, kantor redaksi, dan unit bisnis MNC Group untuk mendapatkan wawasan industri nyata.</p>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 hover:border-primary-blue/30 hover:shadow-md transition-all flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-primary-blue text-white flex items-center justify-center shrink-0 shadow-sm">
                        <span class="iconify text-2xl" data-icon="solar:case-round-minimalistic-bold"></span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-base">Peluang Magang & Fast-Track Rekrutmen</h4>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1">Mendapatkan pengalaman kerja profesional di divisi MNC Group sesuai prodi dan prioritas seleksi karir lulusan terbaik.</p>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 hover:border-primary-blue/30 hover:shadow-md transition-all flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-400 text-slate-900 flex items-center justify-center shrink-0 shadow-sm">
                        <span class="iconify text-2xl" data-icon="solar:diploma-verified-bold"></span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-base">Networking Nasional & Sertifikat Resmi</h4>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1">Membangun jejaring kolaborasi bersama siswa-siswi berprestasi se-Indonesia serta sertifikat penghargaan kehormatan resmi.</p>
                    </div>
                </div>

            </div>

            <!-- Right: Original 'keuntungan.webp' Graphic Asset (5 Cols) -->
            <div class="lg:col-span-5">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-slate-100 bg-white group">
                    <img src="{{ asset('icon/keuntungan.webp') }}" alt="Keuntungan Beasiswa MNCU" class="w-full h-auto object-contain group-hover:scale-105 transition-transform duration-500">
                </div>
            </div>

        </div>

    </div>
</section>

<!-- Scholarship Coverage Section (With Original 'scholarr.webp' Asset) -->
<section class="py-20 lg:py-28 bg-gradient-to-br from-[#021b44] via-[#022868] to-[#011638] text-white relative overflow-hidden reveal-init">
    <div class="absolute inset-0 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:24px_24px] opacity-15"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-block text-xs font-bold uppercase tracking-wider text-amber-400 bg-amber-400/10 border border-amber-400/20 px-3.5 py-1.5 rounded-full mb-3">
                Kategori Pembiayaan
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                Cakupan Beasiswa Masa Studi
            </h2>
            <p class="text-base sm:text-lg text-slate-300 mt-3">
                Transparansi skema pembiayaan pendidikan yang disiapkan untuk mendukung kelancaran studi Anda hingga wisuda.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left: 2 Tiers (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- Tier 1 -->
                <div class="p-6 sm:p-8 rounded-3xl bg-white/10 backdrop-blur-md border-2 border-amber-400/60 shadow-2xl relative">
                    <div class="absolute -top-3.5 right-6 bg-amber-400 text-slate-950 text-xs font-black uppercase tracking-wider px-3.5 py-1 rounded-full shadow-md">
                        Pilihan Utama
                    </div>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-400/20 text-amber-400 flex items-center justify-center shrink-0">
                            <span class="iconify text-3xl" data-icon="solar:crown-star-bold"></span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-white">Beasiswa Penuh (100%)</h3>
                            <p class="text-xs sm:text-sm text-slate-300">Bebas Biaya Pendidikan Hingga 8 Semester</p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-200 leading-relaxed">
                        Pembebasan biaya kuliah secara 100% penuh sampai selesai masa studi (8 semester), prioritas pembinaan karir, sit-in class unit bisnis, serta mentorship intensif BoD MNC Group.
                    </p>
                </div>

                <!-- Tier 2 -->
                <div class="p-6 sm:p-8 rounded-3xl bg-white/5 backdrop-blur-md border border-white/15 shadow-xl">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0">
                            <span class="iconify text-3xl" data-icon="solar:diploma-verified-bold"></span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-white">Beasiswa Parsial (75%)</h3>
                            <p class="text-xs sm:text-sm text-slate-300">Keringanan Biaya Pendidikan Signifikan</p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-200 leading-relaxed">
                        Pemotongan biaya pendidikan sebesar 75% sampai 8 semester dengan akses fasilitas akademik dan kesempatan mengikuti seluruh program kepemimpinan yang sama.
                    </p>
                </div>

            </div>

            <!-- Right: Original 'scholarr.webp' Graphic (5 Cols) -->
            <div class="lg:col-span-5 text-center">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white/20 bg-white/5 p-4 group">
                    <img src="{{ asset('icon/scholarr.webp') }}" alt="Scholar Portrait MNC University" class="w-full h-auto object-contain max-h-96 mx-auto group-hover:scale-105 transition-transform duration-500">
                </div>
            </div>

        </div>

    </div>
</section>

<!-- Timeline Section: Selection Journey Pipeline -->
<section id="timeline" class="py-20 lg:py-28 bg-white border-t border-slate-100 reveal-init">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-block text-xs font-bold uppercase tracking-wider text-primary-blue bg-blue-50 border border-blue-100 px-3.5 py-1.5 rounded-full mb-3">
                Jadwal & Tahapan Seleksi
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Tahapan Seleksi MFLS Ajaran 2027
            </h2>
            <p class="text-base sm:text-lg text-slate-600 mt-3">
                Ikuti seluruh rangkaian tahapan seleksi secara bertahap dan transparan hingga penetapan awardee resmi.
            </p>
        </div>

        @php
            $timelineSteps = $pengaturan->getTimelineOrDefault();
        @endphp

        <!-- Steps Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($timelineSteps as $item)
            <div class="relative rounded-2xl p-6 border transition-all duration-300 {{ !empty($item['active']) ? 'bg-blue-50/60 border-primary-blue shadow-lg ring-1 ring-primary-blue' : 'bg-white border-slate-200 hover:border-slate-300 hover:shadow-md' }}">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-black px-2.5 py-1 rounded-md {{ !empty($item['active']) ? 'bg-primary-blue text-white' : 'bg-slate-100 text-slate-700' }}">
                        TAHAP {{ $item['step'] }}
                    </span>
                    @if(!empty($item['active']))
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 bg-amber-100 px-2.5 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            Aktif
                        </span>
                    @else
                        <span class="text-xs text-slate-600 font-medium">{{ $item['status'] }}</span>
                    @endif
                </div>

                <h3 class="text-lg font-bold text-slate-900 mb-2 leading-snug">
                    {{ $item['title'] }}
                </h3>

                <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary-blue mb-3">
                    <span class="iconify" data-icon="solar:calendar-bold"></span>
                    <span>{{ $item['date'] }}</span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
                    {{ $item['desc'] }}
                </p>
            </div>
            @endforeach
        </div>

    </div>
</section>

<!-- Requirements & Document Download Section -->
<section id="requirements" class="py-20 lg:py-28 bg-slate-50 border-t border-slate-200/80 reveal-init">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-block text-xs font-bold uppercase tracking-wider text-primary-blue bg-blue-50 border border-blue-100 px-3.5 py-1.5 rounded-full mb-3">
                Syarat & Ketentuan
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Persyaratan Calon Pendaftar & Berkas
            </h2>
            <p class="text-base sm:text-lg text-slate-600 mt-3">
                Pastikan Anda memenuhi kriteria umum serta mengunduh format template berkas resmi di bawah ini.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            <!-- Left: Eligibility Criteria (5 Cols) -->
            <div class="lg:col-span-5 space-y-4">
                <h3 class="text-xl font-bold text-slate-900 border-b pb-3">
                    Kriteria Kelayakan Umum
                </h3>

                <div class="p-4 rounded-xl bg-white border border-slate-200 flex items-start gap-3.5 shadow-sm">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-primary-blue flex items-center justify-center shrink-0 mt-0.5">
                        <span class="iconify text-base" data-icon="solar:user-bold"></span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Siswa Kelas 12 & Gap Year</h4>
                        <p class="text-xs text-slate-600 mt-0.5">Siswa/i kelas 12 SMA/SMK/MA sederajat tahun ajaran 2026/2027, atau lulusan tahun ajaran 2022/2023/2024/2025/2026/2027.</p>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-white border border-slate-200 flex items-start gap-3.5 shadow-sm">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-primary-blue flex items-center justify-center shrink-0 mt-0.5">
                        <span class="iconify text-base" data-icon="solar:chart-2-bold"></span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Prestasi Akademik / Non-Akademik</h4>
                        <p class="text-xs text-slate-600 mt-0.5">Memiliki nilai rapor yang konsisten baik atau memiliki sertifikat penghargaan perlombaan / kegiatan organisasi.</p>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-white border border-slate-200 flex items-start gap-3.5 shadow-sm">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-primary-blue flex items-center justify-center shrink-0 mt-0.5">
                        <span class="iconify text-base" data-icon="solar:cup-first-bold"></span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Jiwa Kepemimpinan & Karakter</h4>
                        <p class="text-xs text-slate-600 mt-0.5">Aktif dalam organisasi sekolah / komunitas pemuda, bersemangat belajar tinggi, dan berkarakter baik.</p>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-white border border-slate-200 flex items-start gap-3.5 shadow-sm">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-primary-blue flex items-center justify-center shrink-0 mt-0.5">
                        <span class="iconify text-base" data-icon="solar:check-read-bold"></span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Komitmen Mengikuti Seleksi</h4>
                        <p class="text-xs text-slate-600 mt-0.5">Bersedia mengikuti seluruh tahapan seleksi (mini project, tes CBT, dan wawancara) secara sungguh-sungguh.</p>
                    </div>
                </div>
            </div>

            <!-- Right: Document Checklist + Direct Download Templates (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">
                <h3 class="text-xl font-bold text-slate-900 border-b pb-3 flex items-center justify-between">
                    <span>Dokumen yang Wajib Disiapkan</span>
                    <span class="text-xs font-normal text-slate-500">Format PDF / Gambar</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    <!-- Doc 1: Rapor -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-white shadow-sm flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
                            <span class="iconify text-lg" data-icon="solar:document-text-bold"></span>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Rapor Semester 1 - 5</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Format PDF (Maks. 5 MB)</p>
                        </div>
                    </div>

                    <!-- Doc 2: Pas Foto -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-white shadow-sm flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
                            <span class="iconify text-lg" data-icon="solar:camera-bold"></span>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Pas Foto Terbaru</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Format JPG / PNG (Ukuran 3x4 cm)</p>
                        </div>
                    </div>

                    <!-- Doc 3: Sertifikat Prestasi -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-white shadow-sm flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
                            <span class="iconify text-lg" data-icon="solar:medal-ribbon-bold"></span>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Sertifikat Prestasi</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Opsional / Pendukung (Format PDF)</p>
                        </div>
                    </div>

                    <!-- Doc 4: Personal Statement with Download -->
                    <div class="p-4 rounded-xl border border-blue-200 bg-blue-50/50 shadow-sm flex flex-col justify-between">
                        <div class="flex items-start gap-3 mb-2">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 text-primary-blue flex items-center justify-center shrink-0">
                                <span class="iconify text-lg" data-icon="solar:pen-new-square-bold"></span>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">Personal Statement</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Esai motivasi (Maks. 500 kata)</p>
                            </div>
                        </div>
                        <a href="{{ asset('icon/Personal Statement MFLS 2026.pdf') }}" download class="inline-flex items-center gap-1.5 text-xs font-bold text-primary-blue hover:underline mt-1">
                            <span class="iconify" data-icon="solar:download-square-bold"></span>
                            <span>Unduh Template Esai PDF</span>
                        </a>
                    </div>

                    <!-- Doc 5: Study Plan with Download -->
                    <div class="p-4 rounded-xl border border-blue-200 bg-blue-50/50 shadow-sm flex flex-col justify-between">
                        <div class="flex items-start gap-3 mb-2">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 text-primary-blue flex items-center justify-center shrink-0">
                                <span class="iconify text-lg" data-icon="solar:book-2-bold"></span>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">Study Plan</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Rencana studi dan aspirasi karir</p>
                            </div>
                        </div>
                        <a href="{{ asset('icon/Study Plan MFLS 2026_2.pdf') }}" download class="inline-flex items-center gap-1.5 text-xs font-bold text-primary-blue hover:underline mt-1">
                            <span class="iconify" data-icon="solar:download-square-bold"></span>
                            <span>Unduh Template Study Plan PDF</span>
                        </a>
                    </div>

                    <!-- Doc 6: Surat Rekomendasi with Download -->
                    <div class="p-4 rounded-xl border border-blue-200 bg-blue-50/50 shadow-sm flex flex-col justify-between">
                        <div class="flex items-start gap-3 mb-2">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 text-primary-blue flex items-center justify-center shrink-0">
                                <span class="iconify text-lg" data-icon="solar:diploma-bold"></span>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">Surat Rekomendasi Sekolah</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Khusus siswa aktif (Alumni tidak wajib)</p>
                            </div>
                        </div>
                        <a href="{{ asset('icon/Surat Rekomendasi MFLS_2.pdf') }}" download class="inline-flex items-center gap-1.5 text-xs font-bold text-primary-blue hover:underline mt-1">
                            <span class="iconify" data-icon="solar:download-square-bold"></span>
                            <span>Unduh Template Rekomendasi PDF</span>
                        </a>
                    </div>

                </div>

                <!-- Registration Fast CTA -->
                <div class="p-5 rounded-2xl bg-slate-900 text-white flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
                    <div>
                        <div class="font-bold text-base">Sudah Menyiapkan Berkas Anda?</div>
                        <div class="text-xs text-slate-300">Pendaftaran akun gratis tanpa dipungut biaya apapun.</div>
                    </div>
                    @if($buka)
                        <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-slate-900 bg-amber-400 hover:bg-amber-300 transition-colors shrink-0">
                            <span>Daftar Sekarang</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @else
                        <span class="text-xs text-slate-400 font-medium">Pendaftaran sedang ditutup</span>
                    @endif
                </div>

            </div>

        </div>

    </div>
</section>

<!-- Testimonials Section: "Apa Kata Mereka?" 18-Awardee Interactive Slider -->
<section class="py-20 lg:py-28 bg-white border-t border-slate-100 reveal-init overflow-hidden">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-14">
            <span class="inline-block text-xs font-bold uppercase tracking-wider text-primary-blue bg-blue-50 border border-blue-100 px-3.5 py-1.5 rounded-full mb-3">
                Kisah & Kesan Awardee
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Apa Kata Penerima Beasiswa MFLS?
            </h2>
            <p class="text-base sm:text-lg text-slate-600 mt-3">
                Kisah nyata 18 mahasiswa berprestasi yang berhasil meraih beasiswa 100% dan bertumbuh bersama MNC University.
            </p>
        </div>

        @php
            $testimonials = [
                ['name' => 'Irfan Syahfutra', 'batch' => 'Awardee MFLS 2025', 'major' => 'S1 Sistem Informasi', 'photo' => 'Irfan.webp', 'quote' => 'Terima kasih MNCU Future Leader Scholarship atas pengalaman yang luar biasa. Selama kegiatan ini, kami belajar banyak hal baru yang melatih mental, tanggung jawab, dan kepemimpinan. Momen ini menjadi pijakan berharga untuk terus berkembang.'],
                ['name' => 'Satria Pambudi Attur Rohman', 'batch' => 'Awardee MFLS 2025', 'major' => 'S1 Desain Komunikasi Visual', 'photo' => 'Satria.webp', 'quote' => 'Terimakasih banyak untuk MNCU Future Leader Scholarship buat kesempatan, ilmu, dan pengalaman baru yang bener bener luar biasa. Di sini, aku nggak cuma sekedar belajar teori, tapi juga dilatih buat jadi pribadi yang punya jiwa leadership atau kepemimpinan yang kuat.'],
                ['name' => 'Cellindia Vanesa Heriyandu', 'batch' => 'Awardee MFLS 2025', 'major' => 'S1 Pendidikan Bahasa Inggris', 'photo' => 'cellin.webp', 'quote' => 'Melalui rangkaian kegiatan MFLS, saya belajar nilai kepemimpinan, kerja sama, dan keberanian untuk terus berkembang. Dipertemukan dengan teman, kakak pembimbing, serta dosen-dosen inspiratif menjadi pengalaman berharga yang membentuk cara berpikir saya.'],
                ['name' => 'Sandy Arif Saputra', 'batch' => 'Awardee MFLS 2025', 'major' => 'S1 Ilmu Komputer', 'photo' => 'Sandy.webp', 'quote' => 'Mengikuti MNCU Future Leader Scholarship jadi salah satu pengalaman paling berkesan buat aku. Bertemu teman-teman hebat, para mentor yang inspiring, serta dosen-dosen dengan kualitas yang really next level adalah hal yang nggak akan aku lupain. Failure is part of the journey, and trying is always the first win.'],
                ['name' => 'Silvi Melani Putri', 'batch' => 'Awardee MFLS 2025', 'major' => 'S1 Pendidikan Matematika', 'photo' => 'Silvi.webp', 'quote' => 'Terima kasih MNCU Future Leader Scholarship atas ilmu, pengalaman, dan kesempatan yang sangat berharga. Program ini memberikan berbagai wawasan dan pengalaman baru yang sangat menginspirasi serta menjadi bekal berharga bagi kami untuk pengembangan diri di masa depan.'],
                ['name' => 'Kirania Rizma Az-Zahra', 'batch' => 'Awardee MFLS 2025', 'major' => 'S1 Akuntansi', 'photo' => 'Kirania.webp', 'quote' => 'Long story short I survived! Terima kasih MNCU Future Leader Scholarship atas beasiswa 100%. Dalam program ini, bukan hanya memberi aku beasiswa, namun juga memberi ruang untuk tumbuh, belajar, dan bermimpi lebih jauh.'],
                ['name' => 'Innez Putria Abidin', 'batch' => 'Awardee MFLS 2025', 'major' => 'S1 Manajemen', 'photo' => 'Innez.webp', 'quote' => 'Terima kasih MNCU Future Leader Scholarship atas pengalaman dan pembelajaran yang luar biasa. Selama mengikuti program ini, saya banyak belajar hal baru dan menginspirasi terutama tentang leadership, kerja sama, dan pengembangan diri yang akan berguna di masa depan.'],
                ['name' => 'Rizki', 'batch' => 'Awardee MFLS 2025', 'major' => 'S1 Sistem Informasi', 'photo' => 'Rizki.webp', 'quote' => 'Terima kasih kepada MNC University yang telah memberikan harapan bagi generasi muda Indonesia melalui Beasiswa 100% MNCU Future Leader Scholarship. Kesempatan ini menjadi jembatan untuk anak desa bisa menggapai masa depan yang lebih baik.'],
                ['name' => 'Andini Raissa', 'batch' => 'Awardee MFLS 2025', 'major' => 'S1 Manajemen', 'photo' => 'Andini.webp', 'quote' => 'Terima kasih MNCU Future Leader Scholarship atas kesempatan luar biasa yang membuka ruang tumbuh, membentuk karakter, dan menguatkan jiwa kepemimpinan kami, setiap prosesnya menjadi pijakan untuk melangkah lebih berani, berpikir lebih visioner, dan berdampak lebih nyata.'],
                ['name' => 'Alfiah Wirda Lukmansyah', 'batch' => 'Awardee MFLS 2025', 'major' => 'S1 Akuntansi', 'photo' => 'Alfiah.webp', 'quote' => 'MFLS bukan hanya memberikan beasiswa, tapi juga keluarga baru dan pengalaman yang luar biasa. Terima kasih atas kesempatan ini!'],
                ['name' => 'Dzakiyah Febriyanti', 'batch' => 'Awardee MFLS 2025', 'major' => 'S1 Ilmu Komputer', 'photo' => 'Dzakiyah.webp', 'quote' => 'Terima kasih MNCU Future Leader Scholarship atas pengalaman luar biasa ini. Sit in class yang saya ikuti menjadi ruang belajar yang sangat berharga, di sana saya dilatih untuk mengasah leadership, memperluas wawasan, dan mendapatkan banyak inspirasi baru.'],
                ['name' => 'Gavino Pasha Putra', 'batch' => 'Awardee MFLS 2025', 'major' => 'S1 Ilmu Komputer', 'photo' => 'Gavino.webp', 'quote' => 'Terima kasih atas MNCU Future Leader Scholarship atas program beasiswa yang sudah diselenggarakan. Bagi saya ini bukanlah akhir, tetapi ini adalah awal untuk kita mencapai level yang lebih tinggi lagi kedepannya.'],
                ['name' => 'Rahmalia Putri', 'batch' => 'Awardee MFLS 2025', 'major' => 'S1 Desain Komunikasi Visual', 'photo' => 'raput.webp', 'quote' => 'Saya sangat berterimakasih sebesar-besarnya kepada MNC University karena telah memberikan kesempatan dan tanggung jawab sebagai penerima beasiswa 100%. Dengan beasiswa ini merupakan langkah awal bagi kami yang mau berjuang dan berusaha.'],
                ['name' => 'Muhamad Rifatur Seva Kuswara', 'batch' => 'Awardee MFLS 2025', 'major' => 'S1 Sistem Informasi', 'photo' => 'Rifatur.webp', 'quote' => 'Saya sangat berterimakasih banyak kepada MNCU Future Leader Scholarship. Dari sini saya belajar banyak sekali, bertemu teman-teman yang hebat, kaka yang keren, dosen-dosen yang sangat luar biasa dan sebuah pengalaman yang sangat berharga.'],
                ['name' => 'Rini Manda', 'batch' => 'Awardee MFLS 2025', 'major' => 'S1 Sains Komunikasi', 'photo' => 'rini.webp', 'quote' => 'Mengikuti program MNCU Future Leader Scholarship merupakan pengalaman yang sangat berharga bagi saya. Program ini tidak hanya memberikan bantuan pendidikan, tetapi juga membentuk karakter, rasa percaya diri, serta semangat untuk terus berkembang dan berprestasi.'],
                ['name' => 'Risky Zuliansyah', 'batch' => 'Awardee MFLS 2025', 'major' => 'S1 Sains Komunikasi', 'photo' => 'Risky.webp', 'quote' => 'Saya menghaturkan apresiasi dan terima kasih yang setulus-tulusnya kepada MNC University. Program ini menjadikan impian saya melangkah ke jenjang pendidikan tinggi di bawah naungan ekosistem media terbesar di Asia menjadi kenyataan.'],
                ['name' => 'Ibrahimovic', 'batch' => 'Awardee MFLS 2025', 'major' => 'S1 Sains Komunikasi', 'photo' => 'Baim.webp', 'quote' => 'Terimakasih atas kesempatan yang telah diberikan kepada saya. Pada program ini saya mendapatkan banyak pelajaran dan kegiatan yang menarik seperti mentoring bimbingan kepemimpinan dan praktek langsung yang membuka wawasan baru.'],
                ['name' => 'Gracia Lamtiar Yosefine Sagala', 'batch' => 'Awardee MFLS 2025', 'major' => 'S1 Akuntansi', 'photo' => 'grace.webp', 'quote' => 'Terima kasih yang sebesar-besarnya kepada MNC University atas kesempatan dan kepercayaan yang diberikan. Beasiswa ini menjadi sumber motivasi untuk terus belajar, berkembang, dan mempersiapkan diri sebagai calon pemimpin masa depan.']
            ];
        @endphp

        <!-- Slider Wrapper with Hover Pause -->
        <div id="testimonial-slider-container" class="relative group/slider">
            
            <!-- Cards Slides Track -->
            <div class="relative min-h-[380px] sm:min-h-[330px] md:min-h-[290px] flex items-center justify-center">
                @foreach($testimonials as $index => $t)
                <div class="testimonial-slide w-full {{ $index === 0 ? 'active opacity-100 relative' : 'opacity-0 absolute inset-0 pointer-events-none' }} transition-all duration-500 ease-out" data-index="{{ $index }}">
                    <div class="bg-gradient-to-br from-slate-50 via-white to-blue-50/40 rounded-3xl p-8 sm:p-12 border border-slate-200/90 shadow-xl shadow-slate-900/5 relative">
                        
                        <!-- Top Header: Quote Icon & Counter Badge -->
                        <div class="flex items-center justify-between mb-6">
                            <div class="text-amber-500">
                                <span class="iconify text-4xl sm:text-5xl" data-icon="solar:quote-up-square-bold"></span>
                            </div>
                            <span class="text-xs font-mono font-bold text-slate-500 bg-white px-3.5 py-1 rounded-full border border-slate-200 shadow-sm">
                                {{ $index + 1 }} / {{ count($testimonials) }}
                            </span>
                        </div>

                        <!-- Quote Text -->
                        <blockquote class="text-slate-800 text-base sm:text-lg md:text-xl font-normal leading-relaxed italic mb-8">
                            "{{ $t['quote'] }}"
                        </blockquote>

                        <!-- Author Profile -->
                        <div class="flex items-center gap-4 pt-6 border-t border-slate-200/80">
                            <img src="{{ asset('icon/' . $t['photo']) }}" alt="{{ $t['name'] }}" class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover border-2 border-primary-blue/30 shadow-md">
                            <div>
                                <h4 class="font-bold text-slate-900 text-base sm:text-lg">{{ $t['name'] }}</h4>
                                <div class="text-xs sm:text-sm text-slate-600 font-medium">{{ $t['major'] }}</div>
                                <div class="text-xs font-semibold text-primary-blue mt-0.5">{{ $t['batch'] }}</div>
                            </div>
                        </div>

                    </div>
                </div>
                @endforeach
            </div>

            <!-- Navigation Controls: Prev & Next Buttons -->
            <button type="button" onclick="prevSlide()" class="absolute -left-3 sm:-left-6 top-1/2 -translate-y-1/2 w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-white border border-slate-200 shadow-lg text-slate-700 hover:text-primary-blue hover:scale-110 active:scale-95 transition-all flex items-center justify-center z-20" aria-label="Slide sebelumnya">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button type="button" onclick="nextSlide()" class="absolute -right-3 sm:-right-6 top-1/2 -translate-y-1/2 w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-white border border-slate-200 shadow-lg text-slate-700 hover:text-primary-blue hover:scale-110 active:scale-95 transition-all flex items-center justify-center z-20" aria-label="Slide berikutnya">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </button>

            <!-- Pagination Dots (Interactive) -->
            <div class="flex items-center justify-center flex-wrap gap-2 mt-8" id="testimonial-dots-container">
                @foreach($testimonials as $index => $t)
                <button type="button" onclick="goToSlide({{ $index }})" class="testimonial-dot h-2.5 rounded-full transition-all duration-300 {{ $index === 0 ? 'w-8 bg-amber-400' : 'w-2.5 bg-slate-300 hover:bg-slate-400' }}" aria-label="Lihat testimoni {{ $t['name'] }}"></button>
                @endforeach
            </div>

            <!-- Pause on Hover Indicator Notice -->
            <div class="text-center text-[11px] text-slate-500 font-medium mt-3 flex items-center justify-center gap-1.5">
                <span class="iconify" data-icon="solar:cursor-bold"></span>
                <span>Arahkan kursor (hover) untuk menjeda otomatis slideshow</span>
            </div>

        </div>

    </div>
</section>

<!-- Berita & Informasi Terkini (jika ada berita terbit) -->
@if($beritas->count() > 0)
<section id="berita" class="py-20 lg:py-28 bg-slate-50 border-t border-slate-200/80 reveal-init">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-12">
            <div>
                <span class="inline-block text-xs font-bold uppercase tracking-wider text-primary-blue bg-blue-50 border border-blue-100 px-3.5 py-1.5 rounded-full mb-3">
                    Berita & Informasi
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Kabar Terbaru Seputar MFLS
                </h2>
            </div>
            <a href="{{ route('pengumuman') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-primary-blue hover:text-blue-800 transition-colors">
                <span>Lihat Semua Informasi</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($beritas as $berita)
            <article class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    @if($berita->thumbnail)
                        <div class="h-48 overflow-hidden bg-slate-100">
                            <img src="{{ asset('storage/' . $berita->thumbnail) }}" alt="{{ $berita->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                    @else
                        <div class="w-full h-48 bg-slate-100 flex items-center justify-center text-slate-400">
                            <span class="iconify text-4xl" data-icon="solar:gallery-bold-duotone"></span>
                        </div>
                    @endif

                    <div class="p-6">
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-500 mb-2">
                            <span class="iconify text-primary-blue" data-icon="solar:calendar-bold"></span>
                            <span>{{ $berita->created_at->format('d M Y') }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-primary-blue transition-colors line-clamp-2 mb-2">
                            <a href="{{ route('berita.show', $berita->slug) }}">{{ $berita->judul }}</a>
                        </h3>
                        <p class="text-sm text-slate-600 line-clamp-3">
                            {{ strip_tags($berita->konten) }}
                        </p>
                    </div>
                </div>

                <div class="px-6 pb-6 pt-0">
                    <a href="{{ route('berita.show', $berita->slug) }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-primary-blue group-hover:text-blue-800">
                        <span>Baca Selengkapnya</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </article>
            @endforeach
        </div>

    </div>
</section>
@endif

<!-- Strategic Partners & Youth Alliances -->
<section class="py-16 bg-white border-t border-slate-100 reveal-init">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        
        <span class="inline-block text-xs font-bold uppercase tracking-wider text-slate-500 bg-slate-50 border border-slate-200 px-3.5 py-1.5 rounded-full mb-3">
            Jaringan Kolaborasi Nasional
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mb-2">
            Mitra Organisasi & Komunitas Kepemudaan
        </h2>
        <p class="text-sm text-slate-600 max-w-xl mx-auto mb-10">
            Bersama mendukung sosialisasi beasiswa dan pengembangan kepemimpinan pelajar di seluruh Indonesia.
        </p>

        <!-- Partner Logos Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 sm:gap-6 items-center">
            @php
                $partners = [
                    ['name' => 'Forum OSIS Nasional', 'logo' => 'Logo FON .webp'],
                    ['name' => 'Muda Berdaya', 'logo' => 'Logo_MudaBerdaya - Square Primary.webp'],
                    ['name' => 'Maju Bersama OSIS', 'logo' => 'MNCU Future Leader Scholarship_20260316_040958_0000.webp'],
                    ['name' => 'Gensmart Indonesia', 'logo' => 'gensmart.webp'],
                    ['name' => 'Zeekend', 'logo' => 'zeekend.webp'],
                    ['name' => 'Forum OSIS Bogor', 'logo' => 'forum osis bogor.webp'],
                    ['name' => 'Forum Pemuda Beraksi Jabar', 'logo' => 'forum pemuda beraksi jawa barat.webp'],
                    ['name' => 'Forum Pemuda Beraksi Banten', 'logo' => 'forum osis banten.webp'],
                    ['name' => 'Tunas Satria Sriwijaya', 'logo' => 'tunas satria sriwijaya.webp'],
                    ['name' => 'Forum OSIS SIT', 'logo' => 'forum osis sekolah islam terpadu.webp'],
                    ['name' => 'Forum OSIS Jakarta Pusat', 'logo' => 'forum osis jakarta pusat.webp'],
                    ['name' => 'Forum OSIS SMK DKJ', 'logo' => 'FORUM OSIS SMK DAERAH KHUSUS JAKARTA.webp'],
                ];
            @endphp

            @foreach($partners as $partner)
            <div class="bg-slate-50/80 rounded-xl p-3.5 border border-slate-200 shadow-sm flex flex-col items-center justify-center h-28 hover:shadow-md hover:bg-white hover:border-slate-300 transition-all duration-200 group">
                <img src="{{ asset('icon/' . $partner['logo']) }}" alt="{{ $partner['name'] }}" class="max-h-14 max-w-full object-contain grayscale group-hover:grayscale-0 transition-all duration-300">
                <span class="text-[10px] font-semibold text-slate-500 group-hover:text-slate-900 transition-colors mt-2 text-center line-clamp-1">{{ $partner['name'] }}</span>
            </div>
            @endforeach
        </div>

    </div>
</section>

<!-- Contact & Information Desk -->
<section id="contact" class="py-20 lg:py-28 bg-slate-50 border-t border-slate-200/80 reveal-init">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-block text-xs font-bold uppercase tracking-wider text-primary-blue bg-blue-50 border border-blue-100 px-3.5 py-1.5 rounded-full mb-3">
                Layanan Informasi & Bantuan
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Hubungi Panitia Seleksi Beasiswa MFLS
            </h2>
            <p class="text-base sm:text-lg text-slate-600 mt-3">
                Memiliki kendala saat proses pendaftaran atau membutuhkan klarifikasi persyaratan? Tim helpdesk kami siap mendampingi Anda.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- WhatsApp -->
            <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center hover:border-primary-blue hover:shadow-xl transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                    <span class="iconify text-3xl" data-icon="solar:chat-round-dots-bold"></span>
                </div>
                <h3 class="font-bold text-slate-900 text-lg mb-1">WhatsApp Center</h3>
                <p class="text-xs text-slate-500 mb-4">Senin - Jumat: 09.00 - 17.00 WIB</p>
                <a href="https://wa.me/6285880059189" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 font-bold text-sm text-emerald-600 hover:text-emerald-700">
                    <span>+62 858-8005-9189</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            <!-- Email -->
            <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center hover:border-primary-blue hover:shadow-xl transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-primary-blue flex items-center justify-center mx-auto mb-4">
                    <span class="iconify text-3xl" data-icon="solar:letter-bold"></span>
                </div>
                <h3 class="font-bold text-slate-900 text-lg mb-1">Email Resmi</h3>
                <p class="text-xs text-slate-500 mb-4">Respon resmi dalam 1 x 24 jam kerja</p>
                <a href="mailto:info@beasiswamncu.com" class="inline-flex items-center gap-1.5 font-bold text-sm text-primary-blue hover:text-blue-800">
                    <span>info@beasiswamncu.com</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            <!-- Instagram -->
            <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center hover:border-primary-blue hover:shadow-xl transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4">
                    <span class="iconify text-3xl" data-icon="solar:camera-bold"></span>
                </div>
                <h3 class="font-bold text-slate-900 text-lg mb-1">Instagram Resmi</h3>
                <p class="text-xs text-slate-500 mb-4">Update live & pengumuman awardee</p>
                <a href="https://www.instagram.com/beasiswamncu/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 font-bold text-sm text-rose-600 hover:text-rose-700">
                    <span>@beasiswamncu</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

        </div>

    </div>
</section>

<!-- Bottom CTA Banner -->
<section class="py-16 bg-gradient-to-r from-primary-blue to-blue-900 text-white text-center">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
            Siap Menjadi Pemimpin Masa Depan Bersama MNC University?
        </h2>
        <p class="text-blue-100 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
            Daftarkan diri Anda hari ini dan raih kesempatan beasiswa kuliah penuh hingga 100% sampai lulus.
        </p>
        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-4">
            @if($buka)
                <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl text-base font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 shadow-xl transition-all duration-200 hover:-translate-y-0.5">
                    <span>Daftar Sekarang (Gratis)</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            @else
                <a href="#timeline" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl text-base font-bold text-slate-300 bg-blue-950/60 border border-blue-400/20">
                    <span>Lihat Jadwal Seleksi Gelombang Berikutnya</span>
                </a>
            @endif
            <a href="https://wa.me/6285880059189" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-base font-semibold text-white bg-white/10 hover:bg-white/20 border border-white/20 transition-all duration-200">
                <span>Tanya Panitia via WhatsApp</span>
            </a>
        </div>
    </div>
</section>

<!-- Scripts: Parallax, Tabs, Countdown, Scroll Reveals -->
<script>
// 1. Interactive 3D Mouse Parallax on Hero Artwork Card
(function() {
    const heroArea = document.getElementById('hero-artwork-area');
    const tiltCard = document.getElementById('hero-tilt-card');
    const glow1 = document.getElementById('ambient-glow-1');
    const glow2 = document.getElementById('ambient-glow-2');

    if (heroArea && tiltCard && window.matchMedia('(pointer: fine)').matches) {
        let isHovered = false;

        heroArea.addEventListener('mouseenter', () => { isHovered = true; });
        heroArea.addEventListener('mouseleave', () => {
            isHovered = false;
            tiltCard.style.transform = 'perspective(1200px) rotateX(0deg) rotateY(0deg) translateZ(0px)';
        });

        heroArea.addEventListener('mousemove', (e) => {
            if (!isHovered) return;
            const rect = heroArea.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            // Subtle rotation between -8 and +8 degrees
            const rotateX = ((y - centerY) / centerY) * -7;
            const rotateY = ((x - centerX) / centerX) * 7;

            tiltCard.style.transform = `perspective(1200px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) translateZ(12px)`;

            // Subtle parallax movement on ambient glows
            if (glow1) {
                glow1.style.transform = `translate(${((x - centerX) * 0.04).toFixed(1)}px, ${((y - centerY) * 0.04).toFixed(1)}px)`;
            }
            if (glow2) {
                glow2.style.transform = `translate(${((x - centerX) * -0.04).toFixed(1)}px, ${((y - centerY) * -0.04).toFixed(1)}px)`;
            }
        });
    }
})();

// 2. Tab Filter for Program Studi
function filterProdi(category, e) {
    const cards = document.querySelectorAll('.prodi-card');
    const tabButtons = document.querySelectorAll('.prodi-tab-btn');

    tabButtons.forEach(btn => {
        btn.classList.remove('active', 'bg-white', 'text-slate-900', 'shadow-sm');
        btn.classList.add('text-slate-600');
    });

    if (e && e.currentTarget) {
        e.currentTarget.classList.add('active', 'bg-white', 'text-slate-900', 'shadow-sm');
        e.currentTarget.classList.remove('text-slate-600');
    }

    cards.forEach(card => {
        if (category === 'all') {
            card.style.display = 'flex';
        } else if (card.classList.contains(category)) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

// 3. Real-time Countdown Timer
(function() {
    const deadlineStr = "{{ $deadlineDate }}";
    const targetDate = new Date(deadlineStr).getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = targetDate - now;

        if (distance <= 0) {
            ['days', 'hours', 'minutes', 'seconds'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = "00";
            });
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        const daysEl = document.getElementById("days");
        const hoursEl = document.getElementById("hours");
        const minutesEl = document.getElementById("minutes");
        const secondsEl = document.getElementById("seconds");

        if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
        if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
        if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
        if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
})();

// 4. Scroll Reveal with IntersectionObserver
document.addEventListener('DOMContentLoaded', function() {
    const reveals = document.querySelectorAll('.reveal-init');
    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                obs.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -40px 0px'
    });

    reveals.forEach(el => observer.observe(el));
});

// 5. Testimonial 18-Awardee Slideshow with Pause-on-Hover
let currentTestimonial = 0;
const totalTestimonials = {{ count($testimonials) }};
let testimonialTimer = null;
const slideDelay = 5000;

function showTestimonial(index) {
    const slides = document.querySelectorAll('.testimonial-slide');
    const dots = document.querySelectorAll('.testimonial-dot');

    if (index >= totalTestimonials) currentTestimonial = 0;
    else if (index < 0) currentTestimonial = totalTestimonials - 1;
    else currentTestimonial = index;

    slides.forEach((slide, i) => {
        if (i === currentTestimonial) {
            slide.classList.remove('opacity-0', 'absolute', 'pointer-events-none');
            slide.classList.add('opacity-100', 'relative');
        } else {
            slide.classList.remove('opacity-100', 'relative');
            slide.classList.add('opacity-0', 'absolute', 'pointer-events-none');
        }
    });

    dots.forEach((dot, i) => {
        if (i === currentTestimonial) {
            dot.classList.remove('w-2.5', 'bg-slate-300');
            dot.classList.add('w-8', 'bg-amber-400');
        } else {
            dot.classList.remove('w-8', 'bg-amber-400');
            dot.classList.add('w-2.5', 'bg-slate-300');
        }
    });
}

function nextSlide() {
    showTestimonial(currentTestimonial + 1);
}

function prevSlide() {
    showTestimonial(currentTestimonial - 1);
}

function goToSlide(index) {
    showTestimonial(index);
    startAutoSlide();
}

function startAutoSlide() {
    stopAutoSlide();
    testimonialTimer = setInterval(nextSlide, slideDelay);
}

function stopAutoSlide() {
    if (testimonialTimer) {
        clearInterval(testimonialTimer);
        testimonialTimer = null;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const sliderContainer = document.getElementById('testimonial-slider-container');
    if (sliderContainer) {
        // Pause on Hover
        sliderContainer.addEventListener('mouseenter', stopAutoSlide);
        sliderContainer.addEventListener('mouseleave', startAutoSlide);

        // Touch Swipe Support for Mobile
        let touchStartX = 0;
        let touchEndX = 0;

        sliderContainer.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
            stopAutoSlide();
        }, { passive: true });

        sliderContainer.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            if (touchStartX - touchEndX > 50) {
                nextSlide();
            } else if (touchEndX - touchStartX > 50) {
                prevSlide();
            }
            startAutoSlide();
        }, { passive: true });

        startAutoSlide();
    }
});
</script>

<!-- Native Helpdesk & WhatsApp Floating Widget -->
<div id="mfls-help-widget" class="fixed bottom-6 right-6 z-50 flex flex-col items-end select-none">
    <!-- Popover Card -->
    <div id="mfls-help-popup" class="hidden opacity-0 translate-y-4 scale-95 transition-all duration-300 ease-out mb-3 w-[90vw] max-w-sm rounded-2xl bg-white shadow-2xl border border-slate-200/90 overflow-hidden ring-1 ring-slate-900/5">
        <!-- Header -->
        <div class="p-4 bg-gradient-to-r from-[#0B1120] via-[#0D1B36] to-[#0870B8] text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center text-white shrink-0 shadow-xs">
                    <span class="iconify text-xl" data-icon="solar:chat-round-line-bold"></span>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h4 class="text-xs font-black tracking-wide text-white">Helpdesk MFLS Batch 3 (Ajaran 2027)</h4>
                        <span class="px-1.5 py-0.2 rounded text-[9px] font-black uppercase tracking-wider bg-amber-400 text-slate-900">2027</span>
                    </div>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-[11px] font-medium text-emerald-300">Panitia Aktif & Online</span>
                    </div>
                </div>
            </div>
            <button type="button" onclick="toggleMflsHelp()" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors" title="Tutup">
                <span class="iconify text-lg" data-icon="solar:close-circle-bold"></span>
            </button>
        </div>

        <!-- Body -->
        <div class="p-4 space-y-3 bg-slate-50/70">
            <div class="bg-white p-3 rounded-xl border border-slate-200/80 shadow-xs text-xs text-slate-600 leading-relaxed">
                <p class="font-bold text-slate-800 mb-1">Halo Calon Pemimpin Masa Depan!</p>
                <p>Butuh panduan pendaftaran, informasi seleksi, atau kendala berkas Beasiswa MFLS Batch 3? Kami siap mendampingi Anda.</p>
            </div>

            <div class="space-y-2">
                <!-- WhatsApp Live Chat -->
                <a href="https://wa.me/6285880059189?text=Halo%20Panitia%20MFLS%20Batch%203,%20saya%20ingin%20bertanya%20seputar%20pendaftaran%20dan%20seleksi%20beasiswa" 
                   target="_blank" rel="noopener noreferrer"
                   class="group flex items-center justify-between p-3 rounded-xl bg-white hover:bg-emerald-50 border border-slate-200/80 hover:border-emerald-300 transition-all duration-200 shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                            <span class="iconify text-lg" data-icon="solar:chat-round-dots-bold"></span>
                        </div>
                        <div class="text-left">
                            <span class="block text-xs font-bold text-slate-800 group-hover:text-emerald-700">Chat WhatsApp Panitia</span>
                            <span class="block text-[10px] text-slate-500 font-medium">+62 858-8005-9189</span>
                        </div>
                    </div>
                    <span class="iconify text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all text-sm" data-icon="solar:arrow-right-bold"></span>
                </a>

                <!-- Saluran WhatsApp Resmi -->
                <a href="https://whatsapp.com/channel/0029VbC0L7I6hENrmgrr9D3D" 
                   target="_blank" rel="noopener noreferrer"
                   class="group flex items-center justify-between p-3 rounded-xl bg-white hover:bg-blue-50 border border-slate-200/80 hover:border-blue-300 transition-all duration-200 shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-primary-blue flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                            <span class="iconify text-lg" data-icon="solar:bell-bing-bold"></span>
                        </div>
                        <div class="text-left">
                            <span class="block text-xs font-bold text-slate-800 group-hover:text-primary-blue">Saluran Info & Update MFLS</span>
                            <span class="block text-[10px] text-slate-500 font-medium">Broadcast Pengumuman Resmi</span>
                        </div>
                    </div>
                    <span class="iconify text-slate-400 group-hover:text-primary-blue group-hover:translate-x-0.5 transition-all text-sm" data-icon="solar:arrow-right-bold"></span>
                </a>

                <!-- Quick Nav to Timeline -->
                <a href="#timeline" onclick="toggleMflsHelp()"
                   class="group flex items-center justify-between p-3 rounded-xl bg-white hover:bg-amber-50 border border-slate-200/80 hover:border-amber-300 transition-all duration-200 shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                            <span class="iconify text-lg" data-icon="solar:calendar-date-bold"></span>
                        </div>
                        <div class="text-left">
                            <span class="block text-xs font-bold text-slate-800 group-hover:text-amber-700">Lihat Timeline Seleksi</span>
                            <span class="block text-[10px] text-slate-500 font-medium">Jadwal tahapan seleksi Batch 3</span>
                        </div>
                    </div>
                    <span class="iconify text-slate-400 group-hover:text-amber-600 group-hover:translate-x-0.5 transition-all text-sm" data-icon="solar:arrow-right-bold"></span>
                </a>
            </div>

            <!-- Footer note -->
            <div class="pt-2 border-t border-slate-200 text-[10px] text-slate-400 text-center font-medium">
                Jam Operasional: Senin - Jumat (08.30 - 17.00 WIB)
            </div>
        </div>
    </div>

    <!-- Floating Toggle Button -->
    <button type="button" id="mfls-help-btn" onclick="toggleMflsHelp()"
            class="group flex items-center gap-2.5 px-4 py-3 sm:px-5 sm:py-3.5 bg-gradient-to-r from-[#0870B8] to-[#0A85D9] hover:from-[#075f9c] hover:to-[#0870B8] text-white rounded-full shadow-xl shadow-blue-900/30 hover:shadow-2xl hover:shadow-blue-900/40 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 border border-blue-400/40 focus:outline-none focus:ring-4 focus:ring-blue-500/25">
        <div class="relative flex items-center justify-center">
            <span id="mfls-help-icon-chat" class="iconify text-xl sm:text-2xl transition-transform group-hover:scale-110" data-icon="solar:chat-round-dots-bold"></span>
            <span id="mfls-help-icon-close" class="iconify text-xl sm:text-2xl hidden transition-transform group-hover:scale-110" data-icon="solar:close-circle-bold"></span>
            <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-emerald-400 ring-2 ring-white rounded-full animate-pulse"></span>
        </div>
        <span class="text-xs sm:text-sm font-bold tracking-tight">Butuh Bantuan?</span>
    </button>
</div>

<script>
function toggleMflsHelp() {
    const popup = document.getElementById('mfls-help-popup');
    const iconChat = document.getElementById('mfls-help-icon-chat');
    const iconClose = document.getElementById('mfls-help-icon-close');
    
    if (popup.classList.contains('hidden')) {
        popup.classList.remove('hidden');
        requestAnimationFrame(() => {
            popup.classList.remove('opacity-0', 'translate-y-4', 'scale-95');
            popup.classList.add('opacity-100', 'translate-y-0', 'scale-100');
        });
        iconChat.classList.add('hidden');
        iconClose.classList.remove('hidden');
    } else {
        popup.classList.add('opacity-0', 'translate-y-4', 'scale-95');
        popup.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
        setTimeout(() => {
            popup.classList.add('hidden');
        }, 300);
        iconChat.classList.remove('hidden');
        iconClose.classList.add('hidden');
    }
}

document.addEventListener('click', function(e) {
    const widget = document.getElementById('mfls-help-widget');
    const popup = document.getElementById('mfls-help-popup');
    if (widget && popup && !popup.classList.contains('hidden')) {
        if (!widget.contains(e.target)) {
            toggleMflsHelp();
        }
    }
});
</script>
@endsection

@extends('layouts.cbt')

@section('content')
<div id="cbtApp" class="min-h-screen flex flex-col bg-slate-950 text-slate-100 relative">

    {{-- ======================================================== --}}
    {{-- MODAL PRE-EXAM: Kesiapan Jaringan & Aturan Keamanan Layar --}}
    {{-- ======================================================== --}}
    <div id="preFlightModal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/90 backdrop-blur-xl overflow-y-auto">
        <div class="relative w-full max-w-lg bg-slate-900 border border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-2xl space-y-4 my-auto">
            {{-- Header Modal --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-blue-600/15 border border-blue-500/25 flex items-center justify-center shrink-0">
                    <span class="iconify text-xl sm:text-2xl text-blue-400" data-icon="solar:shield-warning-bold-duotone"></span>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-blue-500/10 text-blue-400 border border-blue-500/20">
                            Protokol CBT
                        </span>
                        <span id="preflightPingBadge" class="px-2 py-0.5 rounded-md text-[9px] font-bold tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Jaringan Stabil
                        </span>
                    </div>
                    <h2 class="text-base sm:text-lg font-black text-white mt-1 leading-snug">{{ $ujian->nama }}</h2>
                    <p class="text-[11px] text-slate-400">MNC Future Leaders Scholarship — Seleksi Mandiri</p>
                </div>
            </div>

            {{-- Ringkasan Sesi (Kompak) --}}
            <div class="grid grid-cols-3 gap-2 p-2.5 sm:p-3 rounded-xl bg-slate-950/60 border border-slate-800 text-center">
                <div>
                    <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Total Soal</p>
                    <p class="text-sm sm:text-base font-black text-white mt-0.5">{{ count($soals) }} Soal</p>
                </div>
                <div class="border-x border-slate-800">
                    <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Alokasi Waktu</p>
                    <p class="text-sm sm:text-base font-black text-blue-400 mt-0.5">{{ $ujian->durasi }} Menit</p>
                </div>
                <div>
                    <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Toleransi Fokus</p>
                    <p class="text-sm sm:text-base font-black text-amber-400 mt-0.5">1x Peringatan</p>
                </div>
            </div>

            {{-- Checklist Instruksi Keamanan --}}
            <div class="space-y-2">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Perhatikan Sebelum Mulai:</p>
                <div class="space-y-2 text-[11px] text-slate-300">
                    <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-800/40 border border-slate-800">
                        <span class="iconify text-base text-emerald-400 shrink-0 mt-0.5" data-icon="solar:wifi-router-bold-duotone"></span>
                        <p><strong class="text-white">Koneksi Internet Stabil:</strong> Pastikan jaringan lancar. Jawaban tersimpan otomatis ke server.</p>
                    </div>
                    <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-800/40 border border-slate-800">
                        <span class="iconify text-base text-amber-400 shrink-0 mt-0.5" data-icon="solar:maximize-square-minimalistic-bold-duotone"></span>
                        <p><strong class="text-white">Layar Penuh Otomatis:</strong> Layar penuh otomatis aktif saat menekan tombol mulai.</p>
                    </div>
                    <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-red-950/30 border border-red-900/40">
                        <span class="iconify text-base text-red-400 shrink-0 mt-0.5" data-icon="solar:danger-triangle-bold-duotone"></span>
                        <p><strong class="text-red-300">Anti Pindah Tab / Layar:</strong> Dilarang beralih tab atau aplikasi. Toleransi: <strong class="text-white">3x peringatan</strong>. Pada pelanggaran ke-3, <strong class="text-red-400">ujian akan terkunci otomatis</strong> dan harus dibuka oleh panitia/admin.</p>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="pt-1 flex flex-col sm:flex-row items-center gap-2">
                <a href="{{ route('ujian.index') }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs uppercase tracking-wider text-center transition-all">
                    Batalkan
                </a>
                <button type="button" onclick="mulaiUjianDenganLayarPenuh()"
                    class="w-full flex-1 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-black text-xs uppercase tracking-wider shadow-md shadow-blue-600/30 transition-all flex items-center justify-center gap-1.5 active:scale-[0.99]">
                    <span>Masuk Layar Penuh & Mulai</span>
                    <span class="iconify text-sm" data-icon="solar:arrow-right-bold"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- TOP APPBAR CBT (Kompak, Proporsional, Responsif)          --}}
    {{-- ======================================================== --}}
    <header class="bg-slate-900/95 border-b border-slate-800/80 backdrop-blur-md px-3 sm:px-4 md:px-6 py-2 sm:py-2.5 sticky top-0 z-30 flex items-center justify-between gap-2 sm:gap-4">
        {{-- Kiri: Brand & Nama Ujian --}}
        <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1 sm:flex-initial">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-600/15 border border-blue-500/30 flex items-center justify-center shrink-0">
                <span class="iconify text-base sm:text-lg text-blue-400" data-icon="solar:diploma-bold-duotone"></span>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-1.5">
                    <span class="text-[9px] font-black uppercase tracking-wider text-blue-400 truncate hidden xs:inline">MFLS CBT</span>
                    <span id="networkPill" class="hidden md:inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[8px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Stabil
                    </span>
                </div>
                <h1 class="text-white font-bold text-xs sm:text-sm truncate max-w-[130px] xs:max-w-[180px] sm:max-w-xs">{{ $ujian->nama }}</h1>
            </div>
        </div>

        {{-- Tengah: Info Peserta (Desktop Only) --}}
        <div class="hidden xl:flex items-center gap-2 px-3 py-1 rounded-lg bg-slate-950/60 border border-slate-800">
            <span class="iconify text-sm text-slate-400" data-icon="solar:user-bold-duotone"></span>
            <div class="text-left">
                <p class="text-[10px] font-bold text-white leading-tight truncate max-w-[130px]">{{ auth()->user()->peserta?->nama ?? auth()->user()->nama }}</p>
                <p class="text-[8px] text-slate-400 leading-none">NISN: {{ auth()->user()->peserta?->nisn ?? '-' }}</p>
            </div>
        </div>

        {{-- Kanan: Timer, Pelanggaran & Tombol Selesai --}}
        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
            {{-- Indikator Pelanggaran Fokus --}}
            <div id="violationBadge" class="px-2 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60 text-center flex items-center gap-1">
                <span class="iconify text-xs text-slate-400" data-icon="solar:eye-scan-bold-duotone"></span>
                <span class="text-[9px] sm:text-[10px] font-bold text-slate-300">Fokus: <strong id="violationCount" class="text-emerald-400">0/3</strong></span>
            </div>

            {{-- Timer --}}
            <div id="timerContainer" class="px-2.5 sm:px-3 py-1 rounded-lg bg-slate-950/80 border border-slate-800 text-center">
                <p class="text-[7px] font-bold uppercase tracking-widest text-slate-400 leading-none hidden sm:block">Sisa Waktu</p>
                <p id="timer" class="text-xs sm:text-sm font-black text-white tabular-nums leading-tight">--:--:--</p>
            </div>

            {{-- Tombol Selesai --}}
            <button type="button" onclick="konfirmasiSelesai()" id="btnSelesai"
                class="px-2.5 sm:px-3.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-bold text-[10px] sm:text-xs uppercase tracking-wider transition-all active:scale-95 shadow-sm shadow-blue-600/20">
                Selesai
            </button>
        </div>
    </header>

    {{-- ======================================================== --}}
    {{-- AREA KERJA UJIAN (Workspace Proporsional)                --}}
    {{-- ======================================================== --}}
    <main class="flex-1 max-w-5xl w-full mx-auto px-3 sm:px-4 md:px-6 py-3 sm:py-4 space-y-3">
        
        {{-- Status Ringkas Bar: Terjawab / Diragukan / Autosave --}}
        <div class="flex items-center justify-between gap-2 text-[11px]">
            <div class="flex items-center gap-1.5">
                <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-slate-300 font-semibold flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <span>Jawab: <strong id="cntJawab" class="text-white">0</strong>/{{ count($soals) }}</span>
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-slate-300 font-semibold flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    <span>Ragu: <strong id="cntRagu" class="text-amber-400">0</strong></span>
                </span>
            </div>

            <div class="flex items-center gap-2">
                <span id="autosaveInfo" class="hidden sm:inline-flex px-2.5 py-1 rounded-lg bg-slate-900/80 border border-slate-800 text-[10px] font-medium text-slate-400 items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    Tersimpan
                </span>
                <button type="button" onclick="bukaDrawerNavigasiMobile()" class="lg:hidden px-2.5 py-1 rounded-lg bg-blue-600/15 border border-blue-500/30 text-blue-400 font-bold text-[11px] flex items-center gap-1 hover:bg-blue-600/25 transition-colors">
                    <span class="iconify" data-icon="solar:grid-bold"></span>
                    <span>Nomor Soal</span>
                </button>
            </div>
        </div>

        {{-- Grid: Navigasi Desktop & Lembar Soal --}}
        <div class="grid grid-cols-1 lg:grid-cols-[14.5rem_1fr] gap-3 sm:gap-4 items-start">
            
            {{-- Panel Nomor Soal (Desktop Sticky) --}}
            <aside class="hidden lg:block bg-slate-900/90 border border-slate-800/80 rounded-2xl p-3.5 shadow-lg lg:sticky lg:top-16">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-800">
                    <div class="flex items-center gap-1.5">
                        <span class="iconify text-sm text-blue-400" data-icon="solar:checklist-bold-duotone"></span>
                        <p class="text-[11px] font-black uppercase tracking-wider text-slate-300">Navigasi Soal</p>
                    </div>
                    <span class="text-[10px] font-bold text-slate-500">{{ count($soals) }} Butir</span>
                </div>

                <div id="panelNomor" class="grid grid-cols-5 gap-1.5 mt-3 max-h-[55vh] overflow-y-auto pr-1">
                    {{-- Diisi JS --}}
                </div>

                <div class="grid grid-cols-3 gap-1 mt-3 pt-2.5 border-t border-slate-800 text-[9px] font-bold text-slate-400">
                    <div class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded bg-blue-600 inline-block"></span>
                        <span>Dijawab</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded bg-amber-500 inline-block"></span>
                        <span>Ragu</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded bg-slate-800 border border-slate-700 inline-block"></span>
                        <span>Kosong</span>
                    </div>
                </div>
            </aside>

            {{-- Kartu Soal Utama --}}
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 sm:p-5 md:p-6 shadow-xl space-y-4">
                
                {{-- Header Soal --}}
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-lg bg-blue-600/15 border border-blue-500/20 text-blue-400 font-black text-xs uppercase tracking-wider">
                            Soal <span id="noAktif">1</span> dari {{ count($soals) }}
                        </span>
                        <span id="kategoriSoal" class="hidden sm:inline-block px-2 py-0.5 rounded-lg bg-slate-800 text-slate-400 font-semibold text-[10px] uppercase tracking-wider">
                            Pilihan Ganda
                        </span>
                    </div>
                    <div class="flex items-center gap-1.5 text-slate-500 text-[10px]">
                        <span class="iconify text-xs" data-icon="solar:shield-check-bold"></span>
                        <span>Terenkripsi</span>
                    </div>
                </div>

                {{-- Konten Pertanyaan & Opsi --}}
                <div id="isiSoal" class="space-y-4">
                    {{-- Diisi JS --}}
                </div>

                {{-- Kontrol Aksi Bawah --}}
                <div class="flex items-center justify-between gap-2 pt-4 border-t border-slate-800">
                    <button type="button" onclick="keSoal(current - 1)" id="btnPrev"
                        class="px-3.5 sm:px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs uppercase tracking-wider transition-all disabled:opacity-30 disabled:pointer-events-none flex items-center gap-1">
                        <span class="iconify text-xs" data-icon="solar:alt-arrow-left-bold"></span>
                        <span>Prev</span>
                    </button>

                    <button type="button" onclick="toggleRagu()" id="btnRagu"
                        class="px-3 sm:px-3.5 py-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-400 font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-1">
                        <span class="iconify text-xs" data-icon="solar:flag-bold-duotone"></span>
                        <span>Ragu-ragu</span>
                    </button>

                    <button type="button" onclick="keSoal(current + 1)" id="btnNext"
                        class="px-3.5 sm:px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs uppercase tracking-wider transition-all disabled:opacity-30 disabled:pointer-events-none flex items-center gap-1 shadow-sm shadow-blue-600/20">
                        <span>Next</span>
                        <span class="iconify text-xs" data-icon="solar:alt-arrow-right-bold"></span>
                    </button>
                </div>
            </div>
        </div>
    </main>

    {{-- ======================================================== --}}
    {{-- MODAL / DRAWER NAVIGASI SOAL MOBILE                      --}}
    {{-- ======================================================== --}}
    <div id="mobileDrawerNav" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-md items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="w-full sm:max-w-md bg-slate-900 border border-slate-800 rounded-t-3xl sm:rounded-2xl p-4 sm:p-5 shadow-2xl space-y-3 max-h-[80vh] flex flex-col">
            <div class="flex items-center justify-between pb-2 border-b border-slate-800 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="iconify text-base text-blue-400" data-icon="solar:checklist-bold-duotone"></span>
                    <h3 class="text-xs font-black uppercase tracking-wider text-white">Daftar Nomor Soal</h3>
                </div>
                <button type="button" onclick="tutupDrawerNavigasiMobile()" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800">
                    <span class="iconify text-lg" data-icon="solar:close-circle-bold"></span>
                </button>
            </div>

            <div id="panelNomorMobile" class="grid grid-cols-5 xs:grid-cols-6 gap-1.5 overflow-y-auto p-1 flex-1">
                {{-- Diisi JS --}}
            </div>

            <div class="grid grid-cols-3 gap-1 pt-2 border-t border-slate-800 text-[9px] font-bold text-slate-400 text-center shrink-0">
                <div class="flex items-center justify-center gap-1">
                    <span class="w-2 h-2 rounded bg-blue-600 inline-block"></span>
                    <span>Dijawab</span>
                </div>
                <div class="flex items-center justify-center gap-1">
                    <span class="w-2 h-2 rounded bg-amber-500 inline-block"></span>
                    <span>Ragu</span>
                </div>
                <div class="flex items-center justify-center gap-1">
                    <span class="w-2 h-2 rounded bg-slate-800 border border-slate-700 inline-block"></span>
                    <span>Kosong</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL PERINGATAN PELANGGARAN (STRIKE 1 & 2 WARNING)       --}}
    {{-- ======================================================== --}}
    <div id="violationWarningModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-red-950/90 backdrop-blur-xl animate-fade-in">
        <div class="relative w-full max-w-sm sm:max-w-md bg-slate-900 border-2 border-red-500 rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-2xl text-center space-y-4">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-red-500/20 border border-red-500/40 flex items-center justify-center">
                <span class="iconify text-3xl text-red-400" data-icon="solar:shield-cross-bold-duotone"></span>
            </div>

            <div>
                <span id="violationWarningBadge" class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-red-500/20 text-red-400 border border-red-500/30">
                    Peringatan 1 dari 3
                </span>
                <h3 id="violationWarningTitle" class="text-base sm:text-lg font-black text-white mt-1.5">Terdeteksi Keluar Layar Ujian!</h3>
                <p id="violationWarningDesc" class="text-[11px] text-slate-300 mt-1.5 leading-relaxed">
                    Anda terdeteksi beralih tab, meminimalkan jendela, atau membuka aplikasi lain. Harap tetap fokus pada lembar pengerjaan.
                </p>
            </div>

            <div class="p-2.5 rounded-xl bg-red-950/50 border border-red-900/50 text-left space-y-1 text-xs text-red-200">
                <p id="violationRemainingTitle" class="font-bold text-red-300 text-[11px] flex items-center gap-1">
                    <span class="iconify text-sm" data-icon="solar:danger-triangle-bold"></span>
                    Toleransi Tersisa: 2 Kali Lagi
                </p>
                <p id="violationRemainingDesc" class="text-[10px] text-slate-400 leading-snug">
                    Jika Anda meninggalkan layar ujian hingga 3 kali, ujian akan <strong class="text-white">otomatis terkunci</strong> dan harus dibuka oleh panitia/admin.
                </p>
            </div>

            <div>
                <button type="button" onclick="tutupPeringatanDanFullscreen()"
                    class="w-full px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white font-black text-xs uppercase tracking-wider shadow-md shadow-red-600/30 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <span class="iconify text-sm" data-icon="solar:maximize-square-minimalistic-bold"></span>
                    <span>Kembali ke Layar Penuh</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL UJIAN TERKUNCI (STRIKE 3 - FINAL LOCKED)            --}}
    {{-- ======================================================== --}}
    <div id="violationResetModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-950/95 backdrop-blur-2xl">
        <div class="relative w-full max-w-sm sm:max-w-md bg-slate-900 border-2 border-red-600 rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-2xl text-center space-y-4">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-red-600/20 border border-red-500/40 flex items-center justify-center">
                <span class="iconify text-3xl text-red-500" data-icon="solar:lock-bold-duotone"></span>
            </div>

            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-red-600 text-white">
                    Ujian Terkunci (3x Pelanggaran)
                </span>
                <h3 class="text-base sm:text-lg font-black text-white mt-1.5">Batas Pelanggaran Terlampaui</h3>
                <p class="text-[11px] text-slate-300 mt-1 leading-relaxed">
                    Anda meninggalkan layar ujian sebanyak <strong class="text-red-400">3 kali</strong>. Sesi ujian Anda telah <strong class="text-white">dikunci oleh sistem</strong> demi menjaga integritas tes.
                </p>
            </div>

            <div class="p-3 rounded-xl bg-slate-800/80 border border-slate-700/80 text-[11px] text-slate-300 text-left space-y-1">
                <p class="font-bold text-amber-400 flex items-center gap-1.5">
                    <span class="iconify text-sm" data-icon="solar:info-circle-bold"></span>
                    Hubungi Panitia / Administrator
                </p>
                <p class="text-[10px] text-slate-400 leading-snug">
                    Kunci sesi ujian hanya dapat dibuka kembali oleh Administrator melalui Panel Admin MFLS.
                </p>
            </div>

            <div id="resetLoadingState" class="flex items-center justify-center gap-2 text-xs font-bold text-slate-400">
                <span class="w-3.5 h-3.5 border-2 border-red-500 border-t-transparent rounded-full animate-spin"></span>
                <span>Mengunci sesi ujian...</span>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL STATUS JARINGAN TERPUTUS (OFFLINE)                 --}}
    {{-- ======================================================== --}}
    <div id="networkOfflineModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div class="w-full max-w-sm bg-slate-900 border border-amber-500/40 rounded-2xl p-5 shadow-2xl text-center space-y-3">
            <div class="w-12 h-12 mx-auto rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center">
                <span class="iconify text-2xl text-amber-400" data-icon="solar:wifi-off-bold-duotone"></span>
            </div>
            <div>
                <h3 class="text-sm sm:text-base font-black text-white">Koneksi Terputus!</h3>
                <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                    Jaringan hilang. <strong class="text-white">Jangan menutup halaman</strong>, sistem menunggu sambungan kembali.
                </p>
            </div>
            <div class="px-3 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-[10px] text-amber-400 font-bold flex items-center justify-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
                <span>Menunggu jaringan aktif...</span>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- OVERLAY HASIL UJIAN (SUBMITTED)                          --}}
    {{-- ======================================================== --}}
    <div id="hasilBox" class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-xl hidden items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl sm:rounded-3xl shadow-2xl max-w-sm w-full p-6 text-center space-y-4">
            <div class="w-14 h-14 mx-auto bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center justify-center">
                <span class="iconify text-3xl text-emerald-400" data-icon="solar:check-circle-bold-duotone"></span>
            </div>
            <div>
                <h2 class="text-lg font-black text-white">Jawaban Terkirim!</h2>
                <p class="text-xs font-semibold text-slate-400 mt-1" id="hasilDetail"></p>
            </div>
            <a href="{{ route('ujian.index') }}"
                class="block w-full px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-black text-xs uppercase tracking-wider transition-all text-center">
                Kembali ke Daftar Ujian
            </a>
        </div>
    </div>

</div>

@push('scripts')
<script>
// ---------- Data State ----------
const SOALS = @json($soals);
const UJIAN_ID = {{ $ujian->id }};
const DURASI_TOTAL = {{ (int) $ujian->durasi * 60 }};
let answers = @json($savedAnswers ?? []);
if (Array.isArray(answers) && answers.length === 0) answers = {};
let ragu = {};
let current = SOALS.length ? Math.max(0, SOALS.findIndex(s => !answers[s.id])) : 0;
if (current < 0) current = 0;
let sisa = {{ (int) $sisaDetik }};
let submitted = false;
let saveTimer = null;
let dirty = false;
let examStarted = false;
let violationCount = 0;
let isHandlingViolation = false;

const OPSI = ['a', 'b', 'c', 'd', 'e'];

function esc(t) {
    const d = document.createElement('div');
    d.textContent = t == null ? '' : String(t);
    return d.innerHTML;
}

function terjawab() { 
    return Object.values(answers).filter(v => v).length; 
}

function updateStats() {
    const elJawab = document.getElementById('cntJawab');
    const elRagu = document.getElementById('cntRagu');
    if (elJawab) elJawab.textContent = terjawab();
    if (elRagu) elRagu.textContent = Object.values(ragu).filter(Boolean).length;
    renderPanel();
}

// ---------- Fullscreen & Pre-flight Gate ----------
function requestFullscreen() {
    const el = document.documentElement;
    if (el.requestFullscreen) {
        return el.requestFullscreen().catch(() => {});
    } else if (el.webkitRequestFullscreen) {
        return el.webkitRequestFullscreen().catch(() => {});
    } else if (el.msRequestFullscreen) {
        return el.msRequestFullscreen().catch(() => {});
    }
}

function exitFullscreen() {
    if (document.exitFullscreen) {
        document.exitFullscreen().catch(() => {});
    }
}

function mulaiUjianDenganLayarPenuh() {
    requestFullscreen();
    examStarted = true;
    const modal = document.getElementById('preFlightModal');
    if (modal) modal.classList.add('hidden');
    tick();
}

// ---------- Monitor Fokus Layar (High-Security Anti-Cheat) ----------
function handleFocusViolation(reason) {
    if (!examStarted || submitted || isHandlingViolation) return;
    isHandlingViolation = true;
    violationCount++;

    const badge = document.getElementById('violationCount');
    if (badge) {
        badge.textContent = `${violationCount}/3`;
        badge.className = violationCount === 1 ? 'text-amber-400 font-black' : (violationCount === 2 ? 'text-orange-400 font-black' : 'text-red-400 font-black');
    }

    if (violationCount === 1) {
        // Strike 1: Peringatan 1 dari 3 (Toleransi 2 lagi)
        const warnModal = document.getElementById('violationWarningModal');
        const badgeEl = document.getElementById('violationWarningBadge');
        const titleEl = document.getElementById('violationWarningTitle');
        const descEl = document.getElementById('violationWarningDesc');
        const remTitleEl = document.getElementById('violationRemainingTitle');
        const remDescEl = document.getElementById('violationRemainingDesc');

        if (badgeEl) badgeEl.textContent = 'Peringatan 1 dari 3';
        if (titleEl) titleEl.textContent = 'Terdeteksi Keluar Layar Ujian!';
        if (descEl) descEl.textContent = 'Anda terdeteksi beralih tab, meminimalkan jendela, atau membuka aplikasi lain. Harap tetap berada di lembar pengerjaan.';
        if (remTitleEl) remTitleEl.innerHTML = '<span class="iconify text-sm" data-icon="solar:danger-triangle-bold"></span> Toleransi Tersisa: 2 Kali Lagi';
        if (remDescEl) remDescEl.innerHTML = 'Jika Anda meninggalkan layar ujian hingga 3 kali, ujian akan <strong class="text-white">otomatis terkunci</strong>.';

        if (warnModal) {
            warnModal.classList.remove('hidden');
            warnModal.classList.add('flex');
        }
    } else if (violationCount === 2) {
        // Strike 2: Peringatan 2 dari 3 (Toleransi 1 lagi - TERAKHIR!)
        const warnModal = document.getElementById('violationWarningModal');
        const badgeEl = document.getElementById('violationWarningBadge');
        const titleEl = document.getElementById('violationWarningTitle');
        const descEl = document.getElementById('violationWarningDesc');
        const remTitleEl = document.getElementById('violationRemainingTitle');
        const remDescEl = document.getElementById('violationRemainingDesc');

        if (badgeEl) badgeEl.textContent = 'Peringatan 2 dari 3 (Terakhir!)';
        if (titleEl) titleEl.textContent = 'Peringatan Keras: Keluar Layar Ujian!';
        if (descEl) descEl.textContent = 'Ini adalah peringatan ke-2 Anda. Jika Anda beralih tab atau membuka aplikasi lain SEKALI LAGI, sesi ujian Anda akan langsung TERKUNCI!';
        if (remTitleEl) remTitleEl.innerHTML = '<span class="iconify text-sm" data-icon="solar:danger-triangle-bold"></span> Toleransi Tersisa: 1 Kali Lagi';
        if (remDescEl) remDescEl.innerHTML = '<strong class="text-white">PERINGATAN:</strong> Pelanggaran berikutnya akan membuat ujian TERKUNCI dan hanya dapat dibuka oleh admin.';

        if (warnModal) {
            warnModal.classList.remove('hidden');
            warnModal.classList.add('flex');
        }
    } else if (violationCount >= 3) {
        // Strike 3: KUNCI SESI KE SERVER & TAMPILKAN MODAL TERKUNCI
        const warnModal = document.getElementById('violationWarningModal');
        if (warnModal) warnModal.classList.add('hidden');

        const resetModal = document.getElementById('violationResetModal');
        if (resetModal) {
            resetModal.classList.remove('hidden');
            resetModal.classList.add('flex');
        }

        // Panggil endpoint kunci di backend
        eksekusiKunciPelanggaran();
    }
}

function tutupPeringatanDanFullscreen() {
    const warnModal = document.getElementById('violationWarningModal');
    if (warnModal) {
        warnModal.classList.add('hidden');
        warnModal.classList.remove('flex');
    }
    requestFullscreen();
    setTimeout(() => {
        isHandlingViolation = false;
    }, 500);
}

async function eksekusiKunciPelanggaran() {
    submitted = true;
    try {
        const res = await fetch(`/pendaftar/ujian/${UJIAN_ID}/kunci-pelanggaran`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });
        const data = await res.json();
        setTimeout(() => {
            window.location.href = data.redirect || "{{ route('ujian.index') }}";
        }, 2500);
    } catch (err) {
        setTimeout(() => {
            window.location.href = "{{ route('ujian.index') }}";
        }, 2500);
    }
}

async function eksekusiResetPelanggaran() {
    return eksekusiKunciPelanggaran();
}

// 1. Deteksi Ganti Tab (visibilitychange)
document.addEventListener('visibilitychange', () => {
    if (document.hidden && examStarted && !submitted) {
        handleFocusViolation('Pindah tab atau jendela disembunyikan');
    }
});

// 2. Deteksi Keluar Layar / Klik Aplikasi Lain (window.blur)
window.addEventListener('blur', () => {
    if (examStarted && !submitted) {
        handleFocusViolation('Fokus jendela browser hilang');
    }
});

// 3. Deteksi Keluar dari Mode Layar Penuh (fullscreenchange)
document.addEventListener('fullscreenchange', () => {
    if (!document.fullscreenElement && examStarted && !submitted && !isHandlingViolation) {
        handleFocusViolation('Keluar dari mode layar penuh');
    }
});

// ---------- Monitor Status Jaringan Internet ----------
function updateNetworkStatus() {
    const isOnline = navigator.onLine;
    const modal = document.getElementById('networkOfflineModal');
    const pill = document.getElementById('networkPill');

    if (!isOnline) {
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        if (pill) {
            pill.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-red-400 animate-pulse"></span> Terputus';
            pill.className = 'hidden md:inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[8px] font-bold bg-red-500/10 text-red-400 border border-red-500/20';
        }
    } else {
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        if (pill) {
            pill.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Stabil';
            pill.className = 'hidden md:inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[8px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
        }
    }
}

window.addEventListener('online', updateNetworkStatus);
window.addEventListener('offline', updateNetworkStatus);

// ---------- Panel Nomor Soal (Desktop & Mobile) ----------
function renderPanel() {
    const html = SOALS.map((s, i) => {
        let cls = 'bg-slate-800 text-slate-300 border-slate-700/80 hover:bg-slate-700';
        if (answers[s.id]) cls = 'bg-blue-600 text-white border-blue-500 shadow-sm shadow-blue-600/30';
        if (ragu[s.id]) cls = 'bg-amber-500 text-slate-950 font-black border-amber-400';
        if (i === current) cls += ' ring-2 ring-blue-400 ring-offset-1 ring-offset-slate-900';
        return `<button type="button" onclick="keSoalDanTutupMobile(${i})" class="h-8 rounded-lg border ${cls} font-bold text-[11px] transition-all flex items-center justify-center">${i + 1}</button>`;
    }).join('');

    const desktopPanel = document.getElementById('panelNomor');
    const mobilePanel = document.getElementById('panelNomorMobile');
    if (desktopPanel) desktopPanel.innerHTML = html;
    if (mobilePanel) mobilePanel.innerHTML = html;
}

function bukaDrawerNavigasiMobile() {
    const drawer = document.getElementById('mobileDrawerNav');
    if (drawer) {
        drawer.classList.remove('hidden');
        drawer.classList.add('flex');
    }
}

function tutupDrawerNavigasiMobile() {
    const drawer = document.getElementById('mobileDrawerNav');
    if (drawer) {
        drawer.classList.add('hidden');
        drawer.classList.remove('flex');
    }
}

function keSoalDanTutupMobile(i) {
    keSoal(i);
    tutupDrawerNavigasiMobile();
}

// ---------- Render Soal ----------
function keSoal(i) {
    if (i < 0 || i >= SOALS.length) return;
    current = i;
    simpanSekarang();
    renderSoal();
    renderPanel();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function renderSoal() {
    if (!SOALS.length) return;
    const s = SOALS[current];
    const elNoAktif = document.getElementById('noAktif');
    if (elNoAktif) elNoAktif.textContent = current + 1;

    let html = `<div class="text-xs sm:text-sm md:text-[15px] font-medium text-slate-200 leading-relaxed whitespace-pre-line">${esc(s.pertanyaan)}</div>`;
    if (s.gambar) {
        html += `<div class="mt-3 p-1.5 bg-slate-950/60 rounded-xl border border-slate-800 inline-block">
            <img src="${esc(s.gambar)}" alt="Gambar soal" class="rounded-lg max-h-52 md:max-h-64 object-contain" draggable="false">
        </div>`;
    }

    html += '<div class="space-y-2 md:space-y-2.5 mt-4">';
    for (const o of OPSI) {
        const teks = s['opsi_' + o];
        const gambar = s['opsi_' + o + '_image'];
        if (!teks && !gambar) continue;
        const aktif = answers[s.id] === o;
        html += `
        <button type="button" onclick="pilih('${o}')" draggable="false"
            class="w-full text-left flex items-start gap-2.5 sm:gap-3 p-2.5 sm:p-3 rounded-xl border transition-all ${aktif ? 'border-blue-500 bg-blue-600/15 shadow-sm shadow-blue-500/10' : 'border-slate-800/90 hover:border-slate-700 bg-slate-900/60 hover:bg-slate-800/40'}">
            <span class="shrink-0 w-6 h-6 sm:w-7 sm:h-7 rounded-lg flex items-center justify-center font-bold text-[10px] sm:text-xs transition-colors ${aktif ? 'bg-blue-600 text-white shadow-sm shadow-blue-600/30' : 'bg-slate-800 text-slate-400'}">${o.toUpperCase()}</span>
            <span class="flex-1 text-xs sm:text-[13px] font-normal sm:font-medium ${aktif ? 'text-white' : 'text-slate-300'} pt-0.5">${teks ? esc(teks) : ''}
                ${gambar ? `<div class="mt-1.5 p-1 bg-slate-950/60 rounded-lg border border-slate-800 inline-block"><img src="${esc(gambar)}" alt="Opsi ${o.toUpperCase()}" class="rounded max-h-32" draggable="false"></div>` : ''}
            </span>
        </button>`;
    }
    html += '</div>';

    document.getElementById('isiSoal').innerHTML = html;
    
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    const btnRagu = document.getElementById('btnRagu');

    if (btnPrev) btnPrev.disabled = current === 0;
    if (btnNext) btnNext.disabled = current === SOALS.length - 1;
    if (btnRagu) {
        btnRagu.className = `px-3 sm:px-3.5 py-2 rounded-xl font-bold text-xs uppercase tracking-wider transition-all border flex items-center gap-1 ${ragu[s.id] ? 'bg-amber-500 text-slate-950 border-amber-400 font-black' : 'bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border-amber-500/30'}`;
    }
}

function pilih(op) {
    const s = SOALS[current];
    answers[s.id] = answers[s.id] === op ? '' : op;
    renderSoal();
    updateStats();
    jadwalSimpan();
}

function toggleRagu() {
    const id = SOALS[current].id;
    ragu[id] = !ragu[id];
    renderSoal();
    updateStats();
}

// ---------- Autosave ----------
function jadwalSimpan() {
    dirty = true;
    const el = document.getElementById('autosaveInfo');
    if (el) el.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Menyimpan...';
    clearTimeout(saveTimer);
    saveTimer = setTimeout(simpanSekarang, 1000);
}

async function simpanSekarang() {
    clearTimeout(saveTimer);
    if (!dirty || sisa <= 0 || submitted) return;
    dirty = false;
    try {
        const res = await fetch(`/pendaftar/ujian/${UJIAN_ID}/draft`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ jawaban: answers }),
        });
        const el = document.getElementById('autosaveInfo');
        if (res.ok) {
            if (el) el.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Tersimpan';
            dirty = false;
        } else if (res.status === 403 || res.status === 422) {
            if (el) el.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Ditutup';
            autoSubmit();
        } else {
            if (el) el.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Gagal';
        }
    } catch (e) {
        const el = document.getElementById('autosaveInfo');
        if (el) el.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Gagal';
    }
}

// ---------- Timer Countdown ----------
function fmt(d) {
    const h = String(Math.floor(d / 3600)).padStart(2, '0');
    const m = String(Math.floor((d % 3600) / 60)).padStart(2, '0');
    const s = String(d % 60).padStart(2, '0');
    return `${h}:${m}:${s}`;
}

function tick() {
    if (!examStarted) return;
    const elTimer = document.getElementById('timer');
    if (elTimer) elTimer.textContent = fmt(Math.max(0, sisa));

    // Peringatan visual jika waktu < 5 menit (300 detik)
    const container = document.getElementById('timerContainer');
    if (sisa <= 300 && container) {
        container.className = 'px-2.5 sm:px-3 py-1 rounded-lg bg-red-950/60 border border-red-500/50 text-center animate-pulse';
        if (elTimer) elTimer.className = 'text-xs sm:text-sm font-black text-red-400 tabular-nums leading-tight';
    }

    if (sisa <= 0 && !submitted) {
        autoSubmit();
        return;
    }
    sisa--;
    setTimeout(tick, 1000);
}

// ---------- Konfirmasi & Submit Final ----------
function konfirmasiSelesai() {
    const kosong = SOALS.length - terjawab();
    Swal.fire({
        title: 'Selesaikan Ujian?',
        text: kosong > 0 
            ? `Masih ada ${kosong} soal belum terjawab. Soal yang kosong akan bernilai 0.` 
            : 'Seluruh jawaban akan dikirimkan secara permanen ke panitia.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kirim Sekarang',
        cancelButtonText: 'Periksa Kembali',
        confirmButtonColor: '#0870B8',
        cancelButtonColor: '#334155',
        background: '#0F172A',
        color: '#F8FAFC',
    }).then(r => {
        if (r.isConfirmed) autoSubmit();
    });
}

async function autoSubmit() {
    if (submitted) return;
    submitted = true;
    clearTimeout(saveTimer);
    const btn = document.getElementById('btnSelesai');
    if (btn) btn.disabled = true;

    try {
        const res = await fetch(`/pendaftar/ujian/${UJIAN_ID}/submit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ answers: answers || {} }),
        });
        const data = await res.json();

        if (res.ok && data.status === 'success') {
            const r = data.results || {};
            document.getElementById('hasilDetail').textContent =
                `Skor: ${r.score ?? '-'} / 100 (${r.correct_answers ?? '-'} benar dari ${r.total_questions ?? SOALS.length} butir).`;
            const box = document.getElementById('hasilBox');
            box.classList.remove('hidden');
            box.classList.add('flex');
            exitFullscreen();
        } else {
            submitted = false;
            if (btn) btn.disabled = false;
            Swal.fire({
                title: 'Pengiriman Gagal',
                text: data.message || 'Silakan coba beberapa saat lagi.',
                icon: 'error',
                background: '#0F172A',
                color: '#F8FAFC',
            });
        }
    } catch (e) {
        submitted = false;
        if (btn) btn.disabled = false;
        Swal.fire({
            title: 'Koneksi Bermasalah',
            text: 'Jawaban Anda telah tersimpan secara draft. Periksa jaringan Anda lalu coba kembali.',
            icon: 'warning',
            background: '#0F172A',
            color: '#F8FAFC',
        });
    }
}

// ---------- Lapisan Proteksi Anti-Copy & Anti-Inspeksi ----------
document.addEventListener('contextmenu', e => e.preventDefault());
document.addEventListener('copy', e => e.preventDefault());
document.addEventListener('cut', e => e.preventDefault());
document.addEventListener('paste', e => e.preventDefault());
document.addEventListener('dragstart', e => e.preventDefault());
document.addEventListener('selectstart', e => {
    if (e.target.closest('.noselect') || !e.target.closest('input, textarea')) {
        e.preventDefault();
    }
});

// Blokir Shortcut DevTools & Tombol Curang
document.addEventListener('keydown', e => {
    const k = e.key.toLowerCase();
    
    // F12 (DevTools)
    if (e.key === 'F12') {
        e.preventDefault();
        return false;
    }
    
    // Ctrl+Shift+I / J / C (DevTools Inspect)
    if ((e.ctrlKey || e.metaKey) && e.shiftKey && ['i', 'j', 'c'].includes(k)) {
        e.preventDefault();
        return false;
    }

    // Ctrl+U (View Source), Ctrl+S (Save), Ctrl+P (Print)
    if ((e.ctrlKey || e.metaKey) && ['u', 's', 'p'].includes(k)) {
        e.preventDefault();
        return false;
    }

    // Ctrl+C, Ctrl+X, Ctrl+V
    if ((e.ctrlKey || e.metaKey) && ['c', 'x', 'v'].includes(k)) {
        e.preventDefault();
        return false;
    }
});

// Peringatan sebelum menutup tab/refresh manual
window.addEventListener('beforeunload', e => {
    if (examStarted && !submitted && sisa > 0) {
        e.preventDefault();
        e.returnValue = 'Ujian sedang berlangsung! Jika Anda menutup halaman ini, sesi dapat dibatalkan.';
    }
});

// ---------- Inisialisasi ----------
renderSoal();
updateStats();

if ({{ (int) $sisaDetik }} <= 0) {
    document.getElementById('timer').textContent = '00:00:00';
    autoSubmit();
}
</script>
@endpush
@endsection

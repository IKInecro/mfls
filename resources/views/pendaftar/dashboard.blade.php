@extends('pendaftar.layout')

@section('content')
    <div class="space-y-8">
        <!-- Notification Section -->
        {{-- @if(($peserta->daftar->status ?? '') == 'lulus')
        <div
            class="relative overflow-hidden bg-emerald-600 p-8 rounded-[2.5rem] shadow-xl shadow-emerald-200 mb-8 border border-white/10 group">
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-20 -mt-20 group-hover:scale-110 transition-transform duration-700">
            </div>
            <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
                <div
                    class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center shrink-0 border border-white/20 shadow-lg">
                    <span class="iconify text-4xl text-white" data-icon="solar:verified-check-bold-duotone"></span>
                </div>
                <div class="text-center md:text-left flex-1">
                    <h3 class="text-white font-black text-xl md:text-2xl tracking-tight leading-none mb-1">Selamat! Anda
                        Lolos Seleksi Berkas</h3>
                    <p class="text-emerald-50/80 text-sm font-medium">Dokumen Anda telah diverifikasi oleh tim panitia.
                        Silakan tunggu informasi jadwal ujian/wawancara melalui email atau grup WhatsApp resmi.</p>
                </div>
                <div class="shrink-0 flex items-center gap-3">
                    <span
                        class="px-6 py-3 bg-white text-emerald-600 rounded-xl font-black text-xs uppercase tracking-widest shadow-lg">Tahap
                        Berikutnya</span>
                </div>
            </div>
        </div>
        @else
        <div
            class="relative overflow-hidden bg-red-600 p-8 rounded-[2.5rem] shadow-xl shadow-red-200 mb-8 border border-white/10 group">
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-20 -mt-20 group-hover:scale-110 transition-transform duration-700">
            </div>
            <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
                <div
                    class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center shrink-0 border border-white/20 shadow-lg">
                    <span class="iconify text-4xl text-white" data-icon="solar:close-circle-bold-duotone"></span>
                </div>
                <div class="text-center md:text-left flex-1">
                    <h3 class="text-white font-black text-xl md:text-2xl tracking-tight leading-none mb-1">Mohon Maaf, Anda
                        Tidak Lolos Seleksi Berkas</h3>
                    <p class="text-red-50/80 text-sm font-medium">Jangan patah semangat! Masih ada kesempatan melalui jalur
                        Beasiswa Mandiri. Hubungi kami via WA: 0811-8122-1792 atau 0811-8122-1791.</p>
                </div>
                <div class="shrink-0 flex items-center gap-3">
                    <a href="https://wa.me/6281181221792" target="_blank"
                        class="px-6 py-3 bg-white text-red-600 rounded-xl font-black text-xs uppercase tracking-widest shadow-lg">Info
                        Beasiswa</a>
                </div>
            </div>
        </div>
        @endif --}}

        @if(!$hasSupportingSubject)
            <!-- Missing Supporting Subject Warning -->
            <div class="relative overflow-hidden bg-navy-mnc p-6 rounded-[2rem] border border-primary-orange/30 shadow-xl mb-8">
                <div class="flex items-center gap-4">
                    <div
                        class="w-10 h-10 bg-primary-orange text-white rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-primary-orange/20">
                        <span class="iconify text-xl" data-icon="solar:danger-bold"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-white font-black text-xs uppercase tracking-tight">Mata Pelajaran Pendukung Belum
                            Lengkap</h3>
                        <p class="text-orange-100/60 text-[10px] font-medium leading-relaxed mt-1">Data nilai Anda memerlukan
                            minimal 2 mata pelajaran pendukung wajib untuk diproses.</p>
                    </div>
                    <a href="{{ route('pendaftar.nilai') }}"
                        class="px-5 py-2 bg-primary-orange text-white rounded-lg font-black text-[10px] uppercase tracking-widest hover:bg-white hover:text-navy-mnc transition-all whitespace-nowrap shadow-lg shadow-primary-orange/20">
                        Lengkapi
                    </a>
                </div>
            </div>
        @endif

        <!-- Banner Penutupan Pendaftaran -->
        <div class="relative overflow-hidden bg-red-600 p-6 rounded-[2rem] border border-red-400/30 shadow-xl mb-8">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-white text-red-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg">
                    <span class="iconify text-2xl" data-icon="solar:lock-bold-duotone"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-white font-black text-sm uppercase tracking-tight">Pendaftaran Telah Ditutup</h3>
                    <p class="text-red-100 text-xs font-medium leading-relaxed mt-1">Maaf, periode pengisian data dan berkas
                        pendaftaran telah ditutup. Tunggu informasi lebih lanjut mengenai tahapan seleksi berikutnya.</p>
                </div>
            </div>
        </div>

        <!-- Akses Ujian Online -->
        <a href="{{ route('ujian.index') }}" class="block group">
            <div class="relative overflow-hidden bg-navy-mnc p-6 rounded-[2rem] border border-primary-orange/30 shadow-xl hover:shadow-2xl transition-all mb-8">
                <div class="absolute top-0 right-0 w-40 h-40 bg-primary-orange/10 rounded-full blur-3xl -mr-10 -mt-10 group-hover:scale-125 transition-transform duration-700"></div>
                <div class="relative flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary-orange text-white rounded-2xl flex items-center justify-center shrink-0 shadow-lg shadow-primary-orange/30">
                        <span class="iconify text-2xl" data-icon="solar:monitor-smartphone-bold"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-white font-black text-sm uppercase tracking-tight">Ujian Online (CBT)</h3>
                        <p class="text-orange-100/60 text-xs font-medium leading-relaxed mt-1">Lihat daftar test, jadwal, dan kerjakan ujian yang sudah dibuka panitia.</p>
                    </div>
                    <span class="px-5 py-2.5 bg-primary-orange text-white rounded-xl font-black text-[10px] uppercase tracking-widest group-hover:bg-white group-hover:text-navy-mnc transition-all whitespace-nowrap shadow-lg">Buka &rarr;</span>
                </div>
            </div>
        </a>

        <!-- Welcome Banner Professional -->
        <div class="relative group">
            <div class="relative overflow-hidden bg-navy-mnc p-8 md:p-14 rounded-[3rem] shadow-2xl border border-white/5">
                <div class="absolute top-0 right-0 w-64 h-64 bg-primary-orange/5 rounded-full blur-3xl -mr-20 -mt-20"></div>

                <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-10">
                    <div class="max-w-xl text-center lg:text-left">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 bg-white/5 backdrop-blur-md rounded-full text-[9px] font-black uppercase tracking-[0.2em] text-primary-orange mb-6 border border-white/10">
                            <span class="relative flex h-1.5 w-1.5">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-orange opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-primary-orange"></span>
                            </span>
                            Scholarship Intake 2026
                        </div>
                        <h1 class="text-4xl md:text-5xl font-black text-white mb-4 leading-tight tracking-tight">
                            Halo, <span class="text-primary-orange italic">{{ explode(' ', $peserta->nama)[0] }}</span>
                        </h1>
                        <p class="text-orange-100/60 font-medium text-base mb-8 leading-relaxed max-w-md">
                            Selamat datang di portal seleksi. Pastikan setiap dokumen dan nilai Anda telah terverifikasi
                            dengan benar.
                        </p>

                        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3">
                            @if(($peserta->daftar->status ?? '') == 'lulus')
                                <div class="px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                                    <p
                                        class="text-emerald-400 font-black flex items-center gap-2 text-[10px] uppercase tracking-widest leading-none">
                                        <span class="iconify" data-icon="solar:verified-check-bold"></span> VERIFIED
                                    </p>
                                </div>
                            @else
                                <div class="px-4 py-2 bg-red-500/10 border border-red-500/20 rounded-xl">
                                    <p
                                        class="text-red-400 font-black flex items-center gap-2 text-[10px] uppercase tracking-widest leading-none">
                                        <span class="iconify" data-icon="solar:close-circle-bold"></span> TIDAK LOLOS
                                    </p>
                                </div>
                            @endif

                            <div class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl">
                                <p class="text-white/60 font-black text-[10px] uppercase tracking-widest leading-none">
                                    Angkatan: {{ $peserta->tahun_lulus }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Readiness Dashboard Style -->
                    <div class="relative shrink-0 scale-90 md:scale-100">
                        <div
                            class="relative w-48 h-48 rounded-full p-1 bg-white/5 border border-white/10 backdrop-blur-xl flex flex-col items-center justify-center shadow-2xl">
                            <div
                                class="text-[9px] font-black text-primary-orange uppercase tracking-[0.3em] mb-1 opacity-70">
                                Readiness</div>
                            <div class="text-5xl font-black text-white leading-none tracking-tighter">{{ $progress }}%</div>

                            <svg class="absolute inset-0 w-full h-full -rotate-90 pointer-events-none"
                                viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="46" fill="none" stroke="rgba(255,255,255,0.03)"
                                    stroke-width="4" />
                                <circle cx="50" cy="50" r="46" fill="none" stroke="#f97316" stroke-width="4"
                                    stroke-dasharray="289" stroke-dashoffset="{{ 289 - (289 * $progress / 100) }}"
                                    stroke-linecap="round"
                                    class="transition-all duration-1000 shadow-[0_0_15px_rgba(249,115,22,0.5)]" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Card Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $stats = [
                    ['label' => 'Personal', 'value' => 'Biodata', 'icon' => 'solar:user-bold-duotone', 'route' => 'pendaftar.biodata'],
                    ['label' => 'Academic', 'value' => 'Nilai Rapor', 'icon' => 'solar:chart-square-bold-duotone', 'route' => 'pendaftar.nilai'],
                    ['label' => 'Files', 'value' => 'Berkas', 'icon' => 'solar:document-bold-duotone', 'route' => 'pendaftar.berkas'],
                    ['label' => 'Social', 'value' => 'Campaign', 'icon' => 'solar:gallery-bold-duotone', 'route' => 'pendaftar.twibbon'],
                ];
            @endphp

            @foreach($stats as $stat)
                <a href="{{ route($stat['route']) }}"
                    class="group bg-white p-5 rounded-[1.5rem] border-2 border-slate-50 hover:border-primary-orange/30 hover:shadow-xl hover:shadow-orange-100 transition-all duration-300">
                    <div class="flex flex-col gap-4">
                        <div
                            class="w-12 h-12 bg-navy-mnc text-primary-orange rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg shadow-navy-mnc/10">
                            <span class="iconify text-2xl" data-icon="{{ $stat['icon'] }}"></span>
                        </div>
                        <div>
                            <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 opacity-70">
                                {{ $stat['label'] }}</h4>
                            <p class="text-[11px] font-black text-navy-mnc uppercase tracking-widest">{{ $stat['value'] }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Timeline & Resources Container -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-10">
            <!-- Activity Timeline -->
            <div class="lg:col-span-2">
                <div
                    class="bg-white rounded-[2.5rem] border-2 border-slate-50 p-8 md:p-10 shadow-sm relative overflow-hidden h-full">
                    <div class="flex items-center justify-between mb-10 relative z-10 text-navy-mnc">
                        <h3 class="text-xl font-black flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-navy-mnc rounded-xl flex items-center justify-center text-primary-orange shadow-lg shadow-navy-mnc/20">
                                <span class="iconify" data-icon="solar:history-bold-duotone" data-width="22"></span>
                            </div>
                            Pusat Aktivitas
                        </h3>
                    </div>

                    <div class="relative pl-10 border-l-2 border-slate-50 space-y-10">
                        @forelse($history as $h)
                            <div class="relative group">
                                <!-- Icon Node -->
                                <div
                                    class="absolute -left-[58px] top-0 w-9 h-9 rounded-xl bg-white border-2 border-slate-50 shadow-sm flex items-center justify-center group-hover:border-primary-orange transition-colors">
                                    <div
                                        class="w-2 h-2 bg-slate-200 rounded-full group-hover:bg-primary-orange transition-colors">
                                    </div>
                                </div>

                                <div
                                    class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50 p-4 rounded-2xl border border-transparent group-hover:border-slate-100 group-hover:bg-white transition-all">
                                    <div class="max-w-lg">
                                        <h4
                                            class="text-[11px] font-black text-navy-mnc uppercase tracking-widest group-hover:text-primary-orange transition-colors">
                                            {{ $h['title'] }}</h4>
                                        <p
                                            class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1 opacity-70">
                                            {{ $h['desc'] }}</p>
                                    </div>
                                    <div class="shrink-0">
                                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">
                                            {{ $h['date'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 opacity-30">
                                <p class="text-[10px] font-black uppercase tracking-widest text-navy-mnc">Belum ada riwayat
                                    aktivitas</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Resources Section (Sidebar Inside Dashboard) -->
            <div class="space-y-6">
                <div class="bg-navy-mnc p-8 rounded-[2.5rem] shadow-xl shadow-navy-mnc/20 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-primary-orange/5 rounded-full blur-2xl -mr-16 -mt-16">
                    </div>
                    <h3 class="text-white font-black text-xs uppercase tracking-[0.2em] mb-8 relative z-10">Resources Hub
                    </h3>

                    <div class="space-y-4 relative z-10">
                        <a href="https://chat.whatsapp.com/CemfyneCf8U30hN5uyXL1e?mode=gi_t" target="_blank"
                            class="flex items-center gap-4 p-4 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 hover:border-primary-orange/50 transition-all group/item">
                            <div
                                class="w-10 h-10 bg-white/5 text-primary-orange rounded-lg flex items-center justify-center shrink-0 border border-white/10 group-hover/item:scale-110 transition-transform">
                                <span class="iconify text-xl" data-icon="solar:users-group-rounded-bold-duotone"></span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[8px] text-orange-400/60 font-black uppercase tracking-widest mb-1">Community
                                </p>
                                <p class="text-white font-black text-[10px] uppercase tracking-tight truncate">WhatsApp
                                    Group</p>
                            </div>
                        </a>

                        <a href="https://instagram.com/beasiswamncu" target="_blank"
                            class="flex items-center gap-4 p-4 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 hover:border-primary-orange/50 transition-all group/item">
                            <div
                                class="w-10 h-10 bg-white/5 text-primary-orange rounded-lg flex items-center justify-center shrink-0 border border-white/10 group-hover/item:scale-110 transition-transform">
                                <span class="iconify text-xl" data-icon="solar:camera-bold-duotone"></span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[8px] text-orange-400/60 font-black uppercase tracking-widest mb-1">Official
                                    IG</p>
                                <p class="text-white font-black text-[10px] uppercase tracking-tight truncate">@beasiswamncu
                                </p>
                            </div>
                        </a>
                    </div>

                    <!-- Support Box -->
                    <div class="mt-8 p-6 bg-white/5 rounded-3xl border border-white/10 relative z-10 backdrop-blur-sm">
                        <p class="text-[9px] font-black text-primary-orange uppercase tracking-widest mb-2 leading-none">
                            Need Help?</p>
                        <p class="text-[10px] text-orange-100/40 font-medium mb-6 leading-relaxed">Admin kami siap membantu
                            kendala sistem pendaftaran Anda.</p>
                        <a href="https://wa.me/6285880059189"
                            class="w-full flex items-center justify-center gap-2 bg-primary-orange text-white text-[9px] font-black py-4 rounded-xl hover:bg-white hover:text-navy-mnc transition-all shadow-lg shadow-primary-orange/20 uppercase tracking-widest">
                            Contact Support <span class="iconify" data-icon="solar:arrow-right-up-bold"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
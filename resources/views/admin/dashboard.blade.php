@extends('layouts.admin')

@section('content')
<div class="space-y-6 sm:space-y-8">

    <!-- 1. EXECUTIVE WELCOME BANNER (Deep Midnight Navy & Royal Blue) -->
    <div class="relative overflow-hidden bg-[#0B1120] text-white p-6 sm:p-8 lg:p-10 rounded-3xl border border-slate-800 shadow-xl shadow-slate-950/20">
        <!-- Subtle Ambient Radial Lights -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6 sm:gap-8">
            <div class="space-y-3 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/5 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-widest text-blue-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                    Sistem Seleksi Terpadu &bull; MFLS Ajaran 2027
                </div>
                
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-white leading-tight">
                    Portal Manajemen Seleksi <span class="text-blue-400">MFLS</span>
                </h1>
                
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                    Selamat datang kembali, <strong class="text-white">{{ auth()->user()->nama ?? auth()->user()->name ?? 'Administrator' }}</strong>. 
                    Pantau statistik pendaftar, verifikasi kelengkapan dokumen, dan kelola seleksi beasiswa dalam satu panel terintegrasi.
                </p>

                <!-- Quick Status Indicators -->
                <div class="flex flex-wrap items-center gap-2 pt-1 text-[11px]">
                    <span class="px-2.5 py-1 rounded-lg bg-slate-900/80 border border-slate-800 text-slate-300 font-semibold flex items-center gap-1.5">
                        <span class="iconify text-blue-400" data-icon="solar:shield-check-bold"></span>
                        Peran: <strong class="text-white uppercase">{{ auth()->user()->role ?? 'Admin' }}</strong>
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-slate-900/80 border border-slate-800 text-slate-300 font-semibold flex items-center gap-1.5">
                        <span class="iconify text-emerald-400" data-icon="solar:check-circle-bold"></span>
                        Server Status: <strong class="text-emerald-400">Aktif & Optimal</strong>
                    </span>
                </div>
            </div>

            <!-- Mini Daily Stat Card -->
            <div class="bg-slate-900/90 border border-slate-800 p-5 sm:p-6 rounded-2xl shadow-inner text-center sm:text-left lg:text-center min-w-[200px] shrink-0">
                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Pendaftar Hari Ini</div>
                <div class="text-3xl sm:text-4xl font-black text-white tracking-tight">{{ number_format($todayPendaftar) }}</div>
                <div class="flex items-center justify-center sm:justify-start lg:justify-center gap-1.5 text-xs font-bold mt-2 {{ $growth >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                    <span class="iconify text-sm" data-icon="{{ $growth >= 0 ? 'solar:arrow-up-linear' : 'solar:arrow-down-linear' }}"></span>
                    <span>{{ round(abs($growth)) }}%</span> 
                    <span class="text-slate-400 font-normal text-[11px]">vs kemarin</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-800/80 text-[10px] text-slate-400">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- PALUGADA HIGH-PRIORITY ACTION ALERT (If Any Pending or User is Palugada) -->
    @php
        $pendingPalugada = \App\Models\Daftar::where('status', 'diajukan_palugada')->count();
    @endphp
    @if($pendingPalugada > 0 && (auth()->user()->role === 'palugada' || auth()->user()->role === 'admin'))
    <div class="p-4 sm:p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-500 flex items-center justify-center shrink-0 border border-amber-500/30">
                <span class="iconify text-xl" data-icon="solar:shield-warning-bold-duotone"></span>
            </div>
            <div>
                <h4 class="text-xs sm:text-sm font-bold text-slate-900 leading-tight">
                    Terdapat <span class="text-amber-600 font-black">{{ $pendingPalugada }} Pendaftar</span> Menunggu Verifikasi Final Palugada
                </h4>
                <p class="text-[11px] sm:text-xs text-slate-600 mt-0.5">
                    Berkas telah disetujui panitia dan memerlukan keputusan persetujuan akhir sebelum penetapan kelulusan.
                </p>
            </div>
        </div>
        <a href="{{ route('admin.palugada.index') }}" 
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold transition-all shadow-sm shrink-0">
            <span>Buka Hub Verifikasi</span>
            <span class="iconify text-sm" data-icon="solar:arrow-right-linear"></span>
        </a>
    </div>
    @endif

    <!-- 2. KEY METRIC CARDS (4 Cards Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <!-- Metric 1: Total Pendaftar -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:border-blue-500/40 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Kandidat</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0870B8] flex items-center justify-center border border-blue-100">
                    <span class="iconify text-xl" data-icon="solar:users-group-rounded-bold-duotone"></span>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalPendaftar) }}</div>
            <div class="flex items-center justify-between text-xs text-slate-500 mt-2 pt-2 border-t border-slate-100">
                <span>Pendaftar Terdaftar</span>
                <span class="font-bold text-blue-600">Aktif</span>
            </div>
        </div>

        <!-- Metric 2: Dokumen Lengkap -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:border-emerald-500/40 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kelengkapan</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                    <span class="iconify text-xl" data-icon="solar:folder-check-bold-duotone"></span>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalLengkap) }}</div>
            <div class="flex items-center justify-between text-xs text-slate-500 mt-2 pt-2 border-t border-slate-100">
                <span>Dokumen 100% Selesai</span>
                <span class="font-bold text-emerald-600">
                    {{ $totalPendaftar > 0 ? round(($totalLengkap / $totalPendaftar) * 100, 1) : 0 }}%
                </span>
            </div>
        </div>

        <!-- Metric 3: Penerima Beasiswa / Lulus -->
        @php
            $totalLulus = \App\Models\Daftar::where('status', 'lulus')->count();
        @endphp
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:border-amber-500/40 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Hasil Seleksi</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100">
                    <span class="iconify text-xl" data-icon="solar:medal-star-bold-duotone"></span>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalLulus) }}</div>
            <div class="flex items-center justify-between text-xs text-slate-500 mt-2 pt-2 border-t border-slate-100">
                <span>Penerima Beasiswa</span>
                <span class="font-bold text-amber-600">Tahap Akhir</span>
            </div>
        </div>

        <!-- Metric 4: Kunjungan Hari Ini -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:border-blue-500/40 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Trafik Web</span>
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center border border-sky-100">
                    <span class="iconify text-xl" data-icon="solar:eye-bold-duotone"></span>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ number_format($todayViews) }}</div>
            <div class="flex items-center justify-between text-xs text-slate-500 mt-2 pt-2 border-t border-slate-100">
                <span>Views Hari Ini</span>
                <span class="font-bold {{ $viewsGrowth >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ round(abs($viewsGrowth)) }}% {{ $viewsGrowth >= 0 ? 'naik' : 'turun' }}
                </span>
            </div>
        </div>

    </div>

    <!-- 3. MAIN ANALYTICS GRID: CHART + TOP PAGES -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left (2 Cols): Registration Trend Chart -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-2">
                        <span class="iconify text-[#0870B8] text-xl" data-icon="solar:chart-2-bold-duotone"></span>
                        Tren Pendaftaran Baru
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Statistik jumlah pembuatan akun pendaftar 7 hari terakhir.</p>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-blue-50 text-[#0870B8] text-xs font-bold self-start sm:self-auto border border-blue-100">
                    <span class="w-2 h-2 rounded-full bg-[#0870B8]"></span>
                    <span>7 Hari Terakhir</span>
                </div>
            </div>

            <!-- Canvas Container -->
            <div class="relative h-[280px] sm:h-[320px] w-full">
                <canvas id="regChart"></canvas>
            </div>
        </div>

        <!-- Right (1 Col): Most Visited Pages -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="mb-5 pb-4 border-b border-slate-100">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <span class="iconify text-sky-600 text-lg" data-icon="solar:layers-bold-duotone"></span>
                        Halaman Terpopuler
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Halaman portal yang paling sering diakses.</p>
                </div>

                <div class="space-y-3">
                    @forelse($topPages as $index => $page)
                        @php
                            $path = parse_url($page->url, PHP_URL_PATH) ?: '/';
                            $maxViews = $topPages->max('views') ?: 1;
                            $pct = round(($page->views / $maxViews) * 100);
                        @endphp
                        <div class="p-3 rounded-xl bg-slate-50/80 hover:bg-blue-50/40 border border-slate-100 hover:border-blue-100 transition-colors">
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-5 h-5 rounded-md bg-white text-slate-700 text-[10px] font-black flex items-center justify-center border border-slate-200 shrink-0">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-800 truncate" title="{{ $page->url }}">
                                        {{ $path }}
                                    </span>
                                </div>
                                <span class="text-xs font-black text-[#0870B8] shrink-0">
                                    {{ number_format($page->views) }} <span class="text-[10px] font-normal text-slate-400">views</span>
                                </span>
                            </div>
                            <div class="w-full bg-slate-200/80 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-[#0870B8] h-full rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-xs text-slate-400">Belum ada rekaman lalu lintas web.</div>
                    @endforelse
                </div>
            </div>

            <!-- Website Insights Pill Stats -->
            <div class="mt-6 pt-4 border-t border-slate-100 grid grid-cols-2 gap-3 text-center">
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Kunjungan</div>
                    <div class="text-sm font-black text-slate-800 mt-0.5">{{ number_format($totalViews) }}</div>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pengunjung Unik</div>
                    <div class="text-sm font-black text-slate-800 mt-0.5">{{ number_format($uniqueVisitorsToday) }}</div>
                </div>
            </div>
        </div>

    </div>

    <!-- 4. BOTTOM ROW: RECENT ACTIVITY LOGS & QUICK SHORTCUTS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left (2 Cols): Recent System Logs -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-2">
                        <span class="iconify text-[#0870B8] text-xl" data-icon="solar:history-bold-duotone"></span>
                        Log Aktivitas Terkini
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Catatan riwayat tindakan pengguna & sistem secara berkala.</p>
                </div>
                <a href="{{ route('admin.activity_log.index') }}" 
                   class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-[#0870B8] text-slate-600 hover:text-white text-xs font-bold transition-all inline-flex items-center gap-1.5">
                    <span>Semua Log</span>
                    <span class="iconify text-xs" data-icon="solar:arrow-right-linear"></span>
                </a>
            </div>

            <div class="space-y-4 max-h-[360px] overflow-y-auto pr-1">
                @forelse($riwayats as $log)
                    <div class="p-3.5 rounded-2xl bg-slate-50/60 hover:bg-slate-50 border border-slate-100 flex items-start gap-3.5 transition-colors">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#0870B8] flex items-center justify-center shrink-0 border border-blue-100 mt-0.5">
                            <span class="iconify text-base" data-icon="solar:user-bold-duotone"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center justify-between gap-1 mb-1">
                                <span class="text-xs font-bold text-slate-800">{{ $log->aksi }}</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    {{ $log->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ $log->deskripsi }}
                            </p>
                            <div class="mt-1.5 flex items-center gap-2 text-[10px] text-slate-400">
                                <span class="font-semibold text-slate-500">{{ $log->pelaku->nama ?? 'Sistem MFLS' }}</span>
                                <span>&bull;</span>
                                <span>{{ $log->created_at->format('d M Y, H:i') }} WIB</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-xs text-slate-400">Belum ada riwayat aktivitas yang tercatat.</div>
                @endforelse
            </div>
        </div>

        <!-- Right (1 Col): Quick System Shortcuts -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="mb-5 pb-4 border-b border-slate-100">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <span class="iconify text-amber-500 text-lg" data-icon="solar:widget-5-bold-duotone"></span>
                        Akses Cepat Panel
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pintasan menuju modul manajemen prioritas.</p>
                </div>

                <div class="space-y-2.5">
                    <a href="{{ route('admin.pendaftar.index') }}" 
                       class="flex items-center justify-between p-3.5 rounded-xl border border-slate-100 hover:border-blue-200 bg-slate-50/50 hover:bg-blue-50/40 text-slate-700 hover:text-[#0870B8] transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-100/60 text-[#0870B8] flex items-center justify-center">
                                <span class="iconify text-base" data-icon="solar:users-group-rounded-bold-duotone"></span>
                            </div>
                            <div>
                                <div class="text-xs font-bold leading-tight">Seleksi Administrasi</div>
                                <div class="text-[10px] text-slate-400">Verifikasi berkas & biodata</div>
                            </div>
                        </div>
                        <span class="iconify text-slate-400 group-hover:translate-x-1 transition-transform" data-icon="solar:arrow-right-linear"></span>
                    </a>

                    @if(Route::has('admin.panel_ujian.index'))
                    <a href="{{ route('admin.panel_ujian.index') }}" 
                       class="flex items-center justify-between p-3.5 rounded-xl border border-slate-100 hover:border-blue-200 bg-slate-50/50 hover:bg-blue-50/40 text-slate-700 hover:text-[#0870B8] transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-100/60 text-[#0870B8] flex items-center justify-center">
                                <span class="iconify text-base" data-icon="solar:monitor-smartphone-bold-duotone"></span>
                            </div>
                            <div>
                                <div class="text-xs font-bold leading-tight">Panel Ujian CBT</div>
                                <div class="text-[10px] text-slate-400">Jadwal & monitoring ujian</div>
                            </div>
                        </div>
                        <span class="iconify text-slate-400 group-hover:translate-x-1 transition-transform" data-icon="solar:arrow-right-linear"></span>
                    </a>
                    @endif

                    <a href="{{ route('admin.pengaturan.pendaftaran.edit') }}" 
                       class="flex items-center justify-between p-3.5 rounded-xl border border-slate-100 hover:border-blue-200 bg-slate-50/50 hover:bg-blue-50/40 text-slate-700 hover:text-[#0870B8] transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-100/60 text-[#0870B8] flex items-center justify-center">
                                <span class="iconify text-base" data-icon="solar:calendar-bold-duotone"></span>
                            </div>
                            <div>
                                <div class="text-xs font-bold leading-tight">Pengaturan Jadwal</div>
                                <div class="text-[10px] text-slate-400">Buka / tutup pendaftaran</div>
                            </div>
                        </div>
                        <span class="iconify text-slate-400 group-hover:translate-x-1 transition-transform" data-icon="solar:arrow-right-linear"></span>
                    </a>

                    @if(Route::has('admin.beasiswa.index'))
                    <a href="{{ route('admin.beasiswa.index') }}" 
                       class="flex items-center justify-between p-3.5 rounded-xl border border-slate-100 hover:border-blue-200 bg-slate-50/50 hover:bg-blue-50/40 text-slate-700 hover:text-[#0870B8] transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-100/60 text-[#0870B8] flex items-center justify-center">
                                <span class="iconify text-base" data-icon="solar:cup-star-bold-duotone"></span>
                            </div>
                            <div>
                                <div class="text-xs font-bold leading-tight">Database Beasiswa</div>
                                <div class="text-[10px] text-slate-400">Kalkulasi skor kelulusan</div>
                            </div>
                        </div>
                        <span class="iconify text-slate-400 group-hover:translate-x-1 transition-transform" data-icon="solar:arrow-right-linear"></span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Program Info Card -->
            <div class="mt-6 p-4 rounded-2xl bg-[#0B1120] text-white text-xs">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                    <span class="font-bold tracking-tight">MNC University 2027</span>
                </div>
                <p class="text-slate-400 text-[11px] leading-relaxed">
                    Pastikan jadwal seleksi dan batas verifikasi berkas selalu disinkronkan dengan kalender akademik resmi.
                </p>
            </div>
        </div>

    </div>

</div>

<!-- Chart.js Engine (Royal Blue Palette) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('regChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    
    // Royal Blue Gradient Fill
    let gradient = ctx.createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0, 'rgba(8, 112, 184, 0.28)'); // #0870B8
    gradient.addColorStop(1, 'rgba(8, 112, 184, 0.01)');

    const chartData = @json($dailyTrend);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(d => {
                const date = new Date(d.date);
                return date.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short' });
            }),
            datasets: [{
                label: 'Pendaftar Baru',
                data: chartData.map(d => d.total),
                borderColor: '#0870B8',
                borderWidth: 3,
                backgroundColor: gradient,
                fill: true,
                tension: 0.35,
                pointRadius: 5,
                pointBackgroundColor: '#FFFFFF',
                pointBorderColor: '#0870B8',
                pointBorderWidth: 2.5,
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#0870B8',
                pointHoverBorderColor: '#FFFFFF',
                pointHoverBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0B1120',
                    titleColor: '#FFFFFF',
                    bodyColor: '#93C5FD',
                    borderColor: '#1E293B',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 12,
                    displayColors: false,
                    titleFont: { family: 'Plus Jakarta Sans', weight: 'bold', size: 12 },
                    bodyFont: { family: 'Plus Jakarta Sans', weight: 'bold', size: 13 },
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' Pendaftar Baru';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { 
                        color: '#F1F5F9',
                        drawBorder: false
                    },
                    ticks: { 
                        font: { weight: '600', family: 'Plus Jakarta Sans', size: 10 }, 
                        color: '#94A3B8',
                        precision: 0,
                        padding: 8
                    },
                    border: { display: false }
                },
                x: {
                    grid: { display: false },
                    ticks: { 
                        font: { weight: '600', family: 'Plus Jakarta Sans', size: 10 }, 
                        color: '#94A3B8',
                        padding: 8
                    },
                    border: { display: false }
                }
            }
        }
    });
});
</script>
@endsection

@extends('layouts.admin')

@section('content')
@php
    $isDetailed = request('view') === 'detailed';
@endphp

<!-- Header & Quick Metrics -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <!-- Total Pendaftar -->
    <a href="{{ route('admin.pendaftar.index') }}" 
       class="bg-[#0B1120] rounded-3xl p-6 text-white shadow-xl shadow-slate-950/15 border border-slate-800 relative overflow-hidden group hover:border-blue-500/50 transition-all">
        <div class="absolute right-0 top-0 w-28 h-28 bg-blue-600/20 rounded-full blur-2xl -mr-8 -mt-8 group-hover:scale-125 transition-transform"></div>
        <div class="flex items-center justify-between relative z-10">
            <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <span class="iconify text-[#0870B8] text-lg" data-icon="solar:users-group-rounded-bold"></span>
                Total Pendaftar
            </span>
            <span class="text-[10px] font-black bg-blue-500/20 text-blue-300 px-2.5 py-0.5 rounded-full border border-blue-500/30">Semua</span>
        </div>
        <p class="text-3xl font-black mt-3 text-white tracking-tight">{{ $counts['total'] ?? $pendaftars->count() }}</p>
        <p class="text-[11px] font-bold text-slate-400 mt-2 flex items-center gap-1">
            <span class="iconify text-slate-500" data-icon="solar:info-circle-linear"></span> Klik untuk reset filter
        </p>
    </a>

    <!-- Menunggu Verifikasi -->
    <a href="{{ route('admin.pendaftar.index', array_merge(request()->query(), ['status' => 'menunggu'])) }}" 
       class="bg-white rounded-3xl p-6 border {{ request('status') === 'menunggu' ? 'border-amber-400 ring-2 ring-amber-400/20 bg-amber-50/20' : 'border-slate-100 hover:border-amber-200' }} shadow-sm relative overflow-hidden group transition-all">
        <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-full blur-2xl -mr-6 -mt-6 group-hover:scale-125 transition-transform"></div>
        <div class="flex items-center justify-between relative z-10">
            <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <span class="iconify text-amber-500 text-lg" data-icon="solar:clock-circle-bold"></span>
                Menunggu
            </span>
            <span class="text-[10px] font-black bg-amber-100 text-amber-700 px-2.5 py-0.5 rounded-full">Perlu Cek</span>
        </div>
        <p class="text-3xl font-black text-slate-800 mt-3 tracking-tight">{{ $counts['menunggu'] ?? 0 }}</p>
        <p class="text-[11px] font-bold text-amber-600 mt-2">Belum Diverifikasi</p>
    </a>

    <!-- Lolos Administrasi -->
    <a href="{{ route('admin.pendaftar.index', array_merge(request()->query(), ['status' => 'lulus'])) }}" 
       class="bg-white rounded-3xl p-6 border {{ request('status') === 'lulus' ? 'border-emerald-400 ring-2 ring-emerald-400/20 bg-emerald-50/20' : 'border-slate-100 hover:border-emerald-200' }} shadow-sm relative overflow-hidden group transition-all">
        <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-full blur-2xl -mr-6 -mt-6 group-hover:scale-125 transition-transform"></div>
        <div class="flex items-center justify-between relative z-10">
            <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <span class="iconify text-emerald-500 text-lg" data-icon="solar:check-circle-bold"></span>
                Lolos Seleksi
            </span>
            <span class="text-[10px] font-black bg-emerald-100 text-emerald-700 px-2.5 py-0.5 rounded-full">Lulus</span>
        </div>
        <p class="text-3xl font-black text-slate-800 mt-3 tracking-tight">{{ $counts['lulus'] ?? 0 }}</p>
        <p class="text-[11px] font-bold text-emerald-600 mt-2">Administrasi Terverifikasi</p>
    </a>

    <!-- Dokumen 100% Lengkap -->
    <a href="{{ route('admin.pendaftar.index', array_merge(request()->query(), ['progress' => '100'])) }}" 
       class="bg-white rounded-3xl p-6 border {{ request('progress') === '100' ? 'border-blue-400 ring-2 ring-blue-400/20 bg-blue-50/20' : 'border-slate-100 hover:border-blue-200' }} shadow-sm relative overflow-hidden group transition-all">
        <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-full blur-2xl -mr-6 -mt-6 group-hover:scale-125 transition-transform"></div>
        <div class="flex items-center justify-between relative z-10">
            <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <span class="iconify text-[#0870B8] text-lg" data-icon="solar:document-bold"></span>
                Berkas Lengkap
            </span>
            <span class="text-[10px] font-black bg-blue-100 text-blue-700 px-2.5 py-0.5 rounded-full">100%</span>
        </div>
        <p class="text-3xl font-black text-slate-800 mt-3 tracking-tight">{{ $counts['lengkap'] ?? 0 }}</p>
        <p class="text-[11px] font-bold text-[#0870B8] mt-2">Semua Berkas Terunggah</p>
    </a>
</div>

<!-- Main Table Container -->
<div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden">
    
    <!-- Quick Status Tabs -->
    <div class="px-6 md:px-8 pt-6 pb-2 border-b border-slate-100 flex items-center gap-2 overflow-x-auto scrollbar-none">
        @php
            $activeStatus = request('status');
            $activeProgress = request('progress');
        @endphp
        
        <a href="{{ route('admin.pendaftar.index', request()->except(['status', 'progress', 'page'])) }}" 
           class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap {{ (!$activeStatus && !$activeProgress) ? 'bg-[#0870B8] text-white shadow-md shadow-blue-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            <span>Semua</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ (!$activeStatus && !$activeProgress) ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $counts['total'] ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.pendaftar.index', array_merge(request()->except(['page', 'progress']), ['status' => 'menunggu'])) }}" 
           class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap {{ ($activeStatus === 'menunggu') ? 'bg-amber-500 text-white shadow-md shadow-amber-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            <span class="w-2 h-2 rounded-full {{ ($activeStatus === 'menunggu') ? 'bg-white' : 'bg-amber-500' }}"></span>
            <span>Menunggu Verifikasi</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($activeStatus === 'menunggu') ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $counts['menunggu'] ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.pendaftar.index', array_merge(request()->except(['page', 'progress']), ['status' => 'lulus'])) }}" 
           class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap {{ ($activeStatus === 'lulus') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            <span class="w-2 h-2 rounded-full {{ ($activeStatus === 'lulus') ? 'bg-white' : 'bg-emerald-500' }}"></span>
            <span>Lulus</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($activeStatus === 'lulus') ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $counts['lulus'] ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.pendaftar.index', array_merge(request()->except(['page', 'progress']), ['status' => 'diajukan_palugada'])) }}" 
           class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap {{ ($activeStatus === 'diajukan_palugada') ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            <span class="w-2 h-2 rounded-full {{ ($activeStatus === 'diajukan_palugada') ? 'bg-white' : 'bg-blue-500' }}"></span>
            <span>Double Check (Palugada)</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($activeStatus === 'diajukan_palugada') ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $counts['palugada'] ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.pendaftar.index', array_merge(request()->except(['page', 'progress']), ['status' => 'tidak_lulus'])) }}" 
           class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap {{ ($activeStatus === 'tidak_lulus') ? 'bg-rose-600 text-white shadow-md shadow-rose-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            <span class="w-2 h-2 rounded-full {{ ($activeStatus === 'tidak_lulus') ? 'bg-white' : 'bg-rose-500' }}"></span>
            <span>Ditolak</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($activeStatus === 'tidak_lulus') ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $counts['tidak_lulus'] ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.pendaftar.index', array_merge(request()->except(['page', 'status']), ['progress' => '100'])) }}" 
           class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap {{ ($activeProgress === '100') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            <span class="iconify text-sm" data-icon="solar:check-read-bold"></span>
            <span>Berkas 100%</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($activeProgress === '100') ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $counts['lengkap'] ?? 0 }}</span>
        </a>

        <a href="{{ route('admin.pendaftar.index', array_merge(request()->except(['page', 'status']), ['progress' => 'under100'])) }}" 
           class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap {{ ($activeProgress === 'under100') ? 'bg-slate-800 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            <span class="iconify text-sm" data-icon="solar:danger-triangle-bold"></span>
            <span>Berkas &lt;100%</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($activeProgress === 'under100') ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $counts['belum_lengkap'] ?? 0 }}</span>
        </a>
    </div>

    <!-- Toolbar Filters & Actions -->
    <div class="p-6 md:p-8 border-b border-slate-100">
        <form method="GET" id="filterForm" class="space-y-4">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('progress'))
                <input type="hidden" name="progress" value="{{ request('progress') }}">
            @endif
            @if(request('view'))
                <input type="hidden" name="view" value="{{ request('view') }}">
            @endif

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <!-- Search Box -->
                <div class="relative flex-1 max-w-lg">
                    <span class="iconify absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg" data-icon="solar:magnifer-linear"></span>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari Nama, NISN, Email, Asal Sekolah, Kota, No WA..." 
                           class="w-full pl-11 pr-10 py-3 bg-slate-50 border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 outline-none transition-all placeholder:text-slate-400 placeholder:font-normal">
                    @if(request('search'))
                        <a href="{{ route('admin.pendaftar.index', request()->except(['search', 'page'])) }}" 
                           class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-500 transition-colors p-1" title="Hapus Pencarian">
                            <span class="iconify text-base" data-icon="solar:close-circle-bold"></span>
                        </a>
                    @endif
                </div>

                <!-- Fast Filter Dropdowns -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Prodi Filter -->
                    <select name="prodi" onchange="document.getElementById('filterForm').submit()" 
                            class="px-4 py-3 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs font-bold text-slate-700 focus:bg-white focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 outline-none transition-all cursor-pointer">
                        <option value="">Semua Program Studi</option>
                        <option value="Desain Komunikasi Visual" {{ request('prodi') == 'Desain Komunikasi Visual' ? 'selected' : '' }}>🎨 DKV</option>
                        <option value="Sains Komunikasi" {{ request('prodi') == 'Sains Komunikasi' ? 'selected' : '' }}>📢 Sains Komunikasi</option>
                        <option value="Manajemen" {{ request('prodi') == 'Manajemen' ? 'selected' : '' }}>💼 Manajemen</option>
                        <option value="Akuntansi" {{ request('prodi') == 'Akuntansi' ? 'selected' : '' }}>📊 Akuntansi</option>
                        <option value="Sains Data" {{ request('prodi') == 'Sains Data' ? 'selected' : '' }}>📉 Sains Data</option>
                        <option value="Ilmu Komputer" {{ request('prodi') == 'Ilmu Komputer' ? 'selected' : '' }}>💻 Ilmu Komputer</option>
                    </select>

                    <!-- Sorting -->
                    <select name="sort" onchange="document.getElementById('filterForm').submit()" 
                            class="px-4 py-3 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs font-bold text-slate-700 focus:bg-white focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 outline-none transition-all cursor-pointer">
                        <option value="priority" {{ request('sort', 'priority') == 'priority' ? 'selected' : '' }}>⭐ Urut: Prioritas (100% & Nilai)</option>
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>🕒 Paling Baru Mendaftar</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>⏳ Paling Awal Mendaftar</option>
                        <option value="nilai_high" {{ request('sort') == 'nilai_high' ? 'selected' : '' }}>📈 Nilai Rapor Tertinggi</option>
                        <option value="nilai_low" {{ request('sort') == 'nilai_low' ? 'selected' : '' }}>📉 Nilai Rapor Terendah</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>🔤 Nama Kandidat (A - Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>🔡 Nama Kandidat (Z - A)</option>
                        <option value="progress_high" {{ request('sort') == 'progress_high' ? 'selected' : '' }}>📁 Berkas Paling Lengkap</option>
                    </select>

                    <!-- Toggle Advanced Filters -->
                    <button type="button" onclick="toggleAdvancedFilters()" 
                            class="px-4 py-3 bg-slate-50 hover:bg-slate-100 border border-slate-200/80 rounded-2xl text-xs font-bold text-slate-600 transition-all flex items-center gap-2">
                        <span class="iconify text-base text-slate-500" data-icon="solar:tuning-square-2-bold"></span>
                        <span>Filter Tanggal / Nilai</span>
                        @if(request('from_date') || request('to_date') || request('filter_nilai'))
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        @endif
                    </button>

                    <!-- Reset Filter Button -->
                    @if(request()->hasAny(['search', 'status', 'prodi', 'from_date', 'to_date', 'filter_nilai', 'progress', 'sort']))
                    <a href="{{ route('admin.pendaftar.index') }}" 
                       class="px-4 py-3 bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 rounded-2xl text-xs font-black transition-all flex items-center gap-1.5" 
                       title="Reset Semua Filter">
                        <span class="iconify text-base" data-icon="solar:refresh-bold"></span>
                        <span>Reset</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Advanced Filter Panel (Collapsible) -->
            <div id="advancedFilterPanel" class="{{ (request('from_date') || request('to_date') || request('filter_nilai')) ? '' : 'hidden' }} pt-4 border-t border-slate-100 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Dari Tanggal Daftar</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-[#0870B8] outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Sampai Tanggal Daftar</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-[#0870B8] outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Nilai Rata-rata Minimum</label>
                    <div class="flex items-center gap-2">
                        <input type="number" step="0.1" name="filter_nilai" value="{{ request('filter_nilai') }}" placeholder="Contoh: 85" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-[#0870B8] outline-none">
                        <button type="submit" class="px-5 py-2.5 bg-[#0870B8] text-white rounded-xl text-xs font-bold hover:bg-[#065A96] transition-all">
                            Terapkan
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- View Mode Switcher & Export CSV -->
        <div class="mt-6 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <!-- View Mode Switcher: Clickable Links + Instant JS Switch -->
            <div class="flex items-center gap-1.5 bg-slate-100 p-1.5 rounded-2xl border border-slate-200/80">
                <a href="{{ route('admin.pendaftar.index', array_merge(request()->query(), ['view' => 'compact'])) }}" 
                   id="btnViewCompact"
                   onclick="switchView(event, 'compact')" 
                   class="px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 {{ !$isDetailed ? 'bg-[#0870B8] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    <span class="iconify text-base" data-icon="solar:widget-bold"></span>
                    <span>Tampilan Fleksibel (Ringkas)</span>
                </a>
                <a href="{{ route('admin.pendaftar.index', array_merge(request()->query(), ['view' => 'detailed'])) }}" 
                   id="btnViewDetailed"
                   onclick="switchView(event, 'detailed')" 
                   class="px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 {{ $isDetailed ? 'bg-[#0870B8] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    <span class="iconify text-base" data-icon="solar:list-bold"></span>
                    <span>Tampilan Rinci (S1-S5)</span>
                </a>
            </div>

            <!-- Export Options: XLSX (Rapi, Auto-Filter, Styling) & CSV -->
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.export', array_merge(request()->all(), ['format' => 'xlsx'])) }}" 
                   class="px-5 py-2.5 bg-emerald-600 text-white rounded-2xl text-xs font-black shadow-md shadow-emerald-500/20 hover:bg-emerald-700 transition-all uppercase tracking-wider flex items-center gap-2"
                   title="Download Rekap Rapi Excel (.xlsx) dengan Auto-Filter, Freeze Panes & Format Kolom Rapi">
                    <span class="iconify text-lg" data-icon="solar:file-spreadsheet-bold"></span>
                    <span>Export Excel (.xlsx)</span>
                </a>
                <a href="{{ route('admin.export', array_merge(request()->all(), ['format' => 'csv'])) }}" 
                   class="px-4 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 rounded-2xl text-xs font-black transition-all uppercase tracking-wider flex items-center gap-1.5"
                   title="Download Rekap CSV mentah dengan UTF-8 BOM">
                    <span class="iconify text-base text-slate-500" data-icon="solar:document-text-bold"></span>
                    <span>CSV</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Table Wrapper -->
    <div class="overflow-x-auto relative">
        <table class="w-full text-left border-collapse" id="pendaftarTable">
            <thead>
                <tr class="bg-slate-50/70 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-100">
                    <th class="px-6 py-5 min-w-[280px]">Kandidat</th>
                    <th class="px-5 py-5 min-w-[170px]">Minat Program Studi</th>

                    <!-- Semester Columns (Toggled by View Mode) -->
                    <th class="col-sem px-3.5 py-5 text-center text-slate-500 font-black min-w-[65px] {{ $isDetailed ? '' : 'hidden' }}" style="{{ $isDetailed ? 'display: table-cell;' : 'display: none;' }}">S1</th>
                    <th class="col-sem px-3.5 py-5 text-center text-slate-500 font-black min-w-[65px] {{ $isDetailed ? '' : 'hidden' }}" style="{{ $isDetailed ? 'display: table-cell;' : 'display: none;' }}">S2</th>
                    <th class="col-sem px-3.5 py-5 text-center text-slate-500 font-black min-w-[65px] {{ $isDetailed ? '' : 'hidden' }}" style="{{ $isDetailed ? 'display: table-cell;' : 'display: none;' }}">S3</th>
                    <th class="col-sem px-3.5 py-5 text-center text-slate-500 font-black min-w-[65px] {{ $isDetailed ? '' : 'hidden' }}" style="{{ $isDetailed ? 'display: table-cell;' : 'display: none;' }}">S4</th>
                    <th class="col-sem px-3.5 py-5 text-center text-slate-500 font-black min-w-[65px] {{ $isDetailed ? '' : 'hidden' }}" style="{{ $isDetailed ? 'display: table-cell;' : 'display: none;' }}">S5</th>

                    <th class="px-5 py-5 text-center min-w-[130px]">
                        <span id="labelNilaiHeader">{{ $isDetailed ? 'AVG' : 'Nilai Rapor' }}</span>
                    </th>
                    <th class="px-5 py-5 text-center min-w-[190px]">Kelengkapan Berkas</th>
                    <th class="px-4 py-5 text-center min-w-[150px]">Video &amp; Medsos</th>
                    <th class="px-5 py-5 text-center min-w-[130px]">Status Seleksi</th>
                    <th class="px-6 py-5 text-right min-w-[160px]">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($pendaftars as $akun)
                @php
                    $peserta = $akun->peserta;
                    $daftar = $peserta?->daftar;
                    $berkas = $peserta?->berkas;
                    $sertifikats = $peserta?->sertifikats ?? collect();
                    
                    $isDKV = str_contains($peserta?->pilihan_prodi ?? '', 'Desain Komunikasi Visual') || str_contains($peserta?->pilihan_prodi2 ?? '', 'Desain Komunikasi Visual') || str_contains($peserta?->pilihan_prodi ?? '', 'DKV');
                    $percentage = $peserta?->progress ?? 0;
                    
                    // Wilayah Jabodetabek
                    $kab = strtolower($peserta?->kabupaten ?: ($daftar?->kabupaten ?: ''));
                    $isJabodetabek = false;
                    foreach(['jakarta', 'bogor', 'depok', 'tangerang', 'bekasi'] as $c) {
                        if (str_contains($kab, $c)) { $isJabodetabek = true; break; }
                    }
                    
                    // Nilai Rapor
                    $avgTotal = $daftar?->rata_rata_nilai ?? 0;
                @endphp
                <tr class="hover:bg-blue-50/30 transition-colors group">
                    <!-- Kandidat Column -->
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3.5">
                            <div class="relative w-11 h-11 rounded-2xl overflow-hidden shadow-sm border border-slate-200 shrink-0 bg-slate-100">
                                @if($berkas && $berkas->foto)
                                    <img src="{{ asset('storage/'.$berkas->foto) }}" alt="{{ $akun->nama }}" class="w-full h-full object-cover">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($akun->nama) }}&background=0870B8&color=fff&bold=true" class="w-full h-full object-cover">
                                @endif
                                @if($percentage == 100)
                                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-white flex items-center justify-center" title="100% Lengkap">
                                        <span class="iconify text-[9px] text-white" data-icon="solar:check-bold"></span>
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('admin.pendaftar.show', $akun->id) }}" class="font-extrabold text-slate-800 text-sm hover:text-[#0870B8] transition-colors truncate block">
                                    {{ $akun->nama }}
                                </a>
                                <div class="text-[11px] font-bold text-slate-500 mt-0.5 truncate flex items-center gap-1.5">
                                    <span class="truncate">{{ $peserta?->nama_sekolah ?: ($daftar?->asal_sekolah ?: '-') }}</span>
                                    @if($peserta?->tahun_lulus)
                                        <span class="px-1.5 py-0.2 bg-slate-100 text-slate-600 rounded text-[9px] font-bold shrink-0">{{ $peserta->tahun_lulus }}</span>
                                    @endif
                                </div>
                                <div class="text-[10px] font-semibold text-slate-400 mt-1 flex items-center gap-2 flex-wrap">
                                    @if($peserta?->no_whatsapp || $daftar?->no_wa)
                                        @php
                                            $wa = preg_replace('/[^0-9]/', '', $peserta?->no_whatsapp ?: $daftar?->no_wa);
                                            if(str_starts_with($wa, '0')) $wa = '62' . substr($wa, 1);
                                        @endphp
                                        <a href="https://wa.me/{{ $wa }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 font-bold flex items-center gap-0.5 hover:underline" title="Chat WhatsApp">
                                            <span class="iconify text-xs" data-icon="solar:phone-bold"></span>
                                            <span>{{ $peserta?->no_whatsapp ?: $daftar?->no_wa }}</span>
                                        </a>
                                    @endif
                                    @if($peserta?->kabupaten || $daftar?->kabupaten)
                                        <span class="text-slate-300">&bull;</span>
                                        <span class="truncate max-w-[130px]">{{ $peserta?->kabupaten ?: $daftar?->kabupaten }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>

                    <!-- Minat Prodi -->
                    <td class="px-5 py-4">
                        @php
                            $p1 = $peserta?->pilihan_prodi ?? '-';
                            $p2 = $peserta?->pilihan_prodi2 ?? '-';
                            if (str_contains($p1, ' | ')) {
                                $parts = explode(' | ', $p1);
                                $p1 = $parts[0] ?? $p1;
                                if($p2 === '-' && isset($parts[1])) $p2 = $parts[1];
                            }
                        @endphp
                        <div class="font-bold text-slate-800 text-xs">
                            <span class="inline-block px-2.5 py-1 rounded-lg {{ $isDKV ? 'bg-purple-100 text-purple-700 font-extrabold border border-purple-200' : 'bg-slate-100 text-slate-700' }}">
                                {{ $p1 }}
                            </span>
                        </div>
                        @if($p2 && $p2 !== '-')
                            <div class="text-[10px] font-semibold text-slate-400 mt-1 pl-1">
                                Pilihan 2: {{ $p2 }}
                            </div>
                        @endif

                        @if($isDKV)
                            <div class="mt-2 flex flex-wrap items-center gap-1">
                                @if(!empty($berkas->surat_buta_warna))
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-md text-[9px] font-black flex items-center gap-0.5 border border-emerald-200" title="Surat Bebas Buta Warna Terunggah">
                                        <span class="iconify text-xs" data-icon="solar:check-circle-bold"></span> Buta Warna OK
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 bg-rose-100 text-rose-600 rounded-md text-[9px] font-black flex items-center gap-0.5 border border-rose-200" title="Surat Bebas Buta Warna Belum Diunggah">
                                        <span class="iconify text-xs" data-icon="solar:close-circle-bold"></span> Buta Warna Miss
                                    </span>
                                @endif

                                @if(!empty($berkas->portfolio))
                                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-md text-[9px] font-black flex items-center gap-0.5 border border-indigo-200" title="Portofolio Terunggah">
                                        <span class="iconify text-xs" data-icon="solar:gallery-bold"></span> Portofolio OK
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-md text-[9px] font-black flex items-center gap-0.5 border border-amber-200" title="Portofolio Belum Diunggah">
                                        <span class="iconify text-xs" data-icon="solar:danger-triangle-bold"></span> Portofolio Miss
                                    </span>
                                @endif
                            </div>
                        @endif
                    </td>

                    <!-- Detailed Semester Columns -->
                    @for($sem = 1; $sem <= 5; $sem++)
                    @php
                        $avgSem = $daftar->{"avg_semester_{$sem}"} ?? 0;
                        $colorSem = $avgSem >= 90 ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 
                                   ($avgSem >= 80 ? 'bg-blue-100 text-blue-800 border-blue-200' : 
                                   ($avgSem >= 70 ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-600 border-slate-200'));
                    @endphp
                    <td class="col-sem px-3.5 py-4 text-center {{ $isDetailed ? '' : 'hidden' }}" style="{{ $isDetailed ? 'display: table-cell;' : 'display: none;' }}">
                        <span class="inline-block px-2.5 py-1 {{ $colorSem }} rounded-lg text-xs font-black border">
                            {{ $avgSem > 0 ? number_format($avgSem, 1) : '-' }}
                        </span>
                    </td>
                    @endfor

                    <!-- Nilai Rapor / AVG Column -->
                    <td class="px-5 py-4 text-center">
                        <div class="inline-flex flex-col items-center">
                            <span class="inline-block px-3 py-1 bg-amber-50 text-amber-800 rounded-xl text-xs font-black border border-amber-200/80 shadow-xs">
                                {{ number_format($avgTotal, 1) }}
                            </span>
                            <!-- Semester Mini Strip (Visible in Compact view) -->
                            <div class="sem-strip flex items-center gap-1 mt-1.5 {{ $isDetailed ? 'hidden' : '' }}" title="Nilai S1 s/d S5">
                                @for($s = 1; $s <= 5; $s++)
                                    @php
                                        $valS = $daftar->{"avg_semester_{$s}"} ?? 0;
                                    @endphp
                                    <span class="text-[9px] font-extrabold px-1 py-0.5 rounded {{ $valS > 0 ? 'bg-slate-100 text-slate-700' : 'bg-slate-50 text-slate-300' }}">
                                        {{ $valS > 0 ? round($valS) : '-' }}
                                    </span>
                                @endfor
                            </div>
                        </div>
                    </td>

                    <!-- Kelengkapan Berkas -->
                    <td class="px-5 py-4">
                        @php
                            $progColor = $percentage == 100 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : 
                                        ($percentage >= 70 ? 'text-blue-700 bg-blue-50 border-blue-200' : 'text-amber-700 bg-amber-50 border-amber-200');
                            $barColor = $percentage == 100 ? 'bg-emerald-500' : ($percentage >= 70 ? 'bg-blue-500' : 'bg-amber-500');
                        @endphp
                        <div class="flex flex-col items-center">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="px-2.5 py-0.5 {{ $progColor }} rounded-full text-[10px] font-black border">
                                    {{ $percentage }}% Lengkap
                                </span>
                            </div>
                            <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden mb-2">
                                <div class="h-full {{ $barColor }} transition-all" style="width: {{ $percentage }}%"></div>
                            </div>
                            
                            <!-- Badges Dokumen -->
                            <div class="flex flex-wrap items-center justify-center gap-1 max-w-[180px]">
                                <span class="px-1.5 py-0.2 rounded text-[8px] font-black {{ ($berkas && $berkas->foto) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-300' }}">FOTO</span>
                                <span class="px-1.5 py-0.2 rounded text-[8px] font-black {{ ($berkas && $berkas->ijazah) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-300' }}">IJAZAH</span>
                                <span class="px-1.5 py-0.2 rounded text-[8px] font-black {{ ($berkas && $berkas->personal_statement) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-300' }}">PS</span>
                                
                                @php
                                    $rCount = 0;
                                    foreach(['rapor1','rapor2','rapor3','rapor4','rapor5'] as $rf) {
                                        if($berkas && !empty($berkas->$rf)) $rCount++;
                                    }
                                @endphp
                                <span class="px-1.5 py-0.2 rounded text-[8px] font-black {{ $rCount == 5 ? 'bg-emerald-100 text-emerald-700' : ($rCount > 0 ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-300') }}">
                                    RAPOR ({{ $rCount }}/5)
                                </span>

                                <span class="px-1.5 py-0.2 rounded text-[8px] font-black {{ $sertifikats->count() > 0 ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-300' }}">
                                    SERTIF ({{ $sertifikats->count() }})
                                </span>
                            </div>
                        </div>
                    </td>

                    <!-- Submisi Video & Medsos -->
                    <td class="px-4 py-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <!-- Video Motivasi Utama -->
                            @if($berkas && $berkas->motivasi_video)
                                <a href="{{ $berkas->motivasi_video }}" target="_blank" 
                                   class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 hover:bg-purple-600 hover:text-white transition-all flex items-center justify-center shadow-xs" 
                                   title="Video Motivasi Utama (Klik untuk buka)">
                                    <span class="iconify text-base" data-icon="solar:videocamera-record-bold"></span>
                                </a>
                            @else
                                <span class="w-8 h-8 rounded-xl bg-slate-50 text-slate-300 flex items-center justify-center border border-slate-100" title="Belum submit Video Motivasi">
                                    <span class="iconify text-sm" data-icon="solar:videocamera-record-linear"></span>
                                </span>
                            @endif

                            <!-- TikTok -->
                            @php
                                $ttLink = $berkas?->motivasi_video_tiktok ?: $peserta?->link_tiktok;
                            @endphp
                            @if($ttLink)
                                <a href="{{ $ttLink }}" target="_blank" 
                                   class="w-8 h-8 rounded-xl bg-slate-100 text-slate-800 hover:bg-slate-900 hover:text-white transition-all flex items-center justify-center shadow-xs" 
                                   title="TikTok Link">
                                    <span class="iconify text-base" data-icon="solar:music-note-bold"></span>
                                </a>
                            @endif

                            <!-- Instagram -->
                            @if($peserta?->link_ig)
                                <a href="{{ $peserta->link_ig }}" target="_blank" 
                                   class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all flex items-center justify-center shadow-xs" 
                                   title="Instagram Link">
                                    <span class="iconify text-base" data-icon="solar:camera-bold"></span>
                                </a>
                            @endif

                            <!-- Twibbon -->
                            @if($peserta?->link_twibbon)
                                <a href="{{ $peserta->link_twibbon }}" target="_blank" 
                                   class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center shadow-xs" 
                                   title="Twibbon Link">
                                    <span class="iconify text-base" data-icon="solar:gallery-bold"></span>
                                </a>
                            @endif
                        </div>
                    </td>

                    <!-- Status Seleksi -->
                    <td class="px-5 py-4 text-center">
                        @php
                            $statusPendaftar = $daftar->status ?? 'menunggu';
                        @endphp
                        @if($statusPendaftar === 'lulus')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[10px] font-black uppercase tracking-wider border border-emerald-200">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                Lulus
                            </span>
                        @elseif($statusPendaftar === 'diajukan_palugada')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-[10px] font-black uppercase tracking-wider border border-blue-200" title="Diajukan untuk pemeriksaan Palugada">
                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                                Double Check
                            </span>
                        @elseif($statusPendaftar === 'tidak_lulus')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 text-rose-700 rounded-full text-[10px] font-black uppercase tracking-wider border border-rose-200">
                                Ditolak
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-[10px] font-black uppercase tracking-wider border border-amber-200">
                                Menunggu
                            </span>
                        @endif
                    </td>

                    <!-- Aksi Column (Always visible, responsive) -->
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <!-- Primary Button: VERIFIKASI -->
                            <a href="{{ route('admin.pendaftar.show', $akun->id) }}" 
                               class="h-9 px-3.5 bg-[#0870B8] text-white rounded-xl text-xs font-black hover:bg-[#065A96] transition-all shadow-md shadow-blue-500/10 flex items-center gap-1.5 border border-blue-600/30">
                                <span>Verifikasi</span>
                                <span class="iconify text-sm" data-icon="solar:pen-new-square-bold"></span>
                            </a>

                            <!-- Secondary Actions -->
                            <!-- Download ZIP -->
                            <a href="{{ route('admin.pendaftar.download_zip', $akun->id) }}" 
                               class="w-9 h-9 flex items-center justify-center bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-800 hover:text-white transition-all shadow-xs border border-slate-200" 
                               title="Download ZIP Semua Berkas">
                                <span class="iconify text-base" data-icon="solar:folder-with-files-bold"></span>
                            </a>

                            <!-- Kirim Sertifikat Email Individu -->
                            <form action="{{ route('admin.pendaftar.send_certificate', $akun->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" onclick="return confirm('Kirim sertifikat ke email {{ $akun->email }}?')" 
                                        class="w-9 h-9 flex items-center justify-center bg-blue-50 text-[#0870B8] rounded-xl hover:bg-[#0870B8] hover:text-white transition-all shadow-xs border border-blue-200" 
                                        title="Kirim Sertifikat via Email">
                                    <span class="iconify text-base" data-icon="solar:letter-send-bold"></span>
                                </button>
                            </form>

                            <!-- Admin Special Options -->
                            @if(auth()->user()->role === 'admin' || in_array(auth()->user()->email, ['dept.adminis@mfls.com', 'info@beasiswamncu.com']))
                            <!-- Reset Password -->
                            <button type="button" onclick="confirmResetPassword({{ $akun->id }}, '{{ $akun->nama }}')" 
                                    class="w-9 h-9 flex items-center justify-center bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-500 hover:text-white transition-all shadow-xs border border-amber-200" 
                                    title="Reset Password Akun">
                                <span class="iconify text-base" data-icon="solar:key-minimalistic-bold-duotone"></span>
                            </button>

                            <!-- Delete Akun -->
                            <form action="{{ route('admin.pendaftar.destroy', $akun->id) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus peserta {{ $akun->nama }} beserta seluruh berkas dan nilainya? Tindakan ini tidak dapat dibatalkan!');"
                                  class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-9 h-9 flex items-center justify-center bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all shadow-xs border border-rose-200" 
                                        title="Hapus Akun Peserta">
                                    <span class="iconify text-base" data-icon="solar:trash-bin-trash-bold"></span>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="15" class="p-16 text-center text-slate-400">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                            <span class="iconify text-3xl" data-icon="solar:magnifer-linear"></span>
                        </div>
                        <h4 class="text-base font-black text-slate-700">Tidak ada data pendaftar yang cocok</h4>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Coba ubah kriteria pencarian atau klik tombol reset filter di bawah.</p>
                        <a href="{{ route('admin.pendaftar.index') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-[#0870B8] text-white rounded-xl text-xs font-bold hover:bg-[#065A96] transition-all">
                            <span class="iconify text-sm" data-icon="solar:refresh-bold"></span>
                            <span>Reset Semua Filter</span>
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer Info -->
    <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs font-bold text-slate-500">
        <div>
            Menampilkan <span class="text-slate-800">{{ $pendaftars->count() }}</span> dari total <span class="text-slate-800">{{ $counts['total'] ?? $pendaftars->count() }}</span> pendaftar
        </div>
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
            <span>Data tersinkronisasi realtime</span>
        </div>
    </div>
</div>

{{-- Form Reset Password --}}
@if(auth()->user()->role === 'admin' || in_array(auth()->user()->email, ['dept.adminis@mfls.com', 'info@beasiswamncu.com']))
<form id="resetPasswordForm" method="POST" style="display:none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="password" id="resetPasswordInput">
</form>
@endif

<!-- Direct inline script so it ALWAYS executes whether on page load or via SPA router -->
<script>
// 1. View Mode Switcher (Fleksibel / Ringkas vs Rinci S1-S5)
function switchView(event, mode) {
    if (event) {
        event.preventDefault();
    }

    const semCols = document.querySelectorAll('.col-sem');
    const semStrips = document.querySelectorAll('.sem-strip');
    const labelNilaiHeader = document.getElementById('labelNilaiHeader');
    const btnCompact = document.getElementById('btnViewCompact');
    const btnDetailed = document.getElementById('btnViewDetailed');

    if (mode === 'detailed') {
        // Tampilkan kolom S1 - S5
        semCols.forEach(el => {
            el.style.display = 'table-cell';
            el.classList.remove('hidden');
        });
        semStrips.forEach(el => el.classList.add('hidden'));
        if (labelNilaiHeader) labelNilaiHeader.innerText = 'AVG';

        if (btnDetailed) {
            btnDetailed.className = 'px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 bg-[#0870B8] text-white shadow-sm';
        }
        if (btnCompact) {
            btnCompact.className = 'px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 text-slate-600 hover:text-slate-900';
        }
    } else {
        // Mode Ringkas (Sembunyikan kolom S1 - S5)
        semCols.forEach(el => {
            el.style.display = 'none';
            el.classList.add('hidden');
        });
        semStrips.forEach(el => el.classList.remove('hidden'));
        if (labelNilaiHeader) labelNilaiHeader.innerText = 'Nilai Rapor';

        if (btnCompact) {
            btnCompact.className = 'px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 bg-[#0870B8] text-white shadow-sm';
        }
        if (btnDetailed) {
            btnDetailed.className = 'px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 text-slate-600 hover:text-slate-900';
        }
    }

    // Persist choice to localStorage and URL
    try {
        localStorage.setItem('adminPendaftarViewMode', mode);
        const url = new URL(window.location.href);
        url.searchParams.set('view', mode);
        window.history.replaceState({}, '', url.toString());

        // Update hidden view input in filterForm
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            let viewInput = filterForm.querySelector('input[name="view"]');
            if (!viewInput) {
                viewInput = document.createElement('input');
                viewInput.type = 'hidden';
                viewInput.name = 'view';
                filterForm.appendChild(viewInput);
            }
            viewInput.value = mode;
        }
    } catch (e) {
        console.warn('Could not update URL or localStorage:', e);
    }
}

// Inisialisasi Mode Tampilan saat dimuat
(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const viewFromUrl = urlParams.get('view');
    const savedView = viewFromUrl || localStorage.getItem('adminPendaftarViewMode') || 'compact';
    switchView(null, savedView);
})();

// 2. Toggle Advanced Filter Panel
function toggleAdvancedFilters() {
    const panel = document.getElementById('advancedFilterPanel');
    if (panel) {
        panel.classList.toggle('hidden');
    }
}

// 3. Reset Password Confirmation Dialog
function confirmResetPassword(userId, name) {
    Swal.fire({
        title: 'Reset Password Akun',
        text: `Masukkan password baru untuk akun ${name}:`,
        input: 'password',
        inputPlaceholder: 'Minimal 6 karakter',
        showCancelButton: true,
        confirmButtonText: 'Reset Password',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#f59e0b',
        preConfirm: (password) => {
            if (!password || password.length < 6) {
                Swal.showValidationMessage('Password minimal 6 karakter');
                return false;
            }
            return password;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('resetPasswordForm');
            if (form) {
                form.action = `/admin/user/${userId}/reset-password`;
                document.getElementById('resetPasswordInput').value = result.value;
                form.submit();
            }
        }
    });
}
</script>
@endsection

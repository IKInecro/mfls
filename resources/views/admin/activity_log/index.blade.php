@extends('layouts.admin')

@section('content')
<div class="space-y-6 sm:space-y-8">

    <!-- Header Banner -->
    <div class="relative overflow-hidden bg-[#0B1120] text-white p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl shadow-slate-950/20">
        <div class="absolute -top-20 -right-20 w-72 h-72 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/5 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-widest text-blue-400 mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    Keamanan & Audit Jejak &bull; Activity Log
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight">
                    Log Aktivitas Sistem
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl">
                    Pantau rekam jejak setiap aksi pengguna dan operasi sistem untuk memastikan integritas dan transparansi seleksi.
                </p>
            </div>
            
            <div class="px-5 py-3.5 rounded-2xl bg-slate-900/90 border border-slate-800 text-center min-w-[150px] shrink-0">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Total Rekaman</div>
                <div class="text-2xl font-black text-white">{{ number_format($riwayats->total()) }} Log</div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <form action="{{ route('admin.activity_log.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Cari Kata Kunci</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 iconify text-slate-400 text-base" data-icon="solar:magnifer-linear"></span>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Deskripsi / Pelaku..." 
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white rounded-xl focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-xs sm:text-sm font-semibold text-slate-700">
                </div>
            </div>

            <!-- Activity Type -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jenis Tindakan</label>
                <div class="relative">
                    <select name="aksi" 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white rounded-xl focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-xs sm:text-sm font-semibold text-slate-700 appearance-none cursor-pointer">
                        <option value="">Semua Aktivitas</option>
                        @foreach($activities as $activity)
                            <option value="{{ $activity }}" {{ request('aksi') == $activity ? 'selected' : '' }}>{{ $activity }}</option>
                        @endforeach
                    </select>
                    <span class="iconify absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" data-icon="solar:alt-arrow-down-bold"></span>
                </div>
            </div>

            <!-- Date -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Catatan</label>
                <input type="date" name="date" value="{{ request('date') }}" 
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white rounded-xl focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-xs sm:text-sm font-semibold text-slate-700">
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" 
                    class="flex-1 py-2.5 px-4 bg-[#0870B8] hover:bg-[#065A96] text-white rounded-xl font-bold text-xs transition-all shadow-xs flex items-center justify-center gap-1.5 cursor-pointer">
                    <span class="iconify text-base" data-icon="solar:filter-bold"></span>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'aksi', 'date']))
                <a href="{{ route('admin.activity_log.index') }}" 
                    class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold text-xs transition-colors flex items-center justify-center">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-400 text-[10px] font-bold uppercase tracking-wider border-b border-slate-100">
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Pelaku</th>
                        <th class="px-6 py-4">Tindakan</th>
                        <th class="px-6 py-4">Deskripsi Aktivitas</th>
                        <th class="px-6 py-4 text-center">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($riwayats as $log)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                            <div class="font-bold text-slate-800">{{ $log->created_at->format('d/m/Y H:i') }}</div>
                            <div class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-bold text-slate-900">{{ $log->pelaku->nama ?? 'Sistem' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $log->pelaku->role ?? 'System Bot' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-bold bg-blue-50 text-[#0870B8] border border-blue-100">
                                {{ $log->aksi }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-600 font-medium max-w-md">
                            {{ $log->deskripsi }}
                        </td>
                        <td class="px-6 py-4 text-center font-mono text-[11px] text-slate-400">
                            {{ $log->ip_address ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-xs">
                            Tidak ditemukan rekaman log aktivitas sesuai filter yang dipilih.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($riwayats->hasPages())
        <div class="p-4 sm:p-6 border-t border-slate-100">
            {{ $riwayats->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

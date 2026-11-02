@extends('layouts.admin')

@section('content')
<div class="space-y-6 sm:space-y-8">

    <!-- 1. EXECUTIVE HEADER BANNER (Midnight Navy & Amber Accent) -->
    <div class="relative overflow-hidden bg-[#0B1120] text-white p-6 sm:p-8 lg:p-10 rounded-3xl border border-slate-800 shadow-xl shadow-slate-950/20">
        <!-- Ambient Radial Background -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6 sm:gap-8">
            <div class="space-y-3 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-500/10 border border-amber-500/30 rounded-full text-[10px] font-bold uppercase tracking-widest text-amber-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    Otoritas Palugada &bull; Tahap Akhir Kelulusan
                </div>
                
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-white leading-tight">
                    Hub Verifikasi Final <span class="text-amber-400">Palugada</span>
                </h1>
                
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                    Evaluasi menyeluruh dan persetujuan akhir untuk pendaftar yang telah disetujui berkasnya oleh tim penilai dan panitia pelaksana.
                </p>

                <div class="flex flex-wrap items-center gap-2 pt-1 text-[11px]">
                    <span class="px-2.5 py-1 rounded-lg bg-slate-900/80 border border-slate-800 text-slate-300 font-semibold flex items-center gap-1.5">
                        <span class="iconify text-amber-400" data-icon="solar:shield-check-bold"></span>
                        Status Verifikasi: <strong class="text-amber-400">Double Check Wajib</strong>
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-slate-900/80 border border-slate-800 text-slate-300 font-semibold flex items-center gap-1.5">
                        <span class="iconify text-blue-400" data-icon="solar:users-group-rounded-bold"></span>
                        Kandidat Menunggu: <strong class="text-white">{{ $pendaftars->count() }} Orang</strong>
                    </span>
                </div>
            </div>

            <!-- Mini Summary Card -->
            <div class="bg-slate-900/90 border border-slate-800 p-5 sm:p-6 rounded-2xl shadow-inner text-center sm:text-left lg:text-center min-w-[220px] shrink-0">
                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Perlu Verifikasi</div>
                <div class="text-3xl sm:text-4xl font-black text-amber-400 tracking-tight">{{ $pendaftars->count() }}</div>
                <div class="mt-2 text-xs font-semibold text-slate-300">
                    Kandidat Diajukan
                </div>
                <div class="mt-3 pt-3 border-t border-slate-800/80 text-[10px] text-slate-400">
                    Menunggu Keputusan Palugada
                </div>
            </div>
        </div>
    </div>

    <!-- 2. DATA TABLE CARD -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <!-- Toolbar -->
        <div class="p-5 sm:p-6 lg:p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span class="iconify text-[#0870B8] text-xl" data-icon="solar:clipboard-check-bold-duotone"></span>
                    Daftar Antrean Persetujuan Akhir
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Pilih kandidat untuk memeriksa berkas, nilai rapor, dan memberikan keputusan final.</p>
            </div>
            
            <form method="GET" class="relative w-full md:w-72">
                <span class="iconify absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base" data-icon="solar:magnifer-linear"></span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / Email..." 
                    class="pl-10 pr-4 py-2.5 w-full bg-slate-50 hover:bg-slate-100/80 focus:bg-white border border-slate-200 focus:border-[#0870B8] focus:ring-2 focus:ring-blue-500/10 rounded-xl text-xs font-semibold transition-all outline-none text-slate-700">
            </form>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-400 text-[10px] font-bold uppercase tracking-wider border-b border-slate-100">
                        <th class="px-6 py-4">Kandidat</th>
                        <th class="px-4 py-4 text-center">Rata-rata Nilai</th>
                        <th class="px-6 py-4 text-left">Pilihan Program Studi</th>
                        <th class="px-6 py-4 text-center">Progres Berkas</th>
                        <th class="px-6 py-4 text-center">Status Verifikasi</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($pendaftars as $akun)
                    @php
                        $peserta = $akun->peserta;
                        $daftar = $peserta->daftar;
                        $percentage = $peserta->progress ?? 0;
                        $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($akun->nama) . "&background=0870B8&color=ffffff&bold=true";
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <!-- Kandidat -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 border border-slate-200 bg-slate-100">
                                    <img src="{{ $avatarUrl }}" alt="{{ $akun->nama }}" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-900 truncate">{{ $akun->nama }}</div>
                                    <div class="text-[10px] text-slate-400 truncate mt-0.5">{{ $peserta->nama_sekolah ?? '-' }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Rata-rata Nilai -->
                        <td class="px-4 py-4 text-center">
                            <span class="inline-block px-2.5 py-1 bg-blue-50 text-[#0870B8] rounded-lg text-xs font-black border border-blue-100">
                                {{ number_format($daftar->rata_rata_nilai ?? 0, 2) }}
                            </span>
                        </td>

                        <!-- Prodi -->
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">{{ $peserta->pilihan_prodi ?? '-' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $peserta->pilihan_prodi_2 ?? 'Pilihan Tunggal' }}</div>
                        </td>

                        <!-- Progres Berkas -->
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex flex-col items-center">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold mb-1.5 {{ $percentage == 100 ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-blue-50 text-[#0870B8] border border-blue-100' }}">
                                    {{ $percentage }}%
                                </span>
                                <div class="w-20 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $percentage == 100 ? 'bg-emerald-500' : 'bg-[#0870B8]' }} rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        </td>

                        <!-- Status Saat Ini -->
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-[10px] font-bold border border-amber-200">
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                                <span>Verifikasi Palugada</span>
                            </span>
                        </td>

                        <!-- Aksi -->
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.pendaftar.show', $akun->id) }}" 
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#0870B8] hover:bg-[#065A96] text-white rounded-xl text-xs font-bold transition-all shadow-xs shrink-0">
                                <span>Periksa & Putuskan</span>
                                <span class="iconify text-sm" data-icon="solar:arrow-right-linear"></span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-blue-50 text-[#0870B8] rounded-2xl flex items-center justify-center mx-auto mb-3 border border-blue-100">
                                <span class="iconify text-3xl" data-icon="solar:check-circle-bold-duotone"></span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800">Semua Berkas Telah Terverifikasi</h3>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                Saat ini tidak ada kandidat yang menunggu verifikasi final Palugada.
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection

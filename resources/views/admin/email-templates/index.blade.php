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
                    Komunikasi &bull; Template Notifikasi
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight">
                    Kelola Template Email Sistem
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl">
                    Sesuaikan narasi dan format HTML email otomatis (OTP, Kelulusan, Sertifikat, Kartu Ujian).
                </p>
            </div>
            
            <div class="px-4 py-3 rounded-2xl bg-slate-900/80 border border-slate-800 text-center min-w-[150px] shrink-0">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Total Template</div>
                <div class="text-2xl font-black text-white">{{ count($templates) }} Jenis</div>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                <span class="iconify text-[#0870B8] text-xl" data-icon="solar:letter-bold-duotone"></span>
                Daftar Template Email Transaksional
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Template yang belum diubah akan secara otomatis memakai tampilan bawaan resmi sistem MFLS.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-400 text-[10px] font-bold uppercase tracking-wider border-b border-slate-100">
                        <th class="px-6 py-4">Nama Template</th>
                        <th class="px-6 py-4">Identifier Key</th>
                        <th class="px-6 py-4 text-center">Status Kustomisasi</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($templates as $t)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#0870B8] flex items-center justify-center shrink-0 border border-blue-100">
                                    <span class="iconify text-base" data-icon="solar:letter-unread-bold"></span>
                                </div>
                                <span class="font-bold text-slate-900 text-xs sm:text-sm">{{ $t['label'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-[11px] text-slate-500">
                            {{ $t['key'] }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($t['row'])
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-[#0870B8] border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#0870B8]"></span>
                                    <span>Dikustomisasi @if($t['row']->updated_at) &bull; {{ $t['row']->updated_at->format('d/m/Y') }} @endif</span>
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                    Bawaan Sistem
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.email-templates.edit', $t['key']) }}"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#0870B8] hover:bg-[#065A96] text-white font-bold text-xs rounded-xl transition-all shadow-xs shrink-0 cursor-pointer">
                                <span class="iconify text-sm" data-icon="solar:pen-new-square-bold"></span>
                                <span>Edit Template</span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

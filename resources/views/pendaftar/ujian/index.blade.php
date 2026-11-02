@extends('pendaftar.layout')

@section('content')
<div class="space-y-4 sm:space-y-5 pb-10">

    {{-- Hero Header (Kompak & Elegan) --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#0B1120] via-[#0F172A] to-[#1E293B] border border-slate-800 p-4 sm:p-6 text-white shadow-lg">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-blue-600/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1 sm:space-y-1.5">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[11px] font-bold">
                    <span class="iconify text-sm" data-icon="solar:diploma-bold-duotone"></span>
                    <span>Modul CBT Mandiri — Ajaran 2027</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white">Ujian Seleksi Online (CBT)</h1>
                <p class="text-xs text-slate-300 max-w-xl leading-relaxed">
                    Ujian kemampuan akademik dan pemetaan potensi calon penerima beasiswa MNC University. Jawaban Anda tersimpan otomatis secara berkala.
                </p>
            </div>
            <div class="shrink-0 flex items-center">
                <a href="{{ route('pendaftar.dashboard') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 text-xs font-bold transition-all shadow-xs">
                    <span class="iconify text-sm" data-icon="solar:arrow-left-bold"></span>
                    <span>Kembali ke Dashboard</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Banner Protokol Keamanan & Kesiapan Jaringan (Proporsional) --}}
    <div class="rounded-xl border border-blue-500/20 bg-blue-50/60 p-3.5 sm:p-4 text-slate-700 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-600/10 border border-blue-500/20 flex items-center justify-center shrink-0 mt-0.5">
                <span class="iconify text-xl text-blue-600" data-icon="solar:shield-warning-bold-duotone"></span>
            </div>
            <div class="space-y-0.5">
                <h2 class="text-xs font-black uppercase tracking-wider text-blue-900">Protokol Keamanan & Kesiapan Jaringan</h2>
                <p class="text-[11px] text-slate-600 leading-relaxed">
                    Pastikan koneksi internet stabil. Sistem berjalan dalam <strong class="text-slate-900">mode layar penuh terpisah</strong> dengan sensor fokus otomatis (<strong class="text-blue-800">Toleransi 1x Peringatan</strong>). Jika meninggalkan layar 2 kali, ujian akan <strong class="text-red-700">di-reset otomatis dari nol</strong>.
                </p>
            </div>
        </div>
        <div class="shrink-0 flex items-center gap-1.5 text-[11px] font-bold text-blue-700 bg-white px-3 py-1.5 rounded-lg border border-blue-200 shadow-xs">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Sensor Pengawas Aktif</span>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('info'))
        <div class="rounded-xl border border-blue-200 bg-blue-50/90 p-3.5 text-xs font-bold text-blue-800 flex items-center gap-2 shadow-xs">
            <span class="iconify text-base text-blue-600 shrink-0" data-icon="solar:info-circle-bold"></span>
            <span>{{ session('info') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50/90 p-3.5 text-xs font-bold text-red-800 flex items-start gap-2 shadow-xs">
            <span class="iconify text-base text-red-600 shrink-0 mt-0.5" data-icon="solar:danger-triangle-bold"></span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Grid Kartu Daftar Ujian --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($cards as $card)
            @php
                $u = $card['ujian'];
                $state = $card['state'];
                $badgeConfig = [
                    'tertutup' => ['bg-slate-100 text-slate-600 border-slate-200', 'solar:lock-bold', 'Belum dibuka'],
                    'mulai' => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'solar:play-circle-bold', 'Siap dikerjakan'],
                    'lanjut' => ['bg-blue-50 text-blue-700 border-blue-200', 'solar:refresh-circle-bold', 'Sedang berjalan'],
                    'waktu_habis' => ['bg-amber-50 text-amber-700 border-amber-200', 'solar:clock-circle-bold', 'Waktu habis'],
                    'selesai' => ['bg-slate-100 text-slate-700 border-slate-200', 'solar:check-circle-bold', 'Sudah dikerjakan'],
                ][$state];
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs hover:shadow-md transition-shadow p-4 sm:p-5 flex flex-col gap-4 self-start">
                
                {{-- Header Kartu --}}
                <div class="flex items-start justify-between gap-2.5">
                    <div class="space-y-0.5">
                        <h2 class="font-bold text-slate-900 text-sm sm:text-base leading-snug">{{ $u->nama }}</h2>
                        <div class="flex items-center gap-2 text-[11px] font-semibold text-slate-400">
                            <span class="flex items-center gap-1">
                                <span class="iconify text-slate-400" data-icon="solar:clock-circle-outline"></span>
                                {{ $u->durasi }} Menit
                            </span>
                            <span>&bull;</span>
                            <span class="flex items-center gap-1">
                                <span class="iconify text-slate-400" data-icon="solar:document-text-outline"></span>
                                {{ $u->soals()->count() }} Soal
                            </span>
                        </div>
                    </div>
                    <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeConfig[0] }}">
                        <span class="iconify text-xs" data-icon="{{ $badgeConfig[1] }}"></span>
                        <span>{{ $badgeConfig[2] }}</span>
                    </span>
                </div>

                {{-- Jadwal Pelaksanaan --}}
                @if($u->jadwal_mulai || $u->jadwal_selesai)
                    <div class="px-3 py-2 rounded-lg bg-slate-50 border border-slate-100 text-[10px] text-slate-500 font-medium flex items-center gap-1.5">
                        <span class="iconify text-slate-400 shrink-0" data-icon="solar:calendar-bold"></span>
                        <span class="truncate">
                            {{ $u->jadwal_mulai?->format('d M Y H:i') ?? '—' }} s.d. {{ $u->jadwal_selesai?->format('d M Y H:i') ?? 'Selesai' }}
                        </span>
                    </div>
                @endif

                {{-- Tombol Aksi / Nilai --}}
                <div class="mt-auto pt-2 border-t border-slate-100">
                    @if($state === 'selesai')
                        <div class="px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200/80 text-center space-y-0.5">
                            <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Skor Ujian Anda</p>
                            <p class="text-xl sm:text-2xl font-black text-slate-900 tabular-nums">{{ $card['nilai'] ?? '—' }}</p>
                            <span class="text-[9px] font-semibold text-emerald-600 flex items-center justify-center gap-1">
                                <span class="iconify" data-icon="solar:check-circle-bold"></span>
                                Selesai Dinilai
                            </span>
                        </div>
                    @elseif($state === 'tertutup')
                        <button disabled class="w-full px-4 py-2.5 rounded-xl bg-slate-100 text-slate-400 font-bold text-xs uppercase tracking-wider cursor-not-allowed text-center">
                            Menunggu panitia membuka
                        </button>
                    @else
                        <a href="{{ route('ujian.show', $u->id) }}"
                            class="block w-full text-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider shadow-sm shadow-blue-600/20 transition-all active:scale-[0.99]">
                            {{ ['mulai' => 'Mulai Ujian', 'lanjut' => 'Lanjutkan Ujian', 'waktu_habis' => 'Selesaikan Ujian'][$state] }}
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border-2 border-dashed border-slate-200 p-8 text-center space-y-2">
                <div class="w-10 h-10 mx-auto rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                    <span class="iconify text-xl" data-icon="solar:inbox-line-bold"></span>
                </div>
                <h2 class="text-xs font-bold text-slate-700">Belum Ada Ujian Tersedia</h2>
                <p class="text-[11px] text-slate-400">Panitia belum membuka sesi ujian CBT untuk periode saat ini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

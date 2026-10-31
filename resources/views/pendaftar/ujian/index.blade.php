@extends('pendaftar.layout')

@section('content')
<div class="space-y-6 pb-10">

    <div>
        <a href="{{ route('pendaftar.dashboard') }}" class="text-xs font-black text-primary-orange hover:underline">&larr; Kembali ke Dashboard</a>
        <h1 class="text-2xl md:text-3xl font-black text-navy-mnc mt-1">Daftar Test Ujian</h1>
        <p class="text-sm text-slate-500 font-semibold mt-1">Kerjakan test yang sudah dibuka panitia. Pastikan koneksi internet stabil — jawaban tersimpan otomatis.</p>
    </div>

    @if(session('info'))
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm font-black text-blue-700">{{ session('info') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-black text-red-700">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($cards as $card)
            @php
                $u = $card['ujian'];
                $state = $card['state'];
                $badge = [
                    'tertutup' => ['bg-red-100 text-red-700', '🔴 Belum dibuka'],
                    'mulai' => ['bg-emerald-100 text-emerald-700', '🟢 Siap dikerjakan'],
                    'lanjut' => ['bg-blue-100 text-blue-700', '🔵 Sedang berjalan'],
                    'waktu_habis' => ['bg-slate-200 text-slate-600', '⏱ Waktu habis'],
                    'selesai' => ['bg-amber-100 text-amber-700', '✅ Sudah dikerjakan'],
                ][$state];
            @endphp
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-6 flex flex-col gap-4 self-start">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-black text-navy-mnc text-lg leading-tight">{{ $u->nama }}</h2>
                        <p class="text-xs font-bold text-slate-400 mt-1">{{ $u->durasi }} menit · {{ $u->soals()->count() }} soal</p>
                    </div>
                    <span class="shrink-0 px-3 py-1 rounded-full text-[11px] font-black {{ $badge[0] }}">{{ $badge[1] }}</span>
                </div>

                @if($u->jadwal_mulai || $u->jadwal_selesai)
                    <p class="text-[11px] font-semibold text-slate-400">
                        Jadwal: {{ $u->jadwal_mulai?->format('d M Y H:i') ?? '—' }} → {{ $u->jadwal_selesai?->format('d M Y H:i') ?? 'selesai waktu' }}
                    </p>
                @endif

                <div class="mt-auto">
                    @if($state === 'selesai')
                        <div class="px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-center">
                            <p class="text-[10px] font-black uppercase tracking-widest text-amber-600">Nilai Anda</p>
                            <p class="text-2xl font-black text-navy-mnc">{{ $card['nilai'] ?? '—' }}</p>
                        </div>
                    @elseif($state === 'tertutup')
                        <button disabled class="w-full px-4 py-3 rounded-xl bg-slate-100 text-slate-400 font-black text-xs uppercase tracking-widest cursor-not-allowed">
                            Menunggu panitia membuka
                        </button>
                    @else
                        <a href="{{ route('ujian.show', $u->id) }}"
                            class="block w-full text-center px-4 py-3 rounded-xl bg-primary-orange hover:bg-orange-600 text-white font-black text-xs uppercase tracking-widest shadow-lg shadow-primary-orange/20 transition-all active:scale-95">
                            {{ ['mulai' => 'Mulai Ujian', 'lanjut' => 'Lanjutkan Ujian', 'waktu_habis' => 'Selesaikan Ujian'][$state] }}
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-[2rem] border-dashed border-slate-300 p-10 text-center text-sm font-bold text-slate-400">
                Belum ada test yang tersedia.
            </div>
        @endforelse
    </div>
</div>
@endsection

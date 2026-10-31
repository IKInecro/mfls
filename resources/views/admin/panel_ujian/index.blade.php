@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-800">Panel Ujian</h1>
            <p class="text-sm text-slate-500 font-semibold mt-1">Kelola test, jadwal mulai–selesai, dan kontrol buka/tutup sesi peserta.</p>
        </div>
        <button onclick="document.getElementById('formTambah').classList.toggle('hidden')"
            class="bg-orange-500 hover:bg-orange-600 text-white font-black text-sm px-6 py-3 rounded-xl transition-colors">
            + Tambah Test Baru
        </button>
    </div>

    {{-- Form tambah test --}}
    <form id="formTambah" method="POST" action="{{ route('admin.panel_ujian.store') }}"
        class="hidden bg-white rounded-2xl border border-slate-200 p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 shadow-sm">
        @csrf
        <div class="sm:col-span-2 lg:col-span-1">
            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-1">Nama Test</label>
            <input name="nama" required value="{{ old('nama') }}" placeholder="mis. Test Wawasan Kebangsaan"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold outline-none focus:ring-4 focus:ring-orange-100">
        </div>
        <div>
            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-1">Durasi (menit)</label>
            <input name="durasi" type="number" min="1" max="600" required value="{{ old('durasi', 60) }}"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold outline-none focus:ring-4 focus:ring-orange-100">
        </div>
        <div>
            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-1">Mulai (opsional)</label>
            <input name="jadwal_mulai" type="datetime-local" value="{{ old('jadwal_mulai') }}"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold outline-none focus:ring-4 focus:ring-orange-100">
        </div>
        <div>
            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-1">Selesai (opsional)</label>
            <input name="jadwal_selesai" type="datetime-local" value="{{ old('jadwal_selesai') }}"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold outline-none focus:ring-4 focus:ring-orange-100">
        </div>
        <div class="sm:col-span-2 lg:col-span-4 flex justify-end gap-3">
            <button type="button" onclick="document.getElementById('formTambah').classList.add('hidden')"
                class="px-5 py-2.5 rounded-xl text-sm font-black text-slate-500 hover:bg-slate-100">Batal</button>
            <button class="bg-slate-900 hover:bg-slate-700 text-white font-black text-sm px-6 py-2.5 rounded-xl">Simpan Test</button>
        </div>
    </form>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-black text-emerald-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-black text-red-700">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-black text-red-700">
            @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($ujians as $ujian)
            @php
                $status = $ujian->statusSesi();
                $badge = [
                    'berjalan' => ['bg-emerald-100 text-emerald-700', '🟢 Berjalan'],
                    'selesai' => ['bg-slate-200 text-slate-600', '⚪ Jadwal selesai'],
                    'tertutup' => ['bg-red-100 text-red-700', '🔴 Belum dibuka'],
                ][$status];
                $selesai = $ujian->jawaban_ujians_count;
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4 self-start">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-black text-slate-800 text-lg leading-tight">{{ $ujian->nama }}</h2>
                        <p class="text-xs font-bold text-slate-400 mt-0.5">ID {{ $ujian->id }} · {{ $ujian->durasi }} menit · {{ $ujian->soals_count }} soal</p>
                    </div>
                    <span class="shrink-0 px-3 py-1 rounded-full text-[11px] font-black {{ $badge[0] }}">{{ $badge[1] }}</span>
                </div>

                <div class="text-xs font-semibold text-slate-500 space-y-1">
                    <div>Jadwal: <span class="text-slate-700 font-black">
                        {{ $ujian->jadwal_mulai?->format('d M Y H:i') ?? '—' }} → {{ $ujian->jadwal_selesai?->format('d M Y H:i') ?? '—' }}
                    </span></div>
                    <div>Peserta selesai: <span class="text-slate-700 font-black">{{ $selesai }} / {{ $totalPeserta }}</span></div>
                    @if($ujian->override_status !== 'auto')
                        <div class="text-amber-600 font-black">⚠ Override: {{ $ujian->override_status === 'buka' ? 'dibuka manual' : 'ditutup manual' }}</div>
                    @endif
                </div>

                {{-- Kontrol sesi --}}
                <div class="flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('admin.panel_ujian.override', $ujian->id) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="override_status" value="buka">
                        <button class="px-3 py-1.5 rounded-lg text-[11px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100">Buka sekarang</button>
                    </form>
                    <form method="POST" action="{{ route('admin.panel_ujian.override', $ujian->id) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="override_status" value="tutup">
                        <button class="px-3 py-1.5 rounded-lg text-[11px] font-black bg-red-50 text-red-700 border border-red-200 hover:bg-red-100">Tutup sekarang</button>
                    </form>
                    @if($ujian->override_status !== 'auto')
                    <form method="POST" action="{{ route('admin.panel_ujian.override', $ujian->id) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="override_status" value="auto">
                        <button class="px-3 py-1.5 rounded-lg text-[11px] font-black bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200">Otomatis</button>
                    </form>
                    @endif
                </div>

                <div class="flex flex-wrap gap-2 border-t border-slate-100 pt-4">
                    <a href="{{ route('admin.soal.index', ['ujian_id' => $ujian->id]) }}"
                        class="px-3 py-1.5 rounded-lg text-[11px] font-black bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100">Kelola Soal</a>

                    <details class="relative inline-block">
                        <summary class="px-3 py-1.5 rounded-lg text-[11px] font-black bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200 cursor-pointer list-none">Jadwal & Edit</summary>
                        <div class="absolute z-20 mt-2 w-80 bg-white border border-slate-200 rounded-xl shadow-xl p-4 space-y-3 left-0">
                            <form method="POST" action="{{ route('admin.panel_ujian.jadwal', $ujian->id) }}" class="space-y-2">
                                @csrf @method('PUT')
                                <p class="text-[11px] font-black uppercase tracking-widest text-slate-400">Atur Jadwal</p>
                                <input name="jadwal_mulai" type="datetime-local" value="{{ $ujian->jadwal_mulai?->format('Y-m-d\TH:i') }}"
                                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs font-semibold">
                                <input name="jadwal_selesai" type="datetime-local" value="{{ $ujian->jadwal_selesai?->format('Y-m-d\TH:i') }}"
                                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs font-semibold">
                                <button class="w-full px-3 py-2 rounded-lg bg-slate-900 text-white text-xs font-black">Simpan Jadwal</button>
                            </form>
                            <form method="POST" action="{{ route('admin.panel_ujian.update', $ujian->id) }}" class="space-y-2 border-t border-slate-100 pt-3">
                                @csrf @method('PUT')
                                <p class="text-[11px] font-black uppercase tracking-widest text-slate-400">Edit Test</p>
                                <input name="nama" value="{{ $ujian->nama }}" required
                                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs font-semibold">
                                <input name="durasi" type="number" min="1" max="600" value="{{ $ujian->durasi }}" required
                                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs font-semibold">
                                <button class="w-full px-3 py-2 rounded-lg bg-blue-600 text-white text-xs font-black">Simpan Test</button>
                            </form>
                        </div>
                    </details>

                    <form method="POST" action="{{ route('admin.panel_ujian.kirim_undangan', $ujian->id) }}"
                        onsubmit="return confirm('Kirim email undangan ujian ini ke semua peserta lulus seleksi?')">
                        @csrf
                        <button class="px-3 py-1.5 rounded-lg text-[11px] font-black bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100">Kirim Undangan</button>
                    </form>

                    <form method="POST" action="{{ route('admin.panel_ujian.destroy', $ujian->id) }}"
                        onsubmit="return confirm('Hapus test \"{{ $ujian->nama }}\" beserta SEMUA soal, jawaban, dan nilainya? Tidak bisa dibatalkan.')">
                        @csrf @method('DELETE')
                        <button class="px-3 py-1.5 rounded-lg text-[11px] font-black bg-red-50 text-red-600 border border-red-200 hover:bg-red-100">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center text-sm font-bold text-slate-400">
                Belum ada test. Klik "+ Tambah Test Baru" untuk membuat Test Akademik, Bahasa Inggris, dll.
            </div>
        @endforelse
    </div>
</div>
@endsection

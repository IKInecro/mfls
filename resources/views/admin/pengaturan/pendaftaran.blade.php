@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-black text-slate-800">Pengaturan Pembukaan Pendaftaran</h1>
        <p class="text-sm text-slate-500 font-semibold mt-1">Atur kapan pendaftaran beasiswa dibuka dan ditutup.</p>
    </div>

    <!-- Status sekarang -->
    <div class="rounded-2xl border p-6 {{ $sedangBuka ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
        <div class="flex items-center gap-3">
            <span class="w-3 h-3 rounded-full {{ $sedangBuka ? 'bg-green-500' : 'bg-red-500' }}"></span>
            <span class="font-black text-sm uppercase tracking-widest {{ $sedangBuka ? 'text-green-700' : 'text-red-700' }}">
                Pendaftaran {{ $sedangBuka ? 'DIBUKA' : 'DITUTUP' }} Sekarang
            </span>
        </div>
        <p class="text-xs text-slate-500 font-semibold mt-2">
            @if($pengaturan->mode === 'buka')
                Mode: Buka paksa (mengabaikan jadwal)
            @elseif($pengaturan->mode === 'tutup')
                Mode: Tutup paksa (mengabaikan jadwal)
            @elseif($pengaturan->pendaftaran_mulai && $pengaturan->pendaftaran_selesai)
                Jadwal: {{ $pengaturan->pendaftaran_mulai->format('d/m/Y H:i') }} &ndash; {{ $pengaturan->pendaftaran_selesai->format('d/m/Y H:i') }}
            @else
                Mode: Ikut jadwal (belum ada jadwal &rarr; tertutup)
            @endif
        </p>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.pengaturan.pendaftaran.update') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-6 shadow-sm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="pendaftaran_mulai" class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Mulai Dibuka</label>
                <input type="datetime-local" id="pendaftaran_mulai" name="pendaftaran_mulai"
                    value="{{ old('pendaftaran_mulai', $pengaturan->pendaftaran_mulai?->format('Y-m-d\TH:i')) }}"
                    class="w-full px-4 py-3 bg-slate-50 border {{ $errors->has('pendaftaran_mulai') ? 'border-red-500' : 'border-slate-200' }} rounded-xl text-sm font-semibold outline-none focus:ring-4 focus:ring-orange-100">
                @error('pendaftaran_mulai') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="pendaftaran_selesai" class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Selesai (Tutup)</label>
                <input type="datetime-local" id="pendaftaran_selesai" name="pendaftaran_selesai"
                    value="{{ old('pendaftaran_selesai', $pengaturan->pendaftaran_selesai?->format('Y-m-d\TH:i')) }}"
                    class="w-full px-4 py-3 bg-slate-50 border {{ $errors->has('pendaftaran_selesai') ? 'border-red-500' : 'border-slate-200' }} rounded-xl text-sm font-semibold outline-none focus:ring-4 focus:ring-orange-100">
                @error('pendaftaran_selesai') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Mode</label>
            <div class="space-y-2">
                <label class="flex items-start gap-3 p-4 border {{ old('mode', $pengaturan->mode) === 'auto' ? 'border-orange-400 bg-orange-50/50' : 'border-slate-200' }} rounded-xl cursor-pointer">
                    <input type="radio" name="mode" value="auto" {{ old('mode', $pengaturan->mode) === 'auto' ? 'checked' : '' }} class="mt-1 accent-orange-500">
                    <span>
                        <span class="block text-sm font-black text-slate-800">Ikut Jadwal (auto)</span>
                        <span class="block text-xs text-slate-500 font-semibold">Buka otomatis saat waktu mulai tiba, tutup otomatis saat waktu selesai lewat.</span>
                    </span>
                </label>
                <label class="flex items-start gap-3 p-4 border {{ old('mode', $pengaturan->mode) === 'buka' ? 'border-orange-400 bg-orange-50/50' : 'border-slate-200' }} rounded-xl cursor-pointer">
                    <input type="radio" name="mode" value="buka" {{ old('mode', $pengaturan->mode) === 'buka' ? 'checked' : '' }} class="mt-1 accent-orange-500">
                    <span>
                        <span class="block text-sm font-black text-slate-800">Buka Paksa</span>
                        <span class="block text-xs text-slate-500 font-semibold">Pendaftaran langsung terbuka, jadwal diabaikan.</span>
                    </span>
                </label>
                <label class="flex items-start gap-3 p-4 border {{ old('mode', $pengaturan->mode) === 'tutup' ? 'border-orange-400 bg-orange-50/50' : 'border-slate-200' }} rounded-xl cursor-pointer">
                    <input type="radio" name="mode" value="tutup" {{ old('mode', $pengaturan->mode) === 'tutup' ? 'checked' : '' }} class="mt-1 accent-orange-500">
                    <span>
                        <span class="block text-sm font-black text-slate-800">Tutup Paksa</span>
                        <span class="block text-xs text-slate-500 font-semibold">Pendaftaran langsung ditutup walau jadwal masih berjalan.</span>
                    </span>
                </label>
            </div>
            @error('mode') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-black text-sm px-6 py-3 rounded-xl transition-colors">
                Simpan Pengaturan
            </button>
            <span class="text-xs text-slate-400 font-semibold">Tanggal wajib diisi saat mode &ldquo;Ikut Jadwal&rdquo;.</span>
        </div>
    </form>

</div>
@endsection

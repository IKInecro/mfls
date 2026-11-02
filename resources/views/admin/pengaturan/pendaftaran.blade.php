@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 sm:space-y-8">

    <!-- Header Banner -->
    <div class="relative overflow-hidden bg-[#0B1120] text-white p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl shadow-slate-950/20">
        <div class="absolute -top-20 -right-20 w-72 h-72 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/5 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-widest text-blue-400 mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    Kontrol Jadwal Pendaftaran
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight">
                    Pengaturan Pembukaan Pendaftaran
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl">
                    Tentukan waktu otomatis pembukaan & penutupan pendaftaran atau gunakan mode override darurat.
                </p>
            </div>
            
            <div class="p-4 rounded-2xl {{ $sedangBuka ? 'bg-emerald-500/10 border-emerald-500/30' : 'bg-rose-500/10 border-rose-500/30' }} border text-center min-w-[170px] shrink-0">
                <div class="text-[10px] font-bold uppercase tracking-wider {{ $sedangBuka ? 'text-emerald-400' : 'text-rose-400' }} flex items-center justify-center gap-1.5 mb-1">
                    <span class="w-2 h-2 rounded-full {{ $sedangBuka ? 'bg-emerald-400 animate-pulse' : 'bg-rose-400' }}"></span>
                    Status Portal
                </div>
                <div class="text-lg font-black {{ $sedangBuka ? 'text-emerald-300' : 'text-rose-300' }}">
                    {{ $sedangBuka ? 'DIBUKA' : 'DITUTUP' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Live Status Overview Card -->
    <div class="rounded-2xl border p-5 sm:p-6 {{ $sedangBuka ? 'bg-emerald-50/70 border-emerald-200' : 'bg-slate-100 border-slate-200' }} flex items-start gap-4">
        <div class="w-10 h-10 rounded-xl {{ $sedangBuka ? 'bg-emerald-500 text-white' : 'bg-slate-700 text-white' }} flex items-center justify-center shrink-0 shadow-xs">
            <span class="iconify text-xl" data-icon="{{ $sedangBuka ? 'solar:check-circle-bold' : 'solar:lock-bold' }}"></span>
        </div>
        <div class="min-w-0 flex-1">
            <h3 class="text-sm font-black {{ $sedangBuka ? 'text-emerald-900' : 'text-slate-800' }}">
                Pendaftaran Sedang {{ $sedangBuka ? 'Aktif Menerima Berkas' : 'Ditutup untuk Umum' }}
            </h3>
            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                @if($pengaturan->mode === 'buka')
                    Mode: <strong class="text-emerald-700">Buka Paksa (Override)</strong> &bull; Jadwal otomatis diabaikan, pendaftar dapat membuat akun kapan pun.
                @elseif($pengaturan->mode === 'tutup')
                    Mode: <strong class="text-rose-700">Tutup Paksa (Override)</strong> &bull; Portal registrasi ditutup segera meskipun jadwal masih berlangsung.
                @elseif($pengaturan->pendaftaran_mulai && $pengaturan->pendaftaran_selesai)
                    Mode: <strong class="text-blue-700">Otomatis Sesuai Jadwal</strong> &bull; Aktif dari <span class="font-bold text-slate-800">{{ $pengaturan->pendaftaran_mulai->format('d M Y, H:i') }}</span> sampai <span class="font-bold text-slate-800">{{ $pengaturan->pendaftaran_selesai->format('d M Y, H:i') }} WIB</span>.
                @else
                    Mode: <strong class="text-slate-700">Otomatis</strong> &bull; Belum ada rentang jadwal terpasang (status default tertutup).
                @endif
            </p>
        </div>
    </div>

    <!-- Form Pengaturan -->
    <form action="{{ route('admin.pengaturan.pendaftaran.update') }}" method="POST" class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 space-y-6 shadow-xs">
        @csrf

        <div>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="iconify text-[#0870B8] text-lg" data-icon="solar:calendar-date-bold-duotone"></span>
                Rentang Jadwal Otomatis
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <div>
                    <label for="pendaftaran_mulai" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mulai Dibuka</label>
                    <input type="datetime-local" id="pendaftaran_mulai" name="pendaftaran_mulai"
                        value="{{ old('pendaftaran_mulai', $pengaturan->pendaftaran_mulai?->format('Y-m-d\TH:i')) }}"
                        class="w-full px-4 py-3 bg-slate-50 hover:bg-white focus:bg-white border {{ $errors->has('pendaftaran_mulai') ? 'border-rose-500' : 'border-slate-200' }} focus:border-[#0870B8] rounded-xl text-xs sm:text-sm font-semibold outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                    @error('pendaftaran_mulai') <p class="text-rose-500 text-xs mt-1.5 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="pendaftaran_selesai" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Selesai (Ditutup)</label>
                    <input type="datetime-local" id="pendaftaran_selesai" name="pendaftaran_selesai"
                        value="{{ old('pendaftaran_selesai', $pengaturan->pendaftaran_selesai?->format('Y-m-d\TH:i')) }}"
                        class="w-full px-4 py-3 bg-slate-50 hover:bg-white focus:bg-white border {{ $errors->has('pendaftaran_selesai') ? 'border-rose-500' : 'border-slate-200' }} focus:border-[#0870B8] rounded-xl text-xs sm:text-sm font-semibold outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                    @error('pendaftaran_selesai') <p class="text-rose-500 text-xs mt-1.5 font-semibold">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                <span class="iconify text-[#0870B8] text-lg" data-icon="solar:tuning-bold-duotone"></span>
                Mode Operasional
            </h3>
            <div class="space-y-3">
                <label class="flex items-start gap-3.5 p-4 border {{ old('mode', $pengaturan->mode) === 'auto' ? 'border-[#0870B8] bg-blue-50/40 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }} rounded-2xl cursor-pointer transition-all">
                    <input type="radio" name="mode" value="auto" {{ old('mode', $pengaturan->mode) === 'auto' ? 'checked' : '' }} class="mt-1 accent-[#0870B8]">
                    <div>
                        <span class="block text-xs sm:text-sm font-bold text-slate-900">Ikut Jadwal Otomatis (Rekomendasi)</span>
                        <span class="block text-xs text-slate-500 mt-0.5">Portal pendaftaran terbuka dan tertutup otomatis secara presisi sesuai tanggal dan jam di atas.</span>
                    </div>
                </label>

                <label class="flex items-start gap-3.5 p-4 border {{ old('mode', $pengaturan->mode) === 'buka' ? 'border-emerald-500 bg-emerald-50/40 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }} rounded-2xl cursor-pointer transition-all">
                    <input type="radio" name="mode" value="buka" {{ old('mode', $pengaturan->mode) === 'buka' ? 'checked' : '' }} class="mt-1 accent-emerald-600">
                    <div>
                        <span class="block text-xs sm:text-sm font-bold text-emerald-800">Buka Paksa (Force Open)</span>
                        <span class="block text-xs text-slate-500 mt-0.5">Pendaftaran langsung dibuka untuk umum seketika, mengabaikan batas jadwal.</span>
                    </div>
                </label>

                <label class="flex items-start gap-3.5 p-4 border {{ old('mode', $pengaturan->mode) === 'tutup' ? 'border-rose-500 bg-rose-50/40 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }} rounded-2xl cursor-pointer transition-all">
                    <input type="radio" name="mode" value="tutup" {{ old('mode', $pengaturan->mode) === 'tutup' ? 'checked' : '' }} class="mt-1 accent-rose-600">
                    <div>
                        <span class="block text-xs sm:text-sm font-bold text-rose-800">Tutup Paksa (Force Close)</span>
                        <span class="block text-xs text-slate-500 mt-0.5">Pendaftaran ditutup segera untuk pemeliharaan sistem atau batas kuota terpenuhi.</span>
                    </div>
                </label>
            </div>
            @error('mode') <p class="text-rose-500 text-xs mt-1.5 font-semibold">{{ $message }}</p> @enderror
        </div>

        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <button type="submit" class="inline-flex items-center justify-center gap-2 bg-[#0870B8] hover:bg-[#065A96] text-white font-bold text-xs sm:text-sm px-6 py-3 rounded-xl transition-all shadow-xs cursor-pointer">
                <span class="iconify text-base" data-icon="solar:diskette-bold"></span>
                <span>Simpan Pengaturan Jadwal</span>
            </button>
            <span class="text-xs text-slate-400">Rentang tanggal wajib diisi jika memilih mode otomatis.</span>
        </div>
    </form>

</div>
@endsection

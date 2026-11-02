@extends('pendaftar.layout')

@section('content')
<div class="max-w-5xl mx-auto space-y-4 sm:space-y-6 pb-12">
    
    <!-- Hero Header (Grand Royal Navy & Slate Theme - Mobile Friendly) -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br from-[#0B1120] via-[#0F172A] to-[#1E293B] text-white p-5 sm:p-7 md:p-8 border border-slate-800 shadow-xl">
        <div class="absolute -top-32 -left-32 w-80 h-80 rounded-full bg-blue-600/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 rounded-full bg-amber-400/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 sm:gap-6">
            <div class="space-y-1.5 sm:space-y-2 max-w-xl">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/10 border border-white/15 text-[10px] sm:text-[11px] font-bold text-slate-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    <span>PROFIL RESMI CALON PENERIMA BEASISWA</span>
                </div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-white tracking-tight">
                    Biodata Lengkap Pendaftar
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 font-medium leading-relaxed">
                    Pastikan seluruh data identitas, kontak domisili, dan asal sekolah Anda terisi lengkap dan akurat untuk proses verifikasi berkas administrasi.
                </p>
            </div>
            
            <div class="shrink-0 w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-blue-400 shadow-sm hidden xs:flex">
                <span class="iconify text-2xl sm:text-3xl" data-icon="solar:user-id-bold-duotone"></span>
            </div>
        </div>
    </div>

    <!-- Main Biodata Form -->
    <form action="{{ route('pendaftar.biodata.store') }}" method="POST" class="space-y-4 sm:space-y-6">
        @csrf

        @php
            $sections = [
                [
                    'label' => 'Identitas Pribadi',
                    'desc' => 'Informasi identitas resmi sesuai KTP atau Kartu Pelajar.',
                    'icon' => 'solar:user-bold-duotone',
                    'fields' => [
                        ['name' => 'nama', 'label' => 'Nama Lengkap', 'placeholder' => 'Nama lengkap sesuai ijazah', 'type' => 'text', 'value' => $peserta->nama ?? Auth::user()->nama, 'icon' => 'solar:user-bold'],
                        ['name' => 'nisn', 'label' => 'Nomor Induk Siswa Nasional (NISN)', 'placeholder' => '10 digit NISN', 'type' => 'text', 'value' => $peserta->nisn, 'icon' => 'solar:card-2-bold'],
                        ['name' => 'jenis_kelamin', 'label' => 'Jenis Kelamin', 'type' => 'select', 'value' => $peserta->jenis_kelamin, 'icon' => 'solar:users-group-two-rounded-bold', 'options' => ['Laki-laki', 'Perempuan']],
                        ['name' => 'tgl_lahir', 'label' => 'Tanggal Lahir', 'type' => 'date', 'value' => $peserta->tgl_lahir, 'icon' => 'solar:calendar-bold']
                    ]
                ],
                [
                    'label' => 'Kontak & Domisili',
                    'desc' => 'Nomor WhatsApp aktif dan wilayah tempat tinggal saat ini.',
                    'icon' => 'solar:map-point-bold-duotone',
                    'fields' => [
                        ['name' => 'no_whatsapp', 'label' => 'Nomor WhatsApp Aktif', 'placeholder' => '08xxxxxxxxxx', 'type' => 'text', 'value' => $peserta->no_whatsapp, 'icon' => 'solar:phone-bold'],
                        ['name' => 'provinsi', 'label' => 'Provinsi Domisili', 'placeholder' => 'Contoh: Jawa Barat', 'type' => 'text', 'value' => $peserta->provinsi, 'icon' => 'solar:map-arrow-down-bold'],
                        ['name' => 'kabupaten', 'label' => 'Kabupaten / Kota', 'placeholder' => 'Contoh: Kota Bogor', 'type' => 'text', 'value' => $peserta->kabupaten, 'full' => true, 'icon' => 'solar:city-bold']
                    ]
                ],
                [
                    'label' => 'Informasi Pendidikan Asal',
                    'desc' => 'Data sekolah menengah atas dan tahun kelulusan Anda.',
                    'icon' => 'solar:bookmark-opened-bold-duotone',
                    'fields' => [
                        ['name' => 'nama_sekolah', 'label' => 'Nama Sekolah (SMA/SMK/MA/Sederajat)', 'placeholder' => 'Nama resmi sekolah asal Anda', 'type' => 'text', 'value' => $peserta->nama_sekolah, 'icon' => 'solar:square-academic-cap-bold', 'full' => true],
                        ['name' => 'tahun_lulus', 'label' => 'Tahun Kelulusan', 'type' => 'select', 'value' => $peserta->tahun_lulus, 'icon' => 'solar:calendar-date-bold', 'options' => ['2022', '2023', '2024', '2025', '2026', '2027']],
                        ['name' => 'no_guru_bk', 'label' => 'Nomor WhatsApp Guru BK (Opsional)', 'placeholder' => '08xxxxxxxxxx', 'type' => 'text', 'value' => $peserta->no_guru_bk, 'icon' => 'solar:chat-round-call-bold'],
                        ['name' => 'kode_referral', 'label' => 'Kode Referral / Rekomendasi (Opsional)', 'placeholder' => 'Masukkan kode jika ada', 'type' => 'text', 'value' => $peserta->daftar->kode_referral ?? '', 'icon' => 'solar:tag-bold', 'full' => true]
                    ]
                ]
            ];
        @endphp

        <!-- Form Cards Loop -->
        @foreach($sections as $sec)
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <!-- Section Header -->
            <div class="px-4 sm:px-6 py-3.5 sm:py-4 bg-slate-50/70 border-b border-slate-200/70 flex items-center gap-3">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center shrink-0">
                    <span class="iconify text-base sm:text-lg" data-icon="{{ $sec['icon'] }}"></span>
                </div>
                <div>
                    <h2 class="text-xs sm:text-sm font-extrabold text-slate-900 tracking-tight">{{ $sec['label'] }}</h2>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 font-medium">{{ $sec['desc'] }}</p>
                </div>
            </div>

            <!-- Fields Grid -->
            <div class="p-4 sm:p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-3.5 sm:gap-5">
                @foreach($sec['fields'] as $f)
                <div class="space-y-1 {{ ($f['full'] ?? false) ? 'md:col-span-2' : '' }}">
                    <label for="{{ $f['name'] }}" class="block text-xs font-bold text-slate-700 tracking-wide">
                        {{ $f['label'] }}
                    </label>
                    <div class="relative group">
                        @if(isset($f['icon']))
                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                                <span class="iconify text-base sm:text-lg" data-icon="{{ $f['icon'] }}"></span>
                            </div>
                        @endif

                        @if($f['type'] === 'select')
                            <select name="{{ $f['name'] }}" 
                                    id="{{ $f['name'] }}" 
                                    required 
                                    class="{{ isset($f['icon']) ? 'pl-9 sm:pl-10' : 'px-3.5 sm:px-4' }} pr-9 sm:pr-10 py-2.5 sm:py-3 w-full bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 outline-none transition-all appearance-none cursor-pointer">
                                <option value="">Pilih {{ $f['label'] }}</option>
                                @foreach($f['options'] as $opt)
                                    <option value="{{ $opt }}" {{ $f['value'] == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 sm:pr-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        @else
                            <input type="{{ $f['type'] }}" 
                                   name="{{ $f['name'] }}" 
                                   id="{{ $f['name'] }}" 
                                   value="{{ $f['value'] }}" 
                                   placeholder="{{ $f['placeholder'] ?? '' }}" 
                                   {{ !str_contains($f['label'], 'Opsional') ? 'required' : '' }}
                                   class="{{ isset($f['icon']) ? 'pl-9 sm:pl-10' : 'px-3.5 sm:px-4' }} pr-3.5 sm:pr-4 py-2.5 sm:py-3 w-full bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 outline-none transition-all">
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <!-- Account Security Box -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-4 sm:px-6 py-3.5 sm:py-4 bg-slate-50/70 border-b border-slate-200/70 flex items-center gap-3">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center shrink-0">
                    <span class="iconify text-base sm:text-lg" data-icon="solar:shield-keyhole-bold-duotone"></span>
                </div>
                <div>
                    <h2 class="text-xs sm:text-sm font-extrabold text-slate-900 tracking-tight">Keamanan & Kredensial Akun</h2>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 font-medium">Alamat email resmi yang terdaftar pada sistem seleksi beasiswa.</p>
                </div>
            </div>
            <div class="p-4 sm:p-6 md:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-slate-500">Email Akun Terdaftar:</span>
                    <p class="text-sm sm:text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                        </svg>
                        <span class="truncate">{{ Auth::user()->email }}</span>
                    </p>
                    <p class="text-[11px] text-slate-400 font-medium">Email ini digunakan untuk notifikasi seleksi dan penerimaan link token verifikasi.</p>
                </div>
                <a href="{{ route('pendaftar.email.change') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 hover:border-slate-300 text-xs font-bold text-slate-700 hover:text-slate-900 hover:bg-slate-50 transition-colors shrink-0">
                    <span>Ubah Alamat Email</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Submit Button Row -->
        <div class="flex items-center justify-end pt-1">
            <button type="submit" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 sm:px-8 py-3 rounded-xl bg-slate-900 hover:bg-black text-white text-xs sm:text-sm font-bold shadow-md hover:shadow-lg transition-all active:scale-[0.99] cursor-pointer">
                <span class="iconify text-base" data-icon="solar:diskette-bold"></span>
                <span>Simpan Pembaruan Biodata</span>
            </button>
        </div>

    </form>

</div>
@endsection

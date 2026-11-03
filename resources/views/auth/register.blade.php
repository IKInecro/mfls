@extends('layouts.auth')

@section('title', 'Pendaftaran Beasiswa MFLS 2027')

@section('hero_width', 'lg:w-5/12')
@section('canvas_width', 'lg:w-7/12')
@section('container_width', 'max-w-2xl')

@section('content')
<div class="w-full space-y-6">

    <!-- Mobile Brand Header (Small Screen Only) -->
    <div class="lg:hidden mb-4">
        <a href="/" class="inline-flex items-center gap-2.5">
            <img src="{{ asset('icon/logoo.png') }}" alt="Logo MFLS" class="h-9 w-auto">
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">MNC UNIVERSITY</span>
                <span class="block text-xs font-extrabold text-slate-900 tracking-tight">Future Leader Scholarship</span>
            </div>
        </a>
    </div>

    <!-- Page Header -->
    <div class="text-left space-y-2">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-200/80 text-amber-800 text-xs font-bold tracking-wide">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span>GELOMBANG PENDAFTARAN AJARAN 2027</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
            Registrasi Calon Penerima Beasiswa
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 font-medium leading-relaxed">
            Buat akun resmi seleksi beasiswa dengan melengkapi 3 bagian formulir di bawah ini. Pastikan seluruh identitas sesuai dokumen asli.
        </p>
    </div>

    <!-- Section Navigation / Quick Step Indicator -->
    <div class="grid grid-cols-3 gap-2 p-1.5 bg-slate-100/80 rounded-2xl border border-slate-200/60 text-xs">
        <a href="#section-akun" class="flex items-center justify-center gap-1.5 py-2 px-2 rounded-xl bg-white text-slate-800 font-bold shadow-xs hover:text-blue-600 transition-all text-center">
            <span class="w-5 h-5 rounded-lg bg-amber-100 text-amber-800 font-black text-[11px] flex items-center justify-center shrink-0">1</span>
            <span class="truncate">Akun & Akses</span>
        </a>
        <a href="#section-pribadi" class="flex items-center justify-center gap-1.5 py-2 px-2 rounded-xl text-slate-600 font-semibold hover:bg-white/60 hover:text-slate-900 transition-all text-center">
            <span class="w-5 h-5 rounded-lg bg-slate-200 text-slate-700 font-bold text-[11px] flex items-center justify-center shrink-0">2</span>
            <span class="truncate">Data Pribadi</span>
        </a>
        <a href="#section-sekolah" class="flex items-center justify-center gap-1.5 py-2 px-2 rounded-xl text-slate-600 font-semibold hover:bg-white/60 hover:text-slate-900 transition-all text-center">
            <span class="w-5 h-5 rounded-lg bg-slate-200 text-slate-700 font-bold text-[11px] flex items-center justify-center shrink-0">3</span>
            <span class="truncate">Asal Sekolah</span>
        </a>
    </div>

    <!-- Live Draft Autosave Alert Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-emerald-50 via-teal-50 to-blue-50 border border-emerald-200/80 p-4 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                    <span class="iconify text-xl" data-icon="solar:shield-check-bold-duotone"></span>
                </div>
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-900">Sistem Draft Otomatis Aktif</span>
                        <span id="draftBadge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                            <span id="draftStatusText">Draft tersimpan di browser</span>
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        Isian form tersimpan otomatis di cache browser Anda. Data tidak akan hilang bila halaman termuat ulang atau tertutup tanpa sengaja.
                    </p>
                </div>
            </div>
            <button type="button" id="btnClearDraft" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200 hover:border-rose-200 text-xs font-semibold shadow-xs transition-all shrink-0">
                <span class="iconify text-sm text-slate-400 group-hover:text-rose-500" data-icon="solar:trash-bin-trash-bold-duotone"></span>
                <span>Reset Form</span>
            </button>
        </div>
    </div>

    <!-- Error Summary Block -->
    @if ($errors->any())
        <div class="bg-rose-50 border-2 border-rose-300 text-rose-800 px-5 py-4 rounded-2xl shadow-sm" role="alert">
            <div class="flex items-center gap-2 font-black text-sm mb-2 text-rose-900">
                <span class="iconify text-lg text-rose-600" data-icon="solar:danger-triangle-bold-duotone"></span>
                <span>Terdapat kesalahan pengisian, mohon periksa kembali:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-1 font-medium text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Registration Form -->
    <form action="/register" method="POST" id="registerForm" class="space-y-6">
        @csrf
        <input type="hidden" name="role" value="pendaftar">

        <!-- ========================================== -->
        <!-- SECTION 1: DETAIL AKUN                     -->
        <!-- ========================================== -->
        <div id="section-akun" class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden scroll-mt-6">
            <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-700 border border-amber-500/20 flex items-center justify-center font-black text-sm">
                        1
                    </div>
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Detail Akun & Kredensial</h2>
                        <p class="text-[11px] text-slate-500">Informasi utama untuk verifikasi dan akses masuk ke sistem portal.</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                    Langkah 1/3
                </span>
            </div>

            <div class="p-5 sm:p-6 space-y-5">
                
                <!-- Nama Lengkap -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="nama" class="block text-xs font-bold text-slate-800">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] font-medium text-slate-400">Sesuai KTP / Ijazah</span>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-600 transition-colors">
                            <span class="iconify text-lg" data-icon="solar:user-bold-duotone"></span>
                        </div>
                        <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                            class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border {{ $errors->has('nama') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 font-medium text-sm text-slate-900"
                            placeholder="Contoh: Muhammad Rayhan Pratama">
                    </div>
                    <p class="text-[11px] text-slate-500 flex items-center gap-1.5">
                        <span class="iconify text-slate-400" data-icon="solar:info-circle-linear"></span>
                        Ketik nama lengkap resmi tanpa singkatan berlebihan untuk mempermudah penerbitan sertifikat & verifikasi beasiswa.
                    </p>
                    @error('nama') <p class="text-rose-600 text-xs font-bold">{{ $message }}</p> @enderror
                </div>

                <!-- NISN & Email (2 Grid) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    <!-- NISN -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="nisn" class="block text-xs font-bold text-slate-800">
                                NISN <span class="text-rose-500">*</span>
                            </label>
                            <span id="nisnCounterBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200">
                                <span id="nisnDigitCount">0</span>/10 Digit
                            </span>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-600 transition-colors">
                                <span class="iconify text-lg" data-icon="solar:card-2-bold-duotone"></span>
                            </div>
                            <input type="text" id="nisn" name="nisn" value="{{ old('nisn') }}" required maxlength="10" inputmode="numeric"
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border {{ $errors->has('nisn') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 font-medium text-sm text-slate-900"
                                placeholder="10 digit nomor siswa">
                        </div>
                        <p class="text-[11px] text-slate-500">10 digit angka resmi Kemdikbudristek (dapat dicek di <a href="https://nisn.data.kemdikbud.go.id" target="_blank" class="text-blue-600 hover:underline">nisn.data.kemdikbud.go.id</a>).</p>
                        @error('nisn') <p class="text-rose-600 text-xs font-bold">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="email" class="block text-xs font-bold text-slate-800">
                                Alamat Email Aktif <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">OTP Dikirim Ke Sini</span>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-600 transition-colors">
                                <span class="iconify text-lg" data-icon="solar:letter-bold-duotone"></span>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border {{ $errors->has('email') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 font-medium text-sm text-slate-900"
                                placeholder="nama.anda@gmail.com">
                        </div>
                        <p class="text-[11px] text-slate-500">Pastikan email aktif dan bisa diakses saat ini untuk menerima kode OTP.</p>
                        @error('email') <p class="text-rose-600 text-xs font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Password & Konfirmasi Password (2 Grid) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    <!-- Password -->
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-bold text-slate-800">
                            Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-600 transition-colors">
                                <span class="iconify text-lg" data-icon="solar:lock-password-bold-duotone"></span>
                            </div>
                            <input type="password" id="password" name="password" required minlength="8"
                                class="w-full pl-11 pr-11 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 font-medium text-sm text-slate-900"
                                placeholder="Minimal 8 karakter">
                            <button type="button" class="btn-toggle-pw absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer" data-target="password" tabindex="-1">
                                <span class="iconify text-lg" data-icon="solar:eye-bold-duotone"></span>
                            </button>
                        </div>
                        <div class="flex items-center gap-1.5 text-[11px] text-slate-500">
                            <span id="pwLengthCheck" class="inline-flex items-center gap-1 text-slate-400">
                                <span class="iconify" data-icon="solar:check-circle-linear"></span> Minimal 8 karakter
                            </span>
                        </div>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-800">
                            Konfirmasi Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-600 transition-colors">
                                <span class="iconify text-lg" data-icon="solar:lock-check-bold-duotone"></span>
                            </div>
                            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                                class="w-full pl-11 pr-11 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 font-medium text-sm text-slate-900"
                                placeholder="Ulangi password di atas">
                            <button type="button" class="btn-toggle-pw absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer" data-target="password_confirmation" tabindex="-1">
                                <span class="iconify text-lg" data-icon="solar:eye-bold-duotone"></span>
                            </button>
                        </div>
                        <div id="pwMatchStatus" class="text-[11px] font-semibold text-slate-400">
                            Ketik ulang password yang sama
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- SECTION 2: INFORMASI PRIBADI               -->
        <!-- ========================================== -->
        <div id="section-pribadi" class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden scroll-mt-6">
            <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-700 border border-blue-500/20 flex items-center justify-center font-black text-sm">
                        2
                    </div>
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Informasi Pribadi & Kontak</h2>
                        <p class="text-[11px] text-slate-500">Data diri dan nomor kontak aktif untuk koordinasi seleksi dan tes online.</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-blue-800 bg-blue-50 px-2.5 py-1 rounded-full border border-blue-200">
                    Langkah 2/3
                </span>
            </div>

            <div class="p-5 sm:p-6 space-y-5">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    <!-- No WhatsApp -->
                    <div class="space-y-1.5">
                        <label for="no_whatsapp" class="block text-xs font-bold text-slate-800">
                            Nomor WhatsApp Aktif <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-600 transition-colors">
                                <span class="iconify text-lg" data-icon="solar:phone-bold-duotone"></span>
                            </div>
                            <input type="text" id="no_whatsapp" name="no_whatsapp" value="{{ old('no_whatsapp') }}" required inputmode="numeric"
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border {{ $errors->has('no_whatsapp') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 font-medium text-sm text-slate-900"
                                placeholder="08xxxxxxxxxx">
                        </div>
                        <p class="text-[11px] text-slate-500">Nomor aktif untuk undangan seleksi, grup peserta & wawancara.</p>
                        @error('no_whatsapp') <p class="text-rose-600 text-xs font-bold">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tanggal Lahir -->
                    <div class="space-y-1.5">
                        <label for="tgl_lahir" class="block text-xs font-bold text-slate-800">
                            Tanggal Lahir <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-600 transition-colors">
                                <span class="iconify text-lg" data-icon="solar:calendar-bold-duotone"></span>
                            </div>
                            <input type="date" id="tgl_lahir" name="tgl_lahir" value="{{ old('tgl_lahir') }}" required
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all font-medium text-sm text-slate-900">
                        </div>
                        <p class="text-[11px] text-slate-500">Sesuai akta kelahiran atau KTP Anda.</p>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="space-y-1.5">
                        <label for="jenis_kelamin" class="block text-xs font-bold text-slate-800">
                            Jenis Kelamin <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-600 transition-colors">
                                <span class="iconify text-lg" data-icon="solar:users-group-two-rounded-bold-duotone"></span>
                            </div>
                            <select id="jenis_kelamin" name="jenis_kelamin" required
                                class="w-full pl-11 pr-10 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all font-medium text-sm text-slate-900 appearance-none cursor-pointer">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                <span class="iconify text-lg" data-icon="solar:alt-arrow-down-bold-duotone"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Tahun Kelulusan -->
                    <div class="space-y-1.5">
                        <label for="tahun_lulus" class="block text-xs font-bold text-slate-800">
                            Tahun Kelulusan <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-600 transition-colors">
                                <span class="iconify text-lg" data-icon="solar:diploma-bold-duotone"></span>
                            </div>
                            <select id="tahun_lulus" name="tahun_lulus" required
                                class="w-full pl-11 pr-10 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all font-medium text-sm text-slate-900 appearance-none cursor-pointer">
                                <option value="">Pilih Tahun Kelulusan</option>
                                <option value="2027" {{ old('tahun_lulus') == '2027' ? 'selected' : '' }}>2027 (Kelas 12 TA 2026/2027)</option>
                                <option value="2026" {{ old('tahun_lulus') == '2026' ? 'selected' : '' }}>2026</option>
                                <option value="2025" {{ old('tahun_lulus') == '2025' ? 'selected' : '' }}>2025</option>
                                <option value="2024" {{ old('tahun_lulus') == '2024' ? 'selected' : '' }}>2024</option>
                                <option value="2023" {{ old('tahun_lulus') == '2023' ? 'selected' : '' }}>2023</option>
                                <option value="2022" {{ old('tahun_lulus') == '2022' ? 'selected' : '' }}>2022</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                <span class="iconify text-lg" data-icon="solar:alt-arrow-down-bold-duotone"></span>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500">Terbuka untuk siswa kelas 12 aktif maupun alumni gap year.</p>
                    </div>

                </div>

                <!-- Eligibility Guide Box -->
                <div class="p-3.5 rounded-xl bg-amber-50/80 border border-amber-200/90 flex items-start gap-3 text-amber-950 text-xs">
                    <span class="iconify text-xl text-amber-600 shrink-0 mt-0.5" data-icon="solar:shield-check-bold"></span>
                    <p class="leading-relaxed">
                        <strong>Kelayakan Peserta:</strong> Terbuka untuk siswa/i aktif kelas 12 SMA/SMK/MA sederajat yang akan lulus tahun 2027, serta alumni lulusan tahun ajaran 2022 hingga 2026.
                    </p>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- SECTION 3: INFORMASI ASAL SEKOLAH          -->
        <!-- ========================================== -->
        <div id="section-sekolah" class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden scroll-mt-6">
            <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-700 border border-emerald-500/20 flex items-center justify-center font-black text-sm">
                        3
                    </div>
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Informasi Sekolah & Rekomendasi</h2>
                        <p class="text-[11px] text-slate-500">Data sekolah asal dan sumber informasi pendaftaran program beasiswa.</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                    Langkah 3/3
                </span>
            </div>

            <div class="p-5 sm:p-6 space-y-5">
                
                <!-- Provinsi & Kabupaten (2 Grid) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    <!-- Provinsi -->
                    <div class="space-y-1.5">
                        <label for="provinsi" class="block text-xs font-bold text-slate-800">
                            Provinsi Asal Sekolah <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-600 transition-colors">
                                <span class="iconify text-lg" data-icon="solar:map-point-wave-bold-duotone"></span>
                            </div>
                            <input type="text" id="provinsi" name="provinsi" value="{{ old('provinsi') }}" required
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 font-medium text-sm text-slate-900"
                                placeholder="Contoh: Jawa Barat">
                        </div>
                        <p class="text-[11px] text-slate-500">Provinsi lokasi SMA/SMK/MA Anda.</p>
                    </div>

                    <!-- Kabupaten / Kota -->
                    <div class="space-y-1.5">
                        <label for="kabupaten" class="block text-xs font-bold text-slate-800">
                            Kota / Kabupaten <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-600 transition-colors">
                                <span class="iconify text-lg" data-icon="solar:city-bold-duotone"></span>
                            </div>
                            <input type="text" id="kabupaten" name="kabupaten" value="{{ old('kabupaten') }}" required
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 font-medium text-sm text-slate-900"
                                placeholder="Contoh: Kota Bandung">
                        </div>
                        <p class="text-[11px] text-slate-500">Kota atau kabupaten tempat sekolah berada.</p>
                    </div>

                </div>

                <!-- Nama Sekolah -->
                <div class="space-y-1.5">
                    <label for="nama_sekolah" class="block text-xs font-bold text-slate-800">
                        Nama Lengkap Sekolah Asal <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-600 transition-colors">
                            <span class="iconify text-lg" data-icon="solar:square-academic-cap-bold-duotone"></span>
                        </div>
                        <input type="text" id="nama_sekolah" name="nama_sekolah" value="{{ old('nama_sekolah') }}" required
                            class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 font-medium text-sm text-slate-900"
                            placeholder="Contoh: SMAN 1 Bandung / SMKN 2 Jakarta">
                    </div>
                    <p class="text-[11px] text-slate-500">Ketik nama resmi sekolah tanpa singkatan yang membingungkan.</p>
                </div>

                <!-- No Guru BK (Opsional) -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="no_guru_bk" class="block text-xs font-bold text-slate-800">
                            No. WhatsApp Guru BK / Wali Kelas
                        </label>
                        <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">Opsional</span>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-600 transition-colors">
                            <span class="iconify text-lg" data-icon="solar:user-speak-bold-duotone"></span>
                        </div>
                        <input type="text" id="no_guru_bk" name="no_guru_bk" value="{{ old('no_guru_bk') }}" inputmode="numeric"
                            class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 font-medium text-sm text-slate-900"
                            placeholder="Contoh: 08123456789 (Boleh dikosongkan)">
                    </div>
                    <p class="text-[11px] text-slate-500">Opsional untuk mempermudah koordinasi rekomendasi prestasi sekolah.</p>
                    @error('no_guru_bk') <p class="text-rose-600 text-xs font-bold">{{ $message }}</p> @enderror
                </div>

                <!-- Sumber Informasi -->
                <div class="space-y-1.5">
                    <label for="sumber_informasi" class="block text-xs font-bold text-slate-800">
                        Dari mana Anda mengetahui program MNCU Future Leader Scholarship? <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-600 transition-colors">
                            <span class="iconify text-lg" data-icon="solar:megaphone-bold-duotone"></span>
                        </div>
                        <select id="sumber_informasi" name="sumber_informasi" required
                            class="w-full pl-11 pr-10 py-3.5 bg-slate-50 border {{ $errors->has('sumber_informasi') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all font-medium text-sm text-slate-900 appearance-none cursor-pointer">
                            <option value="">Pilih Sumber Informasi Utama</option>
                            @php 
                                $sources = [
                                    'Instagram', 
                                    'Tiktok', 
                                    'Facebook', 
                                    'Website MNC University', 
                                    'TV (MNC Media)', 
                                    'Radio', 
                                    'Guru/Kepala Sekolah', 
                                    'Presentasi Di Sekolah', 
                                    'Teman', 
                                    'Keluarga', 
                                    'Pameran Pendidikan', 
                                    'Media Cetak/Brosur', 
                                    'Lainnya'
                                ]; 
                            @endphp
                            @foreach($sources as $src)
                                <option value="{{ $src }}" {{ old('sumber_informasi') == $src ? 'selected' : '' }}>{{ $src }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                            <span class="iconify text-lg" data-icon="solar:alt-arrow-down-bold-duotone"></span>
                        </div>
                    </div>
                    @error('sumber_informasi') <p class="text-rose-600 text-xs font-bold">{{ $message }}</p> @enderror
                </div>

                <!-- Kode Referral (Opsional) -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="kode_referral" class="block text-xs font-bold text-slate-800">
                            Kode Referral / Rekomendasi
                        </label>
                        <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">Opsional</span>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-600 transition-colors">
                            <span class="iconify text-lg" data-icon="solar:ticket-bold-duotone"></span>
                        </div>
                        <input type="text" id="kode_referral" name="kode_referral" value="{{ old('kode_referral') }}"
                            class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border {{ $errors->has('kode_referral') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 font-medium text-sm text-slate-900"
                            placeholder="Masukkan kode referral jika memiliki voucher rekomendasi">
                    </div>
                    <p class="text-[11px] text-slate-500">Dapatkan keuntungan khusus jika Anda memiliki kode dari duta kampus atau mitra sekolah.</p>
                    @error('kode_referral') <p class="text-rose-600 text-xs font-bold">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- VERIFIKASI KEAMANAN & TURNSTILE            -->
        <!-- ========================================== -->
        <div class="bg-slate-50 rounded-2xl border border-slate-200 p-5 space-y-3">
            <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
                <span class="iconify text-lg text-emerald-600" data-icon="solar:shield-check-bold-duotone"></span>
                <span>Verifikasi Keamanan (Anti-Bot)</span>
            </div>
            <p class="text-[11px] text-slate-500 leading-relaxed">
                Silakan selesaikan tantangan verifikasi Cloudflare di bawah ini untuk memastikan formulir diisi oleh manusia asli:
            </p>
            <div class="pt-1">
                <div class="cf-turnstile" data-sitekey="{{ env('TURNSTILE_SITE_KEY') }}"></div>
            </div>
            @error('cf-turnstile-response') 
                <p class="text-rose-600 text-xs font-bold flex items-center gap-1">
                    <span class="iconify" data-icon="solar:danger-triangle-bold"></span>
                    {{ $message }}
                </p> 
            @enderror
        </div>

        <!-- ========================================== -->
        <!-- PERNYATAAN & PERSETUJUAN                   -->
        <!-- ========================================== -->
        <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/60 border border-amber-200 transition-all hover:bg-amber-50">
            <label class="flex items-start gap-3 cursor-pointer select-none">
                <input type="checkbox" id="agreement" required
                    class="mt-1 w-5 h-5 rounded-lg border-amber-300 text-amber-600 focus:ring-amber-500/20 cursor-pointer transition-all shrink-0">
                <div class="text-xs text-slate-700 leading-relaxed font-medium">
                    <span class="font-bold text-slate-900">Pernyataan Kebenaran Data:</span> 
                    Saya menyatakan bahwa seluruh data yang saya isikan pada formulir pendaftaran ini adalah benar, sah, dan dapat dipertanggungjawabkan sesuai dokumen resmi, serta menyetujui seluruh ketentuan seleksi MNCU Future Leader Scholarship.
                </div>
            </label>
        </div>

        <!-- ========================================== -->
        <!-- SUBMIT BUTTON & INFO SESUDAH DAFTAR        -->
        <!-- ========================================== -->
        <div class="space-y-3 pt-2">
            <button type="submit" id="submitBtn" disabled
                class="w-full py-4 px-6 rounded-2xl font-black text-sm transition-all duration-200 flex items-center justify-center gap-2 bg-slate-200 text-slate-400 cursor-not-allowed shadow-none">
                <span class="iconify text-lg" data-icon="solar:lock-bold-duotone" id="btnIcon"></span>
                <span id="btnText">Centang Pernyataan di Atas untuk Mendaftar</span>
            </button>

            <!-- Next Step Alert -->
            <div class="flex items-start gap-2.5 p-3.5 rounded-xl bg-slate-100 text-slate-600 text-[11px] leading-relaxed">
                <span class="iconify text-base text-blue-600 shrink-0 mt-0.5" data-icon="solar:info-circle-bold"></span>
                <p>
                    <strong>Tahap Selanjutnya:</strong> Setelah menekan tombol pendaftaran, sistem akan mengirimkan <strong>6 digit kode OTP</strong> ke email Anda. Masukkan kode tersebut pada halaman verifikasi untuk mengaktifkan akun pendaftar Anda.
                </p>
            </div>
        </div>
    </form>

    <div class="pt-4 border-t border-slate-100 text-center">
        <p class="text-xs font-semibold text-slate-500">
            Sudah pernah mendaftar dan memiliki akun? 
            <a href="/login" class="text-amber-700 hover:text-amber-800 font-bold hover:underline transition-colors ml-1">
                Masuk ke Portal Peserta &rarr;
            </a>
        </p>
    </div>

</div>

<!-- Cloudflare Turnstile API -->
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

<!-- Script Logic: Draft Auto-Save (LocalStorage), Password Toggles & Real-time Validations -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const DRAFT_KEY = 'mfls_reg_draft_2027_v1';
    
    // Elements
    const form = document.getElementById('registerForm');
    const agreement = document.getElementById('agreement');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnIcon = document.getElementById('btnIcon');
    const draftStatusText = document.getElementById('draftStatusText');
    const btnClearDraft = document.getElementById('btnClearDraft');

    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const pwMatchStatus = document.getElementById('pwMatchStatus');
    const pwLengthCheck = document.getElementById('pwLengthCheck');

    const nisnInput = document.getElementById('nisn');
    const nisnDigitCount = document.getElementById('nisnDigitCount');
    const nisnCounterBadge = document.getElementById('nisnCounterBadge');

    // List of form field IDs to automatically save into local draft
    // Note: Passwords are intentionally excluded for user security & privacy!
    const draftFieldIds = [
        'nama', 'nisn', 'email', 'no_whatsapp', 'tgl_lahir', 
        'jenis_kelamin', 'tahun_lulus', 'provinsi', 'kabupaten', 
        'nama_sekolah', 'no_guru_bk', 'sumber_informasi', 'kode_referral'
    ];

    // ==========================================
    // 1. DRAFT LOCALSTORAGE SYSTEM
    // ==========================================
    function saveDraft() {
        try {
            const draft = {};
            draftFieldIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    draft[id] = el.value;
                }
            });
            draft['_saved_at'] = new Date().toISOString();
            localStorage.setItem(DRAFT_KEY, JSON.stringify(draft));

            // Update status indicator
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            if (draftStatusText) {
                draftStatusText.textContent = 'Draft tersimpan otomatis (' + timeStr + ')';
            }
        } catch (e) {
            console.warn('Gagal menyimpan draft ke LocalStorage:', e);
        }
    }

    let saveTimeout = null;
    function triggerAutoSave() {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(saveDraft, 400);
    }

    function restoreDraft() {
        try {
            const raw = localStorage.getItem(DRAFT_KEY);
            if (!raw) return;
            const draft = JSON.parse(raw);
            if (!draft || typeof draft !== 'object') return;

            let restoredCount = 0;
            draftFieldIds.forEach(id => {
                const el = document.getElementById(id);
                // Only restore if current element is empty (preserve server old() values if any)
                if (el && (!el.value || el.value.trim() === '') && draft[id] !== undefined && draft[id] !== '') {
                    el.value = draft[id];
                    restoredCount++;
                }
            });

            if (restoredCount > 0 && draft['_saved_at']) {
                const savedDate = new Date(draft['_saved_at']);
                const timeStr = savedDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                if (draftStatusText) {
                    draftStatusText.textContent = 'Draft dipulihkan (' + timeStr + ')';
                }
            }

            // Update interactive UI elements after restoring
            updateNisnCounter();
            checkPasswordMatch();
            updateSubmitButtonState();
        } catch (e) {
            console.warn('Gagal memulihkan draft:', e);
        }
    }

    // Attach autosave listeners
    draftFieldIds.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', triggerAutoSave);
            el.addEventListener('change', triggerAutoSave);
        }
    });

    // Reset Form & Clear Draft
    if (btnClearDraft) {
        btnClearDraft.addEventListener('click', function() {
            if (window.Swal) {
                Swal.fire({
                    title: 'Reset formulir pendaftaran?',
                    text: 'Semua isian yang telah Anda ketik dan draft di browser ini akan dihapus bersih.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Kosongkan Form',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        localStorage.removeItem(DRAFT_KEY);
                        form.reset();
                        updateNisnCounter();
                        checkPasswordMatch();
                        updateSubmitButtonState();
                        if (draftStatusText) {
                            draftStatusText.textContent = 'Draft telah dikosongkan';
                        }
                    }
                });
            } else if (confirm('Kosongkan semua isian dan hapus draft pendaftaran di browser?')) {
                localStorage.removeItem(DRAFT_KEY);
                form.reset();
                updateNisnCounter();
                checkPasswordMatch();
                updateSubmitButtonState();
            }
        });
    }

    // Clear draft on successful form submit
    form.addEventListener('submit', function() {
        // We leave the draft until OTP is sent, or clear it
        localStorage.removeItem(DRAFT_KEY);
        // Show loading state
        submitBtn.disabled = true;
        btnText.textContent = 'Sedang Mendaftarkan & Mengirim OTP...';
        btnIcon.setAttribute('data-icon', 'solar:restart-bold-duotone');
        btnIcon.classList.add('animate-spin');
    });

    // ==========================================
    // 2. SHOW / HIDE PASSWORD TOGGLE
    // ==========================================
    document.querySelectorAll('.btn-toggle-pw').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            const icon = this.querySelector('.iconify');
            if (targetInput) {
                if (targetInput.type === 'password') {
                    targetInput.type = 'text';
                    if (icon) icon.setAttribute('data-icon', 'solar:eye-closed-bold-duotone');
                } else {
                    targetInput.type = 'password';
                    if (icon) icon.setAttribute('data-icon', 'solar:eye-bold-duotone');
                }
            }
        });
    });

    // ==========================================
    // 3. PASSWORD STRENGTH & MATCH VALIDATION
    // ==========================================
    function checkPasswordMatch() {
        const pw = passwordInput ? passwordInput.value : '';
        const cpw = confirmPasswordInput ? confirmPasswordInput.value : '';

        // Length Check
        if (pwLengthCheck) {
            if (pw.length >= 8) {
                pwLengthCheck.className = 'inline-flex items-center gap-1 text-emerald-600 font-bold';
                pwLengthCheck.innerHTML = '<span class="iconify text-sm" data-icon="solar:check-circle-bold"></span> Minimal 8 karakter terpenuhi';
            } else {
                pwLengthCheck.className = 'inline-flex items-center gap-1 text-slate-400';
                pwLengthCheck.innerHTML = '<span class="iconify text-sm" data-icon="solar:check-circle-linear"></span> Minimal 8 karakter';
            }
        }

        // Match Check
        if (pwMatchStatus) {
            if (!cpw) {
                pwMatchStatus.className = 'text-[11px] font-semibold text-slate-400';
                pwMatchStatus.innerHTML = 'Ketik ulang password yang sama';
                confirmPasswordInput.classList.remove('border-rose-400', 'border-emerald-400');
            } else if (pw === cpw) {
                pwMatchStatus.className = 'text-[11px] font-bold text-emerald-600 flex items-center gap-1';
                pwMatchStatus.innerHTML = '<span class="iconify text-sm" data-icon="solar:check-circle-bold"></span> Password cocok & terkonfirmasi';
                confirmPasswordInput.classList.remove('border-rose-400');
                confirmPasswordInput.classList.add('border-emerald-400');
            } else {
                pwMatchStatus.className = 'text-[11px] font-bold text-rose-600 flex items-center gap-1';
                pwMatchStatus.innerHTML = '<span class="iconify text-sm" data-icon="solar:close-circle-bold"></span> Password tidak sama';
                confirmPasswordInput.classList.add('border-rose-400');
                confirmPasswordInput.classList.remove('border-emerald-400');
            }
        }
    }

    if (passwordInput) passwordInput.addEventListener('input', checkPasswordMatch);
    if (confirmPasswordInput) confirmPasswordInput.addEventListener('input', checkPasswordMatch);

    // ==========================================
    // 4. NUMBER ONLY INPUT FILTERS & NISN COUNTER
    // ==========================================
    function updateNisnCounter() {
        if (!nisnInput) return;
        const len = nisnInput.value.length;
        if (nisnDigitCount) nisnDigitCount.textContent = len;
        
        if (nisnCounterBadge) {
            if (len === 10) {
                nisnCounterBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 border border-emerald-300';
            } else if (len > 0) {
                nisnCounterBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 border border-amber-300';
            } else {
                nisnCounterBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200';
            }
        }
    }

    const numberFieldIds = ['nisn', 'no_whatsapp', 'no_guru_bk'];
    numberFieldIds.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (id === 'nisn') {
                    if (this.value.length > 10) {
                        this.value = this.value.slice(0, 10);
                    }
                    updateNisnCounter();
                }
            });
        }
    });

    // ==========================================
    // 5. AGREEMENT CHECKBOX & SUBMIT BUTTON
    // ==========================================
    function updateSubmitButtonState() {
        if (!agreement || !submitBtn) return;
        if (agreement.checked) {
            submitBtn.disabled = false;
            submitBtn.className = 'w-full py-4 px-6 rounded-2xl font-black text-sm transition-all duration-200 flex items-center justify-center gap-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 shadow-lg shadow-amber-500/25 hover:shadow-xl hover:scale-[1.01] cursor-pointer';
            btnText.textContent = 'Daftar Sekarang & Kirim Kode OTP';
            btnIcon.setAttribute('data-icon', 'solar:arrow-right-bold');
        } else {
            submitBtn.disabled = true;
            submitBtn.className = 'w-full py-4 px-6 rounded-2xl font-black text-sm transition-all duration-200 flex items-center justify-center gap-2 bg-slate-200 text-slate-400 cursor-not-allowed shadow-none';
            btnText.textContent = 'Centang Pernyataan di Atas untuk Mendaftar';
            btnIcon.setAttribute('data-icon', 'solar:lock-bold-duotone');
        }
    }

    if (agreement) {
        agreement.addEventListener('change', updateSubmitButtonState);
    }

    // Execute restoration on initial boot
    restoreDraft();
    updateSubmitButtonState();
});
</script>
@endsection

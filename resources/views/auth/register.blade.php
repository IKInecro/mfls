@extends('layouts.auth')

@section('title', 'Daftar Akun')

@section('content')
<div class="mb-10 text-center lg:text-left">
    <h1 class="text-3xl font-black text-gray-900 mb-3">Pendaftaran MNCU Future Leader Scholarship Ajaran 2027</h1>
    <p class="text-gray-500 font-medium">Buat akun untuk memulai pendaftaran MNCU Future Leader Scholarship Ajaran 2027.</p>
</div>

<form action="/register" method="POST" class="space-y-8">
    @csrf
    <input type="hidden" name="role" value="pendaftar">

    <!-- Error Debugging Block -->
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Ada kesalahan!</strong>
            <span class="block sm:inline">Silakan periksa inputan Anda:</span>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <!-- Section 1: Akun -->
    <div class="space-y-4">
        <h3 class="text-xs font-bold text-primary-gold uppercase tracking-widest flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-primary-gold"></span>
            Detail Akun
        </h3>
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label for="nama" class="block text-xs font-bold text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required class="w-full px-5 py-4 bg-gray-50 border {{ $errors->has('nama') ? 'border-red-500' : 'border-gray-100' }} rounded-2xl focus:ring-4 focus:ring-primary-gold/10 outline-none transition-all placeholder:text-gray-400 font-medium text-sm" placeholder="Sesuai Ijazah/KTP">
                @error('nama') <p class="text-red-500 text-[10px] mt-1 font-bold italic">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="nisn" class="block text-xs font-bold text-gray-700 mb-2">NISN</label>
                <input type="text" id="nisn" name="nisn" value="{{ old('nisn') }}" required maxlength="10" class="w-full px-5 py-4 bg-gray-50 border {{ $errors->has('nisn') ? 'border-red-500' : 'border-gray-100' }} rounded-2xl focus:ring-4 focus:ring-primary-gold/10 outline-none transition-all placeholder:text-gray-400 font-medium text-sm" placeholder="Nomor Induk Siswa Nasional (10 digit)">
                @error('nisn') <p class="text-red-500 text-[10px] mt-1 font-bold italic">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email" class="block text-xs font-bold text-gray-700 mb-2">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full px-5 py-4 bg-gray-50 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-100' }} rounded-2xl focus:ring-4 focus:ring-primary-gold/10 outline-none transition-all placeholder:text-gray-400 font-medium text-sm" placeholder="nama@email.com">
                @error('email') <p class="text-red-500 text-[10px] mt-1 font-bold italic">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-bold text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-gold/10 outline-none transition-all placeholder:text-gray-400 font-medium text-sm" placeholder="••••••••">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-gray-700 mb-2">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-gold/10 outline-none transition-all placeholder:text-gray-400 font-medium text-sm" placeholder="••••••••">
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Info Pribadi -->
    <div class="space-y-4">
        <h3 class="text-xs font-bold text-primary-gold uppercase tracking-widest flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-primary-gold"></span>
            Informasi Pribadi
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="no_whatsapp" class="block text-xs font-bold text-gray-700 mb-2">No. WhatsApp</label>
                <input type="text" id="no_whatsapp" name="no_whatsapp" value="{{ old('no_whatsapp') }}" required class="w-full px-5 py-4 bg-gray-50 border {{ $errors->has('no_whatsapp') ? 'border-red-500' : 'border-gray-100' }} rounded-2xl focus:ring-4 focus:ring-primary-gold/10 outline-none transition-all placeholder:text-gray-400 font-medium text-sm" placeholder="08xxxxxx">
                @error('no_whatsapp') <p class="text-red-500 text-[10px] mt-1 font-bold italic">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="tgl_lahir" class="block text-xs font-bold text-gray-700 mb-2">Tanggal Lahir</label>
                <input type="date" id="tgl_lahir" name="tgl_lahir" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-gold/10 outline-none transition-all text-sm font-medium">
            </div>
            <div>
                <label for="jenis_kelamin" class="block text-xs font-bold text-gray-700 mb-2">Jenis Kelamin</label>
                <select id="jenis_kelamin" name="jenis_kelamin" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-gold/10 outline-none transition-all text-sm font-medium">
                    <option value="">Pilih Gender</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>
            <div>
                <label for="tahun_lulus" class="block text-xs font-bold text-gray-700 mb-2">Tahun Lulus</label>
                <select id="tahun_lulus" name="tahun_lulus" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-gold/10 outline-none transition-all text-sm font-medium">
                    <option value="">Pilih Tahun Kelulusan</option>
                    <option value="2022">2022</option>
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                    <option value="2027">2027 (Kelas 12 TA 2026/2027)</option>
                </select>
            </div>
        </div>

        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200/80 flex items-start gap-2.5 text-amber-900 text-xs">
            <span class="iconify text-base text-amber-600 shrink-0 mt-0.5" data-icon="solar:info-circle-bold"></span>
            <p class="leading-relaxed">
                <strong>Informasi Kelayakan:</strong> Siswa/i kelas 12 SMA/SMK/MA sederajat tahun ajaran 2026/2027, atau lulusan tahun ajaran 2022/2023/2024/2025/2026/2027.
            </p>
        </div>
    </div>

    <!-- Section 3: Info Sekolah -->
    <div class="space-y-4">
        <h3 class="text-xs font-bold text-primary-gold uppercase tracking-widest flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-primary-gold"></span>
            Informasi Sekolah
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="provinsi" class="block text-xs font-bold text-gray-700 mb-2">Provinsi Sekolah</label>
                <input type="text" id="provinsi" name="provinsi" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-gold/10 outline-none transition-all text-sm font-medium" placeholder="Contoh: Jawa Barat">
            </div>
            <div>
                <label for="kabupaten" class="block text-xs font-bold text-gray-700 mb-2">Kota/Kabupaten</label>
                <input type="text" id="kabupaten" name="kabupaten" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-gold/10 outline-none transition-all text-sm font-medium" placeholder="Contoh: Bandung">
            </div>
            <div class="col-span-full">
                <label for="nama_sekolah" class="block text-xs font-bold text-gray-700 mb-2">Nama Sekolah</label>
                <input type="text" id="nama_sekolah" name="nama_sekolah" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-gold/10 outline-none transition-all text-sm font-medium" placeholder="Nama SMA/SMK/MA">
            </div>
            <div class="col-span-full">
                <label for="no_guru_bk" class="block text-xs font-bold text-gray-700 mb-2">No. WhatsApp Guru BK </label>
                <input type="text" id="no_guru_bk" name="no_guru_bk" value="{{ old('no_guru_bk') }}" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-gold/10 outline-none transition-all text-sm font-medium" placeholder="Contoh: 0812xxxx">
                @error('no_guru_bk') <p class="text-red-500 text-[10px] mt-1 font-bold italic">{{ $message }}</p> @enderror
            </div>
            <div class="col-span-full">
                <label for="sumber_informasi" class="block text-xs font-bold text-gray-700 mb-2">Dari mana Anda mengetahui MNCU Future Leader Scholarship? <span class="text-red-500">*</span></label>
                <div class="relative group/select">
                    <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within/select:text-primary-gold transition-colors z-10 pointer-events-none">
                        <span class="iconify" data-icon="solar:info-circle-bold-duotone"></span>
                    </div>
                    <select id="sumber_informasi" name="sumber_informasi" required class="w-full px-5 py-4 pl-14 bg-gray-50 border {{ $errors->has('sumber_informasi') ? 'border-red-500' : 'border-gray-100' }} rounded-2xl focus:ring-4 focus:ring-primary-gold/10 outline-none transition-all text-sm font-medium appearance-none cursor-pointer shadow-sm">
                        <option value="">Pilih Sumber Informasi</option>
                        @php $sources = ['TV', 'Radio', 'Website', 'Instagram', 'Facebook', 'Tiktok', 'Teman', 'Keluarga', 'Guru/Kepala Sekolah', 'Presentasi Di Sekolah', 'Pameran Pendidikan', 'Media Cetak/Brosur', 'Lainnya']; @endphp
                        @foreach($sources as $src)
                            <option value="{{ $src }}" {{ old('sumber_informasi') == $src ? 'selected' : '' }}>{{ $src }}</option>
                        @endforeach
                    </select>
                    <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <span class="iconify" data-icon="solar:alt-arrow-down-bold-duotone"></span>
                    </div>
                </div>
                @error('sumber_informasi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="col-span-full">
                <label for="kode_referral" class="block text-xs font-bold text-gray-700 mb-2">Kode Referral (Opsional)</label>
                <div class="relative group/field">
                    <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within/field:text-primary-gold transition-colors z-10 pointer-events-none">
                        <span class="iconify" data-icon="solar:ticket-bold-duotone"></span>
                    </div>
                    <input type="text" id="kode_referral" name="kode_referral" value="{{ old('kode_referral') }}" class="w-full px-5 py-4 pl-14 bg-gray-50 border {{ $errors->has('kode_referral') ? 'border-red-500' : 'border-gray-100' }} rounded-2xl focus:ring-4 focus:ring-primary-gold/10 outline-none transition-all placeholder:text-gray-400 font-medium text-sm" placeholder="Masukkan kode referral jika ada">
                </div>
                @error('kode_referral') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            <!-- Cloudflare Turnstile -->
            <div class="col-span-full">
                <label for="captcha" class="block text-xs font-bold text-gray-700 mb-2">Keamanan</label>
                <div class="cf-turnstile" data-sitekey="{{ env('TURNSTILE_SITE_KEY') }}"></div>
                @error('cf-turnstile-response') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <div class="flex items-start">
        <input type="checkbox" id="agreement" required class="mt-1 w-5 h-5 border-gray-200 rounded text-primary-gold focus:ring-primary-gold/20 cursor-pointer">
        <label for="agreement" class="ml-3 text-xs font-semibold text-gray-600 leading-relaxed cursor-pointer select-none">
            Data yang saya masukkan sudah benar dan saya menyetujui <a href="#" class="text-primary-gold hover:underline">Syarat & Ketentuan</a> yang berlaku.
        </label>
    </div>

    <button type="submit" id="submitBtn" disabled
        class="w-full bg-gray-300 text-gray-500 cursor-not-allowed font-black py-4 rounded-2xl shadow-none transition-all">
        Daftar Sekarang
    </button>
</form>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

<p class="mt-10 text-center text-sm font-bold text-gray-500">
    Sudah punya akun? 
    <a href="/login" class="text-primary-gold hover:underline">Masuk di sini</a>
</p>

<script>
    const checkbox = document.getElementById('agreement');
    const submitBtn = document.getElementById('submitBtn');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');

    // 1. Password Match Validation
    function checkPasswordMatch() {
        // Hapus pesan error lama jika ada
        const existingError = document.getElementById('password-match-error');
        if (existingError) existingError.remove();

        if (confirmPassword.value && password.value !== confirmPassword.value) {
            const errorMsg = document.createElement('p');
            errorMsg.id = 'password-match-error';
            errorMsg.className = 'text-red-500 text-xs mt-1 font-bold';
            errorMsg.textContent = 'Password tidak sama!';
            confirmPassword.parentNode.appendChild(errorMsg);
            confirmPassword.classList.add('border-red-500');
            confirmPassword.classList.remove('border-gray-100');
        } else {
            confirmPassword.classList.remove('border-red-500');
            confirmPassword.classList.add('border-gray-100');
        }
    }

    password.addEventListener('input', checkPasswordMatch);
    confirmPassword.addEventListener('input', checkPasswordMatch);

    // 2. Number Only Validation
    const numberFields = ['no_whatsapp', 'nisn', 'telp_sekolah', 'no_guru_bk'];

    numberFields.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', function(e) {
                // Hapus karakter non-angka
                this.value = this.value.replace(/[^0-9]/g, '');
                
                // Limit NISN to 10 digits
                if (id === 'nisn' && this.value.length > 10) {
                    this.value = this.value.slice(0, 10);
                }
            });
        }
    });

    // 3. Agreement Checkbox Logic
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed', 'shadow-none');
            submitBtn.classList.add('bg-primary-gold', 'hover:bg-primary-gold-hover', 'text-dark-navy', 'cursor-pointer', 'shadow-xl', 'hover:scale-[1.02]');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed', 'shadow-none');
            submitBtn.classList.remove('bg-primary-gold', 'hover:bg-primary-gold-hover', 'text-dark-navy', 'cursor-pointer', 'shadow-xl', 'hover:scale-[1.02]');
        }
    });
</script>
@endsection

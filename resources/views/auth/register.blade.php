@extends('layouts.auth')

@section('title', 'Daftar Akun - MNCU Future Leader Scholarship')

@section('container_width', 'max-w-xl')

@section('content')
<div class="w-full">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            Pendaftaran Akun
        </h1>
        <p class="text-sm text-slate-500 mt-1.5 leading-relaxed">
            MNCU Future Leader Scholarship Ajaran 2027. Silakan lengkapi formulir di bawah ini.
        </p>
    </div>

    <!-- Draft Autosave Indicator (Simple & Minimal) -->
    <div class="flex items-center justify-between text-xs py-2.5 px-3.5 mb-6 rounded-xl bg-slate-50 border border-slate-200/80">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span id="draftStatusText" class="text-slate-600 font-medium">Draft tersimpan di browser</span>
        </div>
        <button type="button" id="btnClearDraft" class="text-slate-400 hover:text-rose-600 transition-colors text-[11px] font-semibold underline cursor-pointer">
            Reset Form
        </button>
    </div>

    <!-- Error Summary Block -->
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded-xl mb-6 text-xs" role="alert">
            <strong class="font-bold block mb-1">Ada data yang belum sesuai:</strong>
            <ul class="list-disc list-inside space-y-0.5">
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
        <!-- BAGIAN 1: AKUN                             -->
        <!-- ========================================== -->
        <div class="space-y-3.5">
            <h3 class="text-xs font-bold text-amber-600 uppercase tracking-widest flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                Detail Akun
            </h3>

            <!-- Nama Lengkap -->
            <div>
                <label for="nama" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                    class="w-full px-4 py-3 bg-slate-50 border {{ $errors->has('nama') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 text-sm font-medium"
                    placeholder="Sesuai Ijazah / KTP (tanpa singkatan)">
                @error('nama') <p class="text-rose-500 text-[11px] mt-1 font-bold">{{ $message }}</p> @enderror
            </div>

            <!-- NISN & Email (2 Grid) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="nisn" class="block text-xs font-bold text-slate-700">
                            NISN <span class="text-rose-500">*</span>
                        </label>
                        <span id="nisnCounterBadge" class="text-[10px] font-semibold text-slate-400">
                            <span id="nisnDigitCount">0</span>/10 digit
                        </span>
                    </div>
                    <input type="text" id="nisn" name="nisn" value="{{ old('nisn') }}" required maxlength="10" inputmode="numeric"
                        class="w-full px-4 py-3 bg-slate-50 border {{ $errors->has('nisn') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 text-sm font-medium"
                        placeholder="10 digit nomor siswa">
                    @error('nisn') <p class="text-rose-500 text-[11px] mt-1 font-bold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Alamat Email Aktif <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 bg-slate-50 border {{ $errors->has('email') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 text-sm font-medium"
                        placeholder="nama@email.com">
                    <p class="text-[11px] text-slate-400 mt-1">Kode OTP akan dikirim ke email ini.</p>
                    @error('email') <p class="text-rose-500 text-[11px] mt-1 font-bold">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Password & Konfirmasi Password (2 Grid) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required minlength="8"
                            class="w-full pl-4 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 text-sm font-medium"
                            placeholder="Min. 8 karakter">
                        <button type="button" class="btn-toggle-pw absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer" data-target="password" tabindex="-1">
                            <span class="iconify text-base" data-icon="solar:eye-bold-duotone"></span>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Konfirmasi Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                            class="w-full pl-4 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 text-sm font-medium"
                            placeholder="Ulangi password">
                        <button type="button" class="btn-toggle-pw absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer" data-target="password_confirmation" tabindex="-1">
                            <span class="iconify text-base" data-icon="solar:eye-bold-duotone"></span>
                        </button>
                    </div>
                    <p id="pwMatchStatus" class="text-[11px] text-slate-400 mt-1"></p>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- BAGIAN 2: INFORMASI PRIBADI                -->
        <!-- ========================================== -->
        <div class="space-y-3.5 pt-2 border-t border-slate-100">
            <h3 class="text-xs font-bold text-amber-600 uppercase tracking-widest flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                Informasi Pribadi
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- No WhatsApp -->
                <div>
                    <label for="no_whatsapp" class="block text-xs font-bold text-slate-700 mb-1.5">
                        No. WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="no_whatsapp" name="no_whatsapp" value="{{ old('no_whatsapp') }}" required inputmode="numeric"
                        class="w-full px-4 py-3 bg-slate-50 border {{ $errors->has('no_whatsapp') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 text-sm font-medium"
                        placeholder="Contoh: 08123456789">
                    @error('no_whatsapp') <p class="text-rose-500 text-[11px] mt-1 font-bold">{{ $message }}</p> @enderror
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="tgl_lahir" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Tanggal Lahir <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="tgl_lahir" name="tgl_lahir" value="{{ old('tgl_lahir') }}" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all text-sm font-medium text-slate-800">
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label for="jenis_kelamin" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Jenis Kelamin <span class="text-rose-500">*</span>
                    </label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all text-sm font-medium text-slate-800 cursor-pointer">
                        <option value="">Pilih Gender</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- Tahun Lulus -->
                <div>
                    <label for="tahun_lulus" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Tahun Lulus <span class="text-rose-500">*</span>
                    </label>
                    <select id="tahun_lulus" name="tahun_lulus" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all text-sm font-medium text-slate-800 cursor-pointer">
                        <option value="">Pilih Tahun Kelulusan</option>
                        <option value="2027" {{ old('tahun_lulus') == '2027' ? 'selected' : '' }}>2027 (Kelas 12 TA 2026/2027)</option>
                        <option value="2026" {{ old('tahun_lulus') == '2026' ? 'selected' : '' }}>2026</option>
                        <option value="2025" {{ old('tahun_lulus') == '2025' ? 'selected' : '' }}>2025</option>
                        <option value="2024" {{ old('tahun_lulus') == '2024' ? 'selected' : '' }}>2024</option>
                        <option value="2023" {{ old('tahun_lulus') == '2023' ? 'selected' : '' }}>2023</option>
                        <option value="2022" {{ old('tahun_lulus') == '2022' ? 'selected' : '' }}>2022</option>
                    </select>
                </div>
            </div>

            <p class="text-[11px] text-slate-500 bg-amber-50/70 border border-amber-200/60 p-2.5 rounded-xl leading-relaxed">
                <strong>Catatan Kelayakan:</strong> Terbuka untuk siswa/i aktif kelas 12 (lulus 2027) maupun lulusan tahun 2022 s.d. 2026.
            </p>
        </div>

        <!-- ========================================== -->
        <!-- BAGIAN 3: INFORMASI SEKOLAH                -->
        <!-- ========================================== -->
        <div class="space-y-3.5 pt-2 border-t border-slate-100">
            <h3 class="text-xs font-bold text-amber-600 uppercase tracking-widest flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                Informasi Sekolah
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Provinsi -->
                <div>
                    <label for="provinsi" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Provinsi Sekolah <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="provinsi" name="provinsi" value="{{ old('provinsi') }}" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 text-sm font-medium"
                        placeholder="Contoh: Jawa Barat">
                </div>

                <!-- Kabupaten / Kota -->
                <div>
                    <label for="kabupaten" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Kota / Kabupaten <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="kabupaten" name="kabupaten" value="{{ old('kabupaten') }}" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 text-sm font-medium"
                        placeholder="Contoh: Kota Bandung">
                </div>
            </div>

            <!-- Nama Sekolah -->
            <div>
                <label for="nama_sekolah" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Nama Sekolah <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama_sekolah" name="nama_sekolah" value="{{ old('nama_sekolah') }}" required
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 text-sm font-medium"
                    placeholder="Contoh: SMAN 1 Bandung / SMKN 2 Jakarta">
            </div>

            <!-- No Guru BK (WAJIB) -->
            <div>
                <label for="no_guru_bk" class="block text-xs font-bold text-slate-700 mb-1.5">
                    No. WhatsApp Guru BK / Wali Kelas <span class="text-rose-500">* (Wajib)</span>
                </label>
                <input type="text" id="no_guru_bk" name="no_guru_bk" value="{{ old('no_guru_bk') }}" required inputmode="numeric"
                    class="w-full px-4 py-3 bg-slate-50 border {{ $errors->has('no_guru_bk') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 text-sm font-medium"
                    placeholder="Contoh: 08123456789">
                <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk koordinasi konfirmasi rekomendasi sekolah.</p>
                @error('no_guru_bk') <p class="text-rose-500 text-[11px] mt-1 font-bold">{{ $message }}</p> @enderror
            </div>

            <!-- Sumber Informasi -->
            <div>
                <label for="sumber_informasi" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Dari mana mengetahui beasiswa ini? <span class="text-rose-500">*</span>
                </label>
                <select id="sumber_informasi" name="sumber_informasi" required
                    class="w-full px-4 py-3 bg-slate-50 border {{ $errors->has('sumber_informasi') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all text-sm font-medium text-slate-800 cursor-pointer">
                    <option value="">Pilih Sumber Informasi</option>
                    @php 
                        $sources = [
                            'Instagram', 'Tiktok', 'Facebook', 'Website MNC University', 
                            'TV (MNC Media)', 'Radio', 'Guru/Kepala Sekolah', 'Presentasi Di Sekolah', 
                            'Teman', 'Keluarga', 'Pameran Pendidikan', 'Media Cetak/Brosur', 'Lainnya'
                        ]; 
                    @endphp
                    @foreach($sources as $src)
                        <option value="{{ $src }}" {{ old('sumber_informasi') == $src ? 'selected' : '' }}>{{ $src }}</option>
                    @endforeach
                </select>
                @error('sumber_informasi') <p class="text-rose-500 text-[11px] mt-1 font-bold">{{ $message }}</p> @enderror
            </div>

            <!-- Kode Referral (Opsional) -->
            <div>
                <label for="kode_referral" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Kode Referral <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <input type="text" id="kode_referral" name="kode_referral" value="{{ old('kode_referral') }}"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder:text-slate-400 text-sm font-medium"
                    placeholder="Masukkan kode referral jika ada">
            </div>

            <!-- Turnstile Captcha -->
            <div class="pt-2">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Keamanan <span class="text-rose-500">*</span></label>
                <div class="cf-turnstile" data-sitekey="{{ env('TURNSTILE_SITE_KEY') }}"></div>
                @error('cf-turnstile-response') <p class="text-rose-500 text-[11px] mt-1 font-bold">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Persetujuan -->
        <div class="pt-2">
            <label class="flex items-start gap-3 cursor-pointer select-none">
                <input type="checkbox" id="agreement" required
                    class="mt-0.5 w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500/20 cursor-pointer">
                <span class="text-xs text-slate-600 leading-relaxed">
                    Data yang saya masukkan sudah benar dan saya menyetujui <a href="#" class="text-amber-600 font-semibold hover:underline">Syarat & Ketentuan</a> yang berlaku.
                </span>
            </label>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" id="submitBtn" disabled
                class="w-full py-3.5 px-6 rounded-xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-2 bg-slate-200 text-slate-400 cursor-not-allowed shadow-none">
                <span id="btnText">Daftar Sekarang</span>
            </button>
            <p class="text-center text-[11px] text-slate-400 mt-2">
                Setelah mendaftar, kode OTP 6-digit akan dikirimkan ke email Anda untuk verifikasi.
            </p>
        </div>
    </form>

    <!-- Footer Switch to Login -->
    <p class="mt-8 text-center text-xs text-slate-500">
        Sudah punya akun? 
        <a href="/login" class="text-amber-600 font-bold hover:underline ml-1">Masuk di sini</a>
    </p>

</div>

<!-- Cloudflare Turnstile API -->
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

<!-- Script Logic: Draft Auto-Save (LocalStorage), Password Toggles & Validations -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const DRAFT_KEY = 'mfls_reg_draft_2027_v2';
    
    // Elements
    const form = document.getElementById('registerForm');
    const agreement = document.getElementById('agreement');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const draftStatusText = document.getElementById('draftStatusText');
    const btnClearDraft = document.getElementById('btnClearDraft');

    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const pwMatchStatus = document.getElementById('pwMatchStatus');

    const nisnInput = document.getElementById('nisn');
    const nisnDigitCount = document.getElementById('nisnDigitCount');

    // Field list to save in draft (passwords intentionally excluded for security)
    const draftFieldIds = [
        'nama', 'nisn', 'email', 'no_whatsapp', 'tgl_lahir', 
        'jenis_kelamin', 'tahun_lulus', 'provinsi', 'kabupaten', 
        'nama_sekolah', 'no_guru_bk', 'sumber_informasi', 'kode_referral'
    ];

    // ==========================================
    // 1. DRAFT LOCALSTORAGE
    // ==========================================
    function saveDraft() {
        try {
            const draft = {};
            draftFieldIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) draft[id] = el.value;
            });
            draft['_saved_at'] = new Date().toISOString();
            localStorage.setItem(DRAFT_KEY, JSON.stringify(draft));

            if (draftStatusText) {
                const now = new Date();
                const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                draftStatusText.textContent = 'Draft tersimpan otomatis (' + timeStr + ')';
            }
        } catch (e) {
            console.warn('Gagal menyimpan draft:', e);
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

            draftFieldIds.forEach(id => {
                const el = document.getElementById(id);
                // Only restore if empty (preserve server old() values)
                if (el && (!el.value || el.value.trim() === '') && draft[id] !== undefined && draft[id] !== '') {
                    el.value = draft[id];
                }
            });

            updateNisnCounter();
            checkPasswordMatch();
            updateSubmitButtonState();
        } catch (e) {
            console.warn('Gagal memulihkan draft:', e);
        }
    }

    draftFieldIds.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', triggerAutoSave);
            el.addEventListener('change', triggerAutoSave);
        }
    });

    if (btnClearDraft) {
        btnClearDraft.addEventListener('click', function() {
            if (confirm('Kosongkan semua isian formulir dan hapus draft?')) {
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
    }

    form.addEventListener('submit', function() {
        localStorage.removeItem(DRAFT_KEY);
        submitBtn.disabled = true;
        btnText.textContent = 'Memproses Pendaftaran...';
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
    // 3. PASSWORD MATCH CHECK
    // ==========================================
    function checkPasswordMatch() {
        const pw = passwordInput ? passwordInput.value : '';
        const cpw = confirmPasswordInput ? confirmPasswordInput.value : '';

        if (!pwMatchStatus) return;

        if (!cpw) {
            pwMatchStatus.textContent = '';
            confirmPasswordInput.classList.remove('border-rose-400', 'border-emerald-400');
        } else if (pw === cpw) {
            pwMatchStatus.textContent = '✓ Password cocok';
            pwMatchStatus.className = 'text-[11px] font-semibold text-emerald-600 mt-1';
            confirmPasswordInput.classList.remove('border-rose-400');
            confirmPasswordInput.classList.add('border-emerald-400');
        } else {
            pwMatchStatus.textContent = '✗ Password tidak sama';
            pwMatchStatus.className = 'text-[11px] font-semibold text-rose-500 mt-1';
            confirmPasswordInput.classList.add('border-rose-400');
            confirmPasswordInput.classList.remove('border-emerald-400');
        }
    }

    if (passwordInput) passwordInput.addEventListener('input', checkPasswordMatch);
    if (confirmPasswordInput) confirmPasswordInput.addEventListener('input', checkPasswordMatch);

    // ==========================================
    // 4. NUMBER ONLY INPUT FILTERS & NISN COUNTER
    // ==========================================
    function updateNisnCounter() {
        if (!nisnInput || !nisnDigitCount) return;
        const len = nisnInput.value.length;
        nisnDigitCount.textContent = len;
        if (len === 10) {
            nisnDigitCount.parentElement.className = 'text-[10px] font-semibold text-emerald-600';
        } else {
            nisnDigitCount.parentElement.className = 'text-[10px] font-semibold text-slate-400';
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
            submitBtn.className = 'w-full py-3.5 px-6 rounded-xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 text-slate-950 shadow-md shadow-amber-500/20 hover:shadow-lg cursor-pointer';
        } else {
            submitBtn.disabled = true;
            submitBtn.className = 'w-full py-3.5 px-6 rounded-xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-2 bg-slate-200 text-slate-400 cursor-not-allowed shadow-none';
        }
    }

    if (agreement) {
        agreement.addEventListener('change', updateSubmitButtonState);
    }

    restoreDraft();
    updateSubmitButtonState();
});
</script>
@endsection

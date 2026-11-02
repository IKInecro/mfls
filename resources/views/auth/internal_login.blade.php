@extends('layouts.auth')

@section('title', 'Login Internal')

@section('content')
<div class="w-full">

    <!-- Mobile Logo (Only on small screens) -->
    <div class="lg:hidden mb-8">
        <a href="/" class="inline-flex items-center gap-2.5">
            <img src="{{ asset('icon/logoo.png') }}" alt="Logo MFLS" class="h-9 w-auto">
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Internal Admin</span>
        </a>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <span class="inline-block text-[11px] font-bold text-amber-700 bg-amber-50 border border-amber-200/80 px-2.5 py-1 rounded-md uppercase tracking-wider mb-3">
            Akses Terbatas
        </span>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            Portal Internal MFLS
        </h1>
        <p class="mt-2 text-sm text-slate-500 font-medium">
            Khusus Panitia Seleksi, Reviewer Berkas, dan Admin Akademik.
        </p>
    </div>

    <!-- Form -->
    <form action="{{ route('internal.login') }}" method="POST" class="space-y-5" id="internalLoginForm">
        @csrf

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                Email Institusi
            </label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   required 
                   autocomplete="username"
                   value="{{ old('email') }}"
                   class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 text-sm font-medium placeholder:text-slate-400 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none transition-all"
                   placeholder="admin@mncuniversity.ac.id">
            @error('email')
                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Field -->
        <div>
            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                Kode Akses
            </label>
            <div class="relative">
                <input type="password" 
                       id="password" 
                       name="password" 
                       required 
                       autocomplete="current-password"
                       class="w-full pl-4 pr-11 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 text-sm font-medium placeholder:text-slate-400 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none transition-all"
                       placeholder="••••••••">
                
                <!-- Eye Toggle -->
                <button type="button" 
                        id="toggleInternalPassword" 
                        aria-label="Tampilkan kode akses"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 transition-colors cursor-pointer">
                    <svg id="eyeIconInternal" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg id="eyeOffIconInternal" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Remember Me Checkbox -->
        <div class="flex items-center">
            <input type="checkbox" 
                   id="remember" 
                   name="remember" 
                   class="w-4 h-4 text-slate-900 border-slate-300 rounded focus:ring-slate-900 transition-colors">
            <label for="remember" class="ml-2.5 text-xs font-semibold text-slate-600 cursor-pointer select-none">
                Ingat sesi ini di perangkat
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                class="w-full py-3.5 px-4 bg-slate-900 hover:bg-black text-white text-sm font-bold rounded-xl transition-all duration-200 active:scale-[0.99] cursor-pointer">
            Masuk Dashboard
        </button>
    </form>

    <!-- Switch to Participant Link -->
    <div class="mt-8 pt-6 border-t border-slate-100 text-center">
        <p class="text-xs text-slate-500 font-medium">
            Bukan panitia internal? 
            <a href="{{ route('login') }}" class="font-bold text-slate-900 hover:underline transition-colors ml-1">
                Masuk sebagai peserta
            </a>
        </p>
    </div>

</div>

<!-- Password Toggle & Cookie Handler -->
<script>
function setCookie(name, value, days) {
    const expires = new Date();
    expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = `${name}=${encodeURIComponent(value)};expires=${expires.toUTCString()};path=/;SameSite=Lax`;
}

function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for(let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
    }
    return null;
}

document.addEventListener('DOMContentLoaded', function() {
    // Password toggle
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.getElementById('toggleInternalPassword');
    const eyeIcon = document.getElementById('eyeIconInternal');
    const eyeOffIcon = document.getElementById('eyeOffIconInternal');

    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', function() {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            eyeIcon.classList.toggle('hidden', isPassword);
            eyeOffIcon.classList.toggle('hidden', !isPassword);
        });
    }

    // Auto-fill saved internal email
    const emailInput = document.getElementById('email');
    const rememberCheckbox = document.getElementById('remember');
    const loginForm = document.getElementById('internalLoginForm');

    const savedEmail = getCookie('internal_saved_email');
    if (savedEmail && emailInput) {
        emailInput.value = savedEmail;
        if (rememberCheckbox) rememberCheckbox.checked = true;
    }

    if (loginForm) {
        loginForm.addEventListener('submit', function() {
            if (rememberCheckbox && rememberCheckbox.checked && emailInput.value) {
                setCookie('internal_saved_email', emailInput.value, 30);
            } else {
                setCookie('internal_saved_email', '', -1);
            }
        });
    }
});
</script>
@endsection

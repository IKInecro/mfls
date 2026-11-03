<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Quiz Rekomendasi Program Studi') - {{ config('app.name', 'MFLS') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('icon/loog.png') }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('icon/loog.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icon/loog.png') }}">

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <!-- Tailwind Browser 4 & Iconify & SweetAlert2 -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

    <style type="text/tailwindcss">
        @theme {
            --color-primary-gold: #D99B26;
            --color-primary-gold-hover: #C2891E;
            --color-navy-mnc: #0B1120;
            --color-dark-navy: #0B1120;
            --font-jakarta: "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        @layer base {
            html {
                font-family: var(--font-jakarta);
            }
            body {
                @apply font-jakarta text-slate-800 bg-slate-50/70 antialiased selection:bg-slate-900 selection:text-white;
            }
            ::-webkit-scrollbar { @apply w-2; }
            ::-webkit-scrollbar-track { @apply bg-transparent; }
            ::-webkit-scrollbar-thumb { @apply bg-slate-300 rounded-full hover:bg-amber-500 transition-colors; }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between">
    <!-- Quiz Navigation Header -->
    <nav class="h-18 sm:h-20 bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between gap-4">
            <!-- Brand -->
            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ asset('icon/logoo.png') }}" alt="Logo MFLS" class="h-9 sm:h-10 w-auto">
                <div class="hidden sm:block border-l border-slate-200 pl-3">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">MNC UNIVERSITY</span>
                    <span class="block text-xs font-extrabold text-slate-900 tracking-tight mt-0.5">Future Leader Scholarship</span>
                </div>
            </a>

            <!-- Center Badge (Hidden on mobile) -->
            <div class="hidden md:inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-xs font-bold text-slate-700">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span>Analisis Minat & Bakat Program Studi</span>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-3">
                <a href="/" class="text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors flex items-center gap-1.5 px-3 py-2 rounded-xl hover:bg-slate-100">
                    <span class="iconify text-base" data-icon="solar:home-2-bold-duotone"></span>
                    <span class="hidden sm:inline">Beranda</span>
                </a>
                @auth
                    <a href="{{ route('pendaftar.dashboard') }}" class="text-xs font-bold text-slate-900 hover:text-amber-600 transition-colors flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 border border-slate-200">
                        <span class="iconify text-base" data-icon="solar:user-circle-bold-duotone"></span>
                        <span>Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-bold text-slate-900 hover:text-amber-600 transition-colors flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 border border-slate-200">
                        <span class="iconify text-base" data-icon="solar:login-bold-duotone"></span>
                        <span>Masuk Portal</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-1 py-8 sm:py-12">
        @yield('content')
    </main>

    <!-- Clean Footer -->
    <footer class="py-8 border-t border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center space-y-2">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">MNC University • Future Leader Scholarship 2027</p>
            <div class="flex flex-wrap justify-center items-center gap-4 sm:gap-6 text-xs text-slate-500 font-medium pt-1">
                <a href="/" class="hover:text-amber-600 transition-colors">Beranda</a>
                <span>&bull;</span>
                <a href="{{ route('register') }}" class="hover:text-amber-600 transition-colors">Pendaftaran Akun</a>
                <span>&bull;</span>
                <a href="https://wa.me/6287867813183" target="_blank" class="hover:text-amber-600 transition-colors flex items-center gap-1">
                    <span class="iconify" data-icon="solar:phone-bold"></span>
                    <span>Admin WhatsApp (087867813183)</span>
                </a>
            </div>
        </div>
    </footer>
</body>
</html>

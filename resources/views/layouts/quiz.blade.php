<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Analisis Minat & Bakat - {{ config('app.name', 'MFLS') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('icon/loog.png') }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('icon/loog.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icon/loog.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    <!-- Tailwind CDN -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-primary-gold: #d4af37;
            --color-primary-gold-hover: #b8962e;
            --color-navy-mnc: #001f3f;
            --font-jakarta: "Plus Jakarta Sans", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }
        @layer base {
            body {
                @apply font-jakarta text-gray-800 bg-[#FAFBFF] antialiased overflow-x-hidden;
            }
            ::-webkit-scrollbar { @apply w-1.5; }
            ::-webkit-scrollbar-track { @apply bg-transparent; }
            ::-webkit-scrollbar-thumb { @apply bg-gray-200 rounded-full hover:bg-primary-gold transition-colors; }
        }
    </style>
</head>
<body class="font-jakarta antialiased bg-gray-50/50">
    <!-- Quiz Navigation -->
    <nav class="h-20 lg:h-24 bg-white/90 backdrop-blur-md border-b border-gray-100 flex items-center sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 w-full flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 group">
                <div class="p-2 bg-slate-900 rounded-xl group-hover:bg-primary-gold transition-colors">
                    <img src="{{ asset('icon/loog.png') }}" alt="Logo" class="h-8 w-auto">
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-black text-navy-mnc uppercase tracking-widest">MFLS Portal</span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Future Leader Scholarship</span>
                </div>
            </a>

            <div class="flex items-center gap-4">
                @auth
                <a href="{{ route('pendaftar.dashboard') }}" class="text-[10px] font-black text-slate-900 uppercase tracking-widest hover:text-primary-gold transition-colors flex items-center gap-2">
                    <span class="iconify" data-icon="solar:user-circle-bold-duotone"></span>
                    Dashboard
                </a>
                @else
                <a href="{{ route('login') }}" class="text-[10px] font-black text-slate-900 uppercase tracking-widest hover:text-primary-gold transition-colors flex items-center gap-2">
                    <span class="iconify" data-icon="solar:login-bold-duotone"></span>
                    Login
                </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Content Area -->
    <main class="py-10 lg:py-16">
        @yield('content')
    </main>

    <!-- Simple Footer -->
    <footer class="py-12 border-t border-gray-100 bg-white">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4">MNC University • Future Leader Scholarship 2026</p>
            <div class="flex justify-center gap-6">
                <a href="/" class="text-[9px] font-bold text-slate-500 hover:text-primary-gold transition-colors uppercase tracking-widest">Landing Page</a>
                <a href="https://wa.me/6287867813183" class="text-[9px] font-bold text-slate-500 hover:text-primary-gold transition-colors uppercase tracking-widest">Hubungi Kami (087867813183)</a>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>

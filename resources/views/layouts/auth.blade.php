<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - MNCU Future Leader Scholarship</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('icon/loog.png') }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('icon/loog.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icon/loog.png') }}">

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Tailwind Browser 4 & SweetAlert2 -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Iconify -->
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    <style type="text/tailwindcss">
        @theme {
            --color-gold: #D99B26;
            --color-gold-hover: #C2891E;
            --color-navy: #0B1120;
            --color-primary-gold: #D99B26;
            --color-primary-gold-hover: #C2891E;
            --color-dark-navy: #0B1120;
            --font-jakarta: "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        @layer base {
            html {
                font-family: var(--font-jakarta);
            }
            body {
                @apply bg-white text-slate-800 antialiased selection:bg-slate-900 selection:text-white;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-white">
    <div class="flex min-h-screen">
        
        <!-- Left Hero Visual (Desktop Only) - Simple, Atmospheric & Clean -->
        <div class="hidden lg:flex @yield('hero_width', 'lg:w-1/2') relative bg-[#090E1A] overflow-hidden flex-col justify-between p-12 lg:p-16 text-white">
            <!-- Background Image with Soft Vignette -->
            <img src="{{ asset('icon/login.webp') }}" 
                 alt="MNC University" 
                 class="absolute inset-0 w-full h-full object-cover opacity-25 mix-blend-luminosity">
            <div class="absolute inset-0 bg-gradient-to-t from-[#090E1A] via-[#090E1A]/80 to-transparent"></div>

            <!-- Top Brand -->
            <div class="relative z-10">
                <a href="/" class="inline-flex items-center gap-3">
                    <img src="{{ asset('icon/logoo.png') }}" alt="Logo MFLS" class="h-10 w-auto">
                    <div class="border-l border-white/20 pl-3">
                        <span class="block text-xs font-semibold text-slate-400 tracking-wider uppercase">MNC UNIVERSITY</span>
                        <span class="block text-sm font-bold text-white tracking-tight">Future Leader Scholarship</span>
                    </div>
                </a>
            </div>

            <!-- Central Statement -->
            <div class="relative z-10 max-w-md my-auto">
                <p class="text-3xl font-bold text-white leading-tight tracking-tight">
                    &ldquo;Pendidikan adalah senjata paling ampuh untuk mengubah dunia.&rdquo;
                </p>
                <p class="mt-4 text-sm font-semibold text-amber-400/90 tracking-wide">
                    Nelson Mandela
                </p>
            </div>

            <!-- Bottom Note -->
            <div class="relative z-10 text-xs text-slate-500">
                &copy; {{ date('Y') }} MNC University. Hak Cipta Dilindungi.
            </div>
        </div>

        <!-- Right Side Form Canvas - Minimal, Clean & Focus-driven -->
        <div class="w-full @yield('canvas_width', 'lg:w-1/2') flex flex-col justify-between p-6 sm:p-10 lg:p-14 overflow-y-auto">
            
            <!-- Top Utility Link -->
            <div class="flex items-center justify-between w-full @yield('container_width', 'max-w-md') mx-auto mb-8">
                <a href="/" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>

                @if(request()->routeIs('internal.login'))
                    <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 transition-colors">
                        Portal Peserta &rarr;
                    </a>
                @else
                    <a href="{{ route('internal.login') }}" class="text-xs font-semibold text-slate-400 hover:text-slate-700 transition-colors">
                        Akses Internal &rarr;
                    </a>
                @endif
            </div>

            <!-- Form Container -->
            <div class="w-full @yield('container_width', 'max-w-md') mx-auto my-auto">
                @yield('content')
            </div>

            <!-- Simple Footer -->
            <div class="w-full @yield('container_width', 'max-w-md') mx-auto mt-8 pt-4 text-center">
                <p class="text-[11px] text-slate-400">
                    MNC University Future Leader Scholarship 2026
                </p>
            </div>

        </div>

    </div>

    @include('partials.modal-alert')
</body>
</html>
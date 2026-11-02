<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MFLS') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('icon/logoo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('icon/logoo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icon/logoo.png') }}">

    <!-- Open Graph / Facebook / WhatsApp / Instagram -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ config('app.name', 'MFLS') }} - Program Beasiswa">
    <meta property="og:description"
        content="Membangun generasi emas bangsa melalui program beasiswa unggulan dan pembinaan karakter yang berkelanjutan.">
    <meta property="og:image" content="{{ asset('icon/logoo.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="{{ config('app.name', 'MFLS') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ config('app.name', 'MFLS') }} - Program Beasiswa">
    <meta name="twitter:description"
        content="Membangun generasi emas bangsa melalui program beasiswa unggulan dan pembinaan karakter yang berkelanjutan.">
    <meta name="twitter:image" content="{{ asset('icon/loog.jpeg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;500;600;700&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">

    <!-- Tailwind CDN -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-primary-blue: #023681;
            --color-primary-blue-hover: #012659;
            --color-primary-yellow: #f59e0b;
            --color-primary-yellow-hover: #d97706;
            --color-dark-navy: #023681;
            --font-jakarta: "Plus Jakarta Sans", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
        }
        @layer base {
            html, body {
                @apply overflow-x-hidden w-full scroll-smooth;
            }
            body {
                @apply font-jakarta text-slate-800 bg-white antialiased selection:bg-primary-blue selection:text-amber-400;
            }
            /* Custom sleek scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
            }
            ::-webkit-scrollbar-track {
                background: #f1f5f9;
            }
            ::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #023681;
            }
        }
    </style>
    @stack('meta')
    @stack('styles')

</head>

<body class="font-jakarta antialiased">
    <div class="min-h-screen flex flex-col bg-white w-full">
        <!-- Sticky Navbar with Enhanced Blur & Shadow Transition -->
        <nav id="main-navbar" class="fixed top-0 inset-x-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="/" class="flex items-center py-2">
                            <img src="{{ asset('icon/logoo.png') }}" alt="MNCU Future Leader Scholarship Logo"
                                class="h-12 sm:h-14 w-auto object-contain shrink-0 hover:opacity-90 transition-opacity">
                        </a>
                    </div>

                    <div class="hidden md:flex items-center space-x-7">
                        <a href="{{ url('/') }}#home"
                            class="text-sm font-semibold text-slate-600 hover:text-primary-blue transition-colors nav-link">Home</a>
                        <a href="{{ url('/') }}#about"
                            class="text-sm font-semibold text-slate-600 hover:text-primary-blue transition-colors nav-link">Tentang Kami</a>
                        <a href="{{ url('/') }}#program"
                            class="text-sm font-semibold text-slate-600 hover:text-primary-blue transition-colors nav-link">Program Studi</a>
                        <a href="{{ url('/') }}#timeline"
                            class="text-sm font-semibold text-slate-600 hover:text-primary-blue transition-colors nav-link">Timeline</a>
                        <a href="{{ url('/') }}#benefit"
                            class="text-sm font-semibold text-slate-600 hover:text-primary-blue transition-colors nav-link">Benefit</a>
                        <a href="{{ url('/') }}#requirements"
                            class="text-sm font-semibold text-slate-600 hover:text-primary-blue transition-colors nav-link">S&K</a>
                        <a href="{{ url('/') }}#berita"
                            class="text-sm font-semibold text-slate-600 hover:text-primary-blue transition-colors nav-link">Berita</a>
                        <a href="{{ url('/') }}#contact"
                            class="text-sm font-semibold text-slate-600 hover:text-primary-blue transition-colors nav-link">Kontak</a>
                        <a href="{{ route('quiz.show') }}"
                            class="text-xs font-bold uppercase tracking-wider text-primary-blue bg-blue-50 px-3.5 py-1.5 rounded-full border border-blue-200 hover:bg-primary-blue hover:text-white transition-all shadow-sm">Quiz Prodi</a>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="flex items-center gap-4">
                        <!-- Mobile menu button -->
                        <button id="mobile-menu-button"
                            class="md:hidden p-2 rounded-lg text-slate-600 hover:text-primary-blue hover:bg-slate-100 transition-colors" aria-label="Toggle navigation menu">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>

                        @auth
                        <a href="{{ route('pendaftar.dashboard') }}"
                            class="bg-primary-blue hover:bg-primary-blue-hover text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-md shadow-blue-900/15 transition-all hover:-translate-y-0.5 active:translate-y-0">
                            Dashboard
                        </a>
                        @else
                        <a href="/login"
                            class="bg-primary-blue hover:bg-primary-blue-hover text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-md shadow-blue-900/15 transition-all hover:-translate-y-0.5 active:translate-y-0">
                            Masuk
                        </a>
                        @endauth
                    </div>

                </div>

                <!-- Mobile Navigation Menu -->
                <div id="mobile-menu" class="hidden md:hidden border-t border-slate-100 py-4">
                    <div class="flex flex-col space-y-3">
                        <a href="{{ url('/') }}#home"
                            class="text-sm font-semibold text-slate-600 hover:text-primary-blue transition-colors nav-link mobile-nav-link">Home</a>
                        <a href="{{ url('/') }}#about"
                            class="text-sm font-semibold text-slate-600 hover:text-primary-blue transition-colors nav-link mobile-nav-link">Tentang Kami</a>
                        <a href="{{ url('/') }}#program"
                            class="text-sm font-semibold text-slate-600 hover:text-primary-blue transition-colors nav-link mobile-nav-link">Program Studi</a>
                        <a href="{{ url('/') }}#timeline"
                            class="text-sm font-semibold text-slate-600 hover:text-primary-blue transition-colors nav-link mobile-nav-link">Timeline</a>
                        <a href="{{ url('/') }}#benefit"
                            class="text-sm font-semibold text-slate-600 hover:text-primary-blue transition-colors nav-link mobile-nav-link">Benefit</a>
                        <a href="{{ url('/') }}#requirements"
                            class="text-sm font-semibold text-slate-600 hover:text-primary-blue transition-colors nav-link mobile-nav-link">S&K</a>
                        <a href="{{ url('/') }}#berita"
                            class="text-sm font-semibold text-slate-600 hover:text-primary-blue transition-colors nav-link mobile-nav-link">Berita</a>
                        <a href="{{ url('/') }}#contact"
                            class="text-sm font-semibold text-slate-600 hover:text-primary-blue transition-colors nav-link mobile-nav-link">Kontak</a>
                        <a href="{{ route('quiz.show') }}"
                            class="text-xs font-bold uppercase tracking-wider text-primary-blue bg-blue-50 px-3.5 py-2 rounded-xl text-center border border-blue-200">Quiz Rekomendasi Prodi</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-grow pt-20">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-dark-navy text-gray-400 py-20">
            <div class="max-w-7xl mx-auto px-6 sm:px-10 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12 text-center md:text-left">
                    <!-- Brand Info -->
                    <div class="col-span-1 md:col-span-1 flex flex-col items-center md:items-start">
                        <a href="/" class="flex items-center justify-center md:justify-start gap-2 mb-8">
                            <img src="{{ asset('icon/logoo.png') }}" alt="MNCU Future Leader Scholarship Logo"
                                style="height: 80px !important; width: auto !important; max-width: 100% !important; filter: brightness(0) invert(1);"
                                class="object-contain">
                        </a>
                        <p class="text-sm leading-relaxed mb-8 max-w-xs mx-auto md:mx-0">
                            Membangun generasi emas bangsa melalui program beasiswa unggulan dan pembinaan karakter yang
                            berkelanjutan.
                        </p>
                    </div>

                    <!-- Links 1 -->
                    <div>
                        <h4 class="text-white font-bold mb-6">Navigasi Program</h4>
                        <ul class="space-y-3 text-sm">
                            <li><a href="{{ url('/') }}#home" class="hover:text-primary-yellow transition-colors">Beranda</a></li>
                            <li><a href="{{ url('/') }}#about" class="hover:text-primary-yellow transition-colors">Tentang MFLS</a></li>
                            <li><a href="{{ url('/') }}#program" class="hover:text-primary-yellow transition-colors">Program Studi (S1)</a></li>
                            <li><a href="{{ url('/') }}#timeline" class="hover:text-primary-yellow transition-colors">Timeline Seleksi</a></li>
                            <li><a href="{{ url('/') }}#benefit" class="hover:text-primary-yellow transition-colors">Cakupan Beasiswa</a></li>
                            <li><a href="{{ url('/') }}#requirements" class="hover:text-primary-yellow transition-colors">Syarat & Berkas</a></li>
                        </ul>
                    </div>

                    <!-- Links 2 -->
                    <div>
                        <h4 class="text-white font-bold mb-6">Bantuan</h4>
                        <ul class="space-y-4 text-sm">
                            <li><a href="https://wa.me/6285880059189" target="_blank"
                                    class="hover:text-primary-yellow transition-colors">Pusat Bantuan</a></li>
                            <li><a href="https://aistudio.instagram.com/ai/4228437274072360/?utm_source=share"
                                    target="_blank" class="hover:text-primary-yellow transition-colors">FAQ</a></li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div>
                        <h4 class="text-white font-bold mb-6">Lokasi</h4>
                        <ul class="space-y-4 text-sm">
                            <li class="flex items-start md:justify-start justify-center gap-3">
                                <svg class="w-5 h-5 text-primary-yellow shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <a href="https://maps.app.goo.gl/Nhn2Wtm5zdieA6B36" class="footer-link"
                                    target="_blank">MNC University Kampus Menteng-Jakarta Pusat</a>
                            </li>
                            <li class="flex items-start md:justify-start justify-center gap-3">
                                <svg class="w-5 h-5 text-primary-yellow shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <a href="https://maps.app.goo.gl/4GBqapcMidQRVrsUA" class="footer-link"
                                    target="_blank">MNC University-Jakarta Barat</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div
                class="max-w-7xl mx-auto px-6 sm:px-10 lg:px-8 mt-16 pt-8 border-t border-gray-800 text-center flex flex-col items-center">
                <p class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} beasiswamncu. All rights reserved.
                    <span id="easter-egg-trigger" class="ml-1 cursor-pointer hover:text-gray-400 transition-colors"
                        title="Version Info">v1.0.0</span>
                </p>
            </div>
        </footer>

        <script>
            // Easter Egg Script
            document.addEventListener('DOMContentLoaded', function () {
                const trigger = document.getElementById('easter-egg-trigger');
                if (trigger) {
                    let clickCount = 0;
                    let clickTimer;

                    trigger.addEventListener('click', function () {
                        clickCount++;

                        clearTimeout(clickTimer);
                        clickTimer = setTimeout(() => {
                            clickCount = 0;
                        }, 1000); // reset if gap > 1s

                        if (clickCount >= 2) {
                            alert("✨ Website ini dibangun dan didesain dengan sepenuh hati oleh Muhammad Erlangga Putra Witanto ✨");
                            console.log("%c✨ Developed by Muhammad Erlangga Putra Witanto ✨", "color: #fff; background: #EAB308; padding: 10px; border-radius: 5px; font-size: 16px; font-weight: bold; font-family: sans-serif;");
                            clickCount = 0;
                        }
                    });
                }
            });
        </script>

        <!-- Modern Modal Alert & Global Notification System -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @include('partials.modal-alert')
    </div>

    <!-- Smooth Scrolling Navigation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Mobile menu toggle
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function () {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // Smooth scrolling for navigation links
            const navLinks = document.querySelectorAll('.nav-link');

            navLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');

                    // Only smooth scroll if it's a hash link for the current page
                    if (href.includes('#')) {
                        const targetId = href.split('#')[1];
                        const targetElement = document.getElementById(targetId);

                        // If we are on the page where the target exists
                        if (targetElement) {
                            e.preventDefault();
                            const offsetTop = targetElement.offsetTop - 80;
                            window.scrollTo({
                                top: offsetTop,
                                behavior: 'smooth'
                            });

                            // Close mobile menu
                            if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                                mobileMenu.classList.add('hidden');
                            }
                        }
                    }
                });
            });

            // Sticky Navbar Elevation & Shadow on scroll
            const mainNav = document.getElementById('main-navbar');
            window.addEventListener('scroll', function () {
                if (mainNav) {
                    if (window.scrollY > 15) {
                        mainNav.classList.add('shadow-md', 'bg-white/95');
                        mainNav.classList.remove('bg-white/90');
                    } else {
                        mainNav.classList.remove('shadow-md', 'bg-white/95');
                        mainNav.classList.add('bg-white/90');
                    }
                }
            });

            // Active navigation state on scroll
            window.addEventListener('scroll', function () {
                const sections = ['home', 'about', 'program', 'timeline', 'benefit', 'requirements', 'berita', 'contact'];
                const scrollPos = window.scrollY + 100;

                sections.forEach(sectionId => {
                    const section = document.getElementById(sectionId);
                    const navLink = document.querySelector(`a[href$="#${sectionId}"]`);

                    if (section && navLink) {
                        const sectionTop = section.offsetTop;
                        const sectionHeight = section.offsetHeight;

                        if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                            // Remove active class from all nav links
                            navLinks.forEach(link => {
                                link.classList.remove('text-primary-blue', 'font-bold');
                                link.classList.add('text-slate-600');
                            });

                            // Add active class to current nav link
                            navLink.classList.remove('text-slate-600');
                            navLink.classList.add('text-primary-blue', 'font-bold');
                        }
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
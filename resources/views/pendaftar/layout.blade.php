<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Portal Pendaftar - {{ config('app.name', 'MFLS Ajaran 2027') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('icon/loog.png') }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('icon/loog.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icon/loog.png') }}">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Portal Pendaftar - MFLS Ajaran 2027">
    <meta property="og:description" content="Portal Seleksi Resmi MNC University Future Leader Scholarship Ajaran 2027.">
    <meta property="og:image" content="{{ asset('icon/loog.png') }}">

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CDN Browser 4, Iconify, AlpineJS & SweetAlert2 -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style type="text/tailwindcss">
        @theme {
            --color-primary-blue: #0870B8;
            --color-primary-blue-hover: #065A96;
            --color-primary-gold: #F2B451;
            --color-primary-gold-hover: #D99B26;
            --color-dark-navy: #0B1120;
            --color-navy-card: #0F172A;
            --font-jakarta: "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        @layer base {
            html {
                font-family: var(--font-jakarta);
                scroll-behavior: smooth;
            }
            body {
                @apply font-jakarta text-slate-800 bg-slate-50 antialiased selection:bg-[#0870B8] selection:text-white;
            }
            /* Refined Minimalist Scrollbar */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            ::-webkit-scrollbar-track {
                background: transparent;
            }
            ::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 9999px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 flex flex-col justify-between">
    
    <!-- Top Progress Bar for Seamless SPA Navigation -->
    <div id="portal-progress" class="fixed top-0 left-0 h-1 bg-[#0870B8] z-[99999] transition-all duration-200 w-0 opacity-0 pointer-events-none"></div>

    <div class="flex min-h-screen w-full">
        @if(!isset($hideSidebar) || !$hideSidebar)
        <!-- Desktop Sticky Sidebar -->
        <div class="hidden lg:block shrink-0">
            @include('layouts.sidebar')
        </div>
        @endif

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 w-full">
            
            <!-- Top Navigation Appbar -->
            <header class="h-16 lg:h-20 bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-8 lg:px-10 flex items-center justify-between sticky top-0 z-30 transition-all">
                <div class="flex items-center gap-3 sm:gap-4">
                    @if(!isset($hideSidebar) || !$hideSidebar)
                    <!-- Mobile Hamburger Button -->
                    <button type="button" 
                            id="mobile-sidebar-toggle" 
                            aria-label="Buka Menu Navigasi"
                            class="lg:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors cursor-pointer border border-slate-200/80">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    @else
                    <a href="/" class="flex items-center gap-2.5">
                        <img src="{{ asset('icon/loog.png') }}" alt="Logo" class="h-8 w-auto">
                        <span class="text-xs font-bold text-slate-800 tracking-tight hidden sm:block">MNCU Future Leader Scholarship</span>
                    </a>
                    @endif

                    <!-- Greeting & Context -->
                    <div>
                        <h1 class="text-sm sm:text-base lg:text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span>Portal Pendaftar</span>
                            <span class="hidden sm:inline-block w-1 h-1 rounded-full bg-slate-300"></span>
                            <span class="hidden sm:inline-block text-xs font-semibold text-slate-500">MFLS Ajaran 2027</span>
                        </h1>
                        <p class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Status Akun: Calon Penerima Beasiswa</span>
                        </p>
                    </div>
                </div>

                <!-- Right Utility -->
                <div class="flex items-center gap-3">
                    <a href="/" class="hidden md:inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-[#0870B8] transition-colors py-1.5 px-3 rounded-lg hover:bg-blue-50/50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Beranda Situs</span>
                    </a>

                    @auth
                    <div class="flex items-center gap-2.5 pl-2 border-l border-slate-200">
                        @php
                            $user = Auth::user();
                            $foto = $user->peserta->berkas->foto ?? null;
                            $fotoUrl = $foto ? asset('storage/' . $foto) : "https://ui-avatars.com/api/?name=" . urlencode($user->nama) . "&background=0870B8&color=ffffff&bold=true";
                        @endphp
                        <a href="{{ route('pendaftar.biodata') }}" class="flex items-center gap-2 text-left group">
                            <div class="w-8 h-8 rounded-full overflow-hidden border border-slate-200 group-hover:border-[#0870B8] transition-colors">
                                <img src="{{ $fotoUrl }}" alt="{{ $user->nama }}" class="w-full h-full object-cover">
                            </div>
                            <span class="hidden sm:inline-block text-xs font-bold text-slate-800 group-hover:text-[#0870B8] transition-colors max-w-[120px] truncate">
                                {{ explode(' ', $user->nama)[0] }}
                            </span>
                        </a>
                    </div>
                    @endauth
                </div>
            </header>

            <!-- Main Page Content View with Dynamic SPA Container -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                <div id="portal-content" class="transition-opacity duration-150">
                    @yield('content')
                </div>
            </main>

            <!-- Minimalist Footer -->
            <footer class="px-6 py-4 border-t border-slate-200/70 text-center text-xs text-slate-400">
                <p>&copy; {{ date('Y') }} MNC University Future Leader Scholarship. Seluruh hak cipta dilindungi.</p>
            </footer>
        </div>
    </div>

    @if(!isset($hideSidebar) || !$hideSidebar)
    <!-- Mobile Sidebar Offcanvas with Smooth Animation -->
    <div id="mobile-sidebar-overlay" 
         class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-xs lg:hidden opacity-0 pointer-events-none transition-opacity duration-300 ease-out" 
         aria-hidden="true">
        <div id="mobile-sidebar-panel" 
             class="fixed inset-y-0 left-0 w-72 bg-[#0B1120] transform -translate-x-full transition-transform duration-300 ease-out shadow-2xl flex flex-col">
            
            <!-- Mobile Close Header -->
            <div class="p-4 flex items-center justify-between border-b border-slate-800">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">Menu Portal</span>
                <button type="button" 
                        id="mobile-sidebar-close" 
                        aria-label="Tutup Menu"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Injected Sidebar Content -->
            <div class="flex-1 overflow-y-auto">
                @include('layouts.sidebar', ['isMobile' => true])
            </div>
        </div>
    </div>
    @endif

    <!-- Native Lightweight Modal Alerts -->
    @include('partials.modal-alert')

    <!-- Interactive Sidebar Drawer & Seamless SPA Router Script -->
    <script>
    (function() {
        // --- 1. Mobile Drawer Logic ---
        const toggleBtn = document.getElementById('mobile-sidebar-toggle');
        const overlay = document.getElementById('mobile-sidebar-overlay');
        const panel = document.getElementById('mobile-sidebar-panel');
        const closeBtn = document.getElementById('mobile-sidebar-close');

        function openDrawer() {
            if (!overlay || !panel) return;
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100');
            panel.classList.remove('-translate-x-full');
            panel.classList.add('translate-x-0');
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            if (!overlay || !panel) return;
            overlay.classList.add('opacity-0', 'pointer-events-none');
            overlay.classList.remove('opacity-100');
            panel.classList.add('-translate-x-full');
            panel.classList.remove('translate-x-0');
            document.body.style.overflow = '';
        }

        window.closeMobileDrawer = closeDrawer;

        if (toggleBtn) toggleBtn.addEventListener('click', openDrawer);
        if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
        if (overlay) {
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) closeDrawer();
            });
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay && !overlay.classList.contains('pointer-events-none')) {
                closeDrawer();
            }
        });

        // --- 2. Seamless In-Place SPA Navigation (No Page Refresh) ---
        const progressBar = document.getElementById('portal-progress');
        const contentContainer = document.getElementById('portal-content');

        function updateSidebarActiveStates(targetPath) {
            const cleanPath = targetPath.replace(/\/$/, '');
            const allLinks = document.querySelectorAll('aside nav a');
            
            allLinks.forEach(link => {
                const linkHref = link.getAttribute('href');
                if (!linkHref) return;
                const linkPath = new URL(link.href, window.location.origin).pathname.replace(/\/$/, '');
                const isActive = (cleanPath === linkPath) || (linkPath !== '/pendaftar' && cleanPath.startsWith(linkPath));

                const icon = link.querySelector('.iconify');
                const bullet = link.querySelector('span.w-1\\.5.h-1\\.5');
                const badge = link.querySelector('span.rounded-md');

                if (isActive) {
                    link.className = 'flex items-center justify-between px-3.5 py-3 rounded-xl text-xs font-bold transition-all duration-200 group bg-blue-600/15 text-blue-400 border border-blue-500/30 shadow-xs';
                    if (icon) icon.className = 'iconify text-lg transition-transform group-hover:scale-110 text-blue-400';
                    if (badge) badge.className = 'px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-blue-500 text-white';
                } else {
                    link.className = 'flex items-center justify-between px-3.5 py-3 rounded-xl text-xs font-bold transition-all duration-200 group text-slate-400 hover:text-white hover:bg-slate-800/60';
                    if (icon) icon.className = 'iconify text-lg transition-transform group-hover:scale-110 text-slate-500 group-hover:text-slate-300';
                    if (badge) badge.className = 'px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-slate-800 text-slate-400 group-hover:text-white';
                }
            });
        }

        function reexecuteScripts(container) {
            if (!container) return;
            const scripts = container.querySelectorAll('script');
            scripts.forEach(oldScript => {
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(attr => {
                    newScript.setAttribute(attr.name, attr.value);
                });
                newScript.textContent = oldScript.textContent;
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });

            // Dispatch global event for component reinitialization
            window.dispatchEvent(new CustomEvent('portal:content-loaded'));

            // Re-scan Iconify icons
            if (window.Iconify && typeof window.Iconify.scan === 'function') {
                window.Iconify.scan(container);
            }
        }

        function navigateSpa(url, pushState = true) {
            if (!contentContainer) {
                window.location.href = url;
                return;
            }

            if (progressBar) {
                progressBar.style.transition = 'width 0.25s ease, opacity 0.2s ease';
                progressBar.style.width = '35%';
                progressBar.style.opacity = '1';
            }
            contentContainer.style.opacity = '0.35';

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.text();
            })
            .then(html => {
                if (progressBar) progressBar.style.width = '85%';
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // 1. Update document title
                if (doc.title) document.title = doc.title;

                // 2. Extract and swap main content
                const newContent = doc.getElementById('portal-content');
                if (newContent) {
                    contentContainer.innerHTML = newContent.innerHTML;
                } else {
                    window.location.href = url;
                    return;
                }

                // 3. Update History
                if (pushState) {
                    window.history.pushState({ path: url }, '', url);
                }

                // 4. Update Sidebar Menu Highlights
                const targetPath = new URL(url, window.location.origin).pathname;
                updateSidebarActiveStates(targetPath);

                // 5. Scroll smoothly to top
                window.scrollTo({ top: 0, behavior: 'instant' });

                // 6. Complete Progress Bar
                if (progressBar) {
                    progressBar.style.width = '100%';
                    setTimeout(() => {
                        progressBar.style.opacity = '0';
                        setTimeout(() => { progressBar.style.width = '0%'; }, 200);
                    }, 150);
                }
                contentContainer.style.opacity = '1';

                // 7. Re-execute scripts
                reexecuteScripts(contentContainer);

                // 8. Close Mobile Drawer
                closeDrawer();
            })
            .catch(err => {
                console.warn('SPA Navigation fallback to hard navigation:', err);
                window.location.href = url;
            });
        }

        // Global Link Interceptor for Portal Links
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;

            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || link.target === '_blank') return;

            // Check if link is an internal pendaftar route
            try {
                const targetUrl = new URL(link.href, window.location.origin);
                if (targetUrl.origin === window.location.origin && targetUrl.pathname.startsWith('/pendaftar')) {
                    // Bypass logout, download zip, or exam routes
                    if (targetUrl.pathname.includes('/logout') || 
                        targetUrl.pathname.includes('/download') || 
                        targetUrl.pathname.includes('/ujian/')) {
                        return;
                    }

                    e.preventDefault();
                    if (window.location.href === targetUrl.href) return;
                    navigateSpa(targetUrl.href, true);
                }
            } catch (err) {}
        });

        // Browser Back/Forward history buttons
        window.addEventListener('popstate', function() {
            navigateSpa(window.location.href, false);
        });

    })();
    </script>
</body>
</html>

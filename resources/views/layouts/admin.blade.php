<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Dashboard - {{ config('app.name', 'MFLS') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('icon/loog.png') }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('icon/loog.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icon/loog.png') }}">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Admin Dashboard - {{ config('app.name', 'MFLS') }}">
    <meta property="og:description"
        content="Membangun generasi emas bangsa melalui program beasiswa unggulan dan pembinaan karakter yang berkelanjutan.">
    <meta property="og:image" content="{{ asset('icon/loog.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="{{ config('app.name', 'MFLS') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="Admin Dashboard - {{ config('app.name', 'MFLS') }}">
    <meta name="twitter:description"
        content="Membangun generasi emas bangsa melalui program beasiswa unggulan dan pembinaan karakter yang berkelanjutan.">
    <meta name="twitter:image" content="{{ asset('icon/loog.png') }}">

    <!-- Fonts & Scripts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-primary-orange: #F97316;
            --color-primary-blue: #2563EB;
            --color-dark-navy: #111827;
            --font-jakarta: "Plus Jakarta Sans", sans-serif;
        }
    </style>

    <!-- Summernote CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    @stack('styles')
</head>

<body class="font-jakarta antialiased bg-slate-50 text-slate-800">

    <!-- ===== MOBILE TOPBAR (hidden on md+) ===== -->
    <header class="md:hidden fixed top-0 left-0 right-0 z-40 bg-white border-b border-slate-200 shadow-sm">
        <div class="flex items-center justify-between px-4 h-16">
            <a href="#" class="flex items-center gap-2.5">
                <img src="{{ asset('icon/loog.png') }}"
                    class="w-9 h-9 rounded-xl object-cover shadow-md shadow-orange-400/20" alt="Logo MFLS">
                <div>
                    <span class="block font-black text-base text-slate-800 leading-tight">MNCU <span
                            class="text-orange-500">Future Leader Scholarship</span></span>
                    <span
                        class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-tight">Admin
                        Panel</span>
                </div>
            </a>
            <button id="sidebarToggle"
                class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-orange-50 hover:text-orange-500 transition-all"
                aria-label="Toggle Sidebar">
                <span class="iconify text-xl" data-icon="solar:hamburger-menu-bold" id="hamburgerIcon"></span>
            </button>
        </div>
    </header>

    <!-- ===== OVERLAY BACKDROP (mobile only) ===== -->
    <div id="sidebarOverlay" class="fixed inset-0 z-30 bg-slate-900/60 backdrop-blur-sm hidden transition-opacity"
        onclick="closeSidebar()">
    </div>

    <div class="flex min-h-screen">

        <!-- ===== SIDEBAR ===== -->
        <aside id="adminSidebar" class="w-72 bg-white border-r border-slate-200 flex flex-col fixed h-full z-40
                   shadow-xl shadow-slate-200/50 -translate-x-full md:translate-x-0
                   transition-transform duration-300 ease-in-out">

            <!-- Logo - desktop only -->
            <div class="hidden md:block p-8 pb-4">
                <a href="#" class="flex items-center gap-3 group">
                    <img src="{{ asset('icon/loog.png') }}"
                        class="w-12 h-12 rounded-xl object-cover shadow-lg shadow-orange-500/20 group-hover:scale-110 transition-transform"
                        alt="Logo MFLS">
                    <div>
                        <span class="block font-black tracking-tight text-lg text-slate-800 leading-tight">MNCU <span
                                class="text-orange-500">Future Leader</span></span>
                        <span
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-tight">Scholarship
                            Admin</span>
                    </div>
                </a>
            </div>

            <!-- Spacer for mobile topbar -->
            <div class="md:hidden h-16 flex-shrink-0"></div>

            <!-- Navigation Links -->
            <nav class="flex-grow px-4 pb-4 space-y-1 overflow-y-auto mt-4">
                @if(auth()->user()->role !== 'dosen')
                    <div class="px-4 py-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">Main Menu</div>

                    <a href="{{ route('admin.dashboard') }}" onclick="closeSidebar()"
                        class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all
                            {{ request()->routeIs('admin.dashboard') ? 'bg-orange-50 text-orange-600 shadow-sm ring-1 ring-orange-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span
                            class="iconify text-xl {{ request()->routeIs('admin.dashboard') ? 'text-orange-600' : 'text-slate-400' }}"
                            data-icon="solar:chart-square-bold"></span>
                        Dashboard
                    </a>
                @endif

                @if(auth()->user()->role !== 'dosen')
                    <a href="{{ route('admin.pengaturan.pendaftaran.edit') }}" onclick="closeSidebar()"
                        class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all
                            {{ request()->routeIs('admin.pengaturan.pendaftaran.*') ? 'bg-orange-50 text-orange-600 shadow-sm ring-1 ring-orange-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span
                            class="iconify text-xl {{ request()->routeIs('admin.pengaturan.pendaftaran.*') ? 'text-orange-600' : 'text-slate-400' }}"
                            data-icon="solar:calendar-bold"></span>
                        Pengaturan Pendaftaran
                    </a>
                @endif

                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.email-templates.index') }}" onclick="closeSidebar()"
                        class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all
                            {{ request()->routeIs('admin.email-templates.*') ? 'bg-orange-50 text-orange-600 shadow-sm ring-1 ring-orange-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span
                            class="iconify text-xl {{ request()->routeIs('admin.email-templates.*') ? 'text-orange-600' : 'text-slate-400' }}"
                            data-icon="solar:letter-bold"></span>
                        Kelola Email
                    </a>
                @endif

                @if((auth()->user()->role === 'admin' || auth()->user()->role === 'panitia' || auth()->user()->role === 'akademik' || auth()->user()->role === 'palugada') && (in_array(auth()->user()->role, ['akademik', 'palugada']) || in_array(auth()->user()->email, ['dion@gmail.com', 'info@beasiswamncu.com'])))
                    <a href="{{ route('admin.beasiswa.index') }}" onclick="closeSidebar()"
                        class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all
                            {{ request()->routeIs('admin.beasiswa.*') ? 'bg-orange-50 text-orange-600 shadow-sm ring-1 ring-orange-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span
                            class="iconify text-xl {{ request()->routeIs('admin.beasiswa.*') ? 'text-orange-600' : 'text-slate-400' }}"
                            data-icon="solar:cup-star-bold"></span>
                        Seleksi Beasiswa
                    </a>
                @endif

                @if(auth()->user()->role === 'dosen')
                    <div class="px-4 py-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">Penilaian</div>
                @else
                    <div class="px-4 py-2 mt-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Master Data
                    </div>
                @endif

                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'panitia' || auth()->user()->role === 'akademik' || auth()->user()->role === 'palugada')
                    @if(auth()->user()->role === 'palugada')
                        <a href="{{ route('admin.palugada.index') }}" onclick="closeSidebar()"
                            class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all
                                    {{ request()->routeIs('admin.palugada.index') ? 'bg-orange-50 text-orange-600 shadow-sm ring-1 ring-orange-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <span
                                class="iconify text-xl {{ request()->routeIs('admin.palugada.index') ? 'text-orange-600' : 'text-slate-400' }}"
                                data-icon="solar:shield-check-bold"></span>
                            Verifikasi Final (Palugada)
                        </a>
                    @endif
                    <a href="{{ route('admin.pendaftar.index') }}" onclick="closeSidebar()"
                        class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all
                            {{ request()->routeIs('admin.pendaftar.index') ? 'bg-blue-50 text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span
                            class="iconify text-xl {{ request()->routeIs('admin.pendaftar.index') ? 'text-blue-600' : 'text-slate-400' }}"
                            data-icon="solar:users-group-rounded-bold"></span>
                        Seleksi Administrasi (Semua)
                    </a>
                @endif

                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'akademik' || auth()->user()->role === 'palugada')
                    <a href="{{ route('admin.hasil_ujian.index') }}" onclick="closeSidebar()"
                        class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all
                            {{ request()->routeIs('admin.hasil_ujian.*') ? 'bg-blue-50 text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span
                            class="iconify text-xl {{ request()->routeIs('admin.hasil_ujian.*') ? 'text-blue-600' : 'text-slate-400' }}"
                            data-icon="solar:document-text-bold"></span>
                        Hasil Ujian
                    </a>
                    <a href="{{ route('admin.seleksi_ujian.index') }}" onclick="closeSidebar()"
                        class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all
                            {{ request()->routeIs('admin.seleksi_ujian.*') ? 'bg-blue-50 text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span
                            class="iconify text-xl {{ request()->routeIs('admin.seleksi_ujian.*') ? 'text-blue-600' : 'text-slate-400' }}"
                            data-icon="solar:shield-star-bold"></span>
                        Seleksi Ujian
                    </a>
                @endif

                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'mentor' || auth()->user()->role === 'akademik' || auth()->user()->role === 'palugada')
                    <a href="{{ route('admin.penilaian.index') }}" onclick="closeSidebar()"
                        class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all
                            {{ request()->routeIs('admin.penilaian.index') ? 'bg-blue-50 text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span
                            class="iconify text-xl {{ request()->routeIs('admin.penilaian.index') ? 'text-blue-600' : 'text-slate-400' }}"
                            data-icon="solar:user-id-bold"></span>
                        Penilaian Mentor
                    </a>
                @endif

                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'mentor' || auth()->user()->role === 'akademik' || auth()->user()->role === 'palugada' || auth()->user()->role === 'dosen')
                    <a href="{{ route('admin.penilaian.akademik.index') }}" onclick="closeSidebar()"
                        class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all
                            {{ request()->routeIs('admin.penilaian.akademik.*') ? 'bg-orange-50 text-orange-600 shadow-sm ring-1 ring-orange-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span
                            class="iconify text-xl {{ request()->routeIs('admin.penilaian.akademik.*') ? 'text-orange-600' : 'text-slate-400' }}"
                            data-icon="solar:medal-ribbon-bold"></span>
                        Penilaian Akademik (Wawancara)
                    </a>
                @endif

                @if(auth()->check() && (auth()->user()->role === 'admin' || in_array(auth()->user()->email, ['dendi.pratama@mncu.ac.id', 'muhammad.rezki@mncu.ac.id', 'noval.adi@mncu.ac.id'])))
                    <a href="{{ route('admin.wawancara_bod.index') }}" onclick="closeSidebar()"
                        class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all
                            {{ request()->routeIs('admin.wawancara_bod.*') ? 'bg-red-50 text-red-600 shadow-sm ring-1 ring-red-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span
                            class="iconify text-xl {{ request()->routeIs('admin.wawancara_bod.*') ? 'text-red-600' : 'text-slate-400' }}"
                            data-icon="solar:user-hand-up-bold"></span>
                        Wawancara BoD
                    </a>
                @endif


                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'akademik')
                    <a href="{{ route('admin.panel_ujian.index') }}" onclick="closeSidebar()"
                        class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all
                            {{ request()->routeIs('admin.panel_ujian.*') ? 'bg-blue-50 text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span
                            class="iconify text-xl {{ request()->routeIs('admin.panel_ujian.*') ? 'text-blue-600' : 'text-slate-400' }}"
                            data-icon="solar:monitor-smartphone-bold"></span>
                        Panel Ujian
                    </a>
                @endif

                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'akademik' || auth()->user()->role === 'palugada')
                    <a href="{{ route('admin.soal.index') }}" onclick="closeSidebar()"
                        class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all
                            {{ request()->routeIs('admin.soal.index') ? 'bg-blue-50 text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        <span
                            class="iconify text-xl {{ request()->routeIs('admin.soal.index') ? 'text-blue-600' : 'text-slate-400' }}"
                            data-icon="solar:pen-new-square-bold"></span>
                        Bank Soal
                    </a>
                @endif

                @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'panitia' || auth()->user()->role === 'palugada' || in_array(auth()->user()->email, ['dion@gmail.com', 'adminis@mfls.com', 'info@beasiswamncu.com'])))
                    <div class="px-4 py-2 mt-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Admin
                        Control</div>
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'palugada' || auth()->user()->email === 'info@beasiswamncu.com')
                        <a href="{{ route('admin.user.index') }}" onclick="closeSidebar()"
                            class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all
                                    {{ request()->routeIs('admin.user.index') ? 'bg-slate-100 text-slate-800 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <span
                                class="iconify text-xl {{ request()->routeIs('admin.user.index') ? 'text-slate-800' : 'text-slate-400' }}"
                                data-icon="solar:settings-bold"></span>
                            Manajemen User
                        </a>
                        <a href="{{ route('admin.activity_log.index') }}" onclick="closeSidebar()"
                            class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all
                                    {{ request()->routeIs('admin.activity_log.*') ? 'bg-slate-100 text-slate-800 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <span
                                class="iconify text-xl {{ request()->routeIs('admin.activity_log.*') ? 'text-slate-800' : 'text-slate-400' }}"
                                data-icon="solar:history-bold"></span>
                            Log Aktivitas
                        </a>
                    @endif

                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'panitia' || auth()->user()->role === 'palugada' || in_array(auth()->user()->email, ['dion@gmail.com', 'dept.adminis@mfls.com', 'info@beasiswamncu.com']))
                        <a href="{{ route('admin.berita.index') }}" onclick="closeSidebar()"
                            class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all
                                    {{ request()->routeIs('admin.berita.*') ? 'bg-slate-100 text-slate-800 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <span
                                class="iconify text-xl {{ request()->routeIs('admin.berita.*') ? 'text-slate-800' : 'text-slate-400' }}"
                                data-icon="solar:document-bold"></span>
                            Manajemen Berita
                        </a>
                    @endif
                    <a href="{{ route('pengumuman') }}" target="_blank"
                        class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-bold transition-all text-orange-500 hover:bg-orange-50 hover:text-orange-600">
                        <span class="iconify text-xl text-orange-500" data-icon="solar:eye-bold"></span>
                        Live Pengumuman
                    </a>
                @endif
            </nav>

            <!-- Akun Login + Logout -->
            <div class="p-4 md:p-6 border-t border-slate-100 bg-slate-50/50 space-y-3">
                @auth
                    <div class="flex items-center gap-3 px-3 py-3 bg-white rounded-2xl border border-slate-100 shadow-sm">
                        <div
                            class="w-9 h-9 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600 font-black text-sm flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-black text-slate-800 truncate leading-tight">
                                {{ auth()->user()->nama ?? auth()->user()->name ?? '-' }}
                            </p>
                            <p class="text-[10px] text-slate-400 truncate leading-tight">
                                {{ auth()->user()->email ?? '-' }}
                            </p>
                        </div>
                        <span class="iconify text-slate-300 text-lg flex-shrink-0"
                            data-icon="solar:user-circle-bold"></span>
                    </div>
                @endauth

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center justify-between w-full px-5 py-3 text-red-500 bg-white hover:bg-red-50 border border-slate-200 hover:border-red-200 rounded-2xl font-bold transition-all shadow-sm group">
                        <span class="group-hover:translate-x-1 transition-transform">Keluar Sistem</span>
                        <span class="iconify text-xl" data-icon="solar:logout-2-bold"></span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ===== MAIN CONTENT ===== -->
        <main class="flex-1 md:ml-72 pt-20 md:pt-0 px-4 pb-8 sm:px-6 md:px-8 md:py-8 lg:px-12 lg:py-12 min-w-0">
            @yield('content')
        </main>
    </div>

    <script>
        // ===== Mobile Sidebar Toggle =====
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        const hamburgerIcon = document.getElementById('hamburgerIcon');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            hamburgerIcon.setAttribute('data-icon', 'solar:close-circle-bold');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            hamburgerIcon.setAttribute('data-icon', 'solar:hamburger-menu-bold');
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                if (sidebar.classList.contains('-translate-x-full')) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            });
        }

        // Auto-close sidebar saat resize ke desktop
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 768) {
                closeSidebar();
            }
        });

        // ===== SweetAlert Notifications =====
        @if(session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#F97316',
                borderRadius: '1.5rem'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'Gagal!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonColor: '#ef4444',
                borderRadius: '1.5rem'
            });
        @endif

        @if(session('loginError'))
            Swal.fire({
                title: 'Tidak Dapat Menyimpan!',
                text: "{{ session('loginError') }}",
                icon: 'error',
                confirmButtonColor: '#ef4444',
                borderRadius: '1.5rem'
            });
        @endif

        @if(session('warning'))
            Swal.fire({
                title: 'Perhatian!',
                text: "{{ session('warning') }}",
                icon: 'warning',
                confirmButtonColor: '#F97316',
                borderRadius: '1.5rem'
            });
        @endif
    </script>
    @stack('scripts')
</body>

</html>
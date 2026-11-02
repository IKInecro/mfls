<!-- Sidebar Component - Deep Slate & Royal Navy Theme -->
<aside class="{{ ($isMobile ?? false) ? 'w-full h-full' : 'w-64 xl:w-72 sticky top-0 h-screen' }} bg-[#0B1120] text-slate-300 flex flex-col justify-between border-r border-slate-800 select-none z-30">
    
    <!-- Top Brand Logo Area -->
    <div>
        <div class="p-6 border-b border-slate-800/80">
            <a href="/" class="flex items-center gap-3 group focus:outline-none">
                <div class="p-2 rounded-xl bg-white/5 border border-white/10 group-hover:border-blue-500/50 transition-colors">
                    <img src="{{ asset('icon/logoo.png') }}" alt="MFLS Logo" class="h-8 w-auto">
                </div>
                <div>
                    <span class="block text-[10px] font-bold text-amber-400 tracking-wider uppercase">BATCH 3</span>
                    <span class="block text-xs font-black text-white tracking-tight leading-tight group-hover:text-blue-300 transition-colors">
                        MNC University
                    </span>
                    <span class="block text-[10px] font-semibold text-slate-400 tracking-tight">
                        Future Leader Scholarship
                    </span>
                </div>
            </a>
        </div>

        <!-- Navigation Menu Links -->
        <nav class="p-4 space-y-1.5 overflow-y-auto">
            @php
                $menus = [
                    [
                        'route' => 'pendaftar.dashboard',
                        'label' => 'Dashboard',
                        'icon' => 'solar:home-2-bold-duotone',
                    ],
                    [
                        'route' => 'pendaftar.biodata',
                        'label' => 'Biodata Diri',
                        'icon' => 'solar:user-circle-bold-duotone',
                    ],
                    [
                        'route' => 'pendaftar.berkas',
                        'label' => 'Upload Berkas',
                        'icon' => 'solar:cloud-upload-bold-duotone',
                    ],
                    [
                        'route' => 'pendaftar.nilai',
                        'label' => 'Nilai Rapor',
                        'icon' => 'solar:chart-square-bold-duotone',
                    ],
                    [
                        'route' => 'pendaftar.twibbon',
                        'label' => 'Twibbon',
                        'icon' => 'solar:gallery-wide-bold-duotone',
                    ],
                    [
                        'route' => 'ujian.index',
                        'label' => 'Ujian Online (CBT)',
                        'icon' => 'solar:monitor-smartphone-bold-duotone',
                        'badge' => 'CBT'
                    ],
                ];
            @endphp

            @foreach($menus as $menu)
                @php 
                    $isActive = request()->routeIs($menu['route']) || request()->routeIs($menu['route'] . '.*');
                @endphp
                <a href="{{ route($menu['route']) }}"
                   class="flex items-center justify-between px-3.5 py-3 rounded-xl text-xs font-bold transition-all duration-200 group {{ $isActive ? 'bg-blue-600/15 text-blue-400 border border-blue-500/30 shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <div class="flex items-center gap-3">
                        <span class="iconify text-lg transition-transform group-hover:scale-110 {{ $isActive ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }}"
                              data-icon="{{ $menu['icon'] }}"></span>
                        <span>{{ $menu['label'] }}</span>
                    </div>

                    @if(isset($menu['badge']))
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider {{ $isActive ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400 group-hover:text-white' }}">
                            {{ $menu['badge'] }}
                        </span>
                    @elseif($isActive)
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    @endif
                </a>
            @endforeach
        </nav>
    </div>

    <!-- Bottom User Profile Card -->
    <div class="p-4 border-t border-slate-800/80">
        @auth
            @php
                $user = Auth::user();
                $foto = $user->peserta->berkas->foto ?? null;
                $fotoUrl = $foto ? asset('storage/' . $foto) : "https://ui-avatars.com/api/?name=" . urlencode($user->nama) . "&background=0870B8&color=ffffff&bold=true";
            @endphp
            <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-xl overflow-hidden shrink-0 border border-slate-700 bg-slate-800">
                        <img src="{{ $fotoUrl }}" alt="{{ $user->nama }}" class="w-full h-full object-cover">
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-white truncate">
                            {{ $user->nama }}
                        </p>
                        <p class="text-[10px] font-semibold text-slate-400 truncate">
                            NISN: {{ $user->peserta->nisn ?? '-' }}
                        </p>
                    </div>
                </div>

                <!-- Logout Button -->
                <form action="/logout" method="POST" class="m-0 shrink-0" onsubmit="event.preventDefault(); window.confirmLogout(event, this);">
                    @csrf
                    <button type="submit" 
                            title="Keluar dari Akun"
                            class="p-2 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        @else
            <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors">
                <span>Masuk Portal</span>
            </a>
        @endauth
    </div>

</aside>

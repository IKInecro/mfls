<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'CBT Mandiri - MNC Future Leaders Scholarship' }}</title>
    
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/3/3.1.1/iconify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        'navy-mnc': '#0B1120',
                        'royal-mnc': '#0870B8',
                        'gold-mnc': '#F2B451',
                    }
                }
            }
        }
    </script>

    <style type="text/tailwindcss">
        /* Proteksi Anti-Copy & Anti-Seleksi */
        .noselect, .noselect * {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            -webkit-touch-callout: none !important;
        }

        /* Watermark keamanan latar belakang */
        .cbt-watermark {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 1;
            opacity: 0.03;
            background-image: repeating-linear-gradient(
                -45deg,
                rgba(255, 255, 255, 0.4) 0,
                rgba(255, 255, 255, 0.4) 1px,
                transparent 1px,
                transparent 80px
            );
        }

        /* Efek denyut peringatan */
        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.05); opacity: 0.4; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
        .animate-pulse-ring {
            animation: pulse-ring 2s infinite ease-in-out;
        }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 font-sans antialiased overflow-x-hidden noselect select-none">
    {{-- Watermark Keamanan --}}
    <div class="cbt-watermark"></div>

    {{-- Konten Utama CBT --}}
    <div class="relative z-10 min-h-screen flex flex-col">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>

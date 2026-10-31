<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'MFLS') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('icon/loog.png') }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('icon/loog.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icon/loog.png') }}">

    <!-- Open Graph / Facebook / WhatsApp / Instagram -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title') - {{ config('app.name', 'MFLS') }}">
    <meta property="og:description"
        content="Membangun generasi emas bangsa melalui program beasiswa unggulan dan pembinaan karakter yang berkelanjutan.">
    <meta property="og:image" content="{{ asset('icon/loog.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="{{ config('app.name', 'MFLS') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('title') - {{ config('app.name', 'MFLS') }}">
    <meta name="twitter:description"
        content="Membangun generasi emas bangsa melalui program beasiswa unggulan dan pembinaan karakter yang berkelanjutan.">
    <meta name="twitter:image" content="{{ asset('icon/loog.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">

    <!-- Tailwind CDN -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-primary-gold: #F2B451;
            --color-primary-gold-hover: #e0a340;
            --color-dark-navy: #111827;
            --font-jakarta: "Plus Jakarta Sans", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }
        @layer base {
            body {
                @apply font-jakarta text-gray-800 bg-white antialiased;
            }
        }
    </style>

</head>

<body class="font-jakarta antialiased bg-white">
    <div class="flex min-h-screen">
        <!-- Left Side: Image & Quote -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-dark-navy">
            <img src="{{ asset('icon/login.webp') }}" class="absolute inset-0 w-full h-full object-cover opacity-60"
                alt="Login Background">
            <div class="absolute inset-0 bg-gradient-to-tr from-dark-navy/90 via-dark-navy/40 to-primary-gold/20"></div>

            <div class="relative z-10 flex flex-col justify-between p-16 w-full">
                <div>
                    <a href="/" class="flex items-center gap-3">
                        <img src="{{ asset('icon/logoo.png') }}" alt="Logo MFLS" class="h-12 w-auto">
                    </a>
                </div>

                <div class="max-w-md">
                    <h2 class="text-4xl font-extrabold text-white leading-tight mb-6">
                        "Pendidikan adalah senjata paling mematikan di dunia, karena dengan itu Anda dapat mengubah
                        dunia."
                    </h2>
                    <p class="text-primary-gold font-bold">— Nelson Mandela</p>
                </div>

                <div class="text-white/50 text-sm">
                    &copy; 2026 MNCU Future Leader Scholarship
                </div>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-20 bg-white">
            <div class="w-full max-w-md">
                @yield('content')
            </div>
        </div>
    </div>
</body>

</html>
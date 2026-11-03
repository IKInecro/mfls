<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Sedang Diperbarui - MFLS</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('icon/loog.png') }}">

    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;800&family=Syncopate:wght@700&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    <style>
        :root {
            /* Brand Colors from Logo */
            --mfls-orange: #FF5C00;
            --mfls-blue: #1E40AF;
            --mfls-dark: #0D0D0D;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--mfls-dark);
            color: #ffffff;
            margin: 0;
            overflow: hidden;
        }

        .syncopate {
            font-family: 'Syncopate', sans-serif;
        }

        /* Engineering Grid Background */
        .grid-bg {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: -1;
        }

        .giant-bg-text {
            position: absolute;
            font-size: 15vw;
            font-weight: 900;
            h color: transparent;
            -webkit-text-stroke: 1px rgba(255, 255, 255, 0.05);
            line-height: 0.8;
            pointer-events: none;
            user-select: none;
            white-space: nowrap;
        }

        .main-card {
            background-color: #151515;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.5rem;
            box-shadow: 20px 20px 0px rgba(0, 0, 0, 0.3);
        }

        .badge {
            background-color: var(--mfls-orange);
            color: #000;
            padding: 4px 12px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border-radius: 4px;
        }

        .marquee-footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: var(--mfls-orange);
            color: #000;
            padding: 12px 0;
            font-weight: 900;
            text-transform: uppercase;
            overflow: hidden;
            white-space: nowrap;
            z-index: 50;
        }

        .marquee-inner {
            display: inline-block;
            animation: marquee 30s linear infinite;
        }

        @keyframes marquee {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        .logo-box {
            background-color: #fff;
            border-radius: 1.25rem;
            padding: 1rem;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-6">

    <div class="grid-bg"></div>

    <!-- Giant Typography Layer -->
    <div class="giant-bg-text syncopate" style="top: 10%; left: -5%;">UNDER</div>
    <div class="giant-bg-text syncopate" style="bottom: 15%; right: -5%;">MAINTENANCE</div>

    <div class="main-card p-8 md:p-16 max-w-4xl w-full relative">
        <div class="flex flex-col md:flex-row items-center gap-10">
            <!-- Logo Section -->
            <div class="logo-box flex-shrink-0">
                <img src="{{ asset('icon/loog.png') }}" class="w-24 h-24 object-contain" alt="Logo">
            </div>

            <!-- Content Section -->
            <div class="text-center md:text-left space-y-4">
                <!-- <div class="inline-block badge">System Status</div> -->

                <h1 class="text-4xl md:text-6xl font-black tracking-tighter uppercase leading-none">
                    UNDER<br>
                    <span class="text-[--mfls-orange]"> Maintenance.</span>
                </h1>

                <p class="text-slate-400 text-lg md:text-xl font-bold leading-tight max-w-lg">
                    Sistem sedang dalam perbaikan karena banyaknya pendaftar yang mengakses. Mohon tunggu sebentar ya!
                </p>
            </div>
        </div>

        <div class="mt-12 flex flex-wrap justify-center md:justify-start gap-4">
            <a href="https://wa.me/6287867813183"
                class="px-6 py-3 bg-white text-black font-black text-xs uppercase tracking-widest rounded hover:bg-[--mfls-orange] transition-colors">
                Tanya Panitia (087867813183)
            </a>
            <a href="https://www.instagram.com/beasiswamncu?cplk=NDVzMnB0ZzNuanR0"
                class="px-6 py-3 border border-white/20 text-white font-black text-xs uppercase tracking-widest rounded hover:bg-white/10 transition-colors">
                Instagram
            </a>
        </div>
    </div>

    <!-- The Serious Marquee -->
    <div class="marquee-footer">
        <div class="marquee-inner">
            SISTEM SEDANG DALAM PERBAIKAN KARENA BANYAKNYA AKSES &nbsp; • &nbsp;
            SISTEM SEDANG DALAM PERBAIKAN KARENA BANYAKNYA AKSES &nbsp; • &nbsp;
            SISTEM SEDANG DALAM PERBAIKAN KARENA BANYAKNYA AKSES &nbsp; • &nbsp;
            SISTEM SEDANG DALAM PERBAIKAN KARENA BANYAKNYA AKSES &nbsp; • &nbsp;
            SISTEM SEDANG DALAM PERBAIKAN KARENA BANYAKNYA AKSES &nbsp; • &nbsp;
        </div>
    </div>

</body>

</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman MFLS Ajaran 2027 — Segera Hadir</title>
    <meta name="description" content="Pengumuman hasil seleksi MNCU Future Leader Scholarship Ajaran 2027 akan segera dibuka.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --orange: #F97316;
            --gold: #FBBF24;
            --dark: #0F172A;
            --darker: #080E1D;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--darker);
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated background */
        .bg-glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            animation: pulse 6s ease-in-out infinite alternate;
            pointer-events: none;
        }
        .bg-glow-1 {
            width: 500px; height: 500px;
            background: rgba(249, 115, 22, 0.15);
            top: -150px; left: -150px;
        }
        .bg-glow-2 {
            width: 600px; height: 600px;
            background: rgba(251, 191, 36, 0.08);
            bottom: -200px; right: -200px;
            animation-delay: -3s;
        }
        .bg-glow-3 {
            width: 300px; height: 300px;
            background: rgba(99, 102, 241, 0.1);
            top: 40%; left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -1.5s;
        }
        @keyframes pulse {
            from { opacity: 0.5; transform: scale(0.95); }
            to   { opacity: 1;   transform: scale(1.05); }
        }

        /* Floating particles */
        .particles { position: fixed; inset: 0; pointer-events: none; overflow: hidden; }
        .particle {
            position: absolute;
            width: 4px; height: 4px;
            background: var(--orange);
            border-radius: 50%;
            opacity: 0;
            animation: float-up linear infinite;
        }
        @keyframes float-up {
            0%   { transform: translateY(100vh) scale(0); opacity: 0; }
            10%  { opacity: 0.6; }
            90%  { opacity: 0.3; }
            100% { transform: translateY(-10vh) scale(1); opacity: 0; }
        }

        /* Main content */
        .container {
            text-align: center;
            position: relative;
            z-index: 10;
            padding: 2rem;
            max-width: 700px;
            width: 100%;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(249, 115, 22, 0.15);
            border: 1px solid rgba(249, 115, 22, 0.3);
            border-radius: 9999px;
            padding: 6px 16px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--orange);
            margin-bottom: 2rem;
        }
        .badge-dot {
            width: 8px; height: 8px;
            background: var(--orange);
            border-radius: 50%;
            animation: blink 1.2s ease-in-out infinite;
        }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.2; } }

        .logo {
            width: 64px; height: 64px;
            margin: 0 auto 1.5rem;
            object-fit: contain;
        }

        h1 {
            font-size: clamp(2rem, 6vw, 3.5rem);
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #fff 0%, #FCD34D 50%, var(--orange) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            font-size: 1rem;
            color: rgba(255,255,255,0.5);
            font-weight: 500;
            margin-bottom: 3rem;
            line-height: 1.6;
        }

        /* Countdown */
        .countdown-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
            margin-bottom: 1rem;
        }

        .countdown {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }

        .countdown-block {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            padding: 1.25rem 1.5rem;
            min-width: 88px;
            backdrop-filter: blur(20px);
            transition: border-color 0.3s;
        }
        .countdown-block:hover { border-color: rgba(249,115,22,0.4); }

        .countdown-number {
            font-size: 2.5rem;
            font-weight: 900;
            color: white;
            line-height: 1;
            font-variant-numeric: tabular-nums;
            transition: color 0.3s;
        }
        .countdown-number.tick { color: var(--gold); }

        .countdown-unit {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
            margin-top: 6px;
        }

        .countdown-sep {
            font-size: 2rem;
            font-weight: 900;
            color: rgba(255,255,255,0.2);
            animation: sep-blink 1s ease-in-out infinite;
            margin-bottom: 1rem;
        }
        @keyframes sep-blink { 0%, 100% { opacity: 0.2; } 50% { opacity: 0.6; } }

        /* Open time info */
        .open-time-card {
            background: linear-gradient(135deg, rgba(249,115,22,0.1), rgba(251,191,36,0.08));
            border: 1px solid rgba(249,115,22,0.25);
            border-radius: 16px;
            padding: 1rem 1.5rem;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 600;
            color: rgba(255,255,255,0.8);
            margin-bottom: 1.5rem;
        }
        .clock-icon { font-size: 1.5rem; }

        .footnote {
            font-size: 12px;
            color: rgba(255,255,255,0.25);
            font-style: italic;
        }

        /* Opened message */
        #opened-msg {
            display: none;
            animation: fadeIn 0.8s ease forwards;
        }
        @keyframes fadeIn { from { opacity:0; transform: translateY(10px); } to { opacity:1; transform:none; } }
        .opened-btn {
            display: inline-block;
            background: linear-gradient(135deg, var(--orange), var(--gold));
            color: #0F172A;
            font-weight: 900;
            font-size: 14px;
            padding: 1rem 2.5rem;
            border-radius: 14px;
            text-decoration: none;
            letter-spacing: 0.05em;
            box-shadow: 0 8px 30px rgba(249,115,22,0.4);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .opened-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(249,115,22,0.5); }
    </style>
</head>
<body>

<div class="bg-glow bg-glow-1"></div>
<div class="bg-glow bg-glow-2"></div>
<div class="bg-glow bg-glow-3"></div>

<div class="particles" id="particles"></div>

<div class="container">
    <div class="badge">
        <span class="badge-dot"></span>
        MNCU Future Leader Scholarship Ajaran 2027
    </div>

    <img src="{{ asset('icon/loog.png') }}" class="logo" alt="Logo MFLS" onerror="this.style.display='none'">

    <h1>Pengumuman Seleksi<br>Segera Dibuka</h1>
    <p class="subtitle">
        Hasil seleksi beasiswa MNCU Future Leader Scholarship Ajaran 2027<br>
        akan diumumkan pada hari ini pukul <strong style="color:var(--gold)">12.00 WIB</strong>.
    </p>

    <div class="countdown-label">Pengumuman dibuka dalam</div>

    <div class="countdown" id="countdown">
        <div class="countdown-block">
            <div class="countdown-number" id="cd-jam">00</div>
            <div class="countdown-unit">Jam</div>
        </div>
        <div class="countdown-sep">:</div>
        <div class="countdown-block">
            <div class="countdown-number" id="cd-menit">00</div>
            <div class="countdown-unit">Menit</div>
        </div>
        <div class="countdown-sep">:</div>
        <div class="countdown-block">
            <div class="countdown-number" id="cd-detik">00</div>
            <div class="countdown-unit">Detik</div>
        </div>
    </div>

    <div class="open-time-card">
        <span class="clock-icon">🕛</span>
        Pengumuman dibuka: <strong style="color:var(--orange)">Hari ini, 12:00 WIB</strong>
    </div>

    <div id="opened-msg">
        <p style="color:var(--gold);font-weight:800;font-size:1.1rem;margin-bottom:1.5rem;">
            🎉 Pengumuman sudah dibuka! Silakan cek hasil seleksi Anda.
        </p>
        <a href="{{ route('pengumuman') }}" class="opened-btn">Lihat Pengumuman →</a>
    </div>

    <p class="footnote" style="margin-top:1.5rem;">Refresh halaman jika sudah waktunya namun belum terbuka otomatis.</p>
</div>

<script>
    // Open time from server (ISO8601)
    const openTime = new Date('{{ $openTime }}');

    function pad(n) { return String(n).padStart(2, '0'); }

    function tick() {
        const now = new Date();
        const diff = openTime - now;

        if (diff <= 0) {
            // Waktunya tiba — langsung redirect
            window.location.reload();
            return;
        }

        const totalSeconds = Math.floor(diff / 1000);
        const h = Math.floor(totalSeconds / 3600);
        const m = Math.floor((totalSeconds % 3600) / 60);
        const s = totalSeconds % 60;

        const elJam   = document.getElementById('cd-jam');
        const elMenit = document.getElementById('cd-menit');
        const elDetik = document.getElementById('cd-detik');

        // Flash effect on detik
        elDetik.classList.add('tick');
        setTimeout(() => elDetik.classList.remove('tick'), 200);

        elJam.textContent   = pad(h);
        elMenit.textContent = pad(m);
        elDetik.textContent = pad(s);
    }

    tick();
    setInterval(tick, 1000);

    // Floating particles
    const container = document.getElementById('particles');
    for (let i = 0; i < 25; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        p.style.left       = Math.random() * 100 + 'vw';
        p.style.width      = (Math.random() * 4 + 2) + 'px';
        p.style.height     = p.style.width;
        p.style.animationDuration  = (Math.random() * 12 + 8) + 's';
        p.style.animationDelay    = (Math.random() * -15) + 's';
        p.style.background = Math.random() > 0.5 ? '#F97316' : '#FBBF24';
        container.appendChild(p);
    }
</script>
</body>
</html>

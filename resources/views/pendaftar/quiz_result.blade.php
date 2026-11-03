@extends('layouts.quiz')

@section('title', 'Hasil Rekomendasi Program Studi - MFLS')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 space-y-8 pb-32">

    @php
        $prodiDetails = [
            'Manajemen' => [
                'fullName' => 'S1 Manajemen',
                'faculty' => 'Fakultas Ekonomi dan Bisnis',
                'icon' => 'solar:case-round-bold-duotone',
                'focus' => 'Keterampilan manajerial, kepemimpinan modern, pemasaran strategis, keuangan korporasi, dan kewirausahaan.',
                'careers' => ['Business Strategist', 'Corporate Manager', 'Financial Analyst', 'Marketing Director'],
            ],
            'Akuntansi' => [
                'fullName' => 'S1 Akuntansi',
                'faculty' => 'Fakultas Ekonomi dan Bisnis',
                'icon' => 'solar:calculator-bold-duotone',
                'focus' => 'Kurikulum berbasis OBE berfokus pada akuntansi media, environmental & sustainability accounting terhubung MNC Group.',
                'careers' => ['Auditor Publik', 'Tax Consultant', 'Sustainability Accountant', 'Financial Controller'],
            ],
            'Sains Komunikasi' => [
                'fullName' => 'S1 Sains Komunikasi',
                'faculty' => 'Fakultas Ilmu Komunikasi dan Bahasa',
                'icon' => 'solar:chat-round-dots-bold-duotone',
                'focus' => 'Penguasaan media digital, penyiaran (broadcasting), jurnalisme multimedia, dan industri kreatif global 2037.',
                'careers' => ['Broadcast Journalist', 'Public Relations Specialist', 'Media Content Creator', 'Creative Producer'],
            ],
            'Pendidikan Matematika' => [
                'fullName' => 'S1 Pendidikan Matematika',
                'faculty' => 'Fakultas Keguruan dan Ilmu Pendidikan',
                'icon' => 'solar:square-academic-cap-bold-duotone',
                'focus' => 'Pendidik matematika inovatif, pengembang pelatihan digital, data analis, dan aktuaris dengan akses industri MNC.',
                'careers' => ['Pendidik Profesional', 'Data Analyst', 'Aktuaris', 'Instructional Designer'],
            ],
            'Pendidikan Bahasa Inggris' => [
                'fullName' => 'S1 Pendidikan Bahasa Inggris',
                'faculty' => 'Fakultas Keguruan dan Ilmu Pendidikan',
                'icon' => 'solar:global-bold-duotone',
                'focus' => 'Kurikulum OBE berfokus pada edutechnopreneurship dan bahasa Inggris industri media hiburan & pemberitaan.',
                'careers' => ['English Educator', 'Professional Translator', 'News Subtitler', 'International Coordinator'],
            ],
            'Sistem Informasi' => [
                'fullName' => 'S1 Sistem Informasi',
                'faculty' => 'Fakultas Sains dan Teknologi',
                'icon' => 'solar:database-bold-duotone',
                'focus' => 'Analisis proses bisnis, perancangan arsitektur sistem, tata kelola IT, manajemen proyek, dan keamanan informasi.',
                'careers' => ['IT Business Analyst', 'Systems Architect', 'Database Administrator', 'IT Project Manager'],
            ],
            'DKV' => [
                'fullName' => 'S1 Desain Komunikasi Visual',
                'faculty' => 'Fakultas Industri Kreatif',
                'icon' => 'solar:palette-bold-duotone',
                'focus' => 'Desain berkelanjutan, visual storytelling, branding media, UI/UX design, animasi, dan motion graphics terintegrasi MNC.',
                'careers' => ['Brand Strategist', 'UI/UX Designer', 'Motion Animator', 'Creative Art Director'],
            ],
            'Ilmu Komputer' => [
                'fullName' => 'S1 Ilmu Komputer',
                'faculty' => 'Fakultas Sains dan Teknologi',
                'icon' => 'solar:code-square-bold-duotone',
                'focus' => 'Analisis algoritma mendalam, rekayasa perangkat lunak, kecerdasan buatan (AI), data science, dan komputasi awan.',
                'careers' => ['Software Engineer', 'AI & Machine Learning Engineer', 'Cloud Architect', 'Fullstack Developer'],
            ],
        ];

        $detail1 = $prodiDetails[$top1] ?? [
            'fullName' => 'S1 ' . $top1,
            'faculty' => 'MNC University',
            'icon' => 'solar:stars-bold-duotone',
            'focus' => 'Program studi unggulan terakreditasi MNC University.',
            'careers' => ['Professional Specialist', 'Industry Leader'],
        ];

        $detail2 = $prodiDetails[$top2] ?? [
            'fullName' => 'S1 ' . $top2,
            'faculty' => 'MNC University',
            'icon' => 'solar:star-bold-duotone',
            'focus' => 'Alternatif program studi dengan kompatibilitas profil tinggi.',
            'careers' => ['Professional Specialist', 'Analyst'],
        ];
    @endphp

    <!-- Reveal Header Banner -->
    <div class="relative overflow-hidden bg-[#0B1120] rounded-3xl p-6 sm:p-10 md:p-12 text-white border border-slate-800 shadow-xl">
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6 sm:gap-8">
            <!-- Arion Mascot celebrating -->
            <div class="shrink-0 relative group">
                <div class="absolute -inset-4 bg-amber-500/25 rounded-full blur-2xl animate-pulse"></div>
                <img src="{{ asset('icon/scholarr.webp') }}" alt="Arion Mascot" 
                     class="w-32 h-32 sm:w-40 sm:h-40 object-contain relative z-10 drop-shadow-2xl transform hover:scale-105 transition-transform duration-500">
            </div>

            <!-- Content -->
            <div class="text-center md:text-left flex-1 space-y-3">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-400/10 border border-amber-400/20 text-xs font-bold text-amber-300">
                    <span class="iconify text-amber-400 text-sm" data-icon="solar:verified-check-bold"></span>
                    <span>ANALISIS MINAT SELESAI</span>
                </div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-white tracking-tight leading-tight">
                    Rekomendasi <span class="text-amber-400">Program Studi Anda</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 font-medium leading-relaxed max-w-2xl">
                    Selamat! Berdasarkan hasil evaluasi 30 indikator minat dan pola berpikir, berikut adalah Program Studi di MNC University yang paling selaras dengan potensi masa depan Anda.
                </p>
                <div class="pt-2 flex flex-wrap items-center justify-center md:justify-start gap-3 text-xs font-semibold">
                    <span class="px-3 py-1 rounded-xl bg-white/10 border border-white/10 text-slate-200">
                        Tanggal: {{ now()->format('d M Y') }}
                    </span>
                    <span class="px-3 py-1 rounded-xl bg-amber-400/10 border border-amber-400/20 text-amber-300">
                        Basis Algoritma: Multi-Criteria Interest Fit
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Match Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        
        <!-- Primary Match: The Golden Card (Col 7) -->
        <div class="lg:col-span-7 relative group flex">
            <div class="w-full bg-[#0B1120] rounded-3xl p-6 sm:p-8 md:p-10 border-2 border-amber-400/50 shadow-2xl relative overflow-hidden flex flex-col justify-between text-white">
                <div class="absolute -top-12 -right-12 w-48 h-48 bg-amber-500/15 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 space-y-6">
                    <!-- Top Match Badge & Percentage -->
                    <div class="flex items-center justify-between gap-4 pb-4 border-b border-white/10">
                        <div class="flex items-center gap-2.5">
                            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 flex items-center justify-center shadow-md shadow-amber-400/30">
                                <span class="iconify text-xl" data-icon="solar:crown-bold"></span>
                            </span>
                            <div>
                                <span class="block text-[10px] font-bold text-amber-400 uppercase tracking-widest leading-none">Rekomendasi Utama #1</span>
                                <span class="block text-xs font-semibold text-slate-400 mt-0.5">{{ $detail1['faculty'] }}</span>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-3xl sm:text-4xl font-black text-amber-400 tracking-tight leading-none">
                                {{ $percent1 }}%
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kecocokan</span>
                        </div>
                    </div>

                    <!-- Program Title & Description -->
                    <div class="space-y-3">
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-xs font-semibold text-slate-300">
                            <span class="iconify text-amber-400 text-base" data-icon="{{ $detail1['icon'] }}"></span>
                            <span>{{ $detail1['fullName'] }}</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                            {{ $detail1['fullName'] }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                            {{ $detail1['focus'] }}
                        </p>
                    </div>

                    <!-- Career Prospects Tags -->
                    <div class="space-y-2 pt-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Peluang Karir Lulusan:</span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($detail1['careers'] as $c)
                            <span class="px-2.5 py-1 rounded-lg bg-white/10 text-white text-[11px] font-semibold">
                                {{ $c }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Footer Card Note -->
                <div class="relative z-10 pt-6 mt-6 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                    <span class="flex items-center gap-1.5 text-amber-300 font-semibold">
                        <span class="iconify text-base" data-icon="solar:verified-check-bold"></span>
                        Kecocokan Sangat Tinggi
                    </span>
                    <span class="text-[11px]">MNC University 2027</span>
                </div>
            </div>
        </div>

        <!-- Secondary Match: The Silver Card (Col 5) -->
        <div class="lg:col-span-5 flex">
            <div class="w-full bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col justify-between">
                <div class="space-y-5">
                    <!-- Top Match Badge & Percentage -->
                    <div class="flex items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center border border-slate-200">
                                <span class="iconify text-xl" data-icon="solar:medal-star-bold"></span>
                            </span>
                            <div>
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Opsi Alternatif #2</span>
                                <span class="block text-xs font-semibold text-slate-500 mt-0.5">{{ $detail2['faculty'] }}</span>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-none">
                                {{ $percent2 }}%
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kecocokan</span>
                        </div>
                    </div>

                    <!-- Program Title & Description -->
                    <div class="space-y-2.5">
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-slate-100 text-xs font-semibold text-slate-700">
                            <span class="iconify text-base" data-icon="{{ $detail2['icon'] }}"></span>
                            <span>{{ $detail2['fullName'] }}</span>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                            {{ $detail2['fullName'] }}
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            {{ $detail2['focus'] }}
                        </p>
                    </div>

                    <!-- Career Tag -->
                    <div class="space-y-1.5 pt-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Peluang Karir:</span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($detail2['careers'] as $c)
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-medium border border-slate-200">
                                {{ $c }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="pt-5 mt-5 border-t border-slate-100 text-[11px] text-slate-500 flex items-center gap-1.5">
                    <span class="iconify text-base text-blue-600 shrink-0" data-icon="solar:info-circle-bold"></span>
                    <span>Pilihan pendukung yang kuat jika Anda ingin memiliki opsi cadangan.</span>
                </div>
            </div>
        </div>

    </div>

    <!-- AI Personalized Advisor Note (Message from Arion) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 md:p-10 border border-slate-200 shadow-xs space-y-6">
        <div class="flex items-center gap-3.5 pb-4 border-b border-slate-100">
            <div class="w-11 h-11 rounded-2xl bg-[#0B1120] text-amber-400 flex items-center justify-center shrink-0">
                <span class="iconify text-2xl" data-icon="solar:chat-round-line-bold-duotone"></span>
            </div>
            <div>
                <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Ulasan Khusus dari Arion (Advisor Kampus)</h3>
                <p class="text-xs text-slate-500">Analisis kualitatif karakter dan rekomendasi langkah berikutnya.</p>
            </div>
        </div>

        <div class="text-xs sm:text-sm text-slate-700 leading-relaxed space-y-3 prose max-w-none">
            {!! nl2br(e($ai)) !!}
        </div>

        <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200/80 flex items-start gap-3 text-xs text-amber-900 leading-relaxed">
            <span class="iconify text-lg text-amber-600 shrink-0 mt-0.5" data-icon="solar:info-circle-bold"></span>
            <p>
                <strong>Tips Beasiswa:</strong> Pada formulir pendaftaran beasiswa, Anda dapat memilih <strong>{{ $detail1['fullName'] }}</strong> sebagai pilihan prodi utama untuk memaksimalkan peluang lolos seleksi berkas dan wawancara.
            </p>
        </div>
    </div>

    <!-- Call to Action Buttons -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3.5 pt-4">
        @auth
        <a href="{{ route('pendaftar.biodata') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl font-bold text-sm bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 shadow-lg shadow-amber-500/25 transition-all cursor-pointer">
            <span class="iconify text-lg" data-icon="solar:arrow-right-bold"></span>
            <span>Lengkapi Biodata & Pilih Prodi</span>
        </a>
        @else
        <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl font-bold text-sm bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 shadow-lg shadow-amber-500/25 transition-all cursor-pointer">
            <span class="iconify text-lg" data-icon="solar:user-plus-bold"></span>
            <span>Daftar Beasiswa Sekarang</span>
        </a>
        @endauth

        <!-- Share Result Button -->
        <button type="button" onclick="shareResult()" 
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-4 rounded-xl font-bold text-sm bg-white hover:bg-slate-50 text-slate-800 border border-slate-200 shadow-xs transition-all cursor-pointer">
            <span class="iconify text-lg text-emerald-600" data-icon="solar:share-bold-duotone"></span>
            <span>Bagikan ke WhatsApp</span>
        </button>

        <!-- Retake Quiz Button -->
        <a href="{{ route('quiz.show') }}" 
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-4 rounded-xl font-bold text-sm bg-slate-100 hover:bg-slate-200 text-slate-600 transition-all">
            <span class="iconify text-base" data-icon="solar:restart-bold"></span>
            <span>Ulangi Quiz</span>
        </a>
    </div>

</div>

<!-- Confetti Effect Script on Page Mount -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof confetti === 'function') {
        confetti({
            particleCount: 80,
            spread: 70,
            origin: { y: 0.6 }
        });
        setTimeout(() => {
            confetti({
                particleCount: 50,
                angle: 60,
                spread: 55,
                origin: { x: 0 }
            });
            confetti({
                particleCount: 50,
                angle: 120,
                spread: 55,
                origin: { x: 1 }
            });
        }, 300);
    }
});

function shareResult() {
    const prodi = @json($detail1['fullName']);
    const percent = @json($percent1);
    const text = encodeURIComponent(`Halo! Saya baru saja mengikuti Quiz Analisis Minat & Bakat MNC University Future Leader Scholarship dan hasil kecocokan saya adalah ${percent}% di ${prodi}! Coba cek prodi impianmu juga di: ${window.location.origin}/rekomendasi-prodi`);
    window.open(`https://wa.me/?text=${text}`, '_blank');
}
</script>
@endsection

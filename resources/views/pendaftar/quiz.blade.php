@extends('layouts.quiz')

@section('title', 'Quiz Rekomendasi Program Studi - MFLS')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 space-y-8 pb-32">

    <!-- Hero Header Card -->
    <div class="relative overflow-hidden bg-[#0B1120] rounded-3xl p-6 sm:p-10 md:p-12 text-white border border-slate-800 shadow-xl">
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6 sm:gap-8">
            <!-- Arion Mascot -->
            <div class="shrink-0 relative group">
                <div class="absolute -inset-3 bg-amber-500/20 rounded-full blur-xl opacity-75 group-hover:opacity-100 transition duration-700"></div>
                <img src="{{ asset('icon/scholarr.webp') }}" alt="Arion Mascot" 
                     class="w-28 h-28 sm:w-36 sm:h-36 md:w-40 md:h-40 object-contain relative z-10 drop-shadow-xl transform group-hover:scale-105 transition-transform duration-500">
            </div>

            <!-- Header Text -->
            <div class="text-center md:text-left flex-1 space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-xs font-bold text-amber-300">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>AI MATCHING ASSESSMENT 2027</span>
                </div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-white tracking-tight leading-tight">
                    Quiz Rekomendasi <span class="text-amber-400">Program Studi</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 font-medium leading-relaxed max-w-2xl">
                    Jawab {{ count($questions) }} pertanyaan singkat seputar ketertarikan, cara berpikir, dan impian karirmu. Sistem cerdas kami akan menganalisis Program Studi MNC University yang paling sesuai dengan potensimu.
                </p>
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 pt-1 text-[11px] font-semibold text-slate-400">
                    <span class="flex items-center gap-1.5">
                        <span class="iconify text-amber-400 text-sm" data-icon="solar:check-circle-bold"></span>
                        {{ count($questions) }} Pertanyaan
                    </span>
                    <span>&bull;</span>
                    <span class="flex items-center gap-1.5">
                        <span class="iconify text-amber-400 text-sm" data-icon="solar:clock-circle-bold"></span>
                        Estimasi 3 Menit
                    </span>
                    <span>&bull;</span>
                    <span class="flex items-center gap-1.5">
                        <span class="iconify text-amber-400 text-sm" data-icon="solar:stars-minimalistic-bold"></span>
                        Akurasi Berbasis Minat
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Alert if any -->
    @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-800 text-xs font-semibold flex items-center gap-3">
        <span class="iconify text-xl text-rose-600 shrink-0" data-icon="solar:danger-triangle-bold-duotone"></span>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <!-- Sticky Progress Bar & Question Navigator Bar -->
    <div class="sticky top-20 sm:top-24 z-40 bg-white/95 backdrop-blur-md p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-md">
        <div class="flex items-center justify-between gap-4 mb-2">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                <span class="text-xs font-bold text-slate-800">
                    Progress: <span id="answeredCount" class="text-amber-600 font-extrabold">0</span> / {{ count($questions) }} Terjawab
                </span>
            </div>
            
            <div class="flex items-center gap-3">
                <span id="progressText" class="text-xs font-extrabold text-amber-600">0%</span>
                <button type="button" id="toggleMapBtn" 
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors cursor-pointer">
                    <span class="iconify" data-icon="solar:widget-2-bold-duotone"></span>
                    <span>Peta Soal</span>
                </button>
            </div>
        </div>

        <!-- Progress Bar Line -->
        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
            <div id="progressBar" class="h-full bg-gradient-to-r from-amber-500 to-amber-400 rounded-full w-0 transition-all duration-300 ease-out"></div>
        </div>

        <!-- Collapsible Question Mini-Map Drawer -->
        <div id="questionMap" class="hidden pt-4 mt-3 border-t border-slate-100">
            <div class="flex items-center justify-between mb-2 text-xs font-bold text-slate-600">
                <span>Klik nomor soal untuk melompat langsung:</span>
                <span class="text-[11px] text-slate-400 font-normal">Hijau = Terisi</span>
            </div>
            <div class="grid grid-cols-6 sm:grid-cols-10 gap-1.5 text-xs font-bold">
                @for($i = 0; $i < count($questions); $i++)
                <button type="button" 
                        onclick="scrollToQuestion({{ $i }})" 
                        id="nav-badge-{{ $i }}"
                        class="h-8 rounded-lg border border-slate-200 bg-slate-50 text-slate-600 flex items-center justify-center hover:border-amber-500 hover:text-amber-600 transition-all cursor-pointer">
                    {{ $i + 1 }}
                </button>
                @endfor
            </div>
        </div>
    </div>

    <!-- Questions Form -->
    <form action="{{ route('quiz.submit') }}" method="POST" id="quizForm" class="space-y-6">
        @csrf

        @php
            $prodiMeta = [
                'Manajemen' => ['icon' => 'solar:case-round-bold-duotone', 'badge' => 'Bisnis & Manajemen'],
                'Akuntansi' => ['icon' => 'solar:calculator-bold-duotone', 'badge' => 'Akuntansi & Keuangan'],
                'Sains Komunikasi' => ['icon' => 'solar:chat-round-dots-bold-duotone', 'badge' => 'Komunikasi & Media'],
                'Pendidikan Matematika' => ['icon' => 'solar:square-academic-cap-bold-duotone', 'badge' => 'Matematika & Edukasi'],
                'Pendidikan Bahasa Inggris' => ['icon' => 'solar:global-bold-duotone', 'badge' => 'Bahasa & Global'],
                'Sistem Informasi' => ['icon' => 'solar:database-bold-duotone', 'badge' => 'Sistem Informasi'],
                'DKV' => ['icon' => 'solar:palette-bold-duotone', 'badge' => 'Desain & Kreatif'],
                'Ilmu Komputer' => ['icon' => 'solar:code-square-bold-duotone', 'badge' => 'Komputer & Software'],
            ];
        @endphp

        @foreach($questions as $index => $q)
        <div id="question-card-{{ $index }}" 
             class="question-box bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-7 md:p-8 border border-slate-200 shadow-xs hover:border-slate-300 transition-all scroll-mt-36">
            
            <!-- Question Header -->
            <div class="flex items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-amber-50 text-amber-700 font-extrabold text-xs sm:text-sm flex items-center justify-center border border-amber-200 shrink-0">
                        {{ $index + 1 }}
                    </span>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                        Pertanyaan {{ $index + 1 }} dari {{ count($questions) }}
                    </span>
                </div>
                <span id="badge-status-{{ $index }}" class="text-[11px] font-semibold text-slate-400">
                    Belum dipilih
                </span>
            </div>

            <!-- Question Title -->
            <h3 class="text-base sm:text-lg md:text-xl font-bold text-slate-900 leading-snug mb-5">
                {{ $q['question'] }}
            </h3>

            <!-- 8 Options Grid (Responsive 2 to 4 cols) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($q['options'] as $major => $text)
                @php
                    $meta = $prodiMeta[$major] ?? ['icon' => 'solar:star-bold-duotone', 'badge' => $major];
                @endphp
                <label class="option-label relative block cursor-pointer group">
                    <input type="radio" 
                           name="answers[{{ $index }}]" 
                           value="{{ $major }}" 
                           class="peer sr-only" 
                           required
                           onchange="onSelectOption({{ $index }}, {{ count($questions) }})">
                    
                    <div class="h-full p-3.5 rounded-xl border border-slate-200 bg-slate-50/70 hover:bg-white hover:border-slate-300 peer-checked:border-amber-500 peer-checked:bg-amber-50/60 peer-checked:ring-2 peer-checked:ring-amber-500/20 transition-all flex flex-col justify-between space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-600 peer-checked:border-amber-400 peer-checked:text-amber-700 flex items-center justify-center shrink-0">
                                <span class="iconify text-base" data-icon="{{ $meta['icon'] }}"></span>
                            </div>
                            <div class="w-4 h-4 rounded-full border-2 border-slate-300 bg-white flex items-center justify-center shrink-0 peer-checked:border-amber-500 peer-checked:bg-amber-500 transition-colors mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></span>
                            </div>
                        </div>

                        <div>
                            <div class="text-sm font-bold text-slate-800 peer-checked:text-amber-950 transition-colors">
                                {{ $text }}
                            </div>
                            <div class="text-[10px] font-semibold text-slate-400 peer-checked:text-amber-700/80 transition-colors mt-0.5 truncate">
                                {{ $meta['badge'] }}
                            </div>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>

        </div>
        @endforeach

        <!-- Bottom Action Card -->
        <div class="sticky bottom-4 z-40 bg-slate-900/95 backdrop-blur-md text-white p-4 sm:p-5 rounded-2xl border border-slate-800 shadow-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="space-y-0.5 text-center sm:text-left">
                <div class="text-sm font-extrabold text-white flex items-center justify-center sm:justify-start gap-2">
                    <span class="iconify text-amber-400 text-base" data-icon="solar:magic-stick-3-bold-duotone"></span>
                    <span>Analisis Program Studi Impian</span>
                </div>
                <p id="bottomStatus" class="text-xs text-slate-400">
                    Silakan lengkapi semua {{ count($questions) }} pertanyaan di atas.
                </p>
            </div>

            <button type="submit" id="submitBtn"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl font-black text-sm bg-amber-400 hover:bg-amber-300 text-slate-950 shadow-lg shadow-amber-400/20 hover:shadow-xl transition-all cursor-pointer shrink-0">
                <span class="iconify text-lg" data-icon="solar:stars-minimalistic-bold"></span>
                <span id="btnSubmitText">Lihat Rekomendasi Prodi</span>
            </button>
        </div>
    </form>
</div>

<!-- JavaScript Logic: Auto-advance, Progress Tracking, Question Map Jump -->
<script>
    const totalQuestions = {{ count($questions) }};
    const answeredMap = new Map();

    function onSelectOption(index, total) {
        answeredMap.set(index, true);
        updateProgress();

        // Update question card status badge
        const badge = document.getElementById('badge-status-' + index);
        if (badge) {
            badge.textContent = '✓ Terjawab';
            badge.className = 'text-[11px] font-bold text-emerald-600';
        }

        // Update nav badge in mini-map
        const navBadge = document.getElementById('nav-badge-' + index);
        if (navBadge) {
            navBadge.className = 'h-8 rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold';
        }

        // Optional smooth auto-advance to next unanswered question after 280ms
        setTimeout(() => {
            for (let next = index + 1; next < total; next++) {
                if (!answeredMap.has(next)) {
                    scrollToQuestion(next);
                    return;
                }
            }
        }, 280);
    }

    function updateProgress() {
        const count = answeredMap.size;
        const percent = Math.round((count / totalQuestions) * 100);

        const countEl = document.getElementById('answeredCount');
        const textEl = document.getElementById('progressText');
        const barEl = document.getElementById('progressBar');
        const bottomStatus = document.getElementById('bottomStatus');

        if (countEl) countEl.textContent = count;
        if (textEl) textEl.textContent = percent + '%';
        if (barEl) barEl.style.width = percent + '%';

        if (bottomStatus) {
            if (count === totalQuestions) {
                bottomStatus.textContent = '✨ Semua pertanyaan lengkap! Klik tombol untuk melihat hasil.';
                bottomStatus.className = 'text-xs text-emerald-400 font-bold';
            } else {
                bottomStatus.textContent = `Masih ada ${totalQuestions - count} pertanyaan yang belum dipilih.`;
                bottomStatus.className = 'text-xs text-slate-400';
            }
        }
    }

    function scrollToQuestion(index) {
        const card = document.getElementById('question-card-' + index);
        if (card) {
            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            card.classList.add('ring-2', 'ring-amber-500/30');
            setTimeout(() => card.classList.remove('ring-2', 'ring-amber-500/30'), 1200);
        }
    }

    // Toggle mini-map
    const toggleMapBtn = document.getElementById('toggleMapBtn');
    const questionMap = document.getElementById('questionMap');
    if (toggleMapBtn && questionMap) {
        toggleMapBtn.addEventListener('click', function() {
            questionMap.classList.toggle('hidden');
        });
    }

    // Form Submit Validation
    document.getElementById('quizForm').addEventListener('submit', function(e) {
        if (answeredMap.size < totalQuestions) {
            e.preventDefault();
            // Find first unanswered question
            let firstUnanswered = 0;
            for (let i = 0; i < totalQuestions; i++) {
                if (!answeredMap.has(i)) {
                    firstUnanswered = i;
                    break;
                }
            }
            scrollToQuestion(firstUnanswered);

            if (window.Swal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Jawaban Belum Lengkap',
                    text: `Anda baru menjawab ${answeredMap.size} dari ${totalQuestions} pertanyaan. Silakan lengkapi seluruh pertanyaan untuk hasil analisis yang akurat.`,
                    confirmButtonColor: '#0B1120',
                    confirmButtonText: 'Lengkapi Sekarang'
                });
            } else {
                alert(`Mohon lengkapi semua pertanyaan (${answeredMap.size}/${totalQuestions} terjawab).`);
            }
            return;
        }

        // Loading state
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnSubmitText');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-80', 'cursor-wait');
        }
        if (btnText) {
            btnText.textContent = 'Menganalisis Potensi...';
        }

        if (window.Swal) {
            Swal.fire({
                title: 'Menganalisis Karakter & Minat',
                text: 'Mohon tunggu sebentar, sistem sedang memproses tingkat kecocokan Program Studi...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }
    });
</script>
@endsection

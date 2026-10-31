@extends('layouts.quiz')

@section('content')
<div class="max-w-4xl mx-auto space-y-10 pb-32">
    {{-- 1. SOPHISTICATED HEADER --}}
    <div class="relative overflow-hidden bg-slate-900 rounded-[3.5rem] p-10 md:p-16 text-white shadow-2xl border border-white/5">
        <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-primary-gold/5 rounded-full blur-[120px] -mr-40 -mt-40"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
            <!-- Arion Mascot -->
            <div class="shrink-0 relative group">
                <div class="absolute -inset-4 bg-primary-gold/20 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition duration-1000"></div>
                <img src="{{ asset('icon/scholarr.webp') }}" alt="Arion Mascot" class="w-32 h-32 md:w-48 md:h-48 object-contain relative z-10 drop-shadow-[0_20px_50px_rgba(212,175,55,0.2)] transform hover:scale-110 transition-transform duration-700">
            </div>

            <div class="text-center md:text-left flex-1 items-center md:items-start flex flex-col">
                <div class="inline-flex items-center gap-3 bg-white/5 border border-white/10 px-4 py-2 rounded-2xl mb-8 backdrop-blur-md">
                    <span class="iconify text-primary-gold text-lg" data-icon="solar:star-bold-duotone"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.3em] text-primary-gold">Interest Assessment</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-black tracking-tight mb-6 leading-tight">Analisis <span class="text-primary-gold italic text-gradient-gold">Minat & Bakat</span></h1>
                <p class="text-slate-400 text-sm md:text-base font-medium max-w-2xl leading-relaxed">
                    Kami akan menganalisis pola minat dan kepribadianmu melalui {{ count($questions) }} pertanyaan singkat untuk merekomendasikan Program Studi yang paling sesuai dengan potensi dirimu.
                </p>
            </div>
        </div>
    </div>

    @if(session('error'))
    <div class="bg-slate-900 border border-red-500/30 p-6 rounded-[2rem] shadow-xl animate-fade-in flex items-center gap-4">
        <div class="w-10 h-10 bg-red-500 text-white rounded-xl flex items-center justify-center shrink-0">
            <span class="iconify text-xl" data-icon="solar:danger-bold"></span>
        </div>
        <div>
            <h3 class="text-white font-black uppercase tracking-widest text-[10px] mb-1">Analisis Terhenti</h3>
            <p class="text-slate-400 text-[10px] font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    {{-- 2. DYNAMIC PROGRESS --}}
    <div class="sticky top-6 z-40 px-2" x-data="{ progress: 0 }">
        <div class="bg-white/90 backdrop-blur-2xl p-4 rounded-[2rem] border border-slate-100 shadow-2xl max-w-2xl mx-auto ring-1 ring-black/5">
            <div class="flex items-center justify-between mb-2 px-2">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Progress Analisis</span>
                <span class="text-[9px] font-black text-primary-gold uppercase tracking-widest" id="progressText">0% Selesai</span>
            </div>
            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div id="progressBar" class="h-full bg-primary-gold w-0 transition-all duration-700 ease-out"></div>
            </div>
        </div>
    </div>

    {{-- 3. QUESTIONS GRID --}}
    <form action="{{ route('quiz.submit') }}" method="POST" id="quizForm" class="space-y-8">
        @csrf

        @foreach($questions as $index => $q)
        <div class="bg-white rounded-[3rem] p-8 md:p-12 border border-slate-100 shadow-sm relative group overflow-hidden transition-all duration-500">
            <div class="absolute -top-6 -right-6 w-32 h-32 bg-slate-50 rounded-full flex items-center justify-center opacity-40 transition-colors">
                <span class="text-6xl font-black text-slate-100 group-hover:text-primary-gold/10 transition-colors translate-y-4 -translate-x-4">{{ $index + 1 }}</span>
            </div>

            <div class="relative z-10 space-y-10">
                <div class="space-y-3">
                    <h3 class="text-[9px] font-black text-primary-gold uppercase tracking-[0.3em]">Pertanyaan 0{{ $index + 1 }}</h3>
                    <p class="text-2xl md:text-3xl font-black text-slate-800 tracking-tight leading-tight max-w-2xl">{{ $q['question'] }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($q['options'] as $major => $text)
                    <label class="relative group/opt cursor-pointer">
                        <input type="radio" name="answers[{{ $index }}]" value="{{ $major }}" 
                               class="peer absolute opacity-0 w-0 h-0" required
                               onclick="updateProgress({{ $index }}, {{ count($questions) }})">
                        
                        <div class="p-6 rounded-2xl border border-slate-100 bg-slate-50 transition-all duration-300 flex items-start gap-5 peer-checked:border-primary-gold peer-checked:bg-white peer-checked:ring-4 peer-checked:ring-primary-gold/5 group-hover/opt:translate-x-1">
                            <div class="w-6 h-6 rounded-full border-2 border-slate-200 bg-white flex items-center justify-center shrink-0 transition-all peer-checked:border-primary-gold group-hover/opt:scale-110">
                                <div class="w-2.5 h-2.5 rounded-full bg-primary-gold opacity-0 transition-opacity peer-checked:opacity-100"></div>
                            </div>
                            <p class="text-sm font-bold text-slate-500 transition-colors peer-checked:text-slate-900 group-hover/opt:text-slate-700 leading-relaxed">{{ $text }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach

        {{-- 4. ANALYZE ACTION --}}
        <div class="text-center pt-8 space-y-10">
            <button type="submit" id="submitBtn" class="group relative inline-flex items-center justify-center gap-4 bg-slate-900 text-white px-12 py-5 rounded-2xl font-black hover:bg-primary-gold hover:text-slate-900 transition-all shadow-xl active:scale-95 overflow-hidden">
                <div class="absolute inset-0 bg-white/10 scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-500"></div>
                <span class="iconify text-2xl text-primary-gold group-hover:text-slate-900" data-icon="solar:magic-stick-3-bold-duotone"></span>
                <span class="uppercase tracking-[0.2em] text-[10px] relative z-10">Lihat Rekomendasi Program Studi</span>
            </button>
            
            <div class="flex flex-col items-center gap-3 opacity-30">
                <div class="h-px w-12 bg-slate-400"></div>
                <p class="text-[8px] font-black text-slate-500 uppercase tracking-[0.4em]">Integrated Assessment Portal</p>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const answeredIndices = new Set();
    
    function updateProgress(index, total) {
        answeredIndices.add(index);
        const percent = Math.round((answeredIndices.size / total) * 100);
        const bar = document.getElementById('progressBar');
        const text = document.getElementById('progressText');
        
        bar.style.width = percent + '%';
        text.textContent = percent + '% Selesai';
        
        if (percent === 100) {
            text.classList.remove('text-primary-gold');
            text.classList.add('text-slate-900');
            text.textContent = 'Analisis Siap • 100%';
        }
    }

    document.getElementById('quizForm').addEventListener('submit', function(e) {
        const total = {{ count($questions) }};
        if (answeredIndices.size < total) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Data Belum Lengkap',
                text: `Selesaikan seluruh pertanyaan sebelum melanjutkan analisis.`,
                confirmButtonColor: '#0f172a',
                customClass: { popup: 'rounded-[2.5rem]' }
            });
            return;
        }

        const btn = document.getElementById('submitBtn');
        btn.innerHTML = `<span class="iconify text-2xl animate-spin" data-icon="solar:restart-bold"></span> <span class="uppercase tracking-[0.2em] text-[10px]">Menghitung Hasil...</span>`;
        btn.classList.add('pointer-events-none', 'opacity-80');
        
        Swal.fire({
            title: 'MENGANALISIS MINAT',
            text: 'Tunggu sebentar, kami sedang memproses profil Anda...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
            customClass: { popup: 'rounded-[2.5rem]' }
        });
    });
</script>

<style>
    .text-gradient-gold {
        background: linear-gradient(135deg, #d4af37 0%, #f1d37e 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.5s ease-out; }
</style>
@endsection

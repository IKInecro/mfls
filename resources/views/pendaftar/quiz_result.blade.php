@extends('layouts.quiz')

@section('content')
<div class="max-w-5xl mx-auto space-y-10 pb-32">
    {{-- 1. REVEAL HEADER --}}
    <div class="relative overflow-hidden bg-slate-900 rounded-[3.5rem] p-10 md:p-16 text-white shadow-2xl border border-white/5 text-center">
        <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-primary-gold/5 rounded-full blur-[120px] -mr-40 -mt-40"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
            <!-- Arion Mascot -->
            <div class="shrink-0 relative group">
                <div class="absolute -inset-4 bg-primary-gold/20 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition duration-1000"></div>
                <img src="{{ asset('icon/scholarr.webp') }}" alt="Arion Mascot" class="w-40 h-40 md:w-56 md:h-56 object-contain relative z-10 drop-shadow-[0_20px_50px_rgba(212,175,55,0.3)] transform hover:scale-110 transition-transform duration-700">
            </div>

            <div class="text-center md:text-left flex-1 items-center md:items-start flex flex-col">
                <div class="inline-flex items-center gap-3 bg-white/5 border border-white/10 px-6 py-3 rounded-2xl mb-8 backdrop-blur-md">
                    <span class="iconify text-primary-gold text-2xl" data-icon="solar:verified-check-bold"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.3em] text-primary-gold">Analysis Complete</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-black tracking-tight mb-6 leading-tight">Potensi <span class="text-primary-gold italic">Akademik Anda</span></h1>
                <p class="text-slate-400 text-sm md:text-base font-medium max-w-2xl leading-relaxed">
                    Berdasarkan hasil analisis minat dan bakat, berikut adalah Program Studi yang memiliki tingkat kecocokan tertinggi dengan profil Anda.
                </p>
            </div>
        </div>
    </div>

    {{-- 2. DUAL MATCHING CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-stretch">
        <!-- Primary Match: The Golden Card -->
        <div class="md:col-span-12 lg:col-span-7 relative group">
            <div class="absolute -inset-1 bg-primary-gold rounded-[4rem] blur-xl opacity-10"></div>
            <div class="relative bg-slate-900 rounded-[3.5rem] p-10 md:p-14 border border-primary-gold/30 shadow-2xl h-full overflow-hidden">
                <div class="absolute top-0 right-0 p-12 opacity-5 mt-4 group-hover:scale-110 transition-transform duration-700">
                    <span class="iconify text-[120px] text-white" data-icon="solar:medal-star-bold"></span>
                </div>
                
                <div class="relative z-10 space-y-12">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 bg-primary-gold rounded-xl flex items-center justify-center text-slate-900 shadow-xl shadow-primary-gold/20">
                                <span class="iconify text-2xl font-black" data-icon="solar:crown-bold"></span>
                            </span>
                            <span class="text-[10px] font-black text-primary-gold uppercase tracking-[0.34em]">Rekomendasi Utama</span>
                        </div>
                        <div class="text-right">
                            <span class="block text-4xl md:text-5xl font-black text-white tracking-tighter">{{ $percent1 }}<span class="text-2xl text-primary-gold/50">%</span></span>
                            <span class="text-[9px] font-black text-primary-gold uppercase tracking-widest mt-1 opacity-70 italic">Kecocokan</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h2 class="text-4xl md:text-5xl font-black text-white tracking-tighter leading-none">{{ $top1 }}</h2>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-primary-gold animate-pulse"></span>
                            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Karakter Akademik: Alpha</p>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-white/5 flex items-center justify-between">
                        <div class="flex -space-x-2">
                            @for($i=0; $i<5; $i++)
                            <div class="w-8 h-8 rounded-full border-2 border-slate-900 bg-slate-800 flex items-center justify-center">
                                <span class="iconify text-primary-gold text-xs" data-icon="solar:star-bold"></span>
                            </div>
                            @endfor
                        </div>
                        <p class="text-[9px] font-bold text-white/40 uppercase tracking-widest leading-none">Profil Terverifikasi</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Match: The Silver Card -->
        <div class="md:col-span-12 lg:col-span-5">
            <div class="bg-white rounded-[3.5rem] p-10 md:p-14 border border-slate-100 shadow-sm h-full flex flex-col justify-between overflow-hidden relative">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-slate-50 rounded-full opacity-50"></div>
                
                <div class="relative z-10 space-y-10">
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em]">Opsi Alternatif</span>
                        <span class="text-3xl font-black text-slate-900 tracking-tighter">{{ $percent2 }}<span class="text-lg text-slate-200">%</span></span>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-2xl font-black text-slate-800 tracking-tight leading-tight">{{ $top2 }}</h3>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Karakter Akademik: Beta</p>
                    </div>
                </div>

                <div class="relative z-10 pt-10 mt-10 border-t border-slate-50 flex items-center gap-3">
                    <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-300 border border-slate-100">
                        <span class="iconify text-xl font-black" data-icon="solar:star-fall-bold"></span>
                    </div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-relaxed">Pilihan pendukung dengan potensi kompatibilitas tinggi.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. ANALYSIS REPORT --}}
    <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden relative group">
        <div class="p-10 md:p-16 relative z-10">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6 mb-12 pb-10 border-b border-slate-50">
                <div class="w-14 h-14 rounded-2xl bg-slate-900 flex items-center justify-center shadow-xl shadow-black/10">
                    <span class="iconify text-primary-gold text-2xl" data-icon="solar:document-add-bold-duotone"></span>
                </div>
                <div class="space-y-1">
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight leading-none">Laporan Potensi Akademik</h3>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-slate-900 text-primary-gold rounded text-[8px] font-black uppercase tracking-widest leading-none">Official Digital Report</span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none">Dibuat pada: {{ now()->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="prose prose-slate max-w-none prose-headings:font-black prose-headings:tracking-tight prose-p:text-slate-600 prose-p:leading-relaxed text-sm md:text-base markdown-report space-y-4">
                {!! nl2br(e($ai)) !!}
            </div>

            <div class="mt-16 p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 flex items-start gap-6">
                <div class="w-12 h-12 bg-white text-slate-400 rounded-2xl flex items-center justify-center shrink-0 border border-slate-100 italic font-black">!</div>
                <div class="space-y-2">
                    <p class="text-[10px] font-black text-slate-800 uppercase tracking-widest leading-none">Catatan Penting</p>
                    <p class="text-[10px] text-slate-500 font-bold leading-relaxed opacity-80 italic">
                        Hasil ini merupakan analisis sistem berdasarkan data yang Anda berikan. Kami menyarankan Anda untuk tetap berdiskusi dengan pendamping atau orang tua untuk memvalidasi pilihan terbaik masa depan Anda.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. MASTER CALL-TO-ACTION --}}
    <div class="flex flex-col sm:flex-row items-center justify-center gap-6 pt-10 pb-20">
        @auth
        <a href="{{ route('pendaftar.berkas') }}" class="w-full sm:w-auto px-16 py-5 bg-slate-900 text-white font-black rounded-2xl hover:bg-primary-gold hover:text-slate-900 transition-all shadow-xl flex items-center justify-center gap-4 group">
            <span class="text-[10px] uppercase tracking-[0.34em]">Lengkapi Berkas Sekarang</span>
            <span class="iconify text-2xl group-hover:translate-x-1 transition-transform" data-icon="solar:arrow-right-bold"></span>
        </a>
        @else
        <a href="{{ route('register') }}" class="w-full sm:w-auto px-16 py-5 bg-slate-900 text-white font-black rounded-2xl hover:bg-primary-gold hover:text-slate-900 transition-all shadow-xl flex items-center justify-center gap-4 group">
            <span class="text-[10px] uppercase tracking-[0.34em]">Daftar Beasiswa Sekarang</span>
            <span class="iconify text-2xl group-hover:translate-x-1 transition-transform" data-icon="solar:arrow-right-bold"></span>
        </a>
        @endauth
        <a href="{{ route('quiz.show') }}" class="w-full sm:w-auto px-10 py-5 bg-white text-slate-400 font-black rounded-xl border border-slate-100 hover:border-slate-300 hover:text-slate-800 transition-all flex items-center justify-center gap-3 group">
            <span class="iconify text-xl group-hover:rotate-180 transition-transform duration-700" data-icon="solar:restart-bold text-slate-300"></span>
            <span class="text-[9px] uppercase tracking-[0.2em]">Ulangi Analisis</span>
        </a>
    </div>
</div>

<style>
    .markdown-report p { margin-bottom: 1.5rem; }
    .markdown-report h2, .markdown-report h3 { margin-top: 2.5rem; margin-bottom: 1rem; color: #1e293b; font-weight: 900; letter-spacing: -0.02em; }
    .markdown-report strong { color: #0f172a; font-weight: 800; }
</style>
@endsection

@extends('layouts.user')

@section('content')
    <div class="bg-white min-h-screen">
        {{-- 1. HERO HEADER --}}
        <div class="relative pt-24 pb-20 md:pt-40 md:pb-32 overflow-hidden">
            {{-- Dynamic Background Elements --}}
            <div class="absolute inset-0 bg-primary-blue/[0.02] -z-10"></div>
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary-yellow/10 rounded-full blur-[120px] -mr-64 -mt-64 animate-glow-pulse"></div>
            <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-primary-blue/5 rounded-full blur-[100px] -ml-48 -mb-48"></div>
            
            <div class="max-w-4xl mx-auto px-6 text-center">
                {{-- Breadcrumbs --}}
                <div class="inline-flex items-center justify-center gap-3 mb-10 text-[9px] font-black uppercase tracking-[0.3em] text-gray-400 bg-white/50 backdrop-blur-sm px-6 py-2 rounded-full border border-gray-100 shadow-sm">
                    <a href="{{ url('/') }}" class="hover:text-primary-blue transition-colors">Beranda</a>
                    <span class="iconify text-xs opacity-30" data-icon="solar:alt-arrow-right-bold"></span>
                    <a href="{{ url('/') }}#berita" class="hover:text-primary-blue transition-colors">Berita</a>
                    <span class="iconify text-xs text-primary-yellow" data-icon="solar:alt-arrow-right-bold"></span>
                    <span class="text-dark-navy">Detail</span>
                </div>

                <div class="inline-flex items-center gap-2.5 px-4 py-2 bg-primary-blue text-white rounded-full text-[9px] font-black uppercase tracking-[0.2em] mb-8 shadow-xl shadow-primary-blue/20">
                    <span class="w-1.5 h-1.5 bg-primary-yellow rounded-full animate-ping"></span>
                    Official Update
                </div>

                <h1 class="text-4xl md:text-6xl lg:text-7xl font-black text-dark-navy leading-[1.05] tracking-tight mb-10 overflow-visible">
                    {{ $berita->judul }}
                </h1>

                <div class="flex items-center justify-center gap-8 text-gray-500">
                    <div class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-white shadow-sm border border-gray-100 flex items-center justify-center group-hover:bg-primary-yellow transition-colors duration-500">
                            <span class="iconify text-primary-blue group-hover:text-dark-navy transition-colors text-lg" data-icon="solar:calendar-bold-duotone"></span>
                        </div>
                        <div class="text-left">
                            <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest leading-none mb-1">Diterbitkan</p>
                            <p class="text-[11px] font-bold text-dark-navy uppercase tracking-widest leading-none">{{ $berita->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="w-px h-10 bg-gray-100"></div>

                    <div class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-white shadow-sm border border-gray-100 flex items-center justify-center group-hover:bg-primary-blue transition-colors duration-500">
                            <span class="iconify text-primary-blue group-hover:text-white transition-colors text-lg" data-icon="solar:user-bold-duotone"></span>
                        </div>
                        <div class="text-left">
                            <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest leading-none mb-1">Penulis</p>
                            <p class="text-[11px] font-bold text-dark-navy uppercase tracking-widest leading-none">Admin MFLS</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. FEATURED IMAGE --}}
        <div class="max-w-6xl mx-auto px-6 -mt-10 md:-mt-16 relative z-20">
            @if($berita->thumbnail)
                <div class="relative group">
                    <div class="absolute -inset-6 bg-primary-blue/5 rounded-[4rem] blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                    <div class="relative aspect-video md:aspect-[21/9] rounded-[3rem] overflow-hidden shadow-[0_40px_80px_-15px_rgba(2,54,129,0.15)] border-4 border-white">
                        <img src="{{ asset('storage/' . $berita->thumbnail) }}" alt="{{ $berita->judul }}"
                            class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-[5s]">
                    </div>
                </div>
            @endif
        </div>

        {{-- 3. ARTICLE CONTENT --}}
        <div class="max-w-4xl mx-auto px-6 pt-20 pb-32">
            <div class="bg-white rounded-[3rem] p-8 md:p-20 border border-gray-100 shadow-[0_32px_100px_-20px_rgba(2,54,129,0.08)] relative">
                <div class="absolute -top-12 -left-12 w-24 h-24 bg-primary-yellow rounded-full -z-10 opacity-20"></div>
                
                <article class="prose prose-lg prose-slate max-w-none prose-headings:text-dark-navy prose-headings:font-black prose-headings:tracking-tighter prose-p:text-gray-600 prose-p:leading-[1.85] prose-strong:text-dark-navy prose-strong:font-black prose-a:text-primary-blue prose-a:font-bold prose-img:rounded-[2rem] prose-img:shadow-xl relative z-10 custom-article">
                    {!! preg_replace('/on[a-z]+\s*=\s*(["\']).*?\1/i', '', preg_replace('#<script(.*?)>(.*?)</script>#is', '', strip_tags($berita->konten, '<p><br><strong><b><em><i><u><a><h1><h2><h3><h4><h5><h6><ul><ol><li><img><blockquote><table><thead><tbody><tr><th><td><span><div><figure>'))) !!}
                </article>

                {{-- Footer Article --}}
                <div class="mt-24 pt-10 border-t border-gray-50 flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 bg-dark-navy rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-dark-navy/30 relative">
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-primary-yellow rounded-full border-2 border-white"></div>
                            <span class="iconify text-3xl text-primary-yellow" data-icon="solar:medal-star-bold-duotone"></span>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-dark-navy uppercase tracking-[0.2em] mb-1">MNCU Future Leader Scholarship</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic leading-none">Official Publication &copy; {{ date('Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <button onclick="window.print()" class="flex items-center gap-3 h-12 px-6 rounded-2xl bg-gray-50 hover:bg-primary-blue hover:text-white text-gray-500 transition-all duration-300 group">
                            <span class="iconify text-xl group-hover:scale-110 transition-transform" data-icon="solar:printer-bold-duotone"></span>
                            <span class="text-[11px] font-black uppercase tracking-widest">Cetak Berita</span>
                        </button>
                        <a href="https://wa.me/?text={{ urlencode($berita->judul . ' ' . url()->current()) }}" target="_blank" class="flex items-center justify-center w-12 h-12 rounded-2xl bg-[#25D366]/10 text-[#25D366] hover:bg-[#25D366] hover:text-white transition-all duration-300">
                             <span class="iconify text-xl" data-icon="solar:share-bold-duotone"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. LATEST NEWS SUGGESTION --}}
        @php
            $latest_beritas = \App\Models\Berita::where('id', '!=', $berita->id)
                ->where('is_published', true)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        @endphp

        @if($latest_beritas->count() > 0)
            <div class="bg-gray-50/50 py-32">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="flex flex-col md:flex-row items-center md:items-end justify-between mb-16 gap-6 text-center md:text-left">
                        <div>
                            <span class="inline-block px-4 py-1.5 bg-primary-yellow/20 text-dark-navy rounded-full text-[10px] font-black uppercase tracking-widest mb-4">Eksplorasi Lainnya</span>
                            <h2 class="text-4xl md:text-5xl font-black text-dark-navy tracking-tight leading-none">Informasi Terbaru & Update</h2>
                        </div>
                        <a href="{{ url('/') }}#berita" class="group flex items-center gap-4 text-xs font-black text-primary-blue hover:text-dark-navy uppercase tracking-[0.2em] transition-all bg-white px-8 py-4 rounded-2xl shadow-sm hover:shadow-md border border-gray-100">
                            Lihat Semua Berita
                            <span class="iconify text-xl group-hover:translate-x-2 transition-transform" data-icon="solar:alt-arrow-right-bold"></span>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        @foreach($latest_beritas as $item)
                        <div class="bg-white rounded-[2.5rem] border border-gray-100 p-5 shadow-sm hover:shadow-2xl hover:-translate-y-3 transition-all duration-500 group relative overflow-hidden">
                            <div class="aspect-[4/3] rounded-[2rem] overflow-hidden mb-8 relative">
                                <div class="absolute inset-0 bg-dark-navy/20 group-hover:bg-transparent transition-colors duration-500 z-10"></div>
                                @if($item->thumbnail)
                                    <img src="{{ asset('storage/' . $item->thumbnail) }}" alt="{{ $item->judul }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                @else
                                    <div class="w-full h-full bg-primary-blue/5 flex items-center justify-center">
                                        <span class="iconify text-5xl text-primary-blue/10" data-icon="solar:gallery-bold-duotone"></span>
                                    </div>
                                @endif
                                <div class="absolute top-4 left-4 z-20">
                                    <div class="bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-xl border border-white shadow-lg">
                                        <p class="text-[9px] font-black text-dark-navy uppercase tracking-widest">{{ $item->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-3 pb-4">
                                <h4 class="text-xl font-black text-dark-navy mb-6 line-clamp-2 leading-tight group-hover:text-primary-blue transition-colors h-14">
                                    {{ $item->judul }}
                                </h4>
                                <a href="{{ route('berita.show', $item->slug) }}" class="inline-flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.2em] text-primary-blue group-hover:gap-5 transition-all">
                                    Baca Selengkapnya
                                    <span class="iconify text-lg" data-icon="solar:alt-arrow-right-bold"></span>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        .custom-article {
            font-size: 1.15rem;
            line-height: 1.85;
        }

        .custom-article h2 {
            font-size: 2.25rem;
            margin-top: 4rem;
            margin-bottom: 2rem;
            color: #023681;
            letter-spacing: -0.02em;
        }

        .custom-article h3 {
            font-size: 1.75rem;
            margin-top: 3rem;
            margin-bottom: 1.5rem;
            color: #023681;
        }

        .custom-article p {
            margin-bottom: 2rem;
        }

        .custom-article ul, .custom-article ol {
            margin-bottom: 2rem;
            padding-left: 1.5rem;
        }

        .custom-article li {
            margin-bottom: 1rem;
        }

        .custom-article blockquote {
            border-left: 6px solid #fcdb2f;
            background: #f8fafc;
            padding: 3rem;
            border-radius: 2rem;
            font-style: italic;
            color: #334155;
            margin: 4rem 0;
            position: relative;
        }

        .custom-article blockquote::before {
            content: '"';
            position: absolute;
            top: 2rem;
            left: 1rem;
            font-size: 8rem;
            color: #fcdb2f;
            opacity: 0.2;
            line-height: 1;
            font-family: serif;
        }
    </style>
@endsection
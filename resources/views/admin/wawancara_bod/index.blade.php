@extends('layouts.admin')

@section('content')

{{-- Page Header --}}
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-full mb-3">
            <span class="iconify text-amber-500 text-sm" data-icon="solar:cup-star-bold-duotone"></span>
            <span class="text-amber-700 text-[10px] font-black uppercase tracking-widest">Tahap Final</span>
        </div>
        <h2 class="text-2xl font-black text-slate-800 leading-tight">Wawancara BoD</h2>
        <p class="text-slate-400 font-medium text-sm mt-1">Kelayakan penerima <span class="text-amber-600 font-black">Beasiswa 100%</span> — ditentukan langsung oleh Board of Directors.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.wawancara_bod.download_photos') }}" class="px-5 py-3 bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2 transition-all shadow-sm">
            <span class="iconify" data-icon="solar:gallery-download-bold"></span>
            Download Semua Foto
        </a>
        <div class="px-5 py-3 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
            <span class="iconify text-amber-400" data-icon="solar:users-group-two-rounded-bold-duotone"></span>
            <span id="candidateCount">{{ $pesertas->count() }}</span> Kandidat
        </div>
    </div>
</div>

{{-- Search Bar --}}
<div class="mb-6">
    <div class="relative max-w-md">
        <span class="iconify absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none" data-icon="solar:magnifer-bold"></span>
        <input
            type="text"
            id="searchInput"
            placeholder="Cari nama atau NISN (Tekan Enter)..."
            onkeydown="if(event.key === 'Enter') { filterCandidates(); event.preventDefault(); }"
            class="w-full pl-11 pr-12 py-3.5 bg-white border-2 border-slate-200 rounded-2xl text-sm font-medium text-slate-700 placeholder-slate-300 focus:border-amber-400 focus:outline-none transition-all shadow-sm"
        >
        <button onclick="document.getElementById('searchInput').value=''; filterCandidates()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-500 transition-colors" id="clearSearch" style="display:none">
            <span class="iconify" data-icon="solar:close-circle-bold"></span>
        </button>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 flex items-center gap-3 px-6 py-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
        <span class="iconify text-emerald-500 text-xl" data-icon="solar:verified-check-bold-duotone"></span>
        <p class="text-emerald-700 font-bold text-sm">{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 flex items-center gap-3 px-6 py-4 bg-red-50 border border-red-200 rounded-2xl">
        <span class="iconify text-red-500 text-xl" data-icon="solar:danger-triangle-bold-duotone"></span>
        <p class="text-red-700 font-bold text-sm">{{ session('error') }}</p>
    </div>
@endif

@if($pesertas->isEmpty())
    {{-- Empty State --}}
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-16 text-center">
        <div class="w-20 h-20 mx-auto mb-6 bg-slate-50 border border-slate-100 rounded-3xl flex items-center justify-center">
            <span class="iconify text-slate-300 text-4xl" data-icon="solar:cup-star-bold-duotone"></span>
        </div>
        <p class="text-slate-400 font-black text-sm uppercase tracking-widest">Belum ada kandidat beasiswa 100%</p>
        <p class="text-slate-300 text-xs mt-2">Kandidat akan muncul setelah rekomendasi wawancara akademik ditetapkan.</p>
    </div>
@else
    {{-- Cards Grid --}}
    <div class="grid grid-cols-1 gap-5">
        @foreach($pesertas as $akun)
        @php
            $statusWawancara = $akun->peserta?->daftar?->status_wawancara_bod ?? null;
            $nominalFinal    = $akun->peserta?->daftar?->nominal_beasiswa ?? null;
            $rekBeasiswa     = $akun->peserta?->penilaianAkademiks?->first()?->rekomendasi_beasiswa ?? null;

            $penilaianAkademik = $akun->peserta?->penilaianAkademiks?->first();
            $rekProdi1       = $penilaianAkademik?->rekomendasi_prodi_1 ?? null;
            $rekProdi2       = $penilaianAkademik?->rekomendasi_prodi_2 ?? null;

            $isLayak    = $statusWawancara && str_starts_with($statusWawancara, 'Layak');
            $isTidak    = $statusWawancara === 'Tidak Layak';
            $isBelum    = !$statusWawancara;
        @endphp

        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg hover:border-slate-200 transition-all duration-300 overflow-hidden candidate-card"
             data-search="{{ strtolower($akun->nama . ' ' . ($akun->peserta?->nisn ?? '') . ' ' . ($akun->peserta?->daftar?->asal_sekolah ?? '')) }}">
            <div class="flex flex-col lg:flex-row">

                {{-- Left: Colored Status Bar --}}
                <div class="lg:w-2 w-full h-2 lg:h-auto flex-shrink-0 {{ $isLayak ? 'bg-gradient-to-b from-amber-400 to-yellow-500' : ($isTidak ? 'bg-gradient-to-b from-red-400 to-red-600' : 'bg-gradient-to-b from-slate-200 to-slate-300') }}"></div>

                {{-- Main Content --}}
                <div class="flex-1 p-6 flex flex-col xl:flex-row gap-6 items-start xl:items-center">

                    {{-- Identity --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 flex-wrap mb-2">
                            <h3 class="text-slate-900 font-black text-base uppercase tracking-tight">{{ $akun->nama }}</h3>

                            {{-- Status Badge --}}
                            @if($isLayak)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-200 text-amber-700 rounded-full text-[10px] font-black uppercase tracking-wider">
                                    <span class="iconify" data-icon="solar:cup-star-bold"></span>
                                    {{ $statusWawancara }}
                                </span>
                            @elseif($isTidak)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 border border-red-200 text-red-600 rounded-full text-[10px] font-black uppercase tracking-wider">
                                    <span class="iconify" data-icon="solar:close-circle-bold"></span>
                                    Tidak Layak
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 border border-slate-200 text-slate-400 rounded-full text-[10px] font-black uppercase tracking-wider">
                                    <span class="iconify" data-icon="solar:clock-circle-bold"></span>
                                    Belum Dinilai
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-slate-400 font-medium">
                            <span class="flex items-center gap-1">
                                <span class="iconify text-slate-400" data-icon="solar:card-bold"></span>
                                NISN {{ $akun->peserta?->nisn ?? '-' }}
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="iconify text-slate-400" data-icon="solar:buildings-3-bold"></span>
                                {{ $akun->peserta?->daftar?->asal_sekolah ?? '-' }}
                            </span>
                            <span class="flex items-center gap-1 text-slate-500 font-bold">
                                <span class="iconify text-amber-500" data-icon="solar:bookmark-bold"></span>
                                Pil. 1: {{ explode(' | ', $akun->peserta?->pilihan_prodi ?? '')[0] ?? '-' }}
                            </span>
                            <span class="flex items-center gap-1 text-slate-500 font-bold">
                                <span class="iconify text-amber-500" data-icon="solar:bookmark-bold"></span>
                                Pil. 2: {{ explode(' | ', $akun->peserta?->pilihan_prodi ?? '')[1] ?? '-' }}
                            </span>
                        </div>

                        {{-- Rekomendasi & Final Tags --}}
                        <div class="flex flex-wrap gap-2 mt-3">
                            @if($rekBeasiswa)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 border border-purple-100 text-purple-700 rounded-xl text-[10px] font-black uppercase tracking-wider">
                                    <span class="iconify" data-icon="solar:document-text-bold"></span>
                                    Rek. Wawancara: {{ $rekBeasiswa }}
                                </span>
                            @endif
                            @if($rekProdi1)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 border border-blue-100 text-blue-700 rounded-xl text-[10px] font-black uppercase tracking-wider">
                                    <span class="iconify" data-icon="solar:bookmark-square-bold"></span>
                                    Rek. Prodi 1: {{ $rekProdi1 }}
                                </span>
                            @endif
                            @if($rekProdi2)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 border border-blue-100 text-blue-700 rounded-xl text-[10px] font-black uppercase tracking-wider">
                                    <span class="iconify" data-icon="solar:bookmark-square-bold"></span>
                                    Rek. Prodi 2: {{ $rekProdi2 }}
                                </span>
                            @endif
                            @if($nominalFinal)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-wider">
                                    <span class="iconify" data-icon="solar:medal-star-bold"></span>
                                    Keputusan Final: {{ $nominalFinal }}
                                </span>
                            @endif
                        </div>

                    </div>{{-- /Identity --}}

                    {{-- Action Form --}}
                    <div class="w-full xl:w-auto flex-shrink-0">
                        <form action="{{ route('admin.wawancara_bod.update', $akun->peserta?->daftar?->id ?? 0) }}" method="POST" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                            @csrf
                            <div class="relative">
                                <select name="status_wawancara_bod"
                                    class="appearance-none pl-4 pr-10 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:border-amber-400 focus:bg-white outline-none transition-all cursor-pointer min-w-[180px]">
                                    <option value="" {{ empty($statusWawancara) ? 'selected' : '' }}>— Pilih Kelayakan —</option>
                                    
                                    {{-- Backward compatibility support --}}
                                    @if(in_array($statusWawancara, ['Layak (100%)', 'Layak (75%)', 'Layak (50%)', 'Layak (25%)']))
                                        <optgroup label="Pilihan Lama (Belum Pilih Kelas)">
                                            <option value="{{ $statusWawancara }}" selected>{{ $statusWawancara }} (Silakan pilih Reguler/Eksekutif di bawah)</option>
                                        </optgroup>
                                    @endif

                                    <optgroup label="Layak - Kelas Reguler">
                                        <option value="Layak (100% Reguler)" {{ $statusWawancara === 'Layak (100% Reguler)' ? 'selected' : '' }}>✦ Layak 100% — Reguler</option>
                                        <option value="Layak (75% Reguler)"  {{ $statusWawancara === 'Layak (75% Reguler)'  ? 'selected' : '' }}>✦ Layak 75% — Reguler</option>
                                    </optgroup>
                                    <optgroup label="Layak - Kelas Eksekutif">
                                        <option value="Layak (100% Eksekutif)" {{ $statusWawancara === 'Layak (100% Eksekutif)' ? 'selected' : '' }}>✦ Layak 100% — Eksekutif</option>
                                        <option value="Layak (75% Eksekutif)"  {{ $statusWawancara === 'Layak (75% Eksekutif)'  ? 'selected' : '' }}>✦ Layak 75% — Eksekutif</option>
                                    </optgroup>
                                    <optgroup label="Tidak Layak">
                                        <option value="Tidak Layak" {{ $statusWawancara === 'Tidak Layak' ? 'selected' : '' }}>✕ Tidak Layak</option>
                                    </optgroup>
                                </select>
                                <span class="iconify absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs" data-icon="solar:alt-arrow-down-bold"></span>
                            </div>

                            <div class="relative">
                                <select name="rekomendasi_prodi_1"
                                    class="appearance-none pl-4 pr-10 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:border-amber-400 focus:bg-white outline-none transition-all cursor-pointer min-w-[200px]">
                                    <option value="">— Pilih Rekomendasi Prodi —</option>
                                    @foreach(['Sains Komunikasi', 'Desain Komunikasi Visual (DKV)', 'Manajemen', 'Akuntansi', 'Sistem Informasi', 'Pendidikan Bahasa Inggris', 'Pendidikan Matematika', 'Ilmu Komputer'] as $prodi)
                                        <option value="{{ $prodi }}" {{ ($rekProdi1 === $prodi) ? 'selected' : '' }}>{{ $prodi }}</option>
                                    @endforeach
                                </select>
                                <span class="iconify absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs" data-icon="solar:alt-arrow-down-bold"></span>
                            </div>

                            <button type="submit"
                                class="px-5 py-3 bg-slate-900 hover:bg-amber-500 text-white rounded-xl text-[10px] font-black uppercase tracking-wider transition-all shadow-sm hover:shadow-amber-200 flex items-center gap-1.5 whitespace-nowrap justify-center">
                                <span class="iconify" data-icon="solar:diskette-bold"></span>
                                Simpan
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        @endforeach
    </div>
@endif

<div id="noResultsMsg" class="hidden bg-white rounded-[2rem] border border-dashed border-slate-200 p-12 text-center">
    <span class="iconify text-slate-200 text-5xl mb-3 block" data-icon="solar:magnifer-bold"></span>
    <p class="text-slate-400 font-black text-sm uppercase tracking-widest">Tidak ada kandidat ditemukan</p>
    <p class="text-slate-300 text-xs mt-1">Coba kata kunci lain</p>
</div>

<script>
function filterCandidates() {
    const query = document.getElementById('searchInput').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.candidate-card');
    const clearBtn = document.getElementById('clearSearch');
    const noResults = document.getElementById('noResultsMsg');

    clearBtn.style.display = query ? 'block' : 'none';

    let visible = 0;
    cards.forEach(card => {
        const searchData = card.getAttribute('data-search') || '';
        const match = !query || searchData.includes(query);
        card.style.display = match ? '' : 'none';
        if (match) visible++;
    });

    document.getElementById('candidateCount').textContent = visible;
    noResults.classList.toggle('hidden', visible > 0);
}
</script>
@endsection

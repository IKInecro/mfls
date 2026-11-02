@extends('layouts.admin')

@push('styles')
<style>
    @media print {
        @page {
            size: A4;
            margin: 0.4cm 0.8cm;
        }

        /* Hide all UI elements */
        aside, header, nav, .flex.gap-2, a[href*="index"], button, .no-print, .iconify, .iconify-inline {
            display: none !important;
        }

        /* Essential Reset */
        main {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background: white !important;
        }

        body {
            background: white !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: black !important;
            font-size: 7.5pt;
            line-height: 1.1;
            zoom: 0.8;
        }

        /* Extremely Compact Sectioning */
        .section-box {
            background-color: transparent !important;
            border: none !important;
            border-bottom: 0.5px solid #eee !important;
            border-radius: 0 !important;
            padding: 4px 0 !important;
            margin-bottom: 6px !important;
            box-shadow: none !important;
            page-break-inside: avoid;
        }

        h3 { 
            font-size: 8pt !important; 
            margin-bottom: 4px !important;
            padding-bottom: 2px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #1a1a1a;
            border-left: 3px solid #F97316;
            padding-left: 6px;
        }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 7pt !important;
            margin-top: 3px;
        }
        
        table th {
            background-color: #f8fafc !important;
            border: 0.5px solid #e2e8f0 !important;
            padding: 2px 4px !important;
            font-weight: 800;
        }

        table td {
            border: 0.5px solid #e2e8f0 !important;
            padding: 2px 4px !important;
        }

        .bg-slate-50, .bg-blue-50, .bg-emerald-50, .bg-orange-50, .bg-white {
            background: transparent !important;
        }

        .text-blue-600 { color: #2563eb !important; }
        .text-orange-600 { color: #f97316 !important; }
        
        form { display: none !important; }
        .print-only { display: block !important; }
        
        /* Compact Header */
        .official-header {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 12px;
            border-bottom: 1.5px solid #333;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }

        .header-logo {
            width: 45px;
            height: auto;
        }

        .header-text h1 {
            font-size: 11pt !important;
            font-weight: 900 !important;
            margin: 0;
        }

        .header-text p {
            margin: 0;
            font-size: 7.5pt;
            line-height: 1.1;
        }

        /* Compact Grid for Print */
        .grid { display: flex !important; flex-wrap: wrap !important; gap: 8px !important; }
        .md\:grid-cols-2 > div { width: calc(50% - 4px) !important; }
        .lg\:grid-cols-3 > div:first-child { width: 100% !important; }
        
        /* Compact Profile Detail */
        .w-28 { width: 45px !important; height: 60px !important; }
        .text-2xl { font-size: 11pt !important; }
        .text-5xl { font-size: 20pt !important; }
        
        /* Hide non-essential sections for print if needed */
        .preview-decision { margin-top: 4px !important; padding: 6px !important; }
        
        /* Compact Signature and Footer */
        .print-only.mt-12 {
            margin-top: 1.5rem !important;
            padding-top: 0.5rem !important;
        }
        .mb-20 {
            margin-bottom: 2rem !important;
        }
    }

    .print-only { display: none; }
</style>
@endpush

@section('content')
<!-- Official Print Header -->
<div class="official-header hidden">
    <img src="{{ asset('icon/loog.png') }}" class="header-logo" alt="Logo MFLS">
    <div class="header-text">
        <h1>MNC UNIVERSITY</h1>
        <p class="text-orange-500">MNC FUTURE LEADER SCHOLARSHIP (MFLS) AJARAN 2027</p>
        <p>Kompleks MNC Studios, Jl. Perjuangan, Kebon Jeruk, Jakarta Barat 11530</p>
        <div class="mt-2 text-[11pt] font-black uppercase text-slate-800">Rekapitulasi Hasil Seleksi Beasiswa</div>
    </div>
</div>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 no-print">
    <div>
        <h2 class="text-xl sm:text-2xl font-black text-slate-800">Laporan Utama Beasiswa</h2>
        <p class="text-slate-500 font-medium text-sm">Rekapitulasi: {{ $user->nama }}</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-2">
        <button onclick="window.print()" class="px-6 py-3 bg-slate-900 text-white rounded-2xl text-[10px] font-black shadow-xl shadow-slate-200 hover:scale-105 transition-all uppercase tracking-[0.2em] flex items-center justify-center gap-2">
            <span class="iconify" data-icon="solar:printer-bold"></span> Cetak PDF / Laporan
        </button>
        <a href="{{ route('admin.beasiswa.index') }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-2xl text-[10px] font-black shadow-sm hover:bg-slate-50 transition-all uppercase tracking-[0.2em] flex items-center justify-center gap-2">
            <span class="iconify" data-icon="solar:arrow-left-bold"></span> Kembali
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
        <!-- 1. Identitas Global -->
        <div class="section-box bg-white p-5 sm:p-8 rounded-[2rem] sm:rounded-[2.5rem] shadow-sm border border-slate-100">
            @php
                $fotoPath = $user->peserta->berkas->foto ?? null;
                $fotoUrl = $fotoPath ? asset('storage/' . $fotoPath) : "https://ui-avatars.com/api/?name=".urlencode($user->nama)."&background=0F172A&color=fff";
            @endphp
            {{-- Foto + Info: column di mobile, row di sm+ --}}
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
                <div class="w-24 h-32 sm:w-28 sm:h-36 rounded-2xl overflow-hidden shadow-xl border-4 border-white ring-1 ring-slate-100 flex-shrink-0">
                    <img src="{{ $fotoUrl }}" class="w-full h-full object-cover" alt="Foto">
                </div>
                <div class="flex-grow w-full text-center sm:text-left">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 mb-1 uppercase tracking-tight">{{ $user->nama }}</h1>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.1em] mb-4">{{ $user->peserta->nama_sekolah }} | NISN: {{ $user->peserta->nisn }}</p>
                    {{-- Grid skor: 2 kolom di mobile, 4 kolom di sm+ --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="text-center p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <div class="text-[8px] font-black text-slate-400 uppercase mb-1">Rata Rapor</div>
                            <div class="text-sm font-black text-slate-800">{{ number_format($rataRataAkademik, 2) }}</div>
                        </div>
                        <div class="text-center p-3 bg-blue-50/50 rounded-2xl border border-blue-100">
                            <div class="text-[8px] font-black text-blue-400 uppercase mb-1">Skor Mentor</div>
                            <div class="text-sm font-black text-blue-600">{{ number_format($rataRataMentor, 2) }}</div>
                        </div>
                        <div class="text-center p-3 bg-orange-50/50 rounded-2xl border border-orange-100">
                            <div class="text-[8px] font-black text-orange-400 uppercase mb-1">Wawancara</div>
                            <div class="text-sm font-black text-orange-600">{{ number_format($rataRataAkademikFinal, 2) }}</div>
                        </div>
                        <div class="text-center p-3 bg-emerald-50/50 rounded-2xl border border-emerald-100">
                            <div class="text-[8px] font-black text-emerald-400 uppercase mb-1">Skor CBT (TBA/TBI)</div>
                            <div class="text-sm font-black text-emerald-600">{{ number_format($rataRataCbt, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Rincian Nilai Raport -->
        <div class="section-box bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <h3 class="text-sm font-black text-slate-800 mb-6 uppercase tracking-widest flex items-center gap-3">
                <span class="iconify text-xl text-blue-600" data-icon="solar:notebook-bold"></span>
                Rincian Nilai Rapor (Semester 1-5)
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b-2 border-slate-100">
                            <th class="p-3 text-left font-black uppercase text-slate-400">Mata Pelajaran</th>
                            <th class="p-3 text-center font-black uppercase text-slate-400">S1</th>
                            <th class="p-3 text-center font-black uppercase text-slate-400">S2</th>
                            <th class="p-3 text-center font-black uppercase text-slate-400">S3</th>
                            <th class="p-3 text-center font-black uppercase text-slate-400">S4</th>
                            <th class="p-3 text-center font-black uppercase text-slate-400">S5</th>
                            <th class="p-3 text-center font-black uppercase text-slate-700 bg-slate-100">AVG</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($user->peserta->nilais->pluck('matpel')->unique('id') as $mp)
                        <tr>
                            <td class="p-3 font-bold text-slate-700 uppercase">{{ $mp->nama }}</td>
                            @php $mpSum = 0; $mpCount = 0; @endphp
                            @for($s=1;$s<=5;$s++)
                                @php 
                                    $n = $user->peserta->nilais->where('matpel_id', $mp->id)->where('semester', $s)->first()->nilai ?? 0;
                                    if($n > 0) { $mpSum += $n; $mpCount++; }
                                @endphp
                                <td class="p-3 text-center font-medium {{ $n > 0 ? 'text-slate-600' : 'text-slate-200' }}">{{ $n > 0 ? $n : '-' }}</td>
                            @endfor
                            <td class="p-3 text-center font-black bg-slate-50 text-blue-600">{{ $mpCount > 0 ? number_format($mpSum/$mpCount, 1) : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Review Interview Detail -->
        <div class="section-box bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <h3 class="text-sm font-black text-slate-800 mb-6 uppercase tracking-widest text-center">Rekapitulasi Penilaian Wawancara</h3>
            
            @foreach($user->peserta->penilaianAkademiks as $eval)
            <div class="space-y-4">
                <div class="flex justify-between items-center border-b-2 border-orange-50 pb-2">
                    <span class="text-[10px] font-black text-orange-600 uppercase tracking-widest">Wawancara oleh: {{ $eval->penilai->nama ?? 'Dosen' }}</span>
                    <div class="px-3 py-1 bg-orange-500 text-white text-[10px] font-black rounded-lg">TOTAL SKOR: {{ number_format($eval->total_akhir, 2) }}</div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-[10px]">
                    <div class="flex justify-between p-2 bg-slate-50 rounded-xl"><span>Motivasi & Komitmen (25%)</span><span class="font-black">{{ $eval->wawancara_motivasi }}</span></div>
                    <div class="flex justify-between p-2 bg-slate-50 rounded-xl"><span>Prestasi Akademik (20%)</span><span class="font-black">{{ $eval->wawancara_prestasi }}</span></div>
                    <div class="flex justify-between p-2 bg-slate-50 rounded-xl"><span>Karakter & Integritas (20%)</span><span class="font-black">{{ $eval->wawancara_karakter }}</span></div>
                    <div class="flex justify-between p-2 bg-slate-50 rounded-xl"><span>Kontribusi & Kepemimpinan (20%)</span><span class="font-black">{{ $eval->wawancara_kontribusi }}</span></div>
                    <div class="flex justify-between p-2 bg-slate-50 rounded-xl"><span>Kemampuan Komunikasi (15%)</span><span class="font-black">{{ $eval->wawancara_komunikasi }}</span></div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div class="p-3 bg-blue-50/50 rounded-xl border border-blue-100 text-[10px]">
                        <span class="block font-black text-blue-600 uppercase mb-1">Rekomendasi Kelulusan</span>
                        <span class="font-bold text-slate-700">{{ $eval->rekomendasi_akhir ?: '-' }}</span>
                    </div>
                    <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-100 text-[10px]">
                        <span class="block font-black text-emerald-600 uppercase mb-1">Skema Beasiswa</span>
                        <span class="font-bold text-slate-700">{{ $eval->rekomendasi_beasiswa ?: '-' }}</span>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-slate-100 rounded-2xl flex justify-between items-center text-slate-800">
                    <span class="text-[10px] font-black uppercase tracking-widest">Catatan Wawancara:</span>
                    <span class="text-xs font-semibold italic">"{{ $eval->catatan ?: '-' }}"</span>
                </div>
            </div>
            @endforeach

            @php
                $statusBod = $user->peserta->daftar->status_wawancara_bod ?? null;
                $rekProdi1 = $user->peserta->penilaianAkademiks->first()?->rekomendasi_prodi_1 ?? null;
            @endphp
            @if($statusBod)
            <div class="mt-6 p-6 bg-amber-50/50 border border-amber-200 rounded-3xl">
                <div class="flex justify-between items-center border-b-2 border-amber-100 pb-2 mb-4">
                    <span class="text-[10px] font-black text-amber-700 uppercase tracking-widest flex items-center gap-1.5">
                        <span class="iconify text-amber-500 text-sm" data-icon="solar:cup-star-bold"></span>
                        Keputusan Kelayakan Wawancara BoD
                    </span>
                    <span class="px-3 py-1 bg-amber-500 text-white text-[10px] font-black rounded-lg uppercase">BoD Decision</span>
                </div>
                <div class="flex flex-col sm:flex-row justify-between gap-4">
                    <div>
                        <div class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Status Kelayakan</div>
                        <div class="text-sm font-black text-slate-800 uppercase mt-0.5">{{ $statusBod }}</div>
                    </div>
                    @if($rekProdi1)
                    <div>
                        <div class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Rekomendasi Program Studi</div>
                        <div class="text-sm font-black text-slate-800 uppercase mt-0.5">{{ $rekProdi1 }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="section-box no-print bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <h3 class="text-sm font-black text-slate-800 mb-4 uppercase tracking-widest flex items-center gap-2">
                <span class="iconify text-primary-orange" data-icon="solar:magic-stick-3-bold-duotone"></span>
                Hasil Pemetaan Diri (AI Report)
            </h3>
            @php
                $pemetaan = $user->peserta->jawabanUjians->filter(function($j) {
                    return str_contains(strtolower($j->ujian->nama ?? ''), 'pemetaan diri');
                })->first();
            @endphp
            @if($pemetaan)
                <div class="p-6 bg-slate-900 rounded-3xl text-white relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-orange-400/10 rounded-full blur-2xl"></div>
                    <div class="relative z-10">
                        <div class="text-xs md:text-sm leading-relaxed text-slate-300">
                            {!! nl2br(e($pemetaan->getOrGeneratePemetaanReport())) !!}
                        </div>
                    </div>
                </div>
            @else
                <div class="p-6 bg-slate-50 rounded-3xl border border-dashed border-slate-200 text-center">
                    <p class="text-xs text-slate-400 font-bold italic uppercase tracking-widest">Belum ada hasil analisis AI</p>
                </div>
            @endif
        </div>

        <div class="section-box no-print bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <h3 class="text-sm font-black text-slate-800 mb-4 uppercase tracking-widest">Kesimpulan Evaluasi Mentor</h3>
            <div class="space-y-3">
                @forelse($user->peserta->penilaianMentors as $eval)
                <div class="p-4 bg-slate-50 rounded-2xl flex gap-4 items-start">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center font-black text-[10px] flex-shrink-0">M</div>
                    <div>
                        <p class="text-[10px] font-black text-blue-600 uppercase mb-1">{{ $eval->mentor->nama }} <span class="text-slate-400 ml-2">({{ number_format($eval->nilai, 2) }})</span></p>
                        <p class="text-xs text-slate-600 font-medium italic">"{{ $eval->catatan ?: 'Penilaian kuantitatif.' }}"</p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 font-bold italic">Belum ada evaluasi mentor.</p>
                @endforelse
            </div>
        </div>

        <!-- 5. Dispensasi Waktu Ujian -->
        @if(in_array(auth()->user()->role, ['admin', 'palugada']))
        <div class="no-print section-box bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 mt-6 mb-6">
            <h3 class="text-sm font-black text-slate-800 mb-6 uppercase tracking-widest flex items-center gap-3">
                <span class="iconify text-xl text-yellow-600" data-icon="solar:alarm-add-bold"></span>
                Dispensasi Waktu Ujian Khusus
            </h3>
            
            @php
                $ujians = \App\Models\Ujian::where('is_active', true)->get();
                $dispensasiUjian = \App\Models\DispensasiUjian::where('peserta_id', $user->peserta->id ?? 0)->get();
            @endphp
            
            <form action="{{ route('admin.pendaftar.dispensasi_ujian', $user->id) }}" method="POST" class="mb-6 p-6 bg-yellow-50 rounded-3xl border border-yellow-100 flex flex-col md:flex-row gap-4 items-end">
                @csrf
                <div class="flex-grow w-full">
                    <label class="block text-[10px] font-black text-yellow-800 uppercase tracking-widest mb-2">Pilih Ujian Aktif</label>
                    <select name="ujian_id" required class="w-full px-4 py-3 bg-white border border-yellow-200 rounded-2xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="">-- Pilih Ujian --</option>
                        @foreach($ujians as $u)
                            <option value="{{ $u->id }}">{{ $u->nama }} (Default: {{ $u->durasi ?? 60 }} mnt)</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-48">
                    <label class="block text-[10px] font-black text-yellow-800 uppercase tracking-widest mb-2">Tambahan (Menit)</label>
                    <input type="number" name="tambahan_menit" required min="0" placeholder="Misal: 15" class="w-full px-4 py-3 bg-white border border-yellow-200 rounded-2xl text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <button type="submit" class="w-full md:w-auto px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-black rounded-2xl transition-all shadow-md flex items-center justify-center gap-2">
                    <span class="iconify" data-icon="solar:disk-bold"></span> Simpan
                </button>
            </form>

            @if($dispensasiUjian->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($dispensasiUjian as $d)
                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-between">
                            <div>
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $d->ujian->nama ?? 'Ujian Dihapus' }}</div>
                                <div class="text-sm font-black text-yellow-600">+{{ $d->tambahan_menit }} Menit Ekstra</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-[10px] text-slate-400 font-medium italic">*Peserta yang sedang ujian harus me-refresh halamannya agar waktu masuk.</div>
            @endif
        </div>
        @endif
    </div>

    <!-- Right Sidebar / Action -->
    <div class="space-y-6">
        <div class="no-print bg-slate-900 p-8 rounded-[3rem] text-white shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-400/10 rounded-full translate-x-12 -translate-y-12"></div>
            
            <h3 class="text-[10px] font-black mb-8 uppercase tracking-[0.3em] text-slate-500 border-none">Panel Keputusan Final</h3>
            <form action="{{ route('admin.beasiswa.update', $user->id) }}" method="POST" class="space-y-6 relative">
                @csrf
                <input type="hidden" name="status" value="lulus">
                
                <div class="p-6 bg-white/5 border border-white/10 rounded-[2rem] text-center backdrop-blur-md">
                    <div class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Skor Akhir</div>
                    @php
                        $finalScore = ($rataRataAkademik + $rataRataMentor + $rataRataAkademikFinal + $rataRataCbt) / 4;
                    @endphp
                    <div class="text-5xl font-black text-yellow-400">{{ number_format($finalScore, 2) }}</div>
                </div>

                {{-- Kelayakan BoD --}}
                @php
                    $statusBod = $user->peserta->daftar->status_wawancara_bod ?? null;
                @endphp
                <div class="p-4 bg-white/5 border border-white/10 rounded-2xl text-center backdrop-blur-md">
                    <div class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Hasil Wawancara BoD</div>
                    @if($statusBod)
                        <div class="text-xs font-black text-yellow-400 uppercase tracking-wider">{{ $statusBod }}</div>
                    @else
                        <div class="text-[10px] font-bold text-slate-400 italic">BELUM DINILAI BoD</div>
                    @endif
                </div>

                <div class="space-y-2">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Program Studi</label>
                    @php 
                        $savedProdi = $user->peserta->penilaianAkademiks->first()?->rekomendasi_prodi_1 ?? $user->peserta->pilihan_prodi ?? ''; 
                    @endphp
                    <select name="prodi" required class="w-full bg-slate-800 border border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold text-white outline-none focus:border-yellow-400 appearance-none shadow-sm">
                        <option value="">-- Pilih Prodi --</option>
                        <option value="Sains Komunikasi" {{ str_contains($savedProdi, 'Sains Komunikasi') ? 'selected' : '' }}>Sains Komunikasi</option>
                        <option value="Desain Komunikasi Visual" {{ str_contains($savedProdi, 'Desain Komunikasi Visual') ? 'selected' : '' }}>Desain Komunikasi Visual</option>
                        <option value="Manajemen" {{ str_contains($savedProdi, 'Manajemen') ? 'selected' : '' }}>Manajemen</option>
                        <option value="Akuntansi" {{ str_contains($savedProdi, 'Akuntansi') ? 'selected' : '' }}>Akuntansi</option>
                        <option value="Pendidikan Bahasa Inggris" {{ str_contains($savedProdi, 'Pendidikan Bahasa Inggris') ? 'selected' : '' }}>Pendidikan Bahasa Inggris</option>
                        <option value="Pendidikan Matematika" {{ str_contains($savedProdi, 'Pendidikan Matematika') ? 'selected' : '' }}>Pendidikan Matematika</option>
                        <option value="Ilmu Komputer" {{ str_contains($savedProdi, 'Ilmu Komputer') ? 'selected' : '' }}>Ilmu Komputer</option>
                        <option value="Sistem Informasi" {{ str_contains($savedProdi, 'Sistem Informasi') ? 'selected' : '' }}>Sistem Informasi</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Jenis Kelas</label>
                    @php 
                        $savedKelas = $user->peserta->penilaianAkademiks->first()?->rekomendasi_kelas ?? ''; 
                    @endphp
                    <select name="kelas" required class="w-full bg-slate-800 border border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold text-white outline-none focus:border-yellow-400 appearance-none shadow-sm">
                        <option value="">-- Pilih Kelas --</option>
                        <option value="Reguler" {{ $savedKelas == 'Reguler' ? 'selected' : '' }}>Reguler</option>
                        <option value="Karyawan" {{ $savedKelas == 'Karyawan' ? 'selected' : '' }}>Karyawan</option>
                        <option value="Exellent" {{ $savedKelas == 'Exellent' ? 'selected' : '' }}>Exellent</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Skema Beasiswa</label>
                    <select name="nominal_beasiswa" required class="w-full bg-slate-800 border border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold text-white outline-none focus:border-yellow-400 appearance-none shadow-sm">
                        <option value="">-- Pilih Skema --</option>
                        <option value="75%" {{ $user->peserta->daftar->nominal_beasiswa == '75%' ? 'selected' : '' }}>Beasiswa 75% </option>
                        <option value="100%" {{ $user->peserta->daftar->nominal_beasiswa == '100%' ? 'selected' : '' }}>Beasiswa 100% </option>
                    </select>
                </div>

                <button type="submit" class="w-full py-5 bg-yellow-400 text-slate-900 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-white hover:scale-105 active:scale-95 transition-all shadow-xl shadow-yellow-400/20 mb-3">
                    SIMPAN HASIL SELEKSI
                </button>
            </form>

            @if($user->peserta->daftar->status === 'lulus' || !empty($user->peserta->daftar->nominal_beasiswa))
            <form action="{{ route('admin.beasiswa.update', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan kelulusan & menghapus beasiswa kandidat ini?');">
                @csrf
                <input type="hidden" name="status" value="menunggu">
                <input type="hidden" name="nominal_beasiswa" value="">
                <button type="submit" class="w-full py-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-red-500 hover:text-white transition-all shadow-sm">
                    <span class="iconify inline-block mr-1" data-icon="solar:trash-bin-trash-bold"></span> BATALKAN KELULUSAN
                </button>
            </form>
            @endif
        </div>

        <div class="preview-decision print-only section-box bg-white p-6 rounded-2xl border-2 border-slate-900">
            <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4">Ringkasan Hasil Resmi</h4>
            <div class="space-y-3">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-[10px] font-bold text-slate-500">Skor Gabungan Akhir:</span>
                    <span class="text-xs font-black">{{ number_format($finalScore, 2) }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-[10px] font-bold text-slate-500">Skema Beasiswa:</span>
                    <span class="text-xs font-black text-blue-600">{{ $user->peserta->daftar->nominal_beasiswa ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[10px] font-bold text-slate-500">Status Seleksi:</span>
                    <span class="text-xs font-black text-emerald-600 uppercase tracking-wider">Lulus Seleksi Final</span>
                </div>
            </div>
        </div>

        <div class="no-print bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Sumber Verifikasi</h4>
            <div class="space-y-4">
                @if($user->peserta->link_ig)
                    <a href="{{ $user->peserta->link_ig }}" target="_blank" class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl group transition-all hover:bg-blue-50">
                        <span class="text-[10px] font-black text-slate-600 group-hover:text-blue-600 uppercase">Instagram Portfolio</span>
                        <span class="iconify" data-icon="solar:arrow-right-up-bold"></span>
                    </a>
                @endif
                @if($user->peserta->berkas && $user->peserta->berkas->motivasi_video)
                    <a href="{{ $user->peserta->berkas->motivasi_video }}" target="_blank" class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl group transition-all hover:bg-purple-50">
                        <span class="text-[10px] font-black text-purple-600 uppercase">Pitch Deck / Video</span>
                        <span class="iconify" data-icon="solar:videocamera-bold"></span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Print Footer & Signature -->
<div class="print-only mt-12 px-10 border-t border-slate-100 pt-10">
    <div class="flex justify-end">
        <div class="text-center w-64">
            <p class="text-[9pt] mb-20">Jakarta, {{ date('d F Y') }}<br>Mengajukan,<br>Panitia Beasiswa MFLS Ajaran 2027</p>
            <div class="border-b-2 border-slate-900 w-full mb-2"></div>
            <p class="text-[9pt] font-black uppercase tracking-widest text-slate-900">TIM ADMISI MNC UNIVERSITY</p>
        </div>
    </div>
</div>
@endsection

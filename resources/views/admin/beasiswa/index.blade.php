@extends('layouts.admin')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-black text-slate-800">Database Terpusat & Beasiswa</h2>
        <p class="text-slate-500">Rekapitulasi seluruh data: Administrasi, Akademik, dan Penilaian Mentor.</p>
    </div>
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('admin.beasiswa.import.index') }}" class="px-6 py-3 bg-emerald-600 text-white rounded-2xl text-xs font-black shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition-all uppercase tracking-widest flex items-center gap-2">
            <span class="iconify text-lg" data-icon="solar:import-bold"></span> Import Massal
        </a>
        <a href="{{ route('admin.export') }}" class="px-6 py-3 bg-green-600 text-white rounded-2xl text-xs font-black shadow-lg shadow-green-200 hover:bg-green-700 transition-all uppercase tracking-widest flex items-center gap-2">
            <span class="iconify text-lg" data-icon="solar:file-download-bold"></span> Export Laporan
        </a>
    </div>
</div>

<!-- Advanced Filter Panel -->
<div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm mb-8">
    <form method="GET" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pencarian Cepat</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / Kode Ref..." 
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 outline-none text-sm font-bold text-slate-700">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Asal Sekolah</label>
                <input type="text" name="sekolah" value="{{ request('sekolah') }}" placeholder="Semua Sekolah..." 
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 outline-none text-sm font-bold text-slate-700">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Prodi</label>
                <select name="prodi" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 outline-none text-sm font-bold text-slate-700">
                    <option value="">Semua Prodi</option>
                    <option value="Sains Komunikasi" {{ request('prodi') == 'Sains Komunikasi' ? 'selected' : '' }}>Sains Komunikasi</option>
                    <option value="Desain Komunikasi Visual" {{ request('prodi') == 'Desain Komunikasi Visual' ? 'selected' : '' }}>Desain Komunikasi Visual</option>
                    <option value="Manajemen" {{ request('prodi') == 'Manajemen' ? 'selected' : '' }}>Manajemen</option>
                    <option value="Akuntansi" {{ request('prodi') == 'Akuntansi' ? 'selected' : '' }}>Akuntansi</option>
                    <option value="Pendidikan Bahasa Inggris" {{ request('prodi') == 'Pendidikan Bahasa Inggris' ? 'selected' : '' }}>Pend. Bahasa Inggris</option>
                    <option value="Pendidikan Matematika" {{ request('prodi') == 'Pendidikan Matematika' ? 'selected' : '' }}>Pend. Matematika</option>
                    <option value="Ilmu Komputer" {{ request('prodi') == 'Ilmu Komputer' ? 'selected' : '' }}>Ilmu Komputer</option>
                    <option value="Sistem Informasi" {{ request('prodi') == 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Kelengkapan Berkas</label>
                <select name="berkas_status" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 outline-none text-sm font-bold text-slate-700">
                    <option value="">Semua Status</option>
                    <option value="lengkap" {{ request('berkas_status') == 'lengkap' ? 'selected' : '' }}>Berkas Lengkap</option>
                    <option value="belum" {{ request('berkas_status') == 'belum' ? 'selected' : '' }}>Belum Lengkap</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Beasiswa</label>
                <select name="beasiswa" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 outline-none text-sm font-bold text-slate-700">
                    <option value="">Semua Beasiswa</option>
                    <option value="100%" {{ request('beasiswa') == '100%' ? 'selected' : '' }}>100%</option>
                    <option value="75%" {{ request('beasiswa') == '75%' ? 'selected' : '' }}>75%</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Kelas</label>
                <select name="kelas" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 outline-none text-sm font-bold text-slate-700">
                    <option value="">Semua Kelas</option>
                    <option value="Reguler" {{ request('kelas') == 'Reguler' ? 'selected' : '' }}>Reguler</option>
                    <option value="Karyawan" {{ request('kelas') == 'Karyawan' ? 'selected' : '' }}>Karyawan</option>
                    <option value="Exellent" {{ request('kelas') == 'Exellent' ? 'selected' : '' }}>Exellent</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Rank Nilai (Min)</label>
                <select name="min_nilai" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 outline-none text-sm font-bold text-slate-700">
                    <option value="">Semua Nilai</option>
                    <option value="90" {{ request('min_nilai') == '90' ? 'selected' : '' }}>Nilai > 90</option>
                    <option value="80" {{ request('min_nilai') == '80' ? 'selected' : '' }}>Nilai > 80</option>
                    <option value="70" {{ request('min_nilai') == '70' ? 'selected' : '' }}>Nilai > 70</option>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-3 rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 flex items-center justify-center gap-2 text-sm font-bold h-[46px]">
                    <span class="iconify" data-icon="solar:magnifer-linear"></span> Terapkan Filter
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Database Table -->
<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-400 font-black uppercase text-[10px] tracking-[0.2em]">
                <tr>
                    <th class="px-6 py-5">Mahasiswa / Pendaftar</th>
                    <th class="px-6 py-5 text-center">Sekolah</th>
                    <th class="px-6 py-5 text-center">Kode Ref</th>
                    <th class="px-6 py-5 text-left">Minat Prodi</th>
                    <th class="px-6 py-5 text-center">Akademik</th>
                    <th class="px-6 py-5 text-center">Mentor Eval</th>
                    <th class="px-6 py-5 text-center">Berkas</th>
                    <th class="px-6 py-5 text-center">Rekomendasi</th>
                    <th class="px-6 py-5 text-right">Opsi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 uppercase">
                @foreach($pendaftars as $akun)
                @php
                    $avgAkademik = $akun->peserta->daftar->rata_rata_nilai ?? 0;
                    $mentorScore = $akun->peserta->penilaianMentors->avg('nilai') ?? 0;
                    
                    // Cek kelengkapan berkas
                    $totalBerkas = 8;
                    $countBerkas = 0;
                    if($b = $akun->peserta->berkas) {
                        if($b->foto) $countBerkas++;
                        if($b->rapor1) $countBerkas++;
                        if($b->rapor2) $countBerkas++;
                        if($b->rapor3) $countBerkas++;
                        if($b->rapor4) $countBerkas++;
                        if($b->rapor5) $countBerkas++;
                        if($b->ijazah) $countBerkas++;
                        if($b->motivasi_video) $countBerkas++;
                    }
                    // Prodi Logic
                    $isDKV = ($akun->peserta->pilihan_prodi ?? '') == 'Desain Komunikasi Visual';
                @endphp
                <tr class="hover:bg-slate-50/50 transition-all group">
                    <td class="px-6 py-4">
                        <div class="font-black text-slate-800">{{ $akun->nama }}</div>
                        <div class="text-[9px] font-bold text-blue-500 tracking-widest">{{ $akun->peserta->nisn }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="text-xs font-bold text-slate-500">{{ $akun->peserta->nama_sekolah }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-yellow-50 text-yellow-700 rounded-lg text-[10px] font-black border border-yellow-100">
                            {{ $akun->peserta->daftar->kode_referral ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800 text-xs">{{ $akun->peserta->pilihan_prodi ?? '-' }}</div>
                         @if($isDKV)
                            <div class="mt-1 flex items-center gap-1.5">
                                @if(!empty($akun->peserta->berkas->surat_buta_warna))
                                    <span class="px-2 py-0.5 bg-green-50 text-green-600 rounded-md text-[9px] font-black flex items-center gap-1 border border-green-100">
                                        <span class="iconify" data-icon="solar:eye-bold"></span> OK
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 bg-red-50 text-red-600 rounded-md text-[9px] font-black flex items-center gap-1 border border-red-100">
                                        <span class="iconify" data-icon="solar:close-circle-bold"></span> Miss
                                    </span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-base font-black text-slate-800">{{ number_format($avgAkademik, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($mentorScore > 0)
                            <a href="{{ route('admin.beasiswa.show', $akun->id) }}" class="group/note">
                                <span class="text-base font-black text-blue-600 group-hover/note:underline">{{ number_format($mentorScore, 2) }}</span>
                                <span class="block text-[8px] font-black text-blue-400 opacity-0 group-hover/note:opacity-100 transition-all">LIHAT CATATAN </span>
                            </a>
                        @else
                            <span class="text-[10px] font-black text-slate-300 tracking-tighter">BELUM DINILAI</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($countBerkas == $totalBerkas)
                            <span class="w-6 h-6 bg-green-50 text-green-600 rounded-full inline-flex items-center justify-center font-black text-xs">
                                <span class="iconify" data-icon="solar:check-circle-bold"></span>
                            </span>
                        @else
                            <div class="text-[9px] font-black text-red-400">{{ $countBerkas }}/{{ $totalBerkas }} FILE</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if(!empty($akun->peserta->daftar->nominal_beasiswa))
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[9px] font-black border border-emerald-200">
                                SUDAH DIKIRIM ({{ $akun->peserta->daftar->nominal_beasiswa }})
                            </span>
                        @elseif(!empty($akun->peserta->daftar->status_wawancara_bod))
                            <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-lg text-[9px] font-black border border-amber-200">
                                BoD: {{ $akun->peserta->daftar->status_wawancara_bod }}
                            </span>
                        @elseif($avgAkademik >= 85 && $mentorScore >= 80 && $countBerkas == $totalBerkas)
                            <span class="px-3 py-1 bg-yellow-400 text-blue-900 rounded-lg text-[9px] font-black shadow-sm shadow-yellow-200 animate-pulse">SIAP BEASISWA 🏆</span>
                        @elseif($countBerkas < $totalBerkas)
                            <span class="px-3 py-1 bg-slate-100 text-slate-400 rounded-lg text-[9px] font-black italic">DOKUMEN KURANG</span>
                        @else
                            <span class="px-3 py-1 bg-blue-50 text-blue-400 rounded-lg text-[9px] font-black">BELUM DIPROSES</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.beasiswa.show', $akun->id) }}" class="inline-flex items-center gap-2 px-4 py-2 {{ !empty($akun->peserta->daftar->nominal_beasiswa) ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-200' : 'bg-blue-600 hover:bg-blue-700 shadow-blue-200' }} text-white rounded-xl text-[9px] font-black transition-all shadow-sm uppercase tracking-widest leading-none">
                            FULL REPORT <span class="iconify text-xs" data-icon="solar:document-text-bold"></span>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('content')
<!-- Header Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-[#0B1120] rounded-[2rem] p-6 text-white shadow-xl shadow-slate-950/20 border border-slate-800 relative overflow-hidden">
        <div class="absolute right-0 top-0 w-32 h-32 bg-blue-600/15 rounded-full blur-3xl -mr-10 -mt-10"></div>
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
            <span class="iconify text-[#0870B8]" data-icon="solar:users-group-rounded-bold"></span> Total Pendaftar
        </h3>
        <p class="text-4xl font-black mt-2 text-white">{{ $pendaftars->count() }}</p>
        <div class="mt-4 text-[11px] font-bold bg-white/10 text-blue-400 border border-white/10 inline-block px-3 py-1 rounded-full">Kandidat Mahasiswa</div>
    </div>
    
    <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:border-emerald-200 transition-colors">
        <div class="absolute right-0 top-0 w-20 h-20 bg-emerald-50 rounded-full blur-2xl -mr-5 -mt-5"></div>
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
            <span class="iconify text-emerald-500" data-icon="solar:check-circle-bold"></span> Dokumen Lengkap
        </h3>
        <p class="text-4xl font-black text-slate-800 mt-2">
            {{ $pendaftars->filter(fn($p) => ($p->peserta->progress ?? 0) == 100)->count() }}
        </p>
        <p class="text-xs font-bold text-emerald-600 mt-4">Sudah 100%</p>
    </div>

    <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:border-blue-200 transition-colors">
        <div class="absolute right-0 top-0 w-20 h-20 bg-blue-50 rounded-full blur-2xl -mr-5 -mt-5"></div>
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
            <span class="iconify text-blue-500" data-icon="solar:clock-circle-bold"></span> Belum Lengkap
        </h3>
        <p class="text-4xl font-black text-slate-800 mt-2">
            {{ $pendaftars->filter(fn($p) => ($p->peserta->progress ?? 0) < 100)->count() }}
        </p>
        <p class="text-xs font-bold text-blue-600 mt-4">Di Bawah 100%</p>
    </div>
</div>

<div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden">
    <!-- Toolbar -->
    <div class="p-8 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-2xl font-black text-slate-800">Data Seleksi Administrasi</h2>
            <p class="text-slate-500 text-sm font-medium mt-1">Kelola dan verifikasi berkas pendaftar.</p>
        </div>
        <div class="flex flex-wrap items-center gap-4">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                
                <div class="flex items-center gap-2 bg-slate-50 p-1.5 rounded-2xl border border-slate-100">
                    <div class="flex items-center gap-2 px-3">
                        <span class="iconify text-slate-400" data-icon="solar:calendar-linear"></span>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" 
                            class="bg-transparent border-none text-xs font-bold outline-none text-slate-600 focus:ring-0 p-0">
                    </div>
                    <div class="h-4 w-px bg-slate-200"></div>
                    <div class="flex items-center gap-2 px-3">
                        <input type="date" name="to_date" value="{{ request('to_date') }}" 
                            class="bg-transparent border-none text-xs font-bold outline-none text-slate-600 focus:ring-0 p-0">
                    </div>
                </div>

                <div class="relative">
                    <span class="iconify absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" data-icon="solar:magnifer-linear"></span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / Sekolah..." 
                        class="pl-12 pr-6 py-3 w-full md:w-64 bg-slate-50 border-transparent focus:bg-white focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 rounded-xl text-sm font-bold transition-all outline-none text-slate-600">
                </div>

                <button type="submit" class="p-3 bg-[#0870B8] text-white rounded-xl hover:bg-[#065A96] transition-all">
                    <span class="iconify text-xl" data-icon="solar:filter-bold"></span>
                </button>

                @if(request('search') || request('from_date') || request('to_date'))
                <a href="{{ route('admin.pendaftar.index') }}" class="p-3 bg-rose-50 text-rose-500 rounded-xl hover:bg-rose-100 transition-all" title="Reset Filter">
                    <span class="iconify text-xl" data-icon="solar:refresh-bold"></span>
                </a>
                @endif
            </form>

            <div class="flex items-center gap-2">
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'panitia' || in_array(auth()->user()->email, ['dept.adminis@mfls.com', 'info@beasiswamncu.com']))
                <button type="button" onclick="bulkSendCertificates()" class="px-6 py-3 bg-[#0870B8] text-white rounded-xl text-xs font-black shadow-md hover:bg-[#065A96] transition-all uppercase tracking-widest flex items-center gap-2">
                    <span class="iconify text-lg" data-icon="solar:letter-send-bold"></span>
                    Kirim Sertifikat Massal
                </button>
                @endif
                <a href="{{ route('admin.export') }}" class="px-6 py-3 bg-emerald-600 text-white rounded-xl text-xs font-black shadow-md hover:bg-emerald-700 transition-all uppercase tracking-widest flex items-center gap-2">
                    <span class="iconify text-lg" data-icon="solar:file-download-bold"></span>
                    Export
                </a>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-50">
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'panitia' || in_array(auth()->user()->email, ['dept.adminis@mfls.com', 'info@beasiswamncu.com']))
                    <th class="px-6 py-6 text-center">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 rounded text-[#0870B8] focus:ring-[#0870B8] border-slate-300">
                    </th>
                    @endif
                    <th class="px-8 py-6">Kandidat</th>
                    <th class="px-4 py-6 text-center">S1</th>
                    <th class="px-4 py-6 text-center">S2</th>
                    <th class="px-4 py-6 text-center">S3</th>
                    <th class="px-4 py-6 text-center">S4</th>
                    <th class="px-4 py-6 text-center">S5</th>
                    <th class="px-4 py-6 text-center">AVG</th>
                    <th class="px-6 py-6 text-left">Minat Prodi</th>
                    <th class="px-2 py-6 text-center">Video</th>
                    <th class="px-2 py-6 text-center">IG</th>
                    <th class="px-2 py-6 text-center">TikTok</th>
                    <th class="px-2 py-6 text-center">Twibbon</th>
                    <th class="px-6 py-6 text-center">Berkas</th>
                    <th class="px-6 py-6 text-center">Status</th>
                    <th class="px-8 py-6 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($pendaftars as $akun)
                @php
                    $peserta = $akun->peserta;
                    $daftar = $peserta->daftar;
                    $berkas = $peserta->berkas;
                    $sertifikats = $peserta->sertifikats;
                    
                    $isDKV = ($peserta->pilihan_prodi ?? '') == 'Desain Komunikasi Visual (DKV)';
                    $percentage = $peserta->progress;
                @endphp
                <tr class="group hover:bg-slate-50/50 transition-colors">
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'panitia' || in_array(auth()->user()->email, ['dept.adminis@mfls.com', 'info@beasiswamncu.com']))
                    <td class="px-6 py-5 text-center">
                        <input type="checkbox" name="selected_ids[]" value="{{ $akun->id }}" class="row-checkbox w-4 h-4 rounded text-[#0870B8] focus:ring-[#0870B8] border-slate-300">
                    </td>
                    @endif
                    <td class="px-4 md:px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="relative w-12 h-12 rounded-2xl overflow-hidden shadow-sm border border-slate-100 group-hover:scale-105 transition-transform">
                                @if($berkas && $berkas->foto)
                                    <img src="{{ asset('storage/'.$berkas->foto) }}" class="w-full h-full object-cover">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($akun->nama) }}&background=random&color=fff" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div>
                                <div class="font-bold text-slate-900 text-sm">{{ $akun->nama }}</div>
                                <div class="text-[10px] font-bold text-slate-400 mt-0.5 uppercase tracking-wide">{{ $peserta->nama_sekolah ?? $peserta?->nama_sekolah ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <!-- Semester Averages -->
                    @for($sem = 1; $sem <= 5; $sem++)
                    <td class="px-4 py-5 text-center">
                        @php
                            $avgSem = $daftar->{"avg_semester_{$sem}"} ?? 0;
                            $colorClass = $avgSem >= 90 ? 'bg-green-100 text-green-700' : 
                                         ($avgSem >= 80 ? 'bg-blue-100 text-blue-700' : 
                                         ($avgSem >= 70 ? 'bg-yellow-100 text-yellow-700' : 'bg-slate-100 text-slate-600'));
                        @endphp
                        <span class="inline-block px-2 py-1 {{ $colorClass }} rounded-lg text-[11px] font-black">
                            {{ number_format($avgSem, 1) }}
                        </span>
                    </td>
                    @endfor
                    <!-- Overall Average -->
                    <td class="px-4 py-5 text-center">
                        <span class="inline-block px-3 py-1 bg-orange-100 text-orange-700 rounded-lg text-xs font-black border border-orange-200">
                            {{ number_format($daftar->rata_rata_nilai ?? 0, 2) }}
                        </span>
                    </td>
                    <td class="px-6 py-5">
                        <div class="font-bold text-slate-800 text-xs">{{ $peserta->pilihan_prodi ?? '-' }}</div>
                        @if($isDKV)
                            <div class="mt-1 flex items-center gap-1.5">
                                @if(!empty($berkas->surat_buta_warna))
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-md text-[9px] font-black flex items-center gap-1">
                                        <span class="iconify" data-icon="solar:eye-bold"></span> Buta Warna OK
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 bg-red-100 text-red-600 rounded-md text-[9px] font-black flex items-center gap-1">
                                        <span class="iconify" data-icon="solar:close-circle-bold"></span> Buta Warna Missing
                                    </span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <!-- Video Motivation -->
                    <td class="px-2 py-5 text-center">
                        @if($berkas && $berkas->motivasi_video)
                            <a href="{{ $berkas->motivasi_video }}" target="_blank" class="w-8 h-8 inline-flex items-center justify-center bg-purple-100 text-purple-600 rounded-lg hover:bg-purple-600 hover:text-white transition-all shadow-sm" title="Video Motivasi">
                                <span class="iconify" data-icon="solar:videocamera-bold"></span>
                            </a>
                        @else
                            <span class="text-slate-200">-</span>
                        @endif
                    </td>
                    <!-- Instagram -->
                    <td class="px-2 py-5 text-center">
                        @if($peserta->link_ig)
                            <a href="{{ $peserta->link_ig }}" target="_blank" class="w-8 h-8 inline-flex items-center justify-center bg-orange-100 text-orange-600 rounded-lg hover:bg-orange-600 hover:text-white transition-all shadow-sm" title="Instagram">
                                <span class="iconify" data-icon="solar:gallery-bold"></span>
                            </a>
                        @else
                            <span class="text-slate-200">-</span>
                        @endif
                    </td>
                    <!-- TikTok -->
                    <td class="px-2 py-5 text-center">
                        @if($peserta->link_tiktok)
                            <a href="{{ $peserta->link_tiktok }}" target="_blank" class="w-8 h-8 inline-flex items-center justify-center bg-slate-100 text-slate-800 rounded-lg hover:bg-slate-900 hover:text-white transition-all shadow-sm" title="TikTok">
                                <span class="iconify" data-icon="solar:music-note-bold"></span>
                            </a>
                        @else
                            <span class="text-slate-200">-</span>
                        @endif
                    </td>
                    <!-- Twibbon -->
                    <td class="px-2 py-5 text-center">
                        @if($peserta->link_twibbon)
                            <a href="{{ $peserta->link_twibbon }}" target="_blank" class="w-8 h-8 inline-flex items-center justify-center bg-pink-100 text-pink-600 rounded-lg hover:bg-pink-600 hover:text-white transition-all shadow-sm" title="Twibbon">
                                <span class="iconify" data-icon="solar:camera-bold"></span>
                            </a>
                        @else
                            <span class="text-slate-200">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex flex-wrap gap-1 max-w-[120px] mx-auto">
                            <!-- Foto -->
                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black {{ ($berkas && $berkas->foto) ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-slate-50 text-slate-300 border border-slate-100' }}">FOTO</span>
                            
                            <!-- Ijazah -->
                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black {{ ($berkas && $berkas->ijazah) ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-slate-50 text-slate-300 border border-slate-100' }}">IJAZAH</span>
                            
                            <!-- PS -->
                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black {{ ($berkas && $berkas->personal_statement) ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-slate-50 text-slate-300 border border-slate-100' }}">PS</span>
                            
                            <!-- Rapor -->
                            @php
                                $raporCount = 0;
                                foreach(['rapor1','rapor2','rapor3','rapor4','rapor5'] as $r) {
                                    if($berkas && !empty($berkas->$r)) $raporCount++;
                                }
                            @endphp
                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black {{ $raporCount == 5 ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : ($raporCount > 0 ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-slate-50 text-slate-300 border border-slate-100') }}">
                                RAPOR({{ $raporCount }}/5)
                            </span>

                            <!-- Sertifikat -->
                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black {{ $sertifikats->count() > 0 ? 'bg-indigo-100 text-indigo-700 border border-indigo-200' : 'bg-slate-50 text-slate-400 border border-slate-100' }}">
                                SRT({{ $sertifikats->count() }})
                            </span>

                            @if($isDKV)
                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black {{ !empty($berkas->surat_buta_warna) ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-red-100 text-red-600 border border-red-200' }}">
                                    BW
                                </span>
                            @endif
                        </div>
                        @php
                            $progColor = $percentage == 100 ? 'text-emerald-600 bg-emerald-50' : ($percentage >= 50 ? 'text-blue-600 bg-blue-50' : 'text-orange-600 bg-orange-50');
                        @endphp
                        <div class="mt-2 flex flex-col items-center">
                            <span class="px-2 py-0.5 {{ $progColor }} rounded-md text-[9px] font-black border border-current/10 mb-1">
                                {{ $percentage }}%
                            </span>
                            <div class="w-16 h-1 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full {{ $percentage == 100 ? 'bg-emerald-500' : 'bg-blue-500' }}" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-center">
                        @if(($daftar->status ?? 'menunggu') == 'lulus')
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-wide border border-emerald-100">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Lulus
                            </span>
                        @elseif(($daftar->status ?? 'menunggu') == 'diajukan_palugada')
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-wide border border-blue-100">
                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span> Double Check
                            </span>
                        @elseif(($daftar->status ?? 'menunggu') == 'tidak_lulus')
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-black uppercase tracking-wide border border-red-100">
                                Ditolak
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-orange-50 text-orange-600 rounded-full text-[10px] font-black uppercase tracking-wide border border-orange-100">
                                Menunggu
                            </span>
                        @endif
                    </td>
                    <td class="px-4 md:px-8 py-5 text-right">
                         <div class="flex items-center justify-end gap-2 transition-opacity md:opacity-0 md:group-hover:opacity-100">
                             @if(auth()->user()->role === 'admin' || in_array(auth()->user()->email, ['dept.adminis@mfls.com', 'info@beasiswamncu.com']))
                             <button type="button" onclick="confirmResetPassword({{ $akun->id }}, '{{ $akun->nama }}')" class="w-9 h-9 flex items-center justify-center bg-yellow-50 text-yellow-600 rounded-xl hover:bg-yellow-500 hover:text-white transition-all shadow-sm border border-yellow-100" title="Reset Password">
                                <span class="iconify" data-icon="solar:key-minimalistic-bold-duotone"></span>
                             </button>
                             <form action="{{ route('admin.pendaftar.destroy', $akun->id) }}" method="POST"
                                   onsubmit="return confirm('Yakin ingin menghapus peserta {{ $akun->nama }} beserta seluruh berkas dan nilainya? Tindakan ini tidak dapat dibatalkan!');"
                                   class="inline-block">
                                 @csrf
                                 @method('DELETE')
                                 <button type="submit" class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-600 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm border border-red-100" title="Hapus Akun Peserta">
                                     <span class="iconify" data-icon="solar:trash-bin-trash-bold"></span>
                                 </button>
                             </form>
                             @endif
                             <a href="{{ route('admin.pendaftar.download_zip', $akun->id) }}" class="w-9 h-9 flex items-center justify-center bg-slate-50 text-slate-600 rounded-xl hover:bg-slate-800 hover:text-white transition-all shadow-sm border border-slate-100" title="Download ZIP">
                                <span class="iconify" data-icon="solar:folder-with-files-bold"></span>
                            </a>
                            <form action="{{ route('admin.pendaftar.send_certificate', $akun->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" onclick="return confirm('Kirim sertifikat ke email {{ $akun->email }}?')" class="w-9 h-9 flex items-center justify-center bg-blue-50 text-[#0870B8] rounded-xl hover:bg-[#0870B8] hover:text-white transition-all shadow-sm border border-blue-100" title="Kirim Sertifikat via Email">
                                    <span class="iconify text-base" data-icon="solar:letter-send-bold"></span>
                                </button>
                            </form>
                            <a href="{{ route('admin.pendaftar.show', $akun->id) }}" class="h-9 px-4 bg-[#0870B8] text-white rounded-xl text-[10px] font-black hover:bg-[#065A96] transition-all shadow-md hover:shadow-blue-500/20 flex items-center gap-2 border border-blue-600">
                                <span>VERIFIKASI</span>
                                <span class="iconify" data-icon="solar:pen-new-square-bold"></span>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(count($pendaftars) == 0)
    <div class="p-10 text-center text-slate-400">
        <p class="text-sm font-bold">Belum ada data pendaftar.</p>
    </div>
    @endif
</div>
@if(auth()->user()->role === 'admin' || auth()->user()->role === 'panitia' || in_array(auth()->user()->email, ['dept.adminis@mfls.com', 'info@beasiswamncu.com']))
<!-- Modal Progress -->
<div id="progressModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9999] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-100">
        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-blue-50 text-[#0870B8] rounded-2xl flex items-center justify-center mx-auto mb-6">
                <span class="iconify text-3xl animate-spin" data-icon="solar:restart-bold"></span>
            </div>
            <h3 class="text-xl font-black text-slate-800 mb-2">Mengirim Sertifikat...</h3>
            <p class="text-slate-500 text-xs font-semibold mb-8">Mohon tunggu, sedang memproses pengiriman sertifikat.</p>
            
            <div class="relative pt-1">
                <div class="flex mb-2 items-center justify-between">
                    <div>
                        <span class="text-xs font-black inline-block py-1 px-2.5 uppercase rounded-full text-[#0870B8] bg-blue-50 border border-blue-200" id="progressText">
                            0%
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-black inline-block text-[#0870B8]" id="progressCount">
                            0 / 0
                        </span>
                    </div>
                </div>
                <div class="overflow-hidden h-2.5 mb-4 text-xs flex rounded-full bg-slate-100">
                    <div id="progressBar" style="width:0%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-[#0870B8] transition-all duration-500"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

let progressInterval;

function bulkSendCertificates() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

    if (selectedIds.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Silakan pilih setidaknya satu pendaftar.'
        });
        return;
    }

    Swal.fire({
        title: 'Konfirmasi',
        text: `Apakah Anda yakin ingin mengirim sertifikat ke ${selectedIds.length} pendaftar terpilih?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kirim!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show Modal
            document.getElementById('progressModal').classList.remove('hidden');
            
            // Reset Progress UI
            document.getElementById('progressBar').style.width = '0%';
            document.getElementById('progressCount').innerText = `0 / ${selectedIds.length}`;
            document.getElementById('progressText').innerText = '0%';

            fetch("{{ route('admin.pendaftar.bulk_send_certificate') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ selected_ids: selectedIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Batch started:', data.batch_id);
                    localStorage.setItem('activeCertBatchId', data.batch_id);
                    startPolling(data.batch_id);
                } else {
                    document.getElementById('progressModal').classList.add('hidden');
                    Swal.fire('Error', data.message || 'Gagal memulai pengiriman.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('progressModal').classList.add('hidden');
                Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
            });
        }
    });
}

function startPolling(batchId) {
    if (progressInterval) clearInterval(progressInterval);
    
    progressInterval = setInterval(() => {
        fetch(`{{ route('admin.pendaftar.bulk_send_progress') }}?batch_id=${batchId}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => {
            if (response.status === 404) {
                clearInterval(progressInterval);
                localStorage.removeItem('activeCertBatchId');
                document.getElementById('progressModal').classList.add('hidden');
                return null;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;

            console.log('Progress update:', data);
            
            const percentage = data.percentage;
            document.getElementById('progressBar').style.width = `${percentage}%`;
            document.getElementById('progressCount').innerText = `${data.current} / ${data.total}`;
            document.getElementById('progressText').innerText = `${percentage}%`;

            if (data.status === 'completed' || data.current >= data.total) {
                clearInterval(progressInterval);
                localStorage.removeItem('activeCertBatchId');
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Selesai',
                        text: 'Semua sertifikat telah berhasil dikirim ke antrean.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Polling error:', error);
        });
    }, 2000);
}

// Auto-resume on page load
document.addEventListener('DOMContentLoaded', () => {
    const savedBatchId = localStorage.getItem('activeCertBatchId');
    if (savedBatchId) {
        document.getElementById('progressModal').classList.remove('hidden');
        startPolling(savedBatchId);
    }
});
</script>
@endpush
@endif

{{-- Form untuk Reset Password (Khusus Admin) --}}
@if(auth()->user()->role === 'admin' || in_array(auth()->user()->email, ['dept.adminis@mfls.com', 'info@beasiswamncu.com']))
<form id="resetPasswordForm" method="POST" style="display:none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="password" id="resetPasswordInput">
</form>

<script>
function confirmResetPassword(userId, name) {
    Swal.fire({
        title: 'Konfirmasi Reset',
        text: `Apakah Anda benar-benar ingin mereset password untuk ${name}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Reset Password',
                text: `Masukkan password baru untuk ${name}:`,
                input: 'password',
                inputPlaceholder: 'Minimal 6 karakter',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocorrect: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Reset Sekarang',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#f97316',
                showLoaderOnConfirm: true,
                preConfirm: (password) => {
                    if (!password || password.length < 6) {
                        Swal.showValidationMessage('Password minimal 6 karakter');
                        return false;
                    }
                    return password;
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((inputResult) => {
                if (inputResult.isConfirmed) {
                    const form = document.getElementById('resetPasswordForm');
                    form.action = `/admin/user/${userId}/reset-password`;
                    document.getElementById('resetPasswordInput').value = inputResult.value;
                    form.submit();
                }
            });
        }
    });
}
</script>
@endif
@endsection

@extends('layouts.admin')

@section('content')
<!-- Notifikasi Flash -->
@if(session('success'))
<div class="mb-6 p-4 bg-green-500 text-white rounded-2xl shadow-lg shadow-green-200 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <span class="iconify text-xl" data-icon="solar:check-circle-bold"></span>
        <span class="font-bold">{{ session('success') }}</span>
    </div>
    <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200">
        <span class="iconify" data-icon="solar:close-circle-bold"></span>
    </button>
</div>
@endif

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-xl sm:text-2xl font-black text-slate-800">Verifikasi Seleksi Administrasi</h2>
        <p class="text-slate-500 text-sm">Peserta: {{ $user->nama }}</p>
    </div>
    <a href="{{ route('admin.pendaftar.index') }}" class="inline-flex self-start sm:self-auto px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-2xl text-xs font-black shadow-sm hover:bg-slate-50 transition-all uppercase tracking-widest items-center gap-2">
        <span class="iconify" data-icon="solar:arrow-left-bold"></span> Kembali
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Kolom Utama -->
    <div class="lg:col-span-2 space-y-8">
        <!-- 1. Identitas Global dengan Foto 3x4 -->
        <div class="bg-white p-6 sm:p-10 rounded-[2rem] sm:rounded-[3rem] shadow-sm border border-slate-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-orange-50/50 rounded-full translate-x-1/2 -translate-y-1/2"></div>
            {{-- Foto + Nama: column di mobile, row di sm+ --}}
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 mb-8 relative">
                @php
                    $fotoPath = $user->peserta->berkas->foto ?? null;
                    $fotoUrl = $fotoPath ? asset('storage/' . $fotoPath) : "https://ui-avatars.com/api/?name=".urlencode($user->nama)."&background=F97316&color=fff";
                @endphp
                {{-- Foto 3x4 --}}
                <div class="w-28 h-36 sm:w-32 sm:h-40 rounded-2xl overflow-hidden shadow-xl border-4 border-white ring-2 ring-orange-100 flex-shrink-0">
                    <img src="{{ $fotoUrl }}" class="w-full h-full object-cover" alt="Foto {{ $user->nama }}">
                </div>
                <div class="flex-grow w-full text-center sm:text-left">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2">{{ $user->nama }}</h1>
                    <div class="flex items-center justify-center sm:justify-start gap-3 flex-wrap">
                        <span class="px-3 py-1 bg-orange-50 text-orange-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-orange-100">{{ $user->peserta->nisn }}</span>
                        <span class="text-slate-300 font-bold">•</span>
                        <span class="text-slate-500 font-bold uppercase text-[10px] tracking-widest">{{ $user->peserta->nama_sekolah }}</span>
                    </div>
                    {{-- Info detail: 1 kolom di mobile, 2 kolom di sm+ --}}
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-left">
                        <div>
                            <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Email</label>
                            <div class="text-xs font-bold text-slate-700 break-all">{{ $user->email }}</div>
                            @if(auth()->user()->role === 'admin')
                             <button type="button" onclick="confirmResetPassword({{ $user->id }}, '{{ $user->nama }}')" class="mt-1 px-2 py-0.5 bg-yellow-50 text-yellow-600 rounded-md text-[9px] font-black hover:bg-yellow-500 hover:text-white transition-all border border-yellow-100 inline-flex items-center gap-1">
                                <span class="iconify" data-icon="solar:key-minimalistic-bold-duotone"></span> RESET PW
                            </button>
                            <button type="button" onclick="confirmChangeEmail({{ $user->id }}, '{{ $user->nama }}', '{{ $user->email }}')" class="mt-1 px-2 py-0.5 bg-blue-50 text-blue-600 rounded-md text-[9px] font-black hover:bg-blue-600 hover:text-white transition-all border border-blue-100 inline-flex items-center gap-1">
                                <span class="iconify" data-icon="solar:letter-bold-duotone"></span> GANTI EMAIL
                            </button>
                            @endif
                        </div>
                        <div>
                            <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">WhatsApp</label>
                            <div class="text-xs font-bold text-slate-700">{{ $user->peserta->no_whatsapp }}</div>
                        </div>
                        <div>
                            <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">WhatsApp Guru BK</label>
                            <div class="text-xs font-bold text-slate-700">{{ $user->peserta->no_guru_bk ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Tanggal Lahir</label>
                            <div class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($user->peserta->tgl_lahir)->format('d M Y') }}</div>
                        </div>
                        <div>
                            <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Tahun Lulus</label>
                            @php $thnLulus = (int) ($user->peserta->daftar->tahun_lulus ?? $user->peserta->tahun_lulus ?? 2027); @endphp
                            <div class="text-xs font-bold {{ $thnLulus < 2027 ? 'text-purple-600' : 'text-slate-700' }}">
                                {{ $thnLulus }}
                                @if($thnLulus < 2027) <span class="bg-purple-100 text-purple-600 px-1 py-0.5 rounded text-[8px] ml-1">ALUMNI</span> @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Jenis Kelamin</label>
                            <div class="text-xs font-bold text-slate-700">{{ $user->peserta->jenis_kelamin }}</div>
                        </div>
                        <div>
                            <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Program Studi Pilihan</label>
                            <div class="text-xs font-black text-blue-600 uppercase">{{ $user->peserta->pilihan_prodi ?? 'Belum Memilih' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.5. Dispensasi Waktu Ujian -->
        @if(in_array(auth()->user()->role, ['admin', 'palugada']))
        <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100 overflow-hidden mb-8">
            <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-4">
                <span class="w-12 h-12 bg-yellow-50 text-yellow-600 rounded-2xl flex items-center justify-center">
                    <span class="iconify text-2xl" data-icon="solar:alarm-add-bold"></span>
                </span>
                Dispensasi Waktu Ujian
            </h3>
            
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

        <!-- 2. Rincian Raport -->
        <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100 overflow-hidden">
            <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-4">
                <span class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                    <span class="iconify text-2xl" data-icon="solar:book-bold"></span>
                </span>
                Rincian Nilai Raport
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-slate-400 font-black uppercase tracking-widest border-b border-slate-50">
                            <th class="py-4 text-left">Mata Pelajaran</th>
                            @php $maxSem = (int)($user->peserta->daftar->tahun_lulus ?? $user->peserta->tahun_lulus ?? 2027) < 2027 ? 6 : 5; @endphp
                            @for($i=1;$i<=$maxSem;$i++) <th class="py-4 text-center">S{{$i}}</th> @endfor
                            <th class="py-4 text-center bg-slate-50">AVG</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @php $totalNilai = 0; $totalCount = 0; @endphp
                        @foreach($user->peserta->nilais->pluck('matpel')->unique('id') as $mp)
                            <tr>
                                <td class="py-4 font-black uppercase">{{ $mp->nama }}</td>
                                @php $mpAvg = 0; $mpCount = 0; @endphp
                                @for($sem=1;$sem<=$maxSem;$sem++)
                                    @php 
                                        $val = $user->peserta->nilais->where('matpel_id', $mp->id)->where('semester', $sem)->first()->nilai ?? 0;
                                        if($val > 0) { $mpAvg += $val; $mpCount++; $totalNilai += $val; $totalCount++; }
                                    @endphp
                                    <td class="py-4 text-center font-bold {{ $val > 0 ? 'text-slate-700' : 'text-slate-200' }}">{{ $val > 0 ? $val : '-' }}</td>
                                @endfor
                                <td class="py-4 text-center font-black bg-slate-50 text-blue-600">{{ $mpCount > 0 ? number_format($mpAvg/$mpCount, 1) : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-blue-600 text-white font-black text-sm">
                            <td colspan="7" class="p-4 text-right rounded-bl-2xl">RATA-RATA GLOBAL (TOTAL SCORE)</td>
                            <td class="p-4 text-center rounded-br-2xl">{{ $totalCount > 0 ? number_format($totalNilai/$totalCount, 2) : '0' }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- 2.5 Hasil Pemetaan Diri (AI Analysis) -->
        @php
            $pemetaan = $user->peserta->jawabanUjians->filter(function($j) {
                return str_contains(strtolower($j->ujian->nama ?? ''), 'pemetaan diri');
            })->first();
        @endphp
        
        <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
                <h3 class="text-xl font-black text-slate-800 flex items-center gap-4">
                    <span class="w-12 h-12 bg-orange-50 text-primary-orange rounded-2xl flex items-center justify-center">
                        <span class="iconify text-2xl" data-icon="solar:magic-stick-3-bold-duotone"></span>
                    </span>
                    Hasil Pemetaan Diri (AI Analysis)
                </h3>
                @if($pemetaan && $pemetaan->jawaban)
                    <a href="{{ Storage::url($pemetaan->jawaban) }}" target="_blank" class="flex items-center gap-3 px-6 py-3 bg-navy-mnc text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-primary-orange transition-all shadow-lg shadow-navy-mnc/20">
                        <span class="iconify text-xl" data-icon="solar:document-bold-duotone"></span> Lihat Dokumen PDF
                    </a>
                @endif
            </div>

            @if($pemetaan)
                <div class="space-y-6">
                    <!-- AI Conclusion -->
                    <div class="p-8 bg-navy-mnc rounded-[2rem] text-white relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-primary-orange/20 rounded-full blur-3xl -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700"></div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="px-3 py-1 bg-primary-orange text-white rounded-full text-[9px] font-black uppercase tracking-widest">Kesimpulan AI Arion (Analisis Kepribadian)</span>
                            </div>
                            <div class="text-sm md:text-base leading-relaxed text-orange-50/90">
                                {!! nl2br(e($pemetaan->getOrGeneratePemetaanReport())) !!}
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="py-12 text-center bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm text-slate-300">
                        <span class="iconify text-3xl" data-icon="solar:cloud-upload-bold-duotone"></span>
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Belum Ada Dokumen Terunggah</p>
                    <p class="text-[10px] text-slate-400 mt-1 uppercase">Peserta belum mengunggah PDF hasil pemetaan diri.</p>
                </div>
            @endif
        </div>

        <!-- 3. Berkas Dokumen -->
        <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100">
            <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-4">
                <span class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center">
                    <span class="iconify text-2xl" data-icon="solar:folder-with-files-bold"></span>
                </span>
                Checklist Berkas Fisik
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    $berkasItems = [
                        'foto' => 'Foto 3x4',
                        'rapor1' => 'Raport Smst 1',
                        'rapor2' => 'Raport Smst 2',
                        'rapor3' => 'Raport Smst 3',
                        'rapor4' => 'Raport Smst 4',
                        'rapor5' => 'Raport Smst 5',
                    ];

                    $tahunLulus = (int) ($user->peserta->daftar->tahun_lulus ?? $user->peserta->tahun_lulus ?? 2027);
                    if ($tahunLulus < 2027) {
                        $berkasItems['rapor6'] = 'Raport Smst 6';
                    }

                    $berkasItems['ijazah'] = 'Ijazah / SKL';
                    $berkasItems['surat_buta_warna'] = 'Surat Buta Warna';
                    $berkasItems['personal_statement'] = 'Personal Statement';
                    $berkasItems['study_plan'] = 'Study Plan';
                    
                    if ($tahunLulus >= 2027) {
                        $berkasItems['surat_rekomendasi_sekolah'] = 'Surat Rekomendasi';
                    }

                    if (str_contains($user->peserta->pilihan_prodi ?? '', 'DKV')) {
                        $berkasItems['portfolio'] = 'Portofolio';
                    }
                @endphp
                @foreach($berkasItems as $key => $label)
                @php 
                    $val = $user->peserta->berkas->$key ?? null; 
                    $files = json_decode($val, true);
                    if (!is_array($files)) {
                        $files = $val ? [$val] : [];
                    }
                @endphp
                <div class="p-6 {{ count($files) > 0 ? 'bg-emerald-50 border-emerald-100' : 'bg-slate-50 border-slate-100' }} rounded-3xl border flex flex-col justify-center min-h-[100px] relative group">
                    <div class="flex items-center justify-between w-full mb-3">
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $label }}</div>
                        <div class="flex items-center gap-2">
                            <div class="text-xs font-bold {{ count($files) > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                {{ count($files) > 0 ? count($files).' FILE' : 'KOSONG' }}
                            </div>
                            @if(in_array(auth()->user()->role, ['admin', 'akademik']) || in_array(auth()->user()->email, ['dept.adminis@mfls.com', 'info@beasiswamncu.com']))
                                @if(count($files) == 0 || str_starts_with($key, 'rapor'))
                                <button type="button" onclick="openUploadModal('{{ $key }}', '{{ $label }}')" class="p-1.5 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all" title="Upload Berkas">
                                    <span class="iconify" data-icon="solar:upload-bold"></span>
                                </button>
                                @endif
                                @if(count($files) > 0 && !str_starts_with($key, 'rapor'))
                                <form action="{{ route('admin.pendaftar.delete_berkas', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin menghapus dokumen ini?')">
                                    @csrf
                                    <input type="hidden" name="field" value="{{ $key }}">
                                    <button type="submit" class="p-1.5 bg-red-100 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all" title="Hapus Berkas">
                                        <span class="iconify" data-icon="solar:trash-bin-trash-bold"></span>
                                    </button>
                                </form>
                                @endif
                            @endif
                        </div>
                    </div>
                    
                    @if(count($files) > 0)
                        <div class="flex flex-wrap gap-2">
                        @foreach($files as $idx => $path)
                            @php 
                                $isExternal = str_starts_with($path, 'http');
                                $fullUrl = $isExternal ? $path : asset('storage/' . $path);
                            @endphp
                            <div class="flex items-center gap-1">
                                <a href="{{ $fullUrl }}" target="_blank" class="px-3 py-2 bg-white text-emerald-600 rounded-xl border border-emerald-100 shadow-sm hover:scale-105 transition-all text-[10px] font-black flex items-center gap-1">
                                    <span class="iconify" data-icon="{{ $isExternal ? 'solar:link-bold' : 'solar:document-bold' }}"></span> {{ $isExternal ? 'LINK' : 'FILE ' . ($idx + 1) }}
                                </a>
                                @if(str_starts_with($key, 'rapor') && (in_array(auth()->user()->role, ['admin', 'akademik']) || in_array(auth()->user()->email, ['dept.adminis@mfls.com', 'info@beasiswamncu.com'])))
                                <form action="{{ route('admin.pendaftar.delete_berkas', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin menghapus file ini?')">
                                    @csrf
                                    <input type="hidden" name="field" value="{{ $key }}">
                                    <input type="hidden" name="path" value="{{ $path }}">
                                    <button type="submit" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus File Rapor">
                                        <span class="iconify w-4 h-4" data-icon="solar:close-circle-bold"></span>
                                    </button>
                                </form>
                                @endif
                            </div>
                        @endforeach
                        </div>
                    @else
                        <div class="flex items-center gap-2 text-slate-300 font-bold text-xs">
                            <span class="iconify" data-icon="solar:close-circle-bold"></span> Tidak Ada Dokumen
                        </div>
                    @endif
                </div>
                @endforeach
            </div>

            <!-- Sertifikat Terpisah karena Modelnya Berbeda -->
            <div class="mt-8 pt-8 border-t border-slate-50">
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="iconify text-lg text-blue-500" data-icon="solar:medal-ribbons-star-bold"></span>
                    Sertifikat & Penghargaan ({{ $user->peserta->sertifikats->count() }})
                </h4>
                <div class="flex flex-wrap gap-3">
                    @forelse($user->peserta->sertifikats as $ser)
                        <a href="{{ asset('storage/' . $ser->file) }}" target="_blank" class="px-5 py-3 bg-blue-50 text-blue-700 rounded-2xl border border-blue-100 shadow-sm hover:scale-105 transition-all text-[11px] font-black flex items-center gap-2">
                            <span class="iconify text-lg" data-icon="solar:diploma-bold"></span>
                            {{ strtoupper($ser->nama) }}
                        </a>
                    @empty
                        <div class="w-full py-6 bg-slate-50 border-2 border-dashed border-slate-100 rounded-[2rem] text-center text-slate-400 text-[10px] font-bold uppercase tracking-widest">
                            Belum ada sertifikat yang diunggah
                        </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Dedicated Links Section -->
            <div class="mt-8 pt-8 border-t border-slate-50">
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span class="iconify text-lg text-purple-500" data-icon="solar:link-bold"></span>
                    Media & Social Media Presence
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Video Motivasi -->
                    <div class="p-5 bg-purple-50 rounded-2xl border border-purple-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-600 text-white rounded-xl flex items-center justify-center">
                                <span class="iconify text-xl" data-icon="solar:videocamera-bold"></span>
                            </div>
                            <div>
                                <div class="text-[8px] font-black text-purple-400 uppercase tracking-widest">Video Motivasi</div>
                                @if($user->peserta->berkas->motivasi_video ?? null)
                                    <a href="{{ $user->peserta->berkas->motivasi_video }}" target="_blank" class="text-xs font-bold text-purple-700 hover:underline flex items-center gap-1">
                                        LINK VIDEO <span class="iconify text-[10px]" data-icon="solar:arrow-right-up-bold"></span>
                                    </a>
                                @else
                                    <div class="text-xs font-bold text-slate-300 italic uppercase">Not Set</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Instagram -->
                    <div class="p-5 bg-orange-50 rounded-2xl border border-orange-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-500 text-white rounded-xl flex items-center justify-center">
                                <span class="iconify text-xl" data-icon="solar:gallery-bold"></span>
                            </div>
                            <div>
                                <div class="text-[8px] font-black text-orange-400 uppercase tracking-widest">Instagram</div>
                                @if($user->peserta->link_ig)
                                    <a href="{{ $user->peserta->link_ig }}" target="_blank" class="text-xs font-bold text-orange-700 hover:underline flex items-center gap-1">
                                        VIEW PROFILE <span class="iconify text-[10px]" data-icon="solar:arrow-right-up-bold"></span>
                                    </a>
                                @else
                                    <div class="text-xs font-bold text-slate-300 italic uppercase">Not Set</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- TikTok -->
                    <div class="p-5 bg-slate-900 rounded-2xl border border-slate-800 flex items-center justify-between text-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white text-slate-900 rounded-xl flex items-center justify-center">
                                <span class="iconify text-xl" data-icon="solar:music-note-bold"></span>
                            </div>
                            <div>
                                <div class="text-[8px] font-black text-slate-400 uppercase tracking-widest">TikTok</div>
                                @if($user->peserta->link_tiktok)
                                    <a href="{{ $user->peserta->link_tiktok }}" target="_blank" class="text-xs font-bold text-white hover:underline flex items-center gap-1">
                                        VIEW PROFILE <span class="iconify text-[10px]" data-icon="solar:arrow-right-up-bold"></span>
                                    </a>
                                @else
                                    <div class="text-xs font-bold text-slate-600 italic uppercase">Not Set</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Twibbon -->
                    <div class="p-5 bg-pink-50 rounded-2xl border border-pink-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-pink-500 text-white rounded-xl flex items-center justify-center">
                                <span class="iconify text-xl" data-icon="solar:camera-bold"></span>
                            </div>
                            <div>
                                <div class="text-[8px] font-black text-pink-400 uppercase tracking-widest">Twibbon Link</div>
                                @if($user->peserta->link_twibbon)
                                    <a href="{{ $user->peserta->link_twibbon }}" target="_blank" class="text-xs font-bold text-pink-700 hover:underline flex items-center gap-1">
                                        VIEW IMAGE <span class="iconify text-[10px]" data-icon="solar:arrow-right-up-bold"></span>
                                    </a>
                                @else
                                    <div class="text-xs font-bold text-slate-300 italic uppercase">Not Set</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Follow Proof Section --}}
                <div class="mt-8 pt-8 border-t border-slate-50">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <span class="iconify text-lg text-pink-500" data-icon="solar:camera-minimalistic-bold"></span>
                        Follow Proof Screenshots
                    </h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @php
                            $followProofs = [
                                ['field' => 'bukti_follow_ig_beasiswamncu', 'label' => 'IG Beasiswa'],
                                ['field' => 'bukti_follow_ig_mncu', 'label' => 'IG MNCO'],
                                ['field' => 'bukti_follow_tiktok_beasiswamncu', 'label' => 'TikTok Beasiswa'],
                                ['field' => 'bukti_follow_tiktok_mncu', 'label' => 'TikTok MNCU'],
                            ];
                        @endphp
                        @foreach($followProofs as $proof)
                        @php $path = $user->peserta->berkas->{$proof['field']} ?? null; @endphp
                        <div class="p-4 rounded-2xl border {{ $path ? 'bg-emerald-50 border-emerald-100' : 'bg-slate-50 border-slate-100' }} flex flex-col items-center">
                            <div class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2 text-center">{{ $proof['label'] }}</div>
                            @if($path)
                                <a href="{{ asset('storage/' . $path) }}" target="_blank" class="w-12 h-12 rounded-lg overflow-hidden border-2 border-white shadow-sm hover:scale-110 transition-all">
                                    <img src="{{ asset('storage/' . $path) }}" class="w-full h-full object-cover">
                                </a>
                                <div class="text-[10px] font-bold text-emerald-600 mt-2">DITERIMA</div>
                            @else
                                <div class="w-12 h-12 bg-slate-200 rounded-lg flex items-center justify-center text-slate-400">
                                    <span class="iconify" data-icon="solar:close-circle-bold"></span>
                                </div>
                                <div class="text-[10px] font-bold text-slate-400 mt-2 italic uppercase">Waiting</div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Sidebar Verifikasi -->
    <div class="space-y-8">
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-10 rounded-[3rem] text-white shadow-2xl relative overflow-hidden">
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-white/10 rounded-full"></div>
            <h3 class="text-lg font-black mb-10 uppercase tracking-[0.2em] text-orange-100">Decision Center</h3>
            
            <div class="mb-10 p-6 bg-white/10 border border-white/20 rounded-[2rem] text-center backdrop-blur-sm">
                <div class="text-[10px] font-black text-orange-100 uppercase tracking-widest mb-2">Administrasi Status</div>
                @if($user->peserta->daftar->status == 'lulus')
                    <div class="text-xl font-black text-white uppercase flex items-center justify-center gap-2">
                        <span class="iconify" data-icon="solar:check-circle-bold"></span> LOLOS BERKAS
                    </div>
                @elseif($user->peserta->daftar->status == 'diajukan_palugada')
                    <div class="text-xl font-black text-blue-200 animate-pulse uppercase flex items-center justify-center gap-2">
                        <span class="iconify" data-icon="solar:shield-check-bold"></span> DOUBLE CHECK
                    </div>
                @elseif($user->peserta->daftar->status == 'tidak_lulus')
                    <div class="text-xl font-black text-red-200 uppercase flex items-center justify-center gap-2">
                        <span class="iconify" data-icon="solar:close-circle-bold"></span> DITOLAK
                    </div>
                @else
                    <div class="text-xl font-black text-yellow-200 animate-pulse uppercase flex items-center justify-center gap-2">
                        <span class="iconify" data-icon="solar:clock-circle-bold"></span> PENDING
                    </div>
                @endif
            </div>

            <div class="space-y-4 relative">
                <form action="{{ route('admin.pendaftar.verify', $user->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="lulus">
                    <button type="button" onclick='confirmVerify(this, "lulus", @json($user->nama))' class="w-full py-5 bg-white text-emerald-600 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl hover:bg-emerald-50 transition-all flex items-center justify-center gap-2">
                        <span class="iconify" data-icon="solar:check-circle-bold"></span> 
                        @if(auth()->user()->role === 'palugada')
                            Finalisasi Persetujuan
                        @else
                            Setujui Berkas & Profil
                        @endif
                    </button>
                </form>
                <form action="{{ route('admin.pendaftar.verify', $user->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="tidak_lulus">
                    <button type="button" onclick='confirmVerify(this, "tidak_lulus", @json($user->nama))' class="w-full py-5 bg-white/10 border border-white/20 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-500/20 transition-all flex items-center justify-center gap-2">
                        <span class="iconify" data-icon="solar:close-circle-bold"></span> Tolak / Diskualifikasi
                    </button>
                </form>
                @if(($user->peserta->daftar->status ?? 'menunggu') != 'menunggu')
                <form action="{{ route('admin.pendaftar.verify', $user->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="menunggu">
                    <button type="button" onclick='confirmVerify(this, "menunggu", @json($user->nama))' class="w-full py-5 bg-white/10 text-white rounded-2xl font-black text-[9px] uppercase tracking-widest hover:bg-white/20 transition-all flex items-center justify-center gap-2">
                        <span class="iconify" data-icon="solar:restart-bold"></span> Batalkan Keputusan
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function confirmVerify(button, status, name) {
    let title, text, icon, confirmButtonColor;
    const isPalugada = @json(auth()->user()->role === 'palugada');
    
    if (status === 'lulus') { 
        title = isPalugada ? 'Finalisasi Persetujuan?' : 'Setujui Administrasi?'; 
        text = isPalugada ? `Berikan persetujuan final untuk ${name}?` : `Luluskan berkas dan profil ${name}? (Akan diverifikasi ulang oleh Palugada)`; 
        icon = 'success'; 
        confirmButtonColor = '#10b981'; 
    } 
    else if (status === 'tidak_lulus') { title = 'Tolak Administrasi?'; text = `Tolak pendaftaran ${name}?`; icon = 'warning'; confirmButtonColor = '#ef4444'; }
    else { title = 'Reset Keputusan?'; text = `Kembalikan ${name} ke status Menunggu?`; icon = 'info'; confirmButtonColor = '#F97316'; }

    Swal.fire({
        title: title, text: text, icon: icon,
        showCancelButton: true, confirmButtonColor: confirmButtonColor, cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Lakukan!', cancelButtonText: 'Batal',
        borderRadius: '2rem',
        customClass: { title: 'font-black', content: 'font-semibold' }
    }).then((result) => { if (result.isConfirmed) { button.closest('form').submit(); } });
}

function openUploadModal(field, label) {
    Swal.fire({
        title: `Upload ${label}`,
        html: `
            <form id="adminUploadForm" action="{{ route('admin.pendaftar.upload_berkas', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="field" value="${field}">
                <div class="mt-4">
                    <input type="file" name="file" class="w-full text-sm text-slate-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100 outline-none" required>
                    <p class="mt-2 text-xs text-slate-400">PDF/JPG/PNG Max 10MB.</p>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3b82f6',
        preConfirm: () => {
            const fileInput = document.querySelector('#adminUploadForm input[type="file"]');
            if (!fileInput.files.length) {
                Swal.showValidationMessage('Harap pilih file terlebih dahulu');
                return false;
            }
            document.getElementById('adminUploadForm').submit();
        }
    });
}
</script>
@if(auth()->user()->role === 'admin')
<form id="resetPasswordForm" method="POST" style="display:none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="password" id="resetPasswordInput">
</form>

<form id="changeEmailForm" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="email" id="changeEmailInput">
</form>

<script>
function confirmChangeEmail(userId, name, currentEmail) {
    Swal.fire({
        title: 'Ganti Email Pendaftar',
        text: `Masukkan email baru untuk ${name} (Email saat ini: ${currentEmail}):`,
        input: 'email',
        inputPlaceholder: 'Email baru',
        showCancelButton: true,
        confirmButtonText: 'Update Email',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3b82f6',
        showLoaderOnConfirm: true,
        preConfirm: (newEmail) => {
            if (!newEmail) {
                Swal.showValidationMessage('Email tidak boleh kosong');
                return false;
            }
            return newEmail;
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('changeEmailForm');
            form.action = `/admin/pendaftar/${userId}/update-email`;
            document.getElementById('changeEmailInput').value = result.value;
            form.submit();
        }
    });
}

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

<script>
function confirmUpdateTahunLulus(userId, name, currentYear) {
    Swal.fire({
        title: 'Edit Tahun Lulus',
        text: `Ubah tahun lulus untuk ${name}:`,
        input: 'select',
        inputOptions: {
            '2021': '2021',
            '2022': '2022',
            '2023': '2023',
            '2024': '2024',
            '2025': '2025',
            '2026': '2026',
            '2027': '2027'
        },
        inputValue: currentYear,
        showCancelButton: true,
        confirmButtonText: 'Update Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#f97316',
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/pendaftar/${userId}/update-tahun-lulus`;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'tahun_lulus';
            input.value = result.value;
            
            form.appendChild(csrf);
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
@endsection

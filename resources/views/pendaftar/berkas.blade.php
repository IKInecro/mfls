@extends('pendaftar.layout')

@section('content')
<div class="max-w-5xl mx-auto space-y-4 sm:space-y-6 pb-16">
    
    {{-- Hidden Delete Forms --}}
    <form id="deleteFileForm" action="{{ route('pendaftar.berkas.delete_file') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="field" id="deleteField">
        <input type="hidden" name="file_path" id="deleteFilePath">
    </form>
    <form id="deleteSertifikatForm" action="" method="POST" class="hidden">
        @csrf
    </form>

    <!-- Hero Header (Grand Royal Navy & Slate Theme - Mobile Friendly) -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br from-[#0B1120] via-[#0F172A] to-[#1E293B] text-white p-5 sm:p-7 md:p-8 border border-slate-800 shadow-xl">
        <div class="absolute -top-32 -left-32 w-80 h-80 rounded-full bg-blue-600/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 rounded-full bg-amber-400/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 sm:gap-6">
            <div class="space-y-1.5 sm:space-y-2 max-w-xl">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/10 border border-white/15 text-[10px] sm:text-[11px] font-bold text-slate-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    <span>VERIFIKASI BERKAS SELEKSI MFLS 2026</span>
                </div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-white tracking-tight">
                    Pusat Berkas & Dokumen
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 font-medium leading-relaxed">
                    Lengkapi seluruh berkas persyaratan seleksi beasiswa Anda. Format PDF atau gambar (maks. 5MB per berkas).
                </p>
            </div>
            
            <div class="shrink-0 w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-blue-400 shadow-sm hidden xs:flex">
                <span class="iconify text-2xl sm:text-3xl" data-icon="solar:cloud-upload-bold-duotone"></span>
            </div>
        </div>
    </div>

    <!-- Conditional Notice: Pendaftaran Ditutup -->
    @if(!\App\Models\PengaturanPendaftaran::aktif())
    <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-900 border border-slate-800 text-white flex items-center gap-3 sm:gap-4 shadow-xs">
        <div class="w-9 h-9 rounded-xl bg-amber-400/10 border border-amber-400/20 text-amber-400 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <div>
            <h2 class="text-[11px] sm:text-xs font-black uppercase tracking-wider text-amber-400">Periode Upload Berkas Telah Ditutup</h2>
            <p class="text-[11px] sm:text-xs text-slate-300 font-medium mt-0.5">Berkas yang telah diunggah tersimpan aman untuk proses penilaian panitia.</p>
        </div>
    </div>
    @endif

    <!-- Academic Meta & Official Template Downloads -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        <!-- Status Kelulusan -->
        <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-3 sm:col-span-2 lg:col-span-1">
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <span class="iconify text-lg" data-icon="solar:diploma-bold-duotone"></span>
            </div>
            <div class="min-w-0">
                <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kategori Pendaftar</span>
                @php $tahunLulus = (int) ($peserta->daftar->tahun_lulus ?? 2027); @endphp
                <p class="text-xs font-extrabold text-slate-900 mt-0.5 truncate">
                    {{ $tahunLulus < 2027 ? 'Alumni / Gap Year (' . $tahunLulus . ')' : 'Siswa Aktif Kelas 12 (Lulusan 2027)' }}
                </p>
            </div>
        </div>

        <!-- Download Template 1: Personal Statement -->
        <a href="{{ asset('icon/Personal Statement MFLS 2026.pdf') }}" target="_blank" 
           class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-white border border-slate-200/80 hover:border-blue-300 hover:shadow-sm transition-all group flex items-center justify-between gap-2.5">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 group-hover:bg-blue-600 group-hover:text-white transition-colors flex items-center justify-center shrink-0">
                    <span class="iconify text-base" data-icon="solar:document-text-bold"></span>
                </div>
                <div class="min-w-0">
                    <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Format PDF</span>
                    <p class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition-colors truncate">Personal Statement</p>
                </div>
            </div>
            <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        </a>

        <!-- Download Template 2: Study Plan -->
        <a href="{{ asset('icon/Study Plan MFLS 2026_2.pdf') }}" target="_blank" 
           class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-white border border-slate-200/80 hover:border-blue-300 hover:shadow-sm transition-all group flex items-center justify-between gap-2.5">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 group-hover:bg-blue-600 group-hover:text-white transition-colors flex items-center justify-center shrink-0">
                    <span class="iconify text-base" data-icon="solar:notebook-bold"></span>
                </div>
                <div class="min-w-0">
                    <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Format PDF</span>
                    <p class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition-colors truncate">Study Plan MFLS</p>
                </div>
            </div>
            <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        </a>
    </div>

    <!-- 1. PROGRAM STUDI SELECTION -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-4 sm:px-6 py-3.5 sm:py-4 bg-slate-50/70 border-b border-slate-200/70 flex items-center gap-3">
            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center shrink-0">
                <span class="iconify text-base sm:text-lg" data-icon="solar:square-academic-cap-bold-duotone"></span>
            </div>
            <div>
                <h2 class="text-xs sm:text-sm font-extrabold text-slate-900 tracking-tight">Pilihan Program Studi Tujuan</h2>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-medium">Tentukan 2 program studi prioritas yang Anda minati di MNC University.</p>
            </div>
        </div>

        <form action="{{ route('pendaftar.berkas.store') }}" method="POST" class="p-4 sm:p-6 md:p-8 space-y-4 sm:space-y-5">
            @csrf
            <input type="hidden" name="upload_field" value="program_studi">

            @php 
                $currentProdi = $peserta->pilihan_prodi;
                $parts = explode(' | ', $currentProdi);
                $pilihan1 = $parts[0] ?? '';
                $pilihan2 = $parts[1] ?? '';
                $prodis = ['Sains Komunikasi', 'Desain Komunikasi Visual (DKV)', 'Manajemen', 'Akuntansi', 'Sistem Informasi', 'Pendidikan Bahasa Inggris', 'Pendidikan Matematika', 'Ilmu Komputer'];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                <!-- Pilihan 1 -->
                <div class="space-y-1">
                    <label for="prodi1" class="block text-xs font-bold text-slate-700 tracking-wide">
                        Pilihan Program Studi 1 (Utama) <span class="text-blue-600">*</span>
                    </label>
                    <div class="relative">
                        <select name="pilihan_prodi1" id="prodi1" onchange="checkProdiSelection()" required
                                class="w-full bg-slate-50 text-slate-800 font-semibold text-xs sm:text-sm rounded-xl border border-slate-200 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 pl-3.5 pr-9 py-2.5 sm:py-3 outline-none appearance-none transition-all cursor-pointer">
                            <option value="">Pilih Program Studi 1</option>
                            @foreach($prodis as $p)
                                <option value="{{ $p }}" {{ $pilihan1 === $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>
                </div>

                <!-- Pilihan 2 -->
                <div class="space-y-1">
                    <label for="prodi2" class="block text-xs font-bold text-slate-700 tracking-wide">
                        Pilihan Program Studi 2 (Alternatif) <span class="text-blue-600">*</span>
                    </label>
                    <div class="relative">
                        <select name="pilihan_prodi2" id="prodi2" onchange="checkProdiSelection()" required
                                class="w-full bg-slate-50 text-slate-800 font-semibold text-xs sm:text-sm rounded-xl border border-slate-200 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 pl-3.5 pr-9 py-2.5 sm:py-3 outline-none appearance-none transition-all cursor-pointer">
                            <option value="">Pilih Program Studi 2</option>
                            @foreach($prodis as $p)
                                <option value="{{ $p }}" {{ $pilihan2 === $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DKV Blindness Alert -->
            <div id="butaWarnaAlert" class="hidden p-3.5 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-2.5">
                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-xs text-amber-800 font-medium leading-relaxed">
                    <strong>Catatan Program Studi DKV:</strong> Mensyaratkan calon mahasiswa tidak buta warna (parsial maupun total).
                </p>
            </div>

            <div class="flex justify-end pt-1">
                <button type="submit" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold transition-all shadow-xs active:scale-[0.99] cursor-pointer">
                    <span class="iconify text-base" data-icon="solar:diskette-bold"></span>
                    <span>Simpan Pilihan Program Studi</span>
                </button>
            </div>
        </form>
    </div>

    <!-- 2. DOCUMENT UPLOAD SECTION -->
    @if(empty($peserta->pilihan_prodi))
    <!-- Locked Document Upload Placeholder -->
    <div class="p-6 sm:p-10 text-center rounded-2xl sm:rounded-3xl bg-slate-100 border border-slate-200 text-slate-500 space-y-2.5">
        <div class="w-10 h-10 rounded-xl bg-slate-200 text-slate-600 flex items-center justify-center mx-auto">
            <span class="iconify text-xl" data-icon="solar:lock-bold-duotone"></span>
        </div>
        <h3 class="text-sm sm:text-base font-extrabold text-slate-800">Bagian Upload Berkas Terkunci</h3>
        <p class="text-xs text-slate-500 font-medium max-w-md mx-auto">
            Silakan pilih 2 program studi di atas dan klik <strong>"Simpan Pilihan Program Studi"</strong> terlebih dahulu untuk membuka akses pengunggahan berkas persyaratan.
        </p>
    </div>
    @else
    
    @php
        $fileCards = [
            [
                'field' => 'foto',
                'label' => 'Pas Foto Formal',
                'desc' => 'Foto berwarna terbaru dengan pakaian rapi berkerah.',
                'accept' => '.jpg,.jpeg,.png,.webp',
                'maxSize' => 'Max 2MB (JPG / PNG)',
                'icon' => 'solar:user-circle-bold-duotone',
                'val' => $berkas->foto ?? null,
            ],
            [
                'field' => 'kartu_identitas',
                'label' => 'Kartu Identitas (KTP / Pelajar)',
                'desc' => 'Foto atau scan KTP / Kartu Pelajar yang masih berlaku.',
                'accept' => '.pdf,.jpg,.jpeg,.png,.webp',
                'maxSize' => 'Max 2MB (PDF / JPG / PNG)',
                'icon' => 'solar:card-2-bold-duotone',
                'val' => $berkas->kartu_identitas ?? null,
            ],
            [
                'field' => 'kartu_keluarga',
                'label' => 'Kartu Keluarga (KK)',
                'desc' => 'Scan Kartu Keluarga terbaru yang memperlihatkan nama Anda.',
                'accept' => '.pdf,.jpg,.jpeg,.png,.webp',
                'maxSize' => 'Max 2MB (PDF / JPG / PNG)',
                'icon' => 'solar:users-group-two-rounded-bold-duotone',
                'val' => $berkas->kartu_keluarga ?? null,
            ],
            [
                'field' => 'surat_rekomendasi',
                'label' => 'Surat Rekomendasi',
                'desc' => 'Surat rekomendasi dari Kepala Sekolah / Guru BK / Tokoh Masyarakat.',
                'accept' => '.pdf,.jpg,.jpeg,.png,.webp',
                'maxSize' => 'Max 2MB (PDF / JPG / PNG)',
                'icon' => 'solar:diploma-verified-bold-duotone',
                'val' => $berkas->surat_rekomendasi ?? null,
            ],
            [
                'field' => 'study_plan',
                'label' => 'Study Plan (Rencana Studi)',
                'desc' => 'Rencana studi dan sasaran akademik selama 8 semester perkuliahan.',
                'accept' => '.pdf,.docx,.doc',
                'maxSize' => 'Max 5MB (PDF / DOCX)',
                'icon' => 'solar:notebook-bold-duotone',
                'val' => $berkas->study_plan ?? null,
            ],
            [
                'field' => 'personal_statement',
                'label' => 'Personal Statement (Esai)',
                'desc' => 'Esai motivasi, kepemimpinan, dan komitmen kontribusi Anda.',
                'accept' => '.pdf,.docx,.doc',
                'maxSize' => 'Max 5MB (PDF / DOCX)',
                'icon' => 'solar:document-text-bold-duotone',
                'val' => $berkas->personal_statement ?? null,
            ],
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 sm:gap-5">
        @foreach($fileCards as $card)
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs p-4 sm:p-5 flex flex-col justify-between space-y-3">
            
            <!-- Card Header -->
            <div class="space-y-1">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <span class="iconify text-base" data-icon="{{ $card['icon'] }}"></span>
                        </div>
                        <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 tracking-tight truncate">{{ $card['label'] }}</h3>
                    </div>

                    @if($card['val'])
                        <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[9px] sm:text-[10px] font-bold border border-emerald-200/60">
                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Tersimpan</span>
                        </span>
                    @else
                        <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[9px] sm:text-[10px] font-bold">
                            Belum Ada
                        </span>
                    @endif
                </div>
                <!-- Clean Description without squashing pl-10 -->
                <p class="text-[11px] text-slate-500 font-normal leading-relaxed mt-1">{{ $card['desc'] }}</p>
            </div>

            <!-- Upload Zone / File Preview Area -->
            <div class="pt-1">
                @if($card['val'])
                    <!-- File Uploaded Info Box -->
                    <div class="p-2.5 sm:p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-2.5">
                        <div class="flex items-center gap-2 min-w-0">
                            <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <a href="{{ asset('storage/' . $card['val']) }}" target="_blank" 
                               class="text-xs font-bold text-slate-800 hover:text-blue-600 hover:underline truncate">
                                Lihat Berkas Tersimpan
                            </a>
                        </div>
                        <button type="button" 
                                onclick="confirmDeleteFile('{{ $card['field'] }}')"
                                title="Hapus Berkas Ini"
                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                @else
                    <!-- File Upload Form -->
                    <form action="{{ route('pendaftar.berkas.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateSingleForm(this)">
                        @csrf
                        <input type="hidden" name="upload_field" value="{{ $card['field'] }}">

                        <div class="relative">
                            <input type="file" 
                                   id="file-{{ $card['field'] }}" 
                                   name="{{ $card['field'] }}" 
                                   accept="{{ $card['accept'] }}" 
                                   class="hidden" 
                                   onchange="onFileSelected(this, '{{ $card['field'] }}')">
                            
                            <label for="file-{{ $card['field'] }}" 
                                   class="flex flex-col items-center justify-center p-3.5 border border-dashed border-slate-200 hover:border-blue-400 rounded-xl bg-slate-50/60 hover:bg-blue-50/20 transition-all cursor-pointer text-center group">
                                <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-600 transition-colors mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-600 transition-colors">Pilih File Berkas</span>
                                <span class="text-[10px] text-slate-400 mt-0.5">{{ $card['maxSize'] }}</span>
                            </label>

                            <p id="preview-{{ $card['field'] }}" class="hidden text-xs font-bold text-blue-600 mt-2 truncate"></p>
                        </div>

                        <button type="submit" 
                                id="btn-save-{{ $card['field'] }}" 
                                class="hidden mt-2.5 w-full py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all shadow-xs active:scale-[0.99] cursor-pointer">
                            Unggah Dokumen
                        </button>
                    </form>
                @endif
            </div>

        </div>
        @endforeach
    </div>

    <!-- 3. MULTIPLE SERTIFIKAT PRESTASI SECTION -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-4 sm:px-6 py-3.5 sm:py-4 bg-slate-50/70 border-b border-slate-200/70 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center shrink-0">
                    <span class="iconify text-base sm:text-lg" data-icon="solar:medal-star-bold-duotone"></span>
                </div>
                <div>
                    <h2 class="text-xs sm:text-sm font-extrabold text-slate-900 tracking-tight">Sertifikat Prestasi (Opsional)</h2>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 font-medium">Unggah sertifikat kejuaraan akademik, seni, atau organisasi.</p>
                </div>
            </div>
        </div>

        <div class="p-4 sm:p-6 md:p-8 space-y-4 sm:space-y-6">
            <!-- Form Upload Sertifikat Baru -->
            <form action="{{ route('pendaftar.berkas.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateSingleForm(this)" class="p-3.5 sm:p-5 rounded-xl sm:rounded-2xl bg-slate-50/70 border border-slate-200 space-y-3 sm:space-y-4">
                @csrf
                <input type="hidden" name="upload_field" value="prestasi">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">Nama Sertifikat / Prestasi</label>
                        <input type="text" name="nama_sertifikat" placeholder="Contoh: Juara 1 Olimpiade Matematika" required
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 outline-none transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">Pilih File (PDF / JPG / PNG)</label>
                        <input type="file" name="prestasi" accept=".pdf,.jpg,.jpeg,.png,.webp" required
                               class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-blue-50 file:text-blue-700 cursor-pointer">
                    </div>
                </div>

                <div class="flex justify-end pt-1">
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                        <span class="iconify text-base" data-icon="solar:add-circle-bold"></span>
                        <span>Tambah Sertifikat</span>
                    </button>
                </div>
            </form>

            <!-- Daftar Sertifikat yang Telah Diunggah -->
            @php
                $sertifikats = $peserta->sertifikat ?? collect();
            @endphp

            @if($sertifikats->count() > 0)
                <div class="space-y-2">
                    <h3 class="text-xs font-bold text-slate-700">Sertifikat Terdaftar ({{ $sertifikats->count() }} Berkas)</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach($sertifikats as $s)
                            <div class="p-2.5 sm:p-3 rounded-xl bg-white border border-slate-200 flex items-center justify-between gap-2.5">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                        <span class="iconify text-base" data-icon="solar:file-check-bold"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ asset('storage/' . $s->file) }}" target="_blank" class="text-xs font-bold text-slate-800 hover:text-blue-600 hover:underline truncate block">
                                            {{ $s->nama_sertifikat ?? 'Sertifikat' }}
                                        </a>
                                        <span class="text-[10px] text-slate-400 font-medium">Terverifikasi di sistem</span>
                                    </div>
                                </div>
                                <button type="button" 
                                        onclick="confirmDeleteSertifikat('{{ $s->id }}', '{{ addslashes($s->nama_sertifikat ?? '') }}')"
                                        title="Hapus Sertifikat"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif

</div>

<!-- Scripts for File Upload Interactivity -->
<script>
function onFileSelected(input, fieldName) {
    const btn = document.getElementById('btn-save-' + fieldName);
    const preview = document.getElementById('preview-' + fieldName);
    
    if (input.files.length > 0) {
        if (btn) btn.classList.remove('hidden');
        if (preview) {
            preview.textContent = 'File dipilih: ' + input.files[0].name;
            preview.classList.remove('hidden');
        }
    } else {
        if (btn) btn.classList.add('hidden');
        if (preview) {
            preview.textContent = '';
            preview.classList.add('hidden');
        }
    }
}

function checkProdiSelection() {
    const p1 = document.getElementById('prodi1').value;
    const p2 = document.getElementById('prodi2').value;
    const alertBox = document.getElementById('butaWarnaAlert');
    
    if (alertBox) {
        if ((p1 && p1.includes('DKV')) || (p2 && p2.includes('DKV'))) {
            alertBox.classList.remove('hidden');
        } else {
            alertBox.classList.add('hidden');
        }
    }

    if (p1 && p2 && p1 === p2) {
        if (window.modernAlert) {
            window.modernAlert({
                type: 'warning',
                title: 'Program Studi Sama',
                message: 'Silakan pilih Program Studi yang berbeda untuk pilihan 1 dan pilihan 2.',
                confirmText: 'Mengerti'
            });
        }
        document.getElementById('prodi2').value = '';
    }
}

function validateSingleForm(form) {
    const maxSize = 10 * 1024 * 1024; // 10MB
    const fileInputs = form.querySelectorAll('input[type="file"]');
    for (let input of fileInputs) {
        for (let file of input.files) {
            if (file.size > maxSize) {
                if (window.modernAlert) {
                    window.modernAlert({
                        type: 'error',
                        title: 'File Terlalu Besar',
                        message: `Ukuran file "${file.name}" melebihi batas 10MB. Harap kompresi terlebih dahulu.`,
                        confirmText: 'Mengerti'
                    });
                }
                return false;
            }
        }
    }
    return true;
}

function confirmDeleteFile(field, filePath = null) {
    if (window.modernAlert) {
        window.modernAlert({
            type: 'warning',
            title: 'Hapus Dokumen?',
            message: 'Dokumen ini akan dihapus dari server. Pastikan Anda memiliki salinan cadangannya.',
            showCancel: true,
            confirmText: 'Ya, Hapus',
            cancelText: 'Batal',
            onConfirm: function() {
                document.getElementById('deleteField').value = field;
                document.getElementById('deleteFilePath').value = filePath || '';
                document.getElementById('deleteFileForm').submit();
            }
        });
    }
}

function confirmDeleteSertifikat(id, nama) {
    if (window.modernAlert) {
        window.modernAlert({
            type: 'warning',
            title: 'Hapus Sertifikat Prestasi?',
            message: `Apakah Anda yakin ingin menghapus "${nama}"?`,
            showCancel: true,
            confirmText: 'Ya, Hapus',
            cancelText: 'Batal',
            onConfirm: function() {
                const form = document.getElementById('deleteSertifikatForm');
                form.action = "{{ url('pendaftar/berkas/delete-sertifikat') }}/" + id;
                form.submit();
            }
        });
    }
}

@if(!\App\Models\PengaturanPendaftaran::aktif())
document.addEventListener('DOMContentLoaded', function () {
    // Disable inputs only when registration is actually closed
    document.querySelectorAll('input[type="file"], select[name^="pilihan_prodi"]').forEach(el => el.disabled = true);
    document.querySelectorAll('button[type="submit"], button[onclick^="confirmDelete"]').forEach(btn => {
        btn.disabled = true;
        btn.style.opacity = '0.5';
        btn.style.cursor = 'not-allowed';
    });
});
@endif
</script>
@endsection

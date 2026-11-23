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
            @php
                $isDkv = str_contains($peserta->pilihan_prodi ?? '', 'DKV');
            @endphp
            <div id="butaWarnaAlert" class="{{ $isDkv ? '' : 'hidden' }} p-3.5 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-2.5">
                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <div class="text-xs text-amber-900 font-medium leading-relaxed space-y-0.5">
                    <p class="font-bold text-amber-950">Persyaratan Khusus Program Studi DKV:</p>
                    <p class="text-amber-800">
                        Karena Anda memilih DKV, Anda diwajibkan mengunggah <strong>Surat Keterangan Bebas Buta Warna</strong> dan <strong>Portofolio Karya (format PDF / Link G-Drive)</strong> pada bagian dokumen persyaratan di bawah.
                    </p>
                </div>
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
                'field' => 'surat_rekomendasi_sekolah',
                'label' => 'Surat Rekomendasi Sekolah',
                'desc' => 'Surat rekomendasi dari Kepala Sekolah / Guru BK / Tokoh Masyarakat.',
                'accept' => '.pdf,.doc,.docx,.jpg,.jpeg,.png,.webp',
                'maxSize' => 'Max 5MB (PDF / JPG / PNG / DOCX)',
                'icon' => 'solar:diploma-verified-bold-duotone',
                'val' => $berkas->surat_rekomendasi_sekolah ?? $berkas->surat_rekom ?? null,
            ],
            [
                'field' => 'study_plan',
                'label' => 'Study Plan (Rencana Studi)',
                'desc' => 'Rencana studi dan sasaran akademik selama 8 semester perkuliahan.',
                'accept' => '.pdf,.docx,.doc,.jpg,.jpeg,.png,.webp',
                'maxSize' => 'Max 5MB (PDF / DOCX / JPG / PNG)',
                'icon' => 'solar:notebook-bold-duotone',
                'val' => $berkas->study_plan ?? null,
            ],
            [
                'field' => 'ijazah',
                'label' => 'Ijazah / SKL (Surat Keterangan Lulus)',
                'desc' => 'Scan Ijazah (alumni) atau SKL (opsional bagi yang belum terbit).',
                'accept' => '.pdf,.jpg,.jpeg,.png,.webp',
                'maxSize' => 'Max 5MB (PDF / JPG / PNG)',
                'icon' => 'solar:diploma-bold-duotone',
                'val' => $berkas->ijazah ?? null,
            ],
            [
                'field' => 'personal_statement',
                'label' => 'Personal Statement (Esai)',
                'desc' => 'Esai motivasi, kepemimpinan, dan komitmen kontribusi Anda.',
                'accept' => '.pdf,.docx,.doc,.jpg,.jpeg,.png,.webp',
                'maxSize' => 'Max 5MB (PDF / DOCX / JPG / PNG)',
                'icon' => 'solar:document-text-bold-duotone',
                'val' => $berkas->personal_statement ?? null,
            ],
        ];

        // Submission tambahan khusus program studi DKV
        if ($isDkv) {
            $fileCards[] = [
                'field' => 'surat_buta_warna',
                'label' => 'Surat Bebas Buta Warna (DKV)',
                'desc' => 'Surat keterangan medis bebas buta warna dari dokter/puskesmas/klinik (PDF / JPG / PNG).',
                'accept' => '.pdf,.jpg,.jpeg,.png,.webp',
                'maxSize' => 'Max 5MB (PDF / JPG / PNG)',
                'icon' => 'solar:eye-scan-bold-duotone',
                'val' => $berkas->surat_buta_warna ?? null,
                'badge' => 'Wajib DKV',
            ];
            $fileCards[] = [
                'field' => 'portfolio',
                'label' => 'Portofolio Karya (DKV)',
                'desc' => 'Portofolio karya desain / gambar / karya visual (Wajib format PDF, maks 10MB) atau link G-Drive.',
                'accept' => '.pdf',
                'maxSize' => 'Max 10MB (PDF)',
                'icon' => 'solar:gallery-wide-bold-duotone',
                'val' => $berkas->portfolio ?? null,
                'badge' => 'Wajib DKV',
                'isPortfolio' => true,
            ];
        }
    @endphp

    <!-- Tips Upload Berkas -->
    <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-amber-50/80 border border-amber-200 text-amber-900 flex items-start gap-3 shadow-xs">
        <span class="iconify text-xl text-amber-600 shrink-0 mt-0.5" data-icon="solar:info-circle-bold"></span>
        <div class="text-xs space-y-1">
            <p class="font-bold text-amber-950">Panduan Mengunggah Berkas:</p>
            <p class="text-amber-800 leading-relaxed">
                Pastikan dokumen terbaca jelas dengan format <strong>PDF, JPG, PNG, atau WEBP</strong> (maks. 5MB–10MB). Bagi pengguna <strong>iPhone (iOS)</strong>, pastikan foto tidak berformat <em>.HEIC</em> (silakan simpan sebagai JPG atau screenshot terlebih dahulu sebelum diunggah).
            </p>
        </div>
    </div>

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
                        <div class="flex items-center gap-1.5 min-w-0">
                            <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 tracking-tight truncate">{{ $card['label'] }}</h3>
                            @if(!empty($card['badge']))
                                <span class="shrink-0 px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    {{ $card['badge'] }}
                                </span>
                            @endif
                        </div>
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
                            <a href="{{ str_starts_with($card['val'], 'http') ? $card['val'] : asset('storage/' . $card['val']) }}" target="_blank" 
                               class="text-xs font-bold text-slate-800 hover:text-blue-600 hover:underline truncate">
                                {{ str_starts_with($card['val'], 'http') ? 'Buka Link Portofolio' : 'Lihat Berkas Tersimpan' }}
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

                        @if(!empty($card['isPortfolio']))
                            <div class="space-y-2">
                                <div class="relative">
                                    <input type="file" 
                                           id="file-{{ $card['field'] }}" 
                                           name="{{ $card['field'] }}" 
                                           accept="{{ $card['accept'] }}" 
                                           class="hidden" 
                                           onchange="onFileSelected(this, '{{ $card['field'] }}')">
                                    
                                    <label for="file-{{ $card['field'] }}" 
                                           class="flex items-center justify-center gap-2 p-2.5 border border-dashed border-slate-200 hover:border-blue-400 rounded-xl bg-slate-50/60 hover:bg-blue-50/20 transition-all cursor-pointer text-center group">
                                        <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <span class="text-xs font-bold text-slate-700 group-hover:text-blue-600">Pilih File PDF Portofolio</span>
                                        <span class="text-[10px] text-slate-400">({{ $card['maxSize'] }})</span>
                                    </label>

                                    <p id="preview-{{ $card['field'] }}" class="hidden text-xs font-bold text-blue-600 mt-1 truncate"></p>
                                </div>

                                <div class="relative flex items-center justify-center">
                                    <div class="border-t border-slate-200 w-full"></div>
                                    <span class="bg-white px-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider absolute">Atau Link G-Drive</span>
                                </div>

                                <div>
                                    <input type="url" name="portfolio" placeholder="https://drive.google.com/..." 
                                           oninput="if(this.value){ document.getElementById('btn-save-{{ $card['field'] }}').classList.remove('hidden'); } else if(!document.getElementById('file-{{ $card['field'] }}').files.length) { document.getElementById('btn-save-{{ $card['field'] }}').classList.add('hidden'); }"
                                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 placeholder:text-slate-400 focus:border-blue-600 focus:bg-white outline-none transition-all">
                                </div>
                            </div>
                        @else
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
                        @endif

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

    <!-- 3. SUBMISSION VIDEO MOTIVASI SECTION -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-4 sm:px-6 py-3.5 sm:py-4 bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-indigo-500/20 text-indigo-400 border border-indigo-400/30 flex items-center justify-center shrink-0">
                    <span class="iconify text-lg sm:text-xl" data-icon="solar:videocamera-record-bold-duotone"></span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xs sm:text-sm font-extrabold text-white tracking-tight">Submission Video Motivasi</h2>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-500/30 text-indigo-200 border border-indigo-400/30">Tahap Seleksi</span>
                    </div>
                    <p class="text-[10px] sm:text-[11px] text-slate-300 font-medium">Cantumkan tautan (link) video motivasi perkenalan diri dan konten kreatif Anda.</p>
                </div>
            </div>
            @php
                $hasUtama = !empty($berkas->motivasi_video);
                $hasTiktok = !empty($berkas->motivasi_video_tiktok);
            @endphp
            <div class="shrink-0 flex items-center gap-2">
                @if($hasUtama && $hasTiktok)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Video Lengkap</span>
                    </span>
                @elseif($hasUtama || $hasTiktok)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-400/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                        <span>Tersimpan Sebagian</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/10 text-slate-300 border border-white/15">
                        <span>Belum Mengisi</span>
                    </span>
                @endif
            </div>
        </div>

        <form action="{{ route('pendaftar.berkas.store') }}" method="POST" class="p-4 sm:p-6 md:p-8 space-y-5">
            @csrf
            <input type="hidden" name="upload_field" value="motivasi_video">

            <!-- Information Tip -->
            <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-indigo-50/70 border border-indigo-100 text-indigo-950 flex items-start gap-3">
                <span class="iconify text-xl text-indigo-600 shrink-0 mt-0.5" data-icon="solar:info-circle-bold"></span>
                <div class="text-xs space-y-1">
                    <p class="font-bold text-indigo-950">Petunjuk Pengisian Link Video:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-indigo-900 leading-relaxed font-medium">
                        <li><strong>Video Motivasi Utama:</strong> Unggah ke <strong>YouTube (Public / Unlisted)</strong>, <strong>Google Drive (Akses dibuka "Siapa saja yang memiliki link")</strong>, atau <strong>Instagram Reels</strong>.</li>
                        <li><strong>Video TikTok (Opsional):</strong> Jika Anda mengunggah konten video ke TikTok, cantumkan tautan postingan TikTok Anda.</li>
                        <li>Pastikan link dapat diakses dan tidak di-private agar tim panitia beasiswa dapat meninjau video Anda.</li>
                    </ul>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <!-- Field 1: Video Motivasi Utama -->
                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 space-y-3 flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <label for="motivasi_video" class="block text-xs font-black text-slate-800 tracking-wide">
                                Link Video Motivasi Utama <span class="text-indigo-600">*</span>
                            </label>
                            @if(!empty($berkas->motivasi_video))
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Tersimpan
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-200 text-slate-600">
                                    Belum Diisi
                                </span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed font-normal">
                            Link video YouTube / Google Drive / Reels berisi perkenalan diri, alasan memilih MNC University, cita-cita dan komitmen beasiswa.
                        </p>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <span class="iconify text-base" data-icon="solar:link-bold"></span>
                            </div>
                            <input type="text" 
                                   id="motivasi_video" 
                                   name="motivasi_video" 
                                   value="{{ old('motivasi_video', $berkas->motivasi_video ?? '') }}" 
                                   placeholder="https://www.youtube.com/watch?v=... atau https://drive.google.com/..." 
                                   class="w-full bg-white text-slate-800 font-medium text-xs sm:text-sm rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/15 pl-10 pr-3.5 py-2.5 sm:py-3 outline-none transition-all">
                        </div>
                    </div>

                    @if(!empty($berkas->motivasi_video))
                    <div class="pt-2 flex items-center justify-between gap-2 border-t border-slate-200/60">
                        <a href="{{ $berkas->motivasi_video }}" target="_blank" rel="noopener noreferrer" 
                           class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline truncate">
                            <span class="iconify text-sm shrink-0" data-icon="solar:play-bold"></span>
                            <span>Buka / Tonton Video Utama</span>
                            <span class="iconify text-xs shrink-0" data-icon="solar:arrow-right-up-linear"></span>
                        </a>
                        <button type="button" 
                                onclick="confirmDeleteFile('motivasi_video')"
                                title="Hapus Link Ini"
                                class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors shrink-0">
                            <span class="iconify text-base" data-icon="solar:trash-bin-trash-bold"></span>
                        </button>
                    </div>
                    @endif
                </div>

                <!-- Field 2: Video Motivasi TikTok -->
                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 space-y-3 flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <label for="motivasi_video_tiktok" class="block text-xs font-black text-slate-800 tracking-wide flex items-center gap-1.5">
                                <span>Link Konten Video TikTok</span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-200 text-slate-600">Opsional</span>
                            </label>
                            @if(!empty($berkas->motivasi_video_tiktok))
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Tersimpan
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-200 text-slate-600">
                                    Belum Diisi
                                </span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed font-normal">
                            Link video TikTok perkenalan atau kampanye beasiswa yang Anda unggah ke akun TikTok pribadi Anda.
                        </p>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <span class="iconify text-base" data-icon="logos:tiktok-icon"></span>
                            </div>
                            <input type="text" 
                                   id="motivasi_video_tiktok" 
                                   name="motivasi_video_tiktok" 
                                   value="{{ old('motivasi_video_tiktok', $berkas->motivasi_video_tiktok ?? '') }}" 
                                   placeholder="https://www.tiktok.com/@username/video/..." 
                                   class="w-full bg-white text-slate-800 font-medium text-xs sm:text-sm rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/15 pl-10 pr-3.5 py-2.5 sm:py-3 outline-none transition-all">
                        </div>
                    </div>

                    @if(!empty($berkas->motivasi_video_tiktok))
                    <div class="pt-2 flex items-center justify-between gap-2 border-t border-slate-200/60">
                        <a href="{{ $berkas->motivasi_video_tiktok }}" target="_blank" rel="noopener noreferrer" 
                           class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline truncate">
                            <span class="iconify text-sm shrink-0" data-icon="solar:play-bold"></span>
                            <span>Buka / Tonton Video TikTok</span>
                            <span class="iconify text-xs shrink-0" data-icon="solar:arrow-right-up-linear"></span>
                        </a>
                        <button type="button" 
                                onclick="confirmDeleteFile('motivasi_video_tiktok')"
                                title="Hapus Link Ini"
                                class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors shrink-0">
                            <span class="iconify text-base" data-icon="solar:trash-bin-trash-bold"></span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition-all shadow-xs active:scale-[0.99] cursor-pointer">
                    <span class="iconify text-base" data-icon="solar:diskette-bold"></span>
                    <span>Simpan Link Video Motivasi</span>
                </button>
            </div>
        </form>
    </div>

    <!-- 4. MULTIPLE SERTIFIKAT PRESTASI SECTION -->
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
                $sertifikatList = (isset($sertifikats) && $sertifikats->count() > 0) ? $sertifikats : ($peserta->sertifikats ?? collect());
            @endphp

            @if($sertifikatList->count() > 0)
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                            <span class="iconify text-base text-blue-600" data-icon="solar:medal-ribbons-star-bold"></span>
                            <span>Sertifikat Terdaftar ({{ $sertifikatList->count() }} Berkas)</span>
                        </h3>
                        <span class="text-[10px] text-slate-400 font-semibold">Klik nama atau tombol lihat untuk preview file</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach($sertifikatList as $s)
                            <div class="p-2.5 sm:p-3 rounded-xl bg-white border border-slate-200/90 hover:border-blue-300 flex items-center justify-between gap-2.5 shadow-xs transition-all">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                        <span class="iconify text-lg" data-icon="solar:file-check-bold"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ asset('storage/' . $s->file) }}" target="_blank" class="text-xs font-bold text-slate-800 hover:text-blue-600 hover:underline truncate block" title="{{ $s->nama }}">
                                            {{ $s->nama ?? $s->nama_sertifikat ?? 'Sertifikat Prestasi' }}
                                        </a>
                                        <span class="text-[10px] text-slate-400 font-medium">Tersimpan di sistem ({{ $s->tahun ?? date('Y') }})</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <a href="{{ asset('storage/' . $s->file) }}" target="_blank" 
                                       title="Lihat Dokumen"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-50 hover:bg-blue-50 text-slate-600 hover:text-blue-600 text-xs font-bold border border-slate-200 hover:border-blue-200 transition-all">
                                        <span class="iconify text-sm" data-icon="solar:eye-bold"></span>
                                        <span>Lihat</span>
                                    </a>
                                    <button type="button" 
                                            onclick="confirmDeleteSertifikat('{{ $s->id }}', '{{ addslashes($s->nama ?? $s->nama_sertifikat ?? 'Sertifikat') }}')"
                                            title="Hapus Sertifikat"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-3.5 text-center bg-slate-50/80 rounded-xl border border-dashed border-slate-200">
                    <p class="text-xs text-slate-400 font-medium">Belum ada sertifikat prestasi yang diunggah.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- 4. BUKTI FOLLOW MEDIA SOSIAL RESMI SECTION -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-4 sm:px-6 py-3.5 sm:py-4 bg-slate-50/70 border-b border-slate-200/70 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-gradient-to-tr from-amber-500 via-rose-500 to-purple-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <span class="iconify text-base sm:text-lg" data-icon="solar:camera-minimalistic-bold"></span>
                </div>
                <div>
                    <h2 class="text-xs sm:text-sm font-extrabold text-slate-900 tracking-tight">Bukti Follow Media Sosial Resmi (Instagram & TikTok)</h2>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 font-medium">Unggah tangkapan layar (screenshot) bukti bahwa Anda telah mem-follow akun resmi di bawah ini.</p>
                </div>
            </div>
            <span class="text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-200 px-2.5 py-1 rounded-full hidden sm:inline-block">
                Wajib Disertakan
            </span>
        </div>

        <div class="p-4 sm:p-6 md:p-8 space-y-4 sm:space-y-6">
            <!-- Instruction Note -->
            <div class="p-3.5 sm:p-4 rounded-xl bg-amber-50/80 border border-amber-200/80 text-amber-950 text-xs flex items-start gap-3">
                <span class="iconify text-lg text-amber-600 shrink-0 mt-0.5" data-icon="solar:info-circle-bold"></span>
                <p class="leading-relaxed">
                    <strong>Petunjuk Screenshot:</strong> Silakan klik nama akun untuk membuka profil dan mem-follow, lalu ambil tangkapan layar (screenshot) yang memperlihatkan status <em>"Following"</em> atau <em>"Mengikuti"</em> akun Anda. Format: JPG, PNG, atau WebP (maks. 10MB per file).
                </p>
            </div>

            @php
                $socialCards = [
                    [
                        'field' => 'bukti_follow_ig_beasiswamncu',
                        'platform' => 'Instagram',
                        'handle' => '@beasiswamncu',
                        'name' => 'Beasiswa MNC University',
                        'url' => 'https://www.instagram.com/beasiswamncu?cplk=NDVzMnB0ZzNuanR0',
                        'val' => $berkas->bukti_follow_ig_beasiswamncu ?? null,
                    ],
                    [
                        'field' => 'bukti_follow_ig_mncu',
                        'platform' => 'Instagram',
                        'handle' => '@mncuniversity',
                        'name' => 'MNC University Official',
                        'url' => 'https://www.instagram.com/mncuniversity?srtk=MWk4YndhM3VmcDhwaw==',
                        'val' => $berkas->bukti_follow_ig_mncu ?? null,
                    ],
                    [
                        'field' => 'bukti_follow_ig_mncunation',
                        'platform' => 'Instagram',
                        'handle' => '@mncunation',
                        'name' => 'MNCU Nation (Komunitas)',
                        'url' => 'https://www.instagram.com/mncunation?vrfl=N3F1bXZyNHR3ZDNt',
                        'val' => $berkas->bukti_follow_ig_mncunation ?? null,
                    ],
                    [
                        'field' => 'bukti_follow_ig_thearetegen',
                        'platform' => 'Instagram',
                        'handle' => '@thearetegen.id',
                        'name' => 'The Arete Generation',
                        'url' => 'https://www.instagram.com/thearetegen.id?vrfl=MW9iaHBybzNhcWt2OA==',
                        'val' => $berkas->bukti_follow_ig_thearetegen ?? null,
                    ],
                    [
                        'field' => 'bukti_follow_tiktok_beasiswamncu',
                        'platform' => 'TikTok',
                        'handle' => '@beasiswamncu',
                        'name' => 'TikTok Beasiswa MNCU',
                        'url' => 'https://www.tiktok.com/@beasiswamncu?_r=1&_t=ZS-9B6yaQHGujU',
                        'val' => $berkas->bukti_follow_tiktok_beasiswamncu ?? null,
                    ],
                    [
                        'field' => 'bukti_follow_tiktok_mncu',
                        'platform' => 'TikTok',
                        'handle' => '@mncuniversity',
                        'name' => 'TikTok MNC University',
                        'url' => 'https://www.tiktok.com/@mncuniversity?_r=1&_t=ZS-9B6yaYb2CaU',
                        'val' => $berkas->bukti_follow_tiktok_mncu ?? null,
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($socialCards as $sc)
                <div class="p-4 rounded-2xl border {{ $sc['val'] ? 'border-emerald-200 bg-emerald-50/30' : 'border-slate-200 bg-slate-50/60' }} flex flex-col justify-between space-y-3">
                    <div>
                        <!-- Header Card -->
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold {{ $sc['platform'] === 'TikTok' ? 'bg-slate-900 text-white' : 'bg-gradient-to-r from-amber-500 to-rose-500 text-white' }}">
                                {{ $sc['platform'] }}
                            </span>

                            @if($sc['val'])
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Tersimpan</span>
                                </span>
                            @else
                                <span class="text-[10px] font-semibold text-slate-400">
                                    Belum Diunggah
                                </span>
                            @endif
                        </div>

                        <h4 class="text-xs font-extrabold text-slate-900">{{ $sc['name'] }}</h4>
                        <a href="{{ $sc['url'] }}" target="_blank" rel="noopener noreferrer" 
                           class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 hover:text-blue-800 hover:underline mt-0.5 mb-2">
                            <span>{{ $sc['handle'] }}</span>
                            <span class="iconify text-xs" data-icon="solar:arrow-right-up-bold"></span>
                        </a>
                    </div>

                    <!-- Upload Form / Preview Area -->
                    <div>
                        @if($sc['val'])
                            <div class="p-2.5 rounded-xl bg-white border border-slate-200 flex items-center justify-between gap-2">
                                <a href="{{ asset('storage/' . $sc['val']) }}" target="_blank" class="flex items-center gap-2 min-w-0 group">
                                    <img src="{{ asset('storage/' . $sc['val']) }}" alt="Screenshot" class="w-9 h-9 rounded-lg object-cover border border-slate-200 shrink-0">
                                    <div class="min-w-0">
                                        <span class="text-xs font-bold text-slate-800 group-hover:text-blue-600 group-hover:underline truncate block">Lihat Screenshot</span>
                                        <span class="text-[10px] text-emerald-600 font-semibold">Terkonfirmasi</span>
                                    </div>
                                </a>
                                <button type="button" 
                                        onclick="confirmDeleteFile('{{ $sc['field'] }}')"
                                        title="Hapus Screenshot"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        @else
                            <form action="{{ route('pendaftar.berkas.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateSingleForm(this)">
                                @csrf
                                <input type="hidden" name="upload_field" value="{{ $sc['field'] }}">

                                <div class="relative">
                                    <input type="file" 
                                           id="file-{{ $sc['field'] }}" 
                                           name="{{ $sc['field'] }}" 
                                           accept=".jpg,.jpeg,.png,.webp" 
                                           class="hidden" 
                                           onchange="onFileSelected(this, '{{ $sc['field'] }}')">
                                    
                                    <label for="file-{{ $sc['field'] }}" 
                                           class="flex items-center justify-center gap-2 p-2.5 border border-dashed border-slate-300 hover:border-blue-500 rounded-xl bg-white hover:bg-blue-50/30 transition-all cursor-pointer text-center group">
                                        <span class="iconify text-base text-slate-400 group-hover:text-blue-600" data-icon="solar:camera-add-bold"></span>
                                        <span class="text-xs font-bold text-slate-700 group-hover:text-blue-600">Pilih Screenshot</span>
                                    </label>

                                    <p id="preview-{{ $sc['field'] }}" class="hidden text-[11px] font-bold text-blue-600 mt-1.5 truncate"></p>
                                </div>

                                <button type="submit" 
                                        id="btn-save-{{ $sc['field'] }}" 
                                        class="hidden mt-2 w-full py-1.5 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                                    Unggah Bukti Follow
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
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

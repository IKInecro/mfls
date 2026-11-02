@extends('pendaftar.layout')

@section('content')
<div class="max-w-5xl mx-auto space-y-4 sm:space-y-6 pb-28 sm:pb-32" x-data="{ activeTab: 1 }">
    
    <!-- Hero Header (Grand Royal Navy & Slate Theme - Mobile Friendly) -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br from-[#0B1120] via-[#0F172A] to-[#1E293B] text-white p-5 sm:p-7 md:p-8 border border-slate-800 shadow-xl">
        <div class="absolute -top-32 -left-32 w-80 h-80 rounded-full bg-blue-600/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 rounded-full bg-amber-400/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 sm:gap-6">
            <div class="space-y-1.5 sm:space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/10 border border-white/15 text-[10px] sm:text-[11px] font-bold text-slate-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    <span>REKAM AKADEMIS PESERTA SELEKSI</span>
                </div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-white tracking-tight">
                    Input Nilai Rapor Resmi
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 font-medium leading-relaxed">
                    Masukkan nilai pengetahuan rapor (skala 0 - 100) dan lampirkan bukti scan fisik rapor per semester untuk validasi.
                </p>
                <div class="pt-1 flex flex-wrap items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md bg-blue-500/20 text-blue-300 text-[11px] font-semibold border border-blue-500/30">
                        Wajib Minimal 2 Mapel Pendukung
                    </span>
                </div>
            </div>

            <div class="shrink-0 w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-blue-400 shadow-sm hidden xs:flex">
                <span class="iconify text-2xl sm:text-3xl" data-icon="solar:chart-square-bold-duotone"></span>
            </div>
        </div>
    </div>

    <form action="{{ route('pendaftar.nilai.store') }}" method="POST" enctype="multipart/form-data" id="mainNilaiForm" class="space-y-4 sm:space-y-6">
        @csrf

        <!-- Sticky Semester Navigation Bar (Fitted for Mobile without horizontal overflow) -->
        <div class="sticky top-16 sm:top-20 z-20 py-1.5 sm:py-2">
            <div class="bg-white/95 backdrop-blur-md p-1 sm:p-1.5 rounded-xl sm:rounded-2xl border border-slate-200/90 shadow-md flex items-center gap-1 sm:gap-1.5 max-w-lg mx-auto w-full">
                @for ($i = 1; $i <= $maxSemester; $i++)
                    <button type="button" 
                            @click="activeTab = {{ $i }}" 
                            onclick="setActiveSem({{ $i }})"
                            class="flex-1 min-w-0 py-2 sm:py-2.5 px-1 sm:px-3 rounded-lg sm:rounded-xl text-xs font-bold transition-all duration-150 flex items-center justify-center gap-1 cursor-pointer"
                            :class="activeTab === {{ $i }} ? 'bg-[#0870B8] text-white shadow-xs' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100'">
                        <span class="text-[9px] opacity-70 hidden xs:inline">SEM</span>
                        <span class="text-xs font-black">{{ $i }}</span>
                    </button>
                @endfor
            </div>
        </div>

        <!-- Semester Content Panels -->
        <div class="space-y-4 sm:space-y-6">
            @for ($semKey = 1; $semKey <= $maxSemester; $semKey++)
            <div x-show="activeTab === {{ $semKey }}" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-4 sm:space-y-6">

                <!-- 1. Bukti Scan Fisik Rapor Card -->
                <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs p-4 sm:p-6 md:p-8 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5 sm:gap-3">
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                <span class="iconify text-base sm:text-lg" data-icon="solar:camera-bold"></span>
                            </div>
                            <div>
                                <h2 class="text-xs sm:text-sm font-extrabold text-slate-900">Scan Fisik Rapor Semester {{ $semKey }}</h2>
                                <p class="text-[10px] sm:text-[11px] text-slate-500 font-medium">Unggah file scan halaman nilai rapor (PDF/JPG/PNG, maks 10MB).</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6 items-start">
                        <!-- Upload Dropzone -->
                        <div class="lg:col-span-5">
                            <label class="flex flex-col items-center justify-center w-full h-32 sm:h-40 border border-dashed border-slate-200 hover:border-blue-400 rounded-xl sm:rounded-2xl cursor-pointer bg-slate-50/60 hover:bg-blue-50/20 transition-all text-center p-3 sm:p-4 group">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-slate-400 group-hover:text-blue-600 transition-colors mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span class="text-xs font-bold text-slate-700 group-hover:text-blue-600">Pilih Berkas Rapor S{{ $semKey }}</span>
                                <span class="text-[10px] text-slate-400 mt-0.5">Dapat memilih lebih dari 1 file</span>
                                <input type="file" name="rapor{{ $semKey }}[]" multiple accept=".pdf,.jpg,.jpeg,.png,.webp" class="hidden" onchange="previewFiles(this, 'preview-{{ $semKey }}', {{ $semKey }})" />
                            </label>
                        </div>

                        <!-- Uploaded Files Preview List -->
                        <div class="lg:col-span-7 space-y-2 sm:space-y-3">
                            <span class="text-xs font-bold text-slate-700 block">Daftar Berkas Terunggah:</span>
                            <div id="preview-{{ $semKey }}" class="space-y-2">
                                @php 
                                    $field = 'rapor'.$semKey;
                                    $uploadedFiles = ($berkas && $berkas->$field) ? json_decode($berkas->$field) : null;
                                @endphp
                                @if($uploadedFiles)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach(is_array($uploadedFiles) ? $uploadedFiles : [$uploadedFiles] as $idx => $file)
                                        <div class="p-2.5 sm:p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-2">
                                            <a href="{{ asset('storage/' . $file) }}" target="_blank" class="flex items-center gap-2 min-w-0 text-xs font-bold text-slate-800 hover:text-blue-600 truncate">
                                                <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span class="truncate">Berkas {{ $idx + 1 }}</span>
                                            </a>
                                            <button type="button" onclick="confirmDeleteFile('rapor{{ $semKey }}', '{{ $file }}')" class="p-1 rounded text-slate-400 hover:text-rose-600 transition-colors cursor-pointer shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-3 sm:p-4 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-center">
                                        <p class="text-[11px] text-slate-400 font-medium">Belum ada file scan rapor untuk semester ini.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Mata Pelajaran Inti (Bahasa Indonesia, Matematika Wajib, Bahasa Inggris) -->
                <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs p-4 sm:p-6 md:p-8 space-y-4 sm:space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5 sm:gap-3">
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                <span class="iconify text-base sm:text-lg" data-icon="solar:pen-new-square-bold-duotone"></span>
                            </div>
                            <div>
                                <h2 class="text-xs sm:text-sm font-extrabold text-slate-900">Mata Pelajaran Wajib (Semester {{ $semKey }})</h2>
                                <p class="text-[10px] sm:text-[11px] text-slate-500 font-medium">Nilai pengetahuan rapor skala 0 s.d. 100.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                        @foreach($matpels as $mp)
                            @php 
                                $score = isset($existingNilai[$mp->id]) ? ($existingNilai[$mp->id]->where('semester', $semKey)->first()->nilai ?? '') : ''; 
                            @endphp
                            <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 tracking-wide truncate">
                                    {{ $mp->nama }}
                                </label>
                                <div class="relative">
                                    <input type="number" 
                                           step="0.01" 
                                           min="0" 
                                           max="100" 
                                           name="nilai[{{ $mp->id }}][{{ $semKey }}]" 
                                           value="{{ $score }}" 
                                           placeholder="0.00"
                                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm sm:text-base font-bold text-slate-900 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 outline-none transition-all tabular-nums">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[9px] font-bold text-slate-400">Poin</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Mata Pelajaran Pendukung (Min 2) -->
                <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs p-4 sm:p-6 md:p-8 space-y-4 sm:space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5 sm:gap-3">
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                                <span class="iconify text-base sm:text-lg" data-icon="solar:add-circle-bold-duotone"></span>
                            </div>
                            <div>
                                <h2 class="text-xs sm:text-sm font-extrabold text-slate-900">Mata Pelajaran Pendukung (Semester {{ $semKey }})</h2>
                                <p class="text-[10px] sm:text-[11px] text-slate-500 font-medium">Wajib mengisi minimal 2 mata pelajaran pendukung.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 sm:gap-4">
                        @for($slotIdx = 1; $slotIdx <= 2; $slotIdx++)
                            @php
                                $existing = isset($customNilaiBySemester[$semKey]) ? $customNilaiBySemester[$semKey]->values()->get($slotIdx - 1) : null;
                                $subjName = $existing ? $existing->matpel->nama : '';
                                $subjNilai = $existing ? $existing->nilai : '';
                            @endphp
                            <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Mapel Pendukung 0{{ $slotIdx }}</span>
                                    <span class="text-[9px] font-bold text-blue-600">Slot Wajib</span>
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-[11px] font-semibold text-slate-600">Nama Mata Pelajaran</label>
                                    <input type="text" 
                                           name="custom_matpel[{{ $semKey }}][{{ $slotIdx }}][nama]" 
                                           value="{{ old("custom_matpel.$semKey.$slotIdx.nama", $subjName) }}"
                                           placeholder="Contoh: Fisika / Ekonomi" 
                                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 outline-none transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-[11px] font-semibold text-slate-600">Nilai Pengetahuan</label>
                                    <div class="relative">
                                        <input type="number" 
                                               step="0.01" 
                                               min="0" 
                                               max="100" 
                                               name="custom_matpel[{{ $semKey }}][{{ $slotIdx }}][nilai]" 
                                               value="{{ old("custom_matpel.$semKey.$slotIdx.nilai", $subjNilai) }}"
                                               placeholder="0.00"
                                               class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm sm:text-base font-bold text-slate-900 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 outline-none transition-all tabular-nums">
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[9px] font-bold text-slate-400">Poin</span>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

            </div>
            @endfor
        </div>

        <!-- Floating Bottom Dock: Simpan Transkrip Nilai (Responsive Mobile) -->
        <div class="fixed bottom-4 sm:bottom-6 left-1/2 -translate-x-1/2 z-30 w-full max-w-md px-3 sm:px-4 pointer-events-auto">
            <div class="bg-slate-900/95 text-white p-2.5 sm:p-3 rounded-2xl shadow-2xl border border-slate-800 backdrop-blur-md flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0 pl-1.5">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0">
                        <span class="iconify text-sm sm:text-base" data-icon="solar:diskette-bold"></span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] sm:text-xs font-bold text-white truncate">Transkrip Nilai</p>
                        <p class="text-[9px] text-slate-400 truncate">Simpan perubahan</p>
                    </div>
                </div>
                <button type="submit" 
                        class="px-4 sm:px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all shadow-xs active:scale-[0.98] cursor-pointer shrink-0">
                    Simpan Nilai
                </button>
            </div>
        </div>

    </form>

    {{-- Hidden Delete Form --}}
    <form id="deleteFileForm" action="{{ route('pendaftar.berkas.delete_file') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="field" id="deleteField">
        <input type="hidden" name="file_path" id="deleteFilePath">
    </form>
</div>

<!-- Interactive Scripts for Nilai Page -->
<script>
window.__activeSem = 1;
function setActiveSem(i) { window.__activeSem = i; }

function confirmDeleteFile(field, filePath = null) {
    if (window.modernAlert) {
        window.modernAlert({
            type: 'warning',
            title: 'Hapus Berkas Rapor?',
            message: 'Berkas scan rapor yang dihapus tidak dapat dipulihkan kembali.',
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

function previewFiles(input, previewId, sem = null) {
    const previewEl = document.getElementById(previewId);
    if (!previewEl) return;

    let html = '';
    if (input.files && input.files.length > 0) {
        Array.from(input.files).forEach(file => {
            html += `<div class="p-2 sm:p-2.5 rounded-xl bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-200/80 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                <span class="truncate">${file.name}</span>
            </div>`;
        });
    }

    let container = previewEl.querySelector('.new-files-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'new-files-container grid grid-cols-1 sm:grid-cols-2 gap-2 w-full mt-2.5 pt-2.5 border-t border-slate-100';
        previewEl.appendChild(container);
    }
    container.innerHTML = html;
}

(function() {
    const mainForm = document.getElementById('mainNilaiForm');
    if (mainForm) {
        mainForm.addEventListener('submit', function(e) {
            let filledCount = 0;
            document.querySelectorAll('input[name^="custom_matpel"][name$="[nama]"]').forEach(input => {
                const scoreInput = document.querySelector(`input[name="${input.name.replace('[nama]', '[nilai]')}"]`);
                if (input.value.trim() !== '' && scoreInput && scoreInput.value.trim() !== '') {
                    filledCount++;
                }
            });

            if (filledCount < 2) {
                e.preventDefault();
                if (window.modernAlert) {
                    window.modernAlert({
                        type: 'warning',
                        title: 'Lengkapi Mapel Pendukung',
                        message: 'Wajib mengisi minimal 2 Mata Pelajaran Pendukung beserta nilainya.',
                        confirmText: 'Mengerti'
                    });
                }
            }
        });
    }
})();
</script>
@endsection

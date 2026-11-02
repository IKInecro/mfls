@extends('layouts.admin')

@section('content')
<div class="space-y-6 pb-12" id="timeline-manager-root">

    {{-- Breadcrumb & Flash Message --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <span>Pengaturan</span>
                <span class="iconify" data-icon="solar:alt-arrow-right-linear"></span>
                <span class="text-primary-blue">Timeline & Tahapan Seleksi</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <span>Linimasa Seleksi Batch 3 (Ajaran 2027)</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-100 text-primary-blue border border-blue-200">
                    Live Sync
                </span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1">
                Atur jadwal, status pengerjaan, dan sorotan tahap aktif yang langsung ditampilkan pada landing page publik.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ url('/#timeline') }}" target="_blank"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-primary-blue hover:border-blue-300 text-xs font-bold transition-all shadow-xs">
                <span class="iconify text-sm" data-icon="solar:square-top-down-bold"></span>
                <span>Buka Landing Page</span>
            </a>

            <form action="{{ route('admin.pengaturan.timeline.reset') }}" method="POST"
                onsubmit="return confirm('Apakah Anda yakin ingin me-reset linimasa seleksi ke susunan standar 7 tahapan default?')">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 text-xs font-bold transition-all shadow-xs">
                    <span class="iconify text-sm text-slate-500" data-icon="solar:restart-bold"></span>
                    <span>Reset Default</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-3 shadow-xs animate-fadeIn">
            <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="iconify text-lg" data-icon="solar:check-circle-bold"></span>
            </span>
            <div class="flex-1">{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium space-y-1 shadow-xs">
            <div class="font-bold flex items-center gap-2 text-rose-900">
                <span class="iconify text-base" data-icon="solar:danger-triangle-bold"></span>
                <span>Terdapat kesalahan pada input tahapan:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-700">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Split Layout: Left Form Editor (7 cols) + Right Live Preview (5 cols) --}}
    <form action="{{ route('admin.pengaturan.timeline.update') }}" method="POST" id="timelineForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {{-- Column 1: Step Editor (7 cols) --}}
            <div class="lg:col-span-7 space-y-4">
                
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-4 sm:p-5 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span class="iconify text-primary-blue text-lg" data-icon="solar:checklist-minimalistic-bold-duotone"></span>
                            <span>Daftar Tahapan Seleksi</span>
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Ubah teks, urutan, atau tandai tahap yang sedang berjalan (Aktif).
                        </p>
                    </div>

                    <button type="button" onclick="addNewStep()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 border border-blue-200 text-primary-blue text-xs font-bold transition-all shadow-xs">
                        <span class="iconify text-sm" data-icon="solar:add-circle-bold"></span>
                        <span>Tambah Tahap</span>
                    </button>
                </div>

                {{-- Steps Container --}}
                <div id="steps-container" class="space-y-3.5">
                    @foreach($timeline as $index => $item)
                        <div class="step-card bg-white rounded-2xl border border-slate-200/90 shadow-xs hover:border-slate-300 transition-all p-4 sm:p-5 relative group"
                            data-index="{{ $index }}">
                            
                            {{-- Step Header bar --}}
                            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100 gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-slate-900 text-white text-xs font-black flex items-center justify-center step-badge-number">
                                        {{ $item['step'] ?? str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-800 step-card-title-preview truncate max-w-[180px] sm:max-w-xs">
                                        {{ $item['title'] ?? 'Tahapan Seleksi' }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2">
                                    {{-- Radio Tahap Aktif --}}
                                    <label class="cursor-pointer inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border transition-colors {{ !empty($item['active']) ? 'bg-amber-50 text-amber-800 border-amber-300' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                                        <input type="radio" name="active_index" value="{{ $index }}" {{ !empty($item['active']) ? 'checked' : '' }}
                                            class="step-active-radio text-amber-600 focus:ring-amber-500 w-3.5 h-3.5"
                                            onchange="handleActiveStepChange()">
                                        <span>Tahap Aktif</span>
                                    </label>

                                    {{-- Move Up / Down --}}
                                    <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden bg-slate-50">
                                        <button type="button" onclick="moveStep({{ $index }}, -1)" title="Pindahkan Ke Atas"
                                            class="px-1.5 py-1 text-slate-500 hover:text-slate-900 hover:bg-slate-200 transition-colors">
                                            <span class="iconify text-xs" data-icon="solar:arrow-up-linear"></span>
                                        </button>
                                        <div class="w-px h-3 bg-slate-200"></div>
                                        <button type="button" onclick="moveStep({{ $index }}, 1)" title="Pindahkan Ke Bawah"
                                            class="px-1.5 py-1 text-slate-500 hover:text-slate-900 hover:bg-slate-200 transition-colors">
                                            <span class="iconify text-xs" data-icon="solar:arrow-down-linear"></span>
                                        </button>
                                    </div>

                                    {{-- Delete button --}}
                                    <button type="button" onclick="deleteStep({{ $index }})" title="Hapus Tahapan"
                                        class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                        <span class="iconify text-base" data-icon="solar:trash-bin-trash-bold"></span>
                                    </button>
                                </div>
                            </div>

                            {{-- Fields Grid --}}
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                {{-- Step Number Input --}}
                                <div class="sm:col-span-3">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nomor Tahap</label>
                                    <input type="text" name="steps[{{ $index }}][step]" value="{{ $item['step'] ?? str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}"
                                        class="step-input-number w-full text-xs font-semibold px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-primary-blue bg-white"
                                        placeholder="01" required oninput="syncPreview()">
                                </div>

                                {{-- Step Title --}}
                                <div class="sm:col-span-9">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Judul Tahapan</label>
                                    <input type="text" name="steps[{{ $index }}][title]" value="{{ $item['title'] ?? '' }}"
                                        class="step-input-title w-full text-xs font-semibold px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-primary-blue bg-white"
                                        placeholder="Contoh: Pembukaan Pendaftaran Akun" required oninput="syncPreview()">
                                </div>

                                {{-- Date Range --}}
                                <div class="sm:col-span-6">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Rentang Tanggal / Waktu</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                                            <span class="iconify text-sm" data-icon="solar:calendar-bold"></span>
                                        </span>
                                        <input type="text" name="steps[{{ $index }}][date]" value="{{ $item['date'] ?? '' }}"
                                            class="step-input-date w-full text-xs font-semibold pl-8 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-primary-blue bg-white"
                                            placeholder="Contoh: 16 Maret - 04 Mei 2026" required oninput="syncPreview()">
                                    </div>
                                </div>

                                {{-- Status Badge --}}
                                <div class="sm:col-span-6">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Status Tahapan</label>
                                    <div class="flex items-center gap-1.5">
                                        <input type="text" name="steps[{{ $index }}][status]" value="{{ $item['status'] ?? 'Mendatang' }}"
                                            class="step-input-status w-full text-xs font-semibold px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-primary-blue bg-white"
                                            placeholder="Status (Selesai, Sedang Berlangsung, Mendatang)" required oninput="syncPreview()">
                                        
                                        {{-- Quick Status Pills --}}
                                        <div class="flex items-center gap-1 shrink-0">
                                            <button type="button" onclick="setStatusValue({{ $index }}, 'Selesai')"
                                                class="px-2 py-1 text-[10px] font-bold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100" title="Set Selesai">
                                                Done
                                            </button>
                                            <button type="button" onclick="setStatusValue({{ $index }}, 'Sedang Berlangsung')"
                                                class="px-2 py-1 text-[10px] font-bold rounded-lg bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100" title="Set Sedang Berlangsung">
                                                Live
                                            </button>
                                            <button type="button" onclick="setStatusValue({{ $index }}, 'Mendatang')"
                                                class="px-2 py-1 text-[10px] font-bold rounded-lg bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200" title="Set Mendatang">
                                                Soon
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Description --}}
                                <div class="sm:col-span-12">
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Deskripsi & Keterangan Tahap</label>
                                    <textarea name="steps[{{ $index }}][desc]" rows="2"
                                        class="step-input-desc w-full text-xs font-normal px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-primary-blue bg-white"
                                        placeholder="Deskripsikan secara ringkas apa saja yang perlu dipersiapkan pendaftar pada tahapan ini..." required oninput="syncPreview()">{{ $item['desc'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Add step CTA bottom --}}
                <button type="button" onclick="addNewStep()"
                    class="w-full py-3 rounded-2xl border-2 border-dashed border-slate-300 hover:border-primary-blue hover:bg-blue-50/50 text-slate-500 hover:text-primary-blue text-xs font-bold flex items-center justify-center gap-2 transition-all">
                    <span class="iconify text-base" data-icon="solar:add-circle-bold"></span>
                    <span>Tambah Tahapan Seleksi Baru</span>
                </button>

                {{-- Action Bar --}}
                <div class="sticky bottom-4 z-20 bg-white/95 backdrop-blur-md p-4 rounded-2xl border border-slate-200/90 shadow-xl flex items-center justify-between gap-4">
                    <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Pratinjau otomatis sinkron saat mengetik.</span>
                    </div>

                    <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary-blue hover:bg-primary-blue-hover text-white text-xs font-bold transition-all shadow-md shadow-blue-900/20 hover:shadow-lg hover:shadow-blue-900/30">
                            <span class="iconify text-base" data-icon="solar:diskette-bold"></span>
                            <span>Simpan Perubahan Linimasa</span>
                        </button>
                    </div>
                </div>

            </div>

            {{-- Column 2: Sticky Live Preview (5 cols) --}}
            <div class="lg:col-span-5 sticky top-20 space-y-4">
                
                {{-- Preview Header Container --}}
                <div class="bg-gradient-to-r from-[#0B1120] to-[#0F1D36] p-4 rounded-2xl border border-slate-800 text-white shadow-md flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="iconify text-amber-400 text-lg" data-icon="solar:eye-bold"></span>
                            <h3 class="text-xs font-black tracking-wide text-white uppercase">Pratinjau Langsung</h3>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-0.5">Tampilan yang dilihat pengunjung landing page</p>
                    </div>

                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold text-emerald-300 bg-emerald-500/10 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Real-time</span>
                    </span>
                </div>

                {{-- Live Preview Box: Mimicking the Landing Section --}}
                <div class="bg-slate-50/80 rounded-2xl border border-slate-200/90 p-4 max-h-[calc(100vh-180px)] overflow-y-auto space-y-3.5 shadow-inner" id="preview-list-container">
                    {{-- Dynamically populated via syncPreview() --}}
                </div>

                {{-- Tips box --}}
                <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200 text-slate-700 text-[11px] leading-relaxed flex items-start gap-2.5">
                    <span class="iconify text-primary-blue text-base shrink-0 mt-0.5" data-icon="solar:info-circle-bold"></span>
                    <div>
                        <strong class="font-bold text-primary-blue">Petunjuk:</strong>
                        Pilih radio <em>"Tahap Aktif"</em> pada salah satu tahapan untuk memberikan highlight visual bergelombang dan indikator aktif bernyawa di halaman beranda.
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>

{{-- Dynamic Repeater and Live Sync Engine --}}
<script>
(function() {
    function initTimelineManager() {
        window.syncPreview = function() {
            const previewContainer = document.getElementById('preview-list-container');
            if (!previewContainer) return;

            const cards = document.querySelectorAll('.step-card');
            let html = '';

            cards.forEach((card, index) => {
                const stepNum = card.querySelector('.step-input-number')?.value || String(index + 1).padStart(2, '0');
                const title = card.querySelector('.step-input-title')?.value || 'Judul Tahapan';
                const date = card.querySelector('.step-input-date')?.value || 'Tanggal Belum Diisi';
                const status = card.querySelector('.step-input-status')?.value || 'Mendatang';
                const desc = card.querySelector('.step-input-desc')?.value || '';
                const isChecked = card.querySelector('.step-active-radio')?.checked;

                // Sync the mini badge & title on card header
                const badgeNum = card.querySelector('.step-badge-number');
                if (badgeNum) badgeNum.textContent = stepNum;
                const titlePrev = card.querySelector('.step-card-title-preview');
                if (titlePrev) titlePrev.textContent = title;

                // Build live preview card matching landing page exactly
                if (isChecked) {
                    html += `
                    <div class="relative rounded-2xl p-4 sm:p-5 border bg-blue-50/80 border-primary-blue shadow-md ring-1 ring-primary-blue/30 transition-all duration-200">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[11px] font-black px-2.5 py-0.5 rounded-md bg-primary-blue text-white tracking-wider">
                                TAHAP ${escapeHtml(stepNum)}
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-amber-800 bg-amber-100/90 px-2.5 py-0.5 rounded-full border border-amber-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Aktif
                            </span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900 mb-1.5 leading-snug">
                            ${escapeHtml(title)}
                        </h4>
                        <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary-blue mb-2.5">
                            <span class="iconify" data-icon="solar:calendar-bold"></span>
                            <span>${escapeHtml(date)}</span>
                        </div>
                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            ${escapeHtml(desc)}
                        </p>
                    </div>`;
                } else {
                    html += `
                    <div class="relative rounded-2xl p-4 sm:p-5 border bg-white border-slate-200 hover:border-slate-300 shadow-xs transition-all duration-200">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[11px] font-black px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 tracking-wider">
                                TAHAP ${escapeHtml(stepNum)}
                            </span>
                            <span class="text-[11px] text-slate-500 font-semibold px-2 py-0.5 bg-slate-50 rounded-md border border-slate-200">
                                ${escapeHtml(status)}
                            </span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900 mb-1.5 leading-snug">
                            ${escapeHtml(title)}
                        </h4>
                        <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary-blue mb-2.5">
                            <span class="iconify" data-icon="solar:calendar-bold"></span>
                            <span>${escapeHtml(date)}</span>
                        </div>
                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            ${escapeHtml(desc)}
                        </p>
                    </div>`;
                }
            });

            previewContainer.innerHTML = html;

            // Trigger Iconify SVG render on newly inserted preview nodes
            if (window.Iconify && window.Iconify.scan) {
                window.Iconify.scan(previewContainer);
            }
        };

        window.handleActiveStepChange = function() {
            const cards = document.querySelectorAll('.step-card');
            cards.forEach(card => {
                const radio = card.querySelector('.step-active-radio');
                const label = radio?.closest('label');
                if (radio && label) {
                    if (radio.checked) {
                        label.className = 'cursor-pointer inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border transition-colors bg-amber-50 text-amber-800 border-amber-300';
                    } else {
                        label.className = 'cursor-pointer inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border transition-colors bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100';
                    }
                }
            });
            syncPreview();
        };

        window.setStatusValue = function(index, value) {
            const card = document.querySelector(`.step-card[data-index="${index}"]`);
            if (card) {
                const input = card.querySelector('.step-input-status');
                if (input) {
                    input.value = value;
                    syncPreview();
                }
            }
        };

        window.addNewStep = function() {
            const container = document.getElementById('steps-container');
            const total = document.querySelectorAll('.step-card').length;
            const newIndex = total;
            const newStepNum = String(total + 1).padStart(2, '0');

            const div = document.createElement('div');
            div.className = 'step-card bg-white rounded-2xl border border-slate-200/90 shadow-xs hover:border-slate-300 transition-all p-4 sm:p-5 relative group animate-fadeIn';
            div.setAttribute('data-index', newIndex);

            div.innerHTML = `
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100 gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-slate-900 text-white text-xs font-black flex items-center justify-center step-badge-number">
                            ${newStepNum}
                        </span>
                        <span class="text-xs font-bold text-slate-800 step-card-title-preview truncate max-w-[180px] sm:max-w-xs">
                            Tahapan Baru ${newStepNum}
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="cursor-pointer inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border transition-colors bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100">
                            <input type="radio" name="active_index" value="${newIndex}"
                                class="step-active-radio text-amber-600 focus:ring-amber-500 w-3.5 h-3.5"
                                onchange="handleActiveStepChange()">
                            <span>Tahap Aktif</span>
                        </label>

                        <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden bg-slate-50">
                            <button type="button" onclick="moveStep(${newIndex}, -1)" title="Pindahkan Ke Atas"
                                class="px-1.5 py-1 text-slate-500 hover:text-slate-900 hover:bg-slate-200 transition-colors">
                                <span class="iconify text-xs" data-icon="solar:arrow-up-linear"></span>
                            </button>
                            <div class="w-px h-3 bg-slate-200"></div>
                            <button type="button" onclick="moveStep(${newIndex}, 1)" title="Pindahkan Ke Bawah"
                                class="px-1.5 py-1 text-slate-500 hover:text-slate-900 hover:bg-slate-200 transition-colors">
                                <span class="iconify text-xs" data-icon="solar:arrow-down-linear"></span>
                            </button>
                        </div>

                        <button type="button" onclick="deleteStep(${newIndex})" title="Hapus Tahapan"
                            class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                            <span class="iconify text-base" data-icon="solar:trash-bin-trash-bold"></span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <div class="sm:col-span-3">
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Nomor Tahap</label>
                        <input type="text" name="steps[${newIndex}][step]" value="${newStepNum}"
                            class="step-input-number w-full text-xs font-semibold px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-primary-blue bg-white"
                            placeholder="01" required oninput="syncPreview()">
                    </div>

                    <div class="sm:col-span-9">
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Judul Tahapan</label>
                        <input type="text" name="steps[${newIndex}][title]" value="Tahapan Baru ${newStepNum}"
                            class="step-input-title w-full text-xs font-semibold px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-primary-blue bg-white"
                            placeholder="Contoh: Seleksi Lanjutan" required oninput="syncPreview()">
                    </div>

                    <div class="sm:col-span-6">
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Rentang Tanggal / Waktu</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                                <span class="iconify text-sm" data-icon="solar:calendar-bold"></span>
                            </span>
                            <input type="text" name="steps[${newIndex}][date]" value="Segera Diumumkan"
                                class="step-input-date w-full text-xs font-semibold pl-8 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-primary-blue bg-white"
                                placeholder="Contoh: 10 - 15 Juni 2026" required oninput="syncPreview()">
                        </div>
                    </div>

                    <div class="sm:col-span-6">
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Status Tahapan</label>
                        <div class="flex items-center gap-1.5">
                            <input type="text" name="steps[${newIndex}][status]" value="Mendatang"
                                class="step-input-status w-full text-xs font-semibold px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-primary-blue bg-white"
                                placeholder="Status" required oninput="syncPreview()">
                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button" onclick="setStatusValue(${newIndex}, 'Selesai')"
                                    class="px-2 py-1 text-[10px] font-bold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100">
                                    Done
                                </button>
                                <button type="button" onclick="setStatusValue(${newIndex}, 'Sedang Berlangsung')"
                                    class="px-2 py-1 text-[10px] font-bold rounded-lg bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100">
                                    Live
                                </button>
                                <button type="button" onclick="setStatusValue(${newIndex}, 'Mendatang')"
                                    class="px-2 py-1 text-[10px] font-bold rounded-lg bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200">
                                    Soon
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-12">
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Deskripsi & Keterangan Tahap</label>
                        <textarea name="steps[${newIndex}][desc]" rows="2"
                            class="step-input-desc w-full text-xs font-normal px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-primary-blue bg-white"
                            placeholder="Deskripsi kegiatan pada tahapan ini..." required oninput="syncPreview()"></textarea>
                    </div>
                </div>
            `;

            container.appendChild(div);
            reindexSteps();
            syncPreview();

            if (window.Iconify && window.Iconify.scan) {
                window.Iconify.scan(div);
            }
        };

        window.deleteStep = function(index) {
            const cards = document.querySelectorAll('.step-card');
            if (cards.length <= 1) {
                alert('Minimal harus ada satu tahapan seleksi dalam linimasa.');
                return;
            }
            const card = document.querySelector(`.step-card[data-index="${index}"]`);
            if (card) {
                card.remove();
                reindexSteps();
                syncPreview();
            }
        };

        window.moveStep = function(index, direction) {
            const container = document.getElementById('steps-container');
            const cards = Array.from(document.querySelectorAll('.step-card'));
            const currentIndex = cards.findIndex(c => parseInt(c.getAttribute('data-index')) === index);
            
            if (currentIndex < 0) return;
            const targetIndex = currentIndex + direction;
            if (targetIndex < 0 || targetIndex >= cards.length) return;

            const currentCard = cards[currentIndex];
            const targetCard = cards[targetIndex];

            if (direction < 0) {
                container.insertBefore(currentCard, targetCard);
            } else {
                container.insertBefore(targetCard, currentCard);
            }

            reindexSteps();
            syncPreview();
        };

        function reindexSteps() {
            const cards = document.querySelectorAll('.step-card');
            cards.forEach((card, idx) => {
                card.setAttribute('data-index', idx);
                
                const numInput = card.querySelector('.step-input-number');
                if (numInput) numInput.setAttribute('name', `steps[${idx}][step]`);

                const titleInput = card.querySelector('.step-input-title');
                if (titleInput) titleInput.setAttribute('name', `steps[${idx}][title]`);

                const dateInput = card.querySelector('.step-input-date');
                if (dateInput) dateInput.setAttribute('name', `steps[${idx}][date]`);

                const statusInput = card.querySelector('.step-input-status');
                if (statusInput) statusInput.setAttribute('name', `steps[${idx}][status]`);

                const descInput = card.querySelector('.step-input-desc');
                if (descInput) descInput.setAttribute('name', `steps[${idx}][desc]`);

                const radio = card.querySelector('.step-active-radio');
                if (radio) radio.value = idx;

                // Rebind buttons
                const upBtn = card.querySelector('button[title="Pindahkan Ke Atas"]');
                if (upBtn) upBtn.setAttribute('onclick', `moveStep(${idx}, -1)`);

                const downBtn = card.querySelector('button[title="Pindahkan Ke Bawah"]');
                if (downBtn) downBtn.setAttribute('onclick', `moveStep(${idx}, 1)`);

                const delBtn = card.querySelector('button[title="Hapus Tahapan"]');
                if (delBtn) delBtn.setAttribute('onclick', `deleteStep(${idx})`);

                const statusBtns = card.querySelectorAll('button[onclick*="setStatusValue"]');
                if (statusBtns.length >= 3) {
                    statusBtns[0].setAttribute('onclick', `setStatusValue(${idx}, 'Selesai')`);
                    statusBtns[1].setAttribute('onclick', `setStatusValue(${idx}, 'Sedang Berlangsung')`);
                    statusBtns[2].setAttribute('onclick', `setStatusValue(${idx}, 'Mendatang')`);
                }
            });
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Run initial sync
        syncPreview();
        handleActiveStepChange();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTimelineManager);
    } else {
        initTimelineManager();
    }

    // Expose & listen for SPA navigation re-init
    window.addEventListener('admin:content-loaded', function() {
        if (document.getElementById('timeline-manager-root')) {
            initTimelineManager();
        }
    });

    window.initTimelineManager = initTimelineManager;
})();
</script>
@endsection

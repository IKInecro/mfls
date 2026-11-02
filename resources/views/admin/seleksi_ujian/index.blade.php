@extends('layouts.admin')

@section('content')
<div class="mb-8 space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-2xl font-black text-gray-800">Seleksi Kelulusan Ujian CBT</h2>
            <p class="text-gray-500 font-medium">Panel keputusan manual kelulusan berdasarkan hasil kumulatif TBA, TBI, dan Pemetaan Diri.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <!-- Search Form -->
            <form action="{{ route('admin.seleksi_ujian.index') }}" method="GET" class="w-full sm:w-80 relative">
                <input type="hidden" name="sort" value="{{ request('sort', 'a-z') }}">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peserta..." class="w-full pl-12 pr-6 py-4 bg-white border border-gray-100 rounded-3xl text-sm font-bold shadow-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none">
                <span class="iconify absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 text-xl" data-icon="solar:magnifer-linear"></span>
            </form>

            <!-- Tombol Ekspor Excel -->
            <a href="{{ route('admin.seleksi_ujian.export', ['search' => request('search'), 'sort' => request('sort')]) }}" class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-2xl shadow-xs transition-all whitespace-nowrap cursor-pointer">
                <span class="iconify text-base" data-icon="solar:file-download-bold"></span>
                <span>Ekspor Excel</span>
            </a>

            @php
                $adaYangLulus = $pesertas->filter(function($p) {
                    return ($p->status_seleksi_ujian ?? 'menunggu') === 'lulus';
                })->count();
                $belumKirim = $pesertas->filter(function($p) {
                    return ($p->status_seleksi_ujian ?? 'menunggu') === 'lulus' && !$p->is_email_dikirim;
                })->count();
            @endphp
            @if($belumKirim > 0)
            <button type="button" onclick="startBulkEmailSending({{ $belumKirim }})" class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-4 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-widest rounded-3xl shadow-md transition-all whitespace-nowrap cursor-pointer">
                <span class="iconify text-lg" data-icon="solar:letter-bold"></span>
                Kirim Massal ({{ $belumKirim }} Belum Terkirim)
            </button>
            @elseif($adaYangLulus > 0)
            <button type="button" disabled class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-4 bg-gray-100 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-3xl cursor-not-allowed whitespace-nowrap">
                <span class="iconify text-lg" data-icon="solar:check-circle-bold"></span>
                Semua Email Terkirim ({{ $adaYangLulus }} Lulus)
            </button>
            @endif
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-6 py-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-700 font-bold text-sm">
            <span class="iconify text-xl flex-shrink-0" data-icon="solar:check-circle-bold"></span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-6 py-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 font-bold text-sm">
            <span class="iconify text-xl flex-shrink-0" data-icon="solar:danger-circle-bold"></span>
            {{ session('error') }}
        </div>
    @endif

    <!-- Sorting Filters -->
    <div class="flex flex-wrap gap-2 p-1.5 bg-gray-100 rounded-[2rem] w-fit">
        <span class="px-4 py-2.5 text-[9px] font-black text-slate-400 uppercase tracking-widest flex items-center">Urutan:</span>
        <a href="{{ route('admin.seleksi_ujian.index', ['sort' => 'a-z', 'search' => request('search')]) }}" class="px-6 py-2.5 rounded-full text-[9px] font-black uppercase tracking-widest transition-all {{ request('sort', 'a-z') == 'a-z' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Nama (A - Z) 🔠</a>
        <a href="{{ route('admin.seleksi_ujian.index', ['sort' => 'z-a', 'search' => request('search')]) }}" class="px-6 py-2.5 rounded-full text-[9px] font-black uppercase tracking-widest transition-all {{ request('sort') == 'z-a' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Nama (Z - A) 🔡</a>
        <a href="{{ route('admin.seleksi_ujian.index', ['sort' => 'latest', 'search' => request('search')]) }}" class="px-6 py-2.5 rounded-full text-[9px] font-black uppercase tracking-widest transition-all {{ request('sort') == 'latest' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Terbaru 📅</a>
    </div>

    {{-- Summary Stats --}}
    @php
        $totalPeserta = $pesertas->count();
        $totalLulus  = $pesertas->filter(function($p) {
            return ($p->status_seleksi_ujian ?? 'menunggu') === 'lulus';
        })->count();
        $totalGagal  = $pesertas->filter(function($p) {
            return ($p->status_seleksi_ujian ?? 'menunggu') === 'tidak_lulus';
        })->count();
        $totalMenunggu = $pesertas->filter(function($p) {
            return ($p->status_seleksi_ujian ?? 'menunggu') === 'menunggu';
        })->count();
    @endphp
    @if($totalPeserta > 0)
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white border border-gray-100 rounded-2xl p-4 text-center shadow-sm">
            <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Pendaftar CBT</div>
            <div class="text-2xl font-black text-slate-800">{{ $totalPeserta }}</div>
        </div>
        <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 text-center shadow-sm">
            <div class="text-[9px] font-black text-amber-500 uppercase tracking-widest mb-1">⏳ Menunggu</div>
            <div class="text-2xl font-black text-amber-700">{{ $totalMenunggu }}</div>
        </div>
        <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 text-center shadow-sm">
            <div class="text-[9px] font-black text-emerald-500 uppercase tracking-widest mb-1">✅ Lulus Seleksi</div>
            <div class="text-2xl font-black text-emerald-700">{{ $totalLulus }}</div>
        </div>
        <div class="bg-rose-50 border border-rose-100 rounded-2xl p-4 text-center shadow-sm">
            <div class="text-[9px] font-black text-rose-400 uppercase tracking-widest mb-1">❌ Gagal Seleksi</div>
            <div class="text-2xl font-black text-rose-600">{{ $totalGagal }}</div>
        </div>
    </div>
    @endif
</div>

<div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-400 font-black uppercase text-[10px] tracking-[0.2em]">
                <tr>
                    <th class="px-6 py-5 text-center w-12">
                        <input type="checkbox" id="selectAllCheckbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer">
                    </th>
                    <th class="px-6 py-5">Peserta</th>
                    <th class="px-6 py-5 text-center">Nilai TPA</th>
                    <th class="px-6 py-5 text-center">Nilai TBI</th>
                    <th class="px-6 py-5 text-center">Pemetaan Diri (AI)</th>
                    <th class="px-6 py-5 text-center">Status Seleksi</th>
                    <th class="px-6 py-5 text-right">Opsi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($pesertas as $peserta)
                @php
                    $status = $peserta->status_seleksi_ujian ?? 'menunggu';
                    $asalSekolah = $peserta->daftar->asal_sekolah ?? ($peserta->nama_sekolah ?? '-');
                @endphp
                <tr class="hover:bg-slate-50/50 transition-all">
                    {{-- Checkbox --}}
                    <td class="px-6 py-4 text-center">
                        <input type="checkbox" class="candidate-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer" value="{{ $peserta->id }}" onchange="updateBulkActionBar()">
                    </td>
                    {{-- Peserta Info --}}
                    <td class="px-6 py-4">
                        <div class="font-black text-slate-800 uppercase">{{ $peserta->nama }}</div>
                        <div class="text-[10px] font-bold text-blue-500 uppercase">NISN: {{ $peserta->nisn }}</div>
                        <div class="text-[10px] font-semibold text-slate-400 mt-0.5">🏫 {{ $asalSekolah }}</div>
                    </td>

                    {{-- TBA Score --}}
                    <td class="px-6 py-4 text-center">
                        @if($peserta->score_tba !== null)
                            <span class="text-lg font-black {{ $peserta->score_tba >= 70 ? 'text-blue-600' : 'text-amber-600' }}">
                                {{ number_format($peserta->score_tba, 2) }}
                            </span>
                        @else
                            <span class="px-2 py-1 bg-gray-50 text-gray-400 rounded text-[9px] font-bold uppercase tracking-wider">
                                Belum Mengerjakan
                            </span>
                        @endif
                    </td>

                    {{-- TBI Score --}}
                    <td class="px-6 py-4 text-center">
                        @if($peserta->score_tbi !== null)
                            <span class="text-lg font-black {{ $peserta->score_tbi >= 70 ? 'text-blue-600' : 'text-amber-600' }}">
                                {{ number_format($peserta->score_tbi, 2) }}
                            </span>
                        @else
                            <span class="px-2 py-1 bg-gray-50 text-gray-400 rounded text-[9px] font-bold uppercase tracking-wider">
                                Belum Mengerjakan
                            </span>
                        @endif
                    </td>

                    {{-- Pemetaan Diri AI analysis --}}
                    <td class="px-6 py-4 text-center">
                        @if(!empty($peserta->analisis_pemetaan))
                            <button type="button" 
                                    onclick="openAiModal('{{ addslashes($peserta->nama) }}', '{{ rawurlencode($peserta->analisis_pemetaan) }}')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 text-purple-700 hover:bg-purple-600 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest border border-purple-100 cursor-pointer shadow-sm transition-all">
                                <span class="iconify" data-icon="solar:psychology-bold"></span>
                                Lihat Analisis 🧠
                            </button>
                        @else
                            <span class="px-2 py-1 bg-gray-50 text-gray-400 rounded text-[9px] font-bold uppercase tracking-wider">
                                Belum Mengerjakan
                            </span>
                        @endif
                    </td>

                    {{-- Status Seleksi Ujian --}}
                    <td class="px-6 py-4 text-center">
                        <div class="flex flex-col items-center justify-center gap-1.5">
                            @if($status === 'lulus')
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-[9px] font-black uppercase tracking-widest rounded-full border border-emerald-200">
                                    ✅ Lulus Seleksi
                                </span>
                                
                                {{-- Status Notifikasi Email --}}
                                @if($peserta->is_email_dikirim)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-600 text-[8px] font-black uppercase tracking-widest rounded-full border border-blue-100" title="Email notifikasi kelulusan sudah berhasil dikirim">
                                        <span class="iconify text-[10px]" data-icon="solar:check-circle-bold"></span> Sudah Dikirim 📬
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-500 text-[8px] font-black uppercase tracking-widest rounded-full border border-slate-200" title="Email notifikasi kelulusan belum dikirim">
                                        <span class="iconify text-[10px] animate-pulse" data-icon="solar:letter-bold"></span> Belum Dikirim ✉️
                                    </span>
                                @endif
                            @elseif($status === 'tidak_lulus')
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-rose-50 text-rose-600 text-[9px] font-black uppercase tracking-widest rounded-full border border-rose-100">
                                    ❌ Gagal Seleksi
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-50 text-amber-700 text-[9px] font-black uppercase tracking-widest rounded-full border border-amber-200">
                                    ⏳ Menunggu
                                </span>
                            @endif
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2 flex-wrap items-center">
                            {{-- Aksi Manual Kelulusan --}}
                            <div class="inline-flex rounded-xl border border-slate-100 bg-slate-50 p-1 gap-1">
                                <form action="{{ route('admin.seleksi_ujian.update_status', $peserta->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status_seleksi_ujian" value="lulus">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all {{ $status === 'lulus' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-700' }}" title="Set Lulus">
                                        Lolos 👍
                                    </button>
                                </form>
                                <form action="{{ route('admin.seleksi_ujian.update_status', $peserta->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status_seleksi_ujian" value="tidak_lulus">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all {{ $status === 'tidak_lulus' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-700' }}" title="Set Gagal">
                                        Gagal 👎
                                    </button>
                                </form>
                            </div>

                            <a href="{{ route('admin.beasiswa.show', $peserta->akun_id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-xl text-[9px] font-black hover:bg-blue-600 hover:text-white transition-all shadow-sm uppercase tracking-widest">
                                Report 📑
                            </a>

                            {{-- Tombol Kirim Email (hanya untuk yang lulus manual) --}}
                            @if($status === 'lulus')
                            <button type="button" onclick="sendIndividualEmail({{ $peserta->id }}, '{{ addslashes($peserta->nama) }}')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-[9px] font-black transition-all shadow-sm uppercase tracking-widest {{ $peserta->is_email_dikirim ? 'bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white animate-bounce' }}">
                                <span class="iconify" data-icon="{{ $peserta->is_email_dikirim ? 'solar:reply-bold' : 'solar:letter-bold' }}"></span> 
                                {{ $peserta->is_email_dikirim ? 'Kirim Ulang' : 'Email' }}
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
                @if($pesertas->isEmpty())
                <tr>
                    <td colspan="6" class="py-20 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mb-6">
                                <span class="iconify text-4xl text-slate-400" data-icon="solar:document-cross-bold"></span>
                            </div>
                            <h3 class="text-xl font-black text-slate-800 mb-2">Belum Ada Data Peserta</h3>
                            <p class="text-slate-500 font-medium">Belum ada peserta yang lulus tahap administrasi.</p>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- ================= AI ANALISIS MODAL ================= -->
<div id="aiModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm hidden opacity-0 transition-all duration-300">
    <div class="bg-white rounded-[2rem] w-full max-w-2xl mx-4 overflow-hidden border border-slate-100 shadow-2xl flex flex-col max-h-[85vh] scale-95 transition-all duration-300" id="aiModalContent">
        <!-- Modal Header -->
        <div class="p-6 bg-purple-900 text-white flex justify-between items-center">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-purple-700 flex items-center justify-center shadow-lg">
                    <span class="iconify text-2xl text-white" data-icon="solar:psychology-bold"></span>
                </div>
                <div>
                    <h3 class="font-black text-lg uppercase tracking-wide leading-tight">Analisis Psikologi AI</h3>
                    <p class="text-[10px] text-purple-200 font-bold uppercase tracking-wider" id="modalCandidateName"></p>
                </div>
            </div>
            <button onclick="closeAiModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-purple-800 text-purple-100 hover:bg-purple-700 hover:text-white transition-all cursor-pointer">
                <span class="iconify text-xl" data-icon="solar:close-circle-bold"></span>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="p-8 overflow-y-auto flex-grow prose max-w-none text-slate-600 font-medium text-sm leading-relaxed" id="modalBodyText">
        </div>

        <!-- Modal Footer -->
        <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="closeAiModal()" class="px-6 py-3 bg-purple-900 hover:bg-purple-950 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-md cursor-pointer transition-all">
                Tutup Analisis
            </button>
        </div>
    </div>
</div>

<script>
    function openAiModal(name, encodedContent) {
        const modal = document.getElementById('aiModal');
        const content = document.getElementById('aiModalContent');
        const nameEl = document.getElementById('modalCandidateName');
        const bodyEl = document.getElementById('modalBodyText');

        nameEl.innerText = "Hasil Pemetaan Diri: " + name;
        
        // Decode content
        let decoded = decodeURIComponent(encodedContent);
        
        // Convert newlines to breaks or beautiful paragraphs
        bodyEl.innerHTML = decoded.replace(/\n/g, '<br>');

        // Animation
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 50);
    }

    function closeAiModal() {
        const modal = document.getElementById('aiModal');
        const content = document.getElementById('aiModalContent');

        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Select All Checkboxes
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.candidate-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
            updateBulkActionBar();
        });
    }

    function updateBulkActionBar() {
        const checkboxes = document.querySelectorAll('.candidate-checkbox:checked');
        const count = checkboxes.length;
        const bar = document.getElementById('bulkActionBar');
        const countText = document.getElementById('bulkCountText');

        if (count > 0) {
            countText.innerText = count + " Peserta Terpilih";
            bar.classList.remove('translate-y-28', 'opacity-0', 'pointer-events-none');
            bar.classList.add('translate-y-0', 'opacity-100');
        } else {
            bar.classList.add('translate-y-28', 'opacity-0', 'pointer-events-none');
            bar.classList.remove('translate-y-0', 'opacity-100');
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
        }
    }

    function clearSelection() {
        const checkboxes = document.querySelectorAll('.candidate-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
        if (selectAllCheckbox) selectAllCheckbox.checked = false;
        updateBulkActionBar();
    }

    function submitBulkAction(status) {
        const checkboxes = document.querySelectorAll('.candidate-checkbox:checked');
        if (checkboxes.length === 0) return;

        let statusText = status === 'lulus' ? 'LOLOSKAN' : (status === 'tidak_lulus' ? 'GAGALKAN' : 'SET MENUNGGU');
        if (!confirm(`Yakin ingin melakukan aksi ${statusText} massal untuk ${checkboxes.length} peserta terpilih?`)) {
            return;
        }

        const form = document.getElementById('bulkForm');
        const statusInput = document.getElementById('bulkStatusInput');
        const inputsContainer = document.getElementById('bulkFormInputs');

        statusInput.value = status;
        inputsContainer.innerHTML = '';

        checkboxes.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            inputsContainer.appendChild(input);
        });

        form.submit();
    }
</script>

<!-- ================= FLOATING BULK ACTION BAR ================= -->
<div id="bulkActionBar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-slate-900 text-white rounded-3xl px-8 py-5 flex items-center justify-between gap-8 shadow-2xl transition-all duration-300 translate-y-28 opacity-0 pointer-events-none w-[90%] max-w-3xl border border-slate-800">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-400">
            <span class="iconify text-xl" data-icon="solar:users-group-two-rounded-bold"></span>
        </div>
        <div>
            <div class="font-black text-xs uppercase tracking-wider text-slate-400">Aksi Massal</div>
            <div id="bulkCountText" class="font-black text-sm text-white">0 Peserta Terpilih</div>
        </div>
    </div>
    
    <div class="flex items-center gap-3">
        <button type="button" onclick="submitBulkAction('lulus')" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-[9px] font-black uppercase tracking-widest rounded-2xl cursor-pointer shadow-md transition-all">
            Loloskan 👍
        </button>
        <button type="button" onclick="submitBulkAction('tidak_lulus')" class="px-5 py-3 bg-rose-600 hover:bg-rose-700 text-white text-[9px] font-black uppercase tracking-widest rounded-2xl cursor-pointer shadow-md transition-all">
            Gagalkan 👎
        </button>
        <button type="button" onclick="submitBulkAction('menunggu')" class="px-5 py-3 bg-amber-600 hover:bg-amber-700 text-white text-[9px] font-black uppercase tracking-widest rounded-2xl cursor-pointer shadow-md transition-all">
            Set Menunggu ⏳
        </button>
        <button type="button" onclick="clearSelection()" class="px-4 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-[9px] font-black uppercase tracking-widest rounded-2xl cursor-pointer transition-all">
            Batal
        </button>
    </div>
</div>

{{-- Hidden form to handle the POST submission --}}
<form id="bulkForm" action="{{ route('admin.seleksi_ujian.bulk_update') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="status_seleksi_ujian" id="bulkStatusInput" value="">
    <div id="bulkFormInputs"></div>
</form>

<script>
    let progressInterval;

    function startBulkEmailSending(totalCount) {
        Swal.fire({
            title: 'Kirim Email Massal',
            text: `Kirim email notifikasi lolos seleksi ujian ke ${totalCount} peserta yang belum dikirimi email?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Kirim Massal!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const overlay = document.getElementById('loadingOverlay');
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 50);

                // Reset UI
                document.getElementById('bulkProgressBar').style.width = '0%';
                document.getElementById('bulkProgressCount').innerText = `0 / ${totalCount}`;
                document.getElementById('bulkProgressText').innerText = '0%';

                fetch("{{ route('admin.seleksi_ujian.kirim_massal') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        localStorage.setItem('activeUjianEmailBatchId', data.batch_id);
                        localStorage.setItem('activeUjianEmailTotal', data.total);
                        pollEmailProgress(data.batch_id, data.total);
                    } else {
                        hideLoadingOverlay();
                        Swal.fire('Gagal', data.error || 'Terjadi kesalahan saat memulai antrean.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error starting bulk email:', error);
                    hideLoadingOverlay();
                    Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                });
            }
        });
    }

    function pollEmailProgress(batchId, total) {
        if (progressInterval) clearInterval(progressInterval);

        progressInterval = setInterval(() => {
            fetch(`{{ route('admin.seleksi_ujian.kirim_massal_progress') }}?batch_id=${batchId}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(response => {
                if (response.status === 404) {
                    clearInterval(progressInterval);
                    localStorage.removeItem('activeUjianEmailBatchId');
                    localStorage.removeItem('activeUjianEmailTotal');
                    hideLoadingOverlay();
                    return null;
                }
                return response.json();
            })
            .then(data => {
                if (!data) return;

                const percentage = data.percentage;
                document.getElementById('bulkProgressBar').style.width = `${percentage}%`;
                document.getElementById('bulkProgressCount').innerText = `${data.current} / ${data.total}`;
                document.getElementById('bulkProgressText').innerText = `${percentage}%`;

                if (data.status === 'completed' || data.current >= data.total) {
                    clearInterval(progressInterval);
                    localStorage.removeItem('activeUjianEmailBatchId');
                    localStorage.removeItem('activeUjianEmailTotal');
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Selesai!',
                            text: 'Semua email notifikasi lolos seleksi ujian telah sukses terkirim!',
                            confirmButtonColor: '#10b981',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.reload();
                        });
                    }, 800);
                }
            })
            .catch(error => {
                console.error('Polling error:', error);
            });
        }, 1500);
    }

    function hideLoadingOverlay() {
        const overlay = document.getElementById('loadingOverlay');
        overlay.classList.add('opacity-0');
        setTimeout(() => overlay.classList.add('hidden'), 300);
    }

    function sendIndividualEmail(id, name) {
        Swal.fire({
            title: 'Kirim Email',
            text: `Kirim email notifikasi lolos seleksi ujian ke ${name}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Kirim!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show clean loading spinner using Swal
                Swal.fire({
                    title: 'Mengirim Email...',
                    text: 'Mohon tunggu beberapa saat.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Create a dynamic form to submit the request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/seleksi-ujian/${id}/kirim-email`;
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Auto-resume polling on page load if active batch exists
    document.addEventListener('DOMContentLoaded', () => {
        const savedBatchId = localStorage.getItem('activeUjianEmailBatchId');
        const savedTotal = localStorage.getItem('activeUjianEmailTotal');
        if (savedBatchId && savedTotal) {
            const overlay = document.getElementById('loadingOverlay');
            overlay.classList.remove('hidden');
            overlay.classList.remove('opacity-0');
            pollEmailProgress(savedBatchId, parseInt(savedTotal));
        }
    });
</script>

<!-- ================= PREMIUM FULL SCREEN PROGRESS MODAL ================= -->
<div id="loadingOverlay" class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-slate-900/85 backdrop-blur-md hidden opacity-0 transition-all duration-300">
    <div class="bg-white rounded-[2.5rem] p-8 w-full max-w-md border border-slate-100 shadow-2xl text-center space-y-6 mx-4">
        <!-- Animated icon -->
        <div class="relative w-20 h-20 mx-auto">
            <div class="absolute inset-0 rounded-full border-4 border-emerald-100"></div>
            <div class="absolute inset-0 rounded-full border-4 border-emerald-500 border-t-transparent animate-spin"></div>
            <div class="absolute inset-4 rounded-full bg-emerald-50 flex items-center justify-center shadow-inner">
                <span class="iconify text-2xl text-emerald-500 animate-pulse" data-icon="solar:letter-opened-bold-duotone"></span>
            </div>
        </div>
        
        <div>
            <h3 class="text-xl font-black text-slate-800 uppercase tracking-wider mb-2">Mengirim Email Massal... 📧</h3>
            <p class="text-slate-500 font-bold text-xs leading-relaxed">
                Mohon tunggu beberapa saat. Sistem sedang mengirimkan email pengumuman kelulusan ke antrean latar belakang secara aman.
            </p>
        </div>

        <div class="relative pt-1">
            <div class="flex mb-2 items-center justify-between">
                <div>
                    <span class="text-[10px] font-black inline-block py-1 px-3 uppercase rounded-full text-emerald-600 bg-emerald-50" id="bulkProgressText">
                        0%
                    </span>
                </div>
                <div class="text-right">
                    <span class="text-xs font-black inline-block text-emerald-600" id="bulkProgressCount">
                        0 / 0
                    </span>
                </div>
            </div>
            <div class="overflow-hidden h-3 mb-4 text-xs flex rounded-full bg-slate-100 border border-slate-50">
                <div id="bulkProgressBar" style="width:0%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-emerald-500 transition-all duration-500 rounded-full"></div>
            </div>
        </div>
    </div>
</div>
@endsection

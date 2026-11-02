@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-black text-gray-800">Bank Soal Ujian</h2>
    <p class="text-gray-500">Kelola pertanyaan untuk Ujian Online Seleksi MFLS.</p>
</div>

<!-- Filter Tabs -->
<div class="flex gap-2 mb-8 overflow-x-auto pb-2">
    <a href="{{ route('admin.soal.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ !request('ujian_id') ? 'bg-[#0870B8] text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
        Semua Soal
    </a>
    @foreach($ujians as $ujian)
        <a href="{{ route('admin.soal.index', ['ujian_id' => $ujian->id]) }}" class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ request('ujian_id') == $ujian->id ? 'bg-[#0870B8] text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            {{ $ujian->nama }}
        </a>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Kolom Kiri: Form Input (Sticky) -->
    <div class="lg:col-span-1">
        <div class="sticky top-4 space-y-6">
            
            @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 p-4 rounded-2xl mb-4">
                <h4 class="text-rose-800 font-bold text-xs mb-2 uppercase">Terjadi Kesalahan:</h4>
                <ul class="list-disc list-inside text-[11px] text-rose-700 font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(request('ujian_id'))
            @php
                $currentUjian = $ujians->where('id', request('ujian_id'))->first();
            @endphp
            @if($currentUjian)
            <!-- Pengaturan Ujian Card -->
            <div class="bg-white border border-slate-200/80 p-6 rounded-3xl mb-6 shadow-xs">
                <h3 class="font-black text-slate-900 mb-3 text-sm">Pengaturan: {{ $currentUjian->nama }}</h3>
                <form action="{{ route('admin.ujian.update', $currentUjian->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Durasi (Menit)</label>
                        <input type="number" name="durasi" value="{{ $currentUjian->durasi ?? 60 }}" required min="1" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:border-[#0870B8] outline-none">
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Status Sesi Ujian</label>
                        <select name="is_active" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:border-[#0870B8] outline-none">
                            <option value="1" {{ ($currentUjian->is_active ?? true) ? 'selected' : '' }}>Buka (Sesi Aktif)</option>
                            <option value="0" {{ !($currentUjian->is_active ?? true) ? 'selected' : '' }}>Tutup (Sesi Nonaktif)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full bg-[#0870B8] hover:bg-[#065A96] text-white font-bold py-2.5 rounded-xl text-xs transition-all shadow-xs cursor-pointer">
                        Simpan Pengaturan Paket
                    </button>
                </form>
            </div>
            @endif
            @endif

            <!-- Import Card -->
            <div class="bg-blue-50/50 border border-blue-100 p-6 rounded-3xl">
                <h3 class="font-black text-slate-900 mb-1 text-sm">Import Soal Massal</h3>
                <p class="text-xs text-slate-500 mb-4">Mendukung file .docx (Word), .pdf, dan .csv.<br>Format: No. Soal, Opsi A-D/E, Kunci Jawaban.</p>
                <form action="{{ route('admin.soal.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <select name="ujian_id" required class="w-full px-3.5 py-2.5 bg-white border border-blue-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-[#0870B8]">
                            <option value="">-- Pilih Paket Ujian --</option>
                            @foreach($ujians as $ujian)
                                <option value="{{ $ujian->id }}" {{ request('ujian_id') == $ujian->id ? 'selected' : '' }}>{{ $ujian->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="file" name="file_soal" accept=".csv,.txt,.docx,.pdf" required class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-100 file:text-[#0870B8] hover:file:bg-blue-200 mb-3"/>
                    <button class="w-full bg-[#0870B8] hover:bg-[#065A96] text-white font-bold py-2.5 rounded-xl text-xs transition-all shadow-xs cursor-pointer">
                        Unggah & Ekstrak Soal
                    </button>
                </form>
            </div>

            <!-- Manual Input Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Input Manual</h3>
                
                <form action="{{ route('admin.soal.store') }}" method="POST" class="space-y-4" enctype="multipart/form-data" id="manualInputForm">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Kategori Ujian</label>
                        <select name="ujian_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10">
                            <option value="">-- Pilih --</option>
                            @foreach($ujians as $ujian)
                                <option value="{{ $ujian->id }}" {{ request('ujian_id') == $ujian->id ? 'selected' : '' }}>{{ $ujian->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Pertanyaan</label>
                        <textarea name="pertanyaan" rows="3" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10" placeholder="Tulis soal di sini..."></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Gambar (Opsional)</label>
                        <input type="file" name="gambar" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200"/>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 mb-1">OPSI A</label>
                            <input type="text" name="opsi_a" placeholder="Teks Opsi A" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm mb-1">
                            <input type="file" name="opsi_a_image" class="block w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 mb-1">OPSI B</label>
                            <input type="text" name="opsi_b" placeholder="Teks Opsi B" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm mb-1">
                            <input type="file" name="opsi_b_image" class="block w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 mb-1">OPSI C</label>
                            <input type="text" name="opsi_c" placeholder="Teks Opsi C" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm mb-1">
                            <input type="file" name="opsi_c_image" class="block w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 mb-1">OPSI D</label>
                            <input type="text" name="opsi_d" placeholder="Teks Opsi D" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm mb-1">
                            <input type="file" name="opsi_d_image" class="block w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 mb-1">OPSI E (Opsional)</label>
                            <input type="text" name="opsi_e" placeholder="Teks Opsi E" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm mb-1">
                            <input type="file" name="opsi_e_image" class="block w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 mb-1">KUNCI JAWABAN</label>
                            <select name="kunci_jawaban" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold">
                                <option value="">Kosongkan (Tes Kepribadian)</option>
                                <option value="a">A</option>
                                <option value="b">B</option>
                                <option value="c">C</option>
                                <option value="d">D</option>
                                <option value="e">E</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 mb-1">BOBOT</label>
                            <input type="number" name="bobot" value="5" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#0870B8] hover:bg-[#065A96] text-white font-bold py-2.5 rounded-xl transition-all shadow-xs cursor-pointer">
                        Simpan Soal
                    </button>
                </form>

                <script>
                    document.getElementById('manualInputForm').addEventListener('submit', function(e) {
                        const files = this.querySelectorAll('input[type="file"]');
                        const maxSize = 100 * 1024 * 1024; // 100MB per file
                        
                        for (let fileInput of files) {
                            if (fileInput.files.length > 0) {
                                if (fileInput.files[0].size > maxSize) {
                                    e.preventDefault();
                                    Swal.fire({
                                        title: 'File Terlalu Besar!',
                                        text: `File "${fileInput.files[0].name}" melebihi batas 100MB. Silakan gunakan gambar yang lebih kecil.`,
                                        icon: 'error',
                                        confirmButtonColor: '#ef4444'
                                    });
                                    return;
                                }
                            }
                        }
                    });
                </script>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: List Soal -->
    <div class="lg:col-span-2 space-y-4">
        <div class="flex justify-between items-center mb-2">
            <h3 class="font-bold text-gray-800">Daftar Soal ({{ $soals->count() }})</h3>
            
            @if($soals->count() > 0)
            <form action="{{ route('admin.soal.deleteAll') }}" method="POST" id="deleteAllForm">
                @csrf
                <input type="hidden" name="ujian_id" value="{{ request('ujian_id') }}">
                <button type="button" onclick="confirmDeleteAll()" class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 rounded-xl text-xs font-black border border-red-100 hover:bg-red-600 hover:text-white transition-all uppercase tracking-widest shadow-sm">
                    <span class="iconify" data-icon="solar:trash-bin-trash-bold"></span> Hapus Semua {{ request('ujian_id') ? 'Kategori Ini' : '' }}
                </button>
            </form>
            @endif
        </div>

<script>
function confirmDeleteAll() {
    const isFiltered = "{{ request('ujian_id') }}";
    const title = isFiltered ? 'Hapus semua soal di kategori ini?' : 'Hapus SELURUH bank soal?';
    
    Swal.fire({
        title: title,
        text: "Tindakan ini akan menghapus semua soal beserta gambar yang terkait. Data tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus Semua!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteAllForm').submit();
        }
    });
}
</script>

        @forelse($soals as $soal)
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative group">
            
            <!-- Badge & Actions Header -->
            <div class="flex justify-between items-start mb-3 border-b border-gray-50 pb-3">
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-bold rounded uppercase tracking-wide">
                    {{ $soal->ujian->nama ?? 'Tanpa Kategori' }}
                </span>
                <div class="flex gap-2">
                    <span class="text-xs font-bold text-gray-400">Bobot: {{ $soal->bobot }}</span>
                    <div class="h-4 w-[1px] bg-gray-200"></div>
                    <!-- Action Buttons -->
                    <a href="{{ route('admin.soal.edit', $soal->id) }}" class="text-gray-400 hover:text-blue-600 transition-colors" title="Edit">
                        ✏️
                    </a>
                    <form action="{{ route('admin.soal.destroy', $soal->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDeleteSoal(this)" class="text-gray-400 hover:text-red-600 transition-colors" title="Hapus">
                            🗑️
                        </button>
                    </form>
                </div>
            </div>

<script>
function confirmDeleteSoal(button) {
    Swal.fire({
        title: 'Hapus Soal ini?',
        text: "Tindakan ini tidak dapat dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            button.closest('form').submit();
        }
    });
}
</script>

            <!-- Question Body -->
            <div class="flex gap-4">
                <div class="shrink-0 w-8 h-8 bg-[#0870B8] text-white text-xs font-bold rounded-xl flex items-center justify-center shadow-xs">
                    {{ $loop->iteration }}
                </div>
                <div class="flex-grow">
                    <p class="font-bold text-gray-800 text-sm leading-relaxed mb-3">{{ $soal->pertanyaan }}</p>
                    
                    @if($soal->gambar)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $soal->gambar) }}" class="max-h-48 rounded-lg border border-gray-100 shadow-sm cursor-pointer hover:opacity-90 transition-opacity" onclick="window.open(this.src)">
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-600">
                        <div class="p-2 rounded-lg border {{ $soal->kunci_jawaban == 'a' ? 'bg-green-50 border-green-200 text-green-800 font-bold' : 'border-transparent hover:bg-gray-50' }}">
                            <span class="mr-2 opacity-50">A.</span> {{ $soal->opsi_a }}
                            @if($soal->opsi_a_image)
                                <img src="{{ asset('storage/' . $soal->opsi_a_image) }}" class="mt-2 max-h-24 rounded border border-gray-200 cursor-pointer" onclick="window.open(this.src)">
                            @endif
                        </div>
                        <div class="p-2 rounded-lg border {{ $soal->kunci_jawaban == 'b' ? 'bg-green-50 border-green-200 text-green-800 font-bold' : 'border-transparent hover:bg-gray-50' }}">
                            <span class="mr-2 opacity-50">B.</span> {{ $soal->opsi_b }}
                            @if($soal->opsi_b_image)
                                <img src="{{ asset('storage/' . $soal->opsi_b_image) }}" class="mt-2 max-h-24 rounded border border-gray-200 cursor-pointer" onclick="window.open(this.src)">
                            @endif
                        </div>
                        <div class="p-2 rounded-lg border {{ $soal->kunci_jawaban == 'c' ? 'bg-green-50 border-green-200 text-green-800 font-bold' : 'border-transparent hover:bg-gray-50' }}">
                            <span class="mr-2 opacity-50">C.</span> {{ $soal->opsi_c }}
                            @if($soal->opsi_c_image)
                                <img src="{{ asset('storage/' . $soal->opsi_c_image) }}" class="mt-2 max-h-24 rounded border border-gray-200 cursor-pointer" onclick="window.open(this.src)">
                            @endif
                        </div>
                        <div class="p-2 rounded-lg border {{ $soal->kunci_jawaban == 'd' ? 'bg-green-50 border-green-200 text-green-800 font-bold' : 'border-transparent hover:bg-gray-50' }}">
                            <span class="mr-2 opacity-50">D.</span> {{ $soal->opsi_d }}
                            @if($soal->opsi_d_image)
                                <img src="{{ asset('storage/' . $soal->opsi_d_image) }}" class="mt-2 max-h-24 rounded border border-gray-200 cursor-pointer" onclick="window.open(this.src)">
                            @endif
                        </div>
                        @if($soal->opsi_e)
                        <div class="p-2 rounded-lg border {{ $soal->kunci_jawaban == 'e' ? 'bg-green-50 border-green-200 text-green-800 font-bold' : 'border-transparent hover:bg-gray-50' }}">
                            <span class="mr-2 opacity-50">E.</span> {{ $soal->opsi_e }}
                            @if($soal->opsi_e_image)
                                <img src="{{ asset('storage/' . $soal->opsi_e_image) }}" class="mt-2 max-h-24 rounded border border-gray-200 cursor-pointer" onclick="window.open(this.src)">
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white p-12 text-center rounded-2xl border border-dashed border-gray-300">
            <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <h3 class="text-lg font-bold text-gray-800">Belum ada soal ujian</h3>
            <p class="text-gray-500 text-sm mt-1">Silakan input manual atau import file untuk kategori ini.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

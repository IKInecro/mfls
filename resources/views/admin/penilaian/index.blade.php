@extends('layouts.admin')

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-2xl font-black text-slate-800">
                {{ auth()->user()->role === 'dosen' ? 'Evaluasi Wawancara (Dosen)' : ($type === 'mentor' ? 'Evaluasi Peserta (Mentor)' : 'Evaluasi Peserta (Akademik)') }}
            </h2>
            <p class="text-slate-500 font-medium">
                {{ auth()->user()->role === 'dosen' ? 'Berikan penilaian wawancara untuk peserta yang ditugaskan kepada Anda.' : 'Berikan penilaian kualitatif untuk setiap peserta yang lolos seleksi berkas.' }}
            </p>
        </div>
        <!-- @if(in_array(auth()->user()->role, ['admin', 'akademik', 'palugada']) && $type === 'akademik')
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.export_wawancara') }}" class="bg-emerald-600 text-white px-6 py-3 rounded-2xl text-[10px] font-black hover:bg-emerald-700 transition-all uppercase tracking-[0.2em] shadow-lg shadow-emerald-100 flex items-center gap-2">
                    <span class="iconify" data-icon="solar:export-bold"></span>
                    Export Hasil Wawancara
                </a>
            </div>
            @endif
            @if(auth()->user()->email === 'noval.adi@mncu.ac.id')
            <div class="flex items-center gap-3">
                <button type="button" id="btnSyncWawancara" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl text-[10px] font-black hover:bg-indigo-700 transition-all uppercase tracking-[0.2em] shadow-lg shadow-indigo-100 flex items-center gap-2">
                    <span class="iconify" data-icon="solar:refresh-bold"></span>
                    Sync ke Spreadsheet
                </button>
                <a href="https://docs.google.com/spreadsheets/d/1pkgZqQpC-HXUO9VC7G0Qs0fnPekMWts2v29rV43lcRY/edit?gid=883067809#gid=883067809" target="_blank" class="bg-emerald-600 text-white px-6 py-3 rounded-2xl text-[10px] font-black hover:bg-emerald-700 transition-all uppercase tracking-[0.2em] shadow-lg shadow-emerald-100 flex items-center gap-2">
                    <span class="iconify" data-icon="solar:document-bold"></span>
                    Spreadsheet Penilaian Akademik
                </a>
            </div>
            @endif -->
    </div>

    <!-- Filter & Search Section -->
    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm mb-8">
        <form action="{{ $type === 'mentor' ? route('admin.penilaian.index') : route('admin.penilaian.akademik.index') }}"
            method="GET" class="flex flex-col lg:flex-row gap-4">
            <div class="relative flex-grow">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <span class="iconify" data-icon="solar:magnifer-bold"></span>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama, sekolah, atau kabupaten..."
                    class="w-full pl-12 pr-4 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500/20 outline-none font-bold text-slate-700">
            </div>

            <div class="flex flex-wrap gap-2">
                <button type="submit"
                    class="bg-[#0870B8] text-white px-8 py-4 rounded-2xl text-[10px] font-black hover:bg-[#065A96] transition-all uppercase tracking-[0.2em] shadow-xs flex items-center gap-2 cursor-pointer">
                    <span class="iconify" data-icon="solar:magnifer-bold"></span>
                    Cari Peserta
                </button>
                @if(request()->has('search'))
                    <a href="{{ route('admin.penilaian.index') }}"
                        class="bg-slate-100 text-slate-500 px-6 py-4 rounded-2xl text-[10px] font-black hover:bg-slate-200 transition-all uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="iconify" data-icon="solar:refresh-bold"></span>
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if($pendaftars->isEmpty())
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-20">
            <div class="text-center">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="iconify text-5xl text-slate-200" data-icon="solar:user-block-bold-duotone"></span>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2">Tidak Ada Hasil</h3>
                <p class="text-slate-500 font-medium">Maaf, kami tidak menemukan peserta yang sesuai dengan kata kunci Anda.</p>
                <a href="{{ route('admin.penilaian.index') }}"
                    class="inline-block mt-6 text-blue-600 font-bold hover:underline">Lihat Semua Peserta</a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pendaftars as $akun)
                <div
                    class="group bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-2xl hover:shadow-slate-200/50 hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center gap-5 mb-8">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-blue-50 to-indigo-50 text-blue-600 rounded-2xl flex items-center justify-center font-black text-2xl shadow-inner group-hover:from-blue-600 group-hover:to-indigo-600 group-hover:text-white transition-all duration-500">
                            {{ substr($akun->nama, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-black text-slate-800 text-lg leading-tight truncate">{{ $akun->nama }}</h3>
                            <div class="flex items-center gap-2 text-slate-400 mt-1">
                                <span class="iconify shrink-0" data-icon="solar:square-academic-cap-bold"></span>
                                <p class="text-[10px] font-bold uppercase tracking-widest truncate">
                                    {{ $akun->peserta->nama_sekolah ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <div
                                class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-slate-400 shadow-sm font-bold">
                                <span class="iconify" data-icon="solar:map-point-bold"></span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Lokasi
                                </p>
                                <p class="text-xs font-bold text-slate-700 truncate">{{ $akun->peserta->kabupaten ?? '-' }},
                                    {{ $akun->peserta->provinsi ?? '-' }}
                                </p>
                            </div>
                        </div>

                        @if($type === 'akademik')
                            <div class="flex flex-col gap-3 p-4 bg-orange-50/30 rounded-2xl border border-orange-100/50">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-orange-500 shadow-sm font-bold border border-orange-100/30">
                                        <span class="iconify" data-icon="solar:medal-ribbon-bold"></span>
                                    </div>
                                    <div class="min-w-0 flex-grow">
                                        <p class="text-[10px] font-black text-orange-400 uppercase tracking-widest leading-none mb-1">
                                            Status Wawancara</p>
                                        <p class="text-xs font-bold text-slate-700 truncate">
                                            {{ $akun->peserta->ruangan ?? 'Belum Ditentukan' }}
                                            <span class="text-slate-400 font-semibold">•
                                                {{ $akun->peserta->interviewer->nama ?? 'Belum Ditentukan' }}</span>
                                        </p>
                                    </div>
                                </div>

                                @if(in_array(auth()->user()->role, ['admin', 'palugada', 'akademik']) && auth()->user()->email !== 'dendi.pratama@mncu.ac.id')
                                    @php
                                        $roomMap = [
                                            'muhammad.rezki@mncu.ac.id' => 'Ruangan 1',
                                            'humairas.betty@mncu.ac.id' => 'Ruangan 2',
                                            'gilang.surya@mncu.ac.id' => 'Ruangan 3',
                                            'andi.heru@mncu.ac.id' => 'Ruangan 4',
                                            'liena.prajogi@mncu.ac.id' => 'Ruangan 5',
                                            'noval.adi@mncu.ac.id' => 'Ruangan 6',
                                            'neni.nurkhamidah@mncu.ac.id' => 'Ruangan 7',
                                            'anita@mncu.ac.id' => 'Ruangan 8',
                                            'nadya.syifa@mncu.ac.id' => 'Ruangan 9',
                                            'ahmad.fikri@mncu.ac.id' => 'Ruangan 10',
                                            'shelly.morin@mncu.ac.id' => 'Ruangan 11',
                                            'wida.nofiasari@mncu.ac.id' => 'Ruangan 12',
                                            'eko.amri@mncu.ac.id' => 'Ruangan 13',
                                            'bk.ane@mncu.ac.id' => 'Ruangan 14',
                                            'dendi.pratama@mncu.ac.id' => 'Ruangan 15',
                                        ];
                                    @endphp
                                    <form action="{{ route('admin.penilaian.akademik.assign', $akun->id) }}" method="POST"
                                        class="mt-2 pt-2 border-t border-orange-100/30 space-y-2">
                                        @csrf
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label
                                                    class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Ruangan</label>
                                                <input type="text" name="ruangan" value="{{ $akun->peserta->ruangan ?? '' }}" readonly
                                                    placeholder="Ruangan Wawancara"
                                                    class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-[10px] font-bold text-slate-400 outline-none shadow-inner cursor-not-allowed">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Pewawancara</label>
                                                <select name="interviewer_id"
                                                    onchange="const room = this.options[this.selectedIndex].getAttribute('data-room'); if(room) { this.closest('form').querySelector('input[name=\'ruangan\']').value = room; }"
                                                    class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-xl text-[10px] font-bold text-slate-700 outline-none focus:border-orange-500 shadow-sm">
                                                    <option value="">-- Pilih Dosen --</option>
                                                    @foreach($dosens as $dosen)
                                                        @php
                                                            $cleanEmail = strtolower(trim($dosen->email));
                                                            $defaultRoom = $roomMap[$cleanEmail] ?? 'Ruangan Wawancara';
                                                        @endphp
                                                        <option value="{{ $dosen->id }}" data-room="{{ $defaultRoom }}" {{ ($akun->peserta->interviewer_id ?? '') == $dosen->id ? 'selected' : '' }}>
                                                            {{ $dosen->nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <button type="submit"
                                            class="w-full py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-[9px] font-black uppercase tracking-wider transition-all flex items-center justify-center gap-1 shadow-md shadow-orange-500/10">
                                            <span class="iconify" data-icon="solar:check-read-bold"></span> Update Pewawancara
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif

                        @php
                            if ($type === 'mentor') {
                                $myScore = $akun->peserta->penilaianMentors->where('mentor_id', auth()->id())->first();
                                $label = 'Skor Mentor';
                                $scoreVal = $myScore ? $myScore->nilai : null;
                                $themeColor = 'blue';
                            } else {
                                $myScore = $akun->peserta->penilaianAkademiks->where('penilai_id', auth()->id())->first();
                                $label = 'Skor Akademik';
                                $scoreVal = $myScore ? $myScore->total_akhir : null;
                                $themeColor = 'orange';
                            }
                        @endphp

                        <div class="p-4 bg-white rounded-2xl border border-slate-100 shadow-sm">
                            @if($myScore)
                                <div class="flex items-center justify-between font-black">
                                    <div>
                                        <p class="text-[10px] text-slate-400 uppercase tracking-widest leading-none mb-1">{{ $label }}
                                        </p>
                                        <span class="text-3xl text-{{ $themeColor }}-600">{{ $scoreVal }}</span>
                                    </div>
                                    <div
                                        class="flex items-center gap-2 bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-xl border border-emerald-100 shadow-sm">
                                        <span class="iconify" data-icon="solar:verified-check-bold"></span>
                                        <span class="text-[10px] uppercase tracking-widest font-black">SELESAI</span>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center justify-between text-slate-400">
                                    <p class="text-[10px] uppercase tracking-widest font-black">Belum Dinilai</p>
                                    <span class="iconify text-xl animate-pulse" data-icon="solar:star-linear"></span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('admin.penilaian.show', ['id' => $akun->id, 'type' => $type]) }}"
                        class="block w-full py-4 rounded-2xl text-center text-[10px] font-black shadow-lg transition-all uppercase tracking-[0.2em] flex items-center justify-center gap-3 {{ $myScore ? 'bg-slate-50 text-slate-600 border border-slate-100 hover:bg-slate-100' : 'bg-' . $themeColor . '-600 text-white hover:bg-' . $themeColor . '-700 shadow-' . $themeColor . '-200' }}">
                        <span class="iconify" data-icon="{{ $myScore ? 'solar:pen-bold' : 'solar:play-bold' }}"></span>
                        {{ $myScore ? 'Ubah Penilaian' : 'Mulai Evaluasi' }}
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    @if(auth()->user()->email === 'noval.adi@mncu.ac.id')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const btnSync = document.getElementById('btnSyncWawancara');
                if (btnSync) {
                    btnSync.addEventListener('click', function () {
                        Swal.fire({
                            title: 'Sinkronisasi Spreadsheet',
                            text: 'Apakah Anda yakin ingin melakukan sinkronisasi data penilaian akademik ke Google Spreadsheet?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#4f46e5',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Ya, Sinkronkan!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: 'Sedang Sinkronisasi...',
                                    text: 'Mohon tunggu beberapa saat.',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                fetch("{{ route('admin.sync_wawancara_sheet') }}", {
                                    method: 'GET',
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil!',
                                                text: 'Data penilaian akademik berhasil disinkronkan ke Google Spreadsheet.',
                                                confirmButtonColor: '#10b981',
                                                confirmButtonText: 'Selesai'
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Gagal Sinkronisasi',
                                                text: data.error || 'Terjadi kesalahan saat sinkronisasi data.',
                                                confirmButtonColor: '#ef4444'
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error syncing sheet:', error);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Terjadi kesalahan sistem/jaringan.',
                                            confirmButtonColor: '#ef4444'
                                        });
                                    });
                            }
                        });
                    });
                }
            });
        </script>
    @endif
@endsection
@extends('layouts.admin')

@section('content')
<div class="space-y-6 sm:space-y-8">

    <!-- Header Banner -->
    <div class="relative overflow-hidden bg-[#0B1120] text-white p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl shadow-slate-950/20">
        <div class="absolute -top-20 -right-20 w-72 h-72 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/5 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-widest text-blue-400 mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    Computer Based Test &bull; Monitoring Sesi
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight">
                    Panel Manajemen Ujian CBT
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl">
                    Kelola paket test, sinkronisasi jadwal mulai-selesai, dan kendalikan sesi ujian peserta secara terpusat.
                </p>
            </div>
            
            <button onclick="document.getElementById('formTambah').classList.toggle('hidden')"
                class="inline-flex items-center justify-center gap-2 bg-[#0870B8] hover:bg-[#065A96] text-white font-bold text-xs sm:text-sm px-5 py-3 rounded-xl transition-all shadow-xs shrink-0 cursor-pointer">
                <span class="iconify text-base" data-icon="solar:add-circle-bold"></span>
                <span>Buat Test Baru</span>
            </button>
        </div>
    </div>

    {{-- Form Tambah Test --}}
    <form id="formTambah" method="POST" action="{{ route('admin.panel_ujian.store') }}"
        class="hidden bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-4">
        @csrf
        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <span class="iconify text-[#0870B8] text-lg" data-icon="solar:pen-new-square-bold-duotone"></span>
                Form Pembuatan Paket Ujian Baru
            </h3>
            <button type="button" onclick="document.getElementById('formTambah').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                <span class="iconify text-xl" data-icon="solar:close-circle-bold"></span>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="sm:col-span-2 lg:col-span-1">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Test</label>
                <input name="nama" required value="{{ old('nama') }}" placeholder="mis. Tes Potensi Akademik (TPA)"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs sm:text-sm font-semibold outline-none focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Durasi (Menit)</label>
                <input name="durasi" type="number" min="1" max="600" required value="{{ old('durasi', 60) }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs sm:text-sm font-semibold outline-none focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jadwal Mulai (Opsional)</label>
                <input name="jadwal_mulai" type="datetime-local" value="{{ old('jadwal_mulai') }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs sm:text-sm font-semibold outline-none focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jadwal Selesai (Opsional)</label>
                <input name="jadwal_selesai" type="datetime-local" value="{{ old('jadwal_selesai') }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs sm:text-sm font-semibold outline-none focus:border-[#0870B8] focus:ring-4 focus:ring-blue-500/10 transition-all">
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
            <button type="button" onclick="document.getElementById('formTambah').classList.add('hidden')"
                class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100 transition-colors">Batal</button>
            <button class="bg-[#0870B8] hover:bg-[#065A96] text-white font-bold text-xs px-6 py-2.5 rounded-xl transition-all shadow-xs cursor-pointer">
                Simpan & Terbitkan Test
            </button>
        </div>
    </form>

    {{-- Section: Buka Kunci Peserta Terkunci (Pelanggaran Fokus Layar) --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ ($sesiTerkunci->count() > 0) ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ ($sesiTerkunci->count() > 0) ? 'bg-rose-500 animate-ping' : 'bg-emerald-500' }}"></span>
                    <span>{{ $sesiTerkunci->count() }} Peserta Terkunci</span>
                </div>
                <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight mt-1.5 flex items-center gap-2">
                    <span class="iconify text-rose-600 text-xl" data-icon="solar:lock-bold-duotone"></span>
                    <span>Buka Kunci Akses Ujian Peserta (Pelanggaran Fokus CBT)</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Peserta yang terdeteksi beralih tab / aplikasi sebanyak 3 kali otomatis dikunci oleh sistem. Administrator dapat membuka kunci agar peserta dapat melanjutkan ujian atau mereset sesi dari awal.
                </p>
            </div>
        </div>

        @if($sesiTerkunci->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-black uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-3">Nama & Kontak Peserta</th>
                            <th class="py-3 px-3">Paket Ujian</th>
                            <th class="py-3 px-3">Status Pelanggaran</th>
                            <th class="py-3 px-3">Waktu Terkunci</th>
                            <th class="py-3 px-3 text-right">Tindakan Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($sesiTerkunci as $s)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-3">
                                    <div class="font-extrabold text-slate-900 text-sm">{{ $s->peserta?->nama ?? $s->peserta?->akun?->nama ?? 'Peserta #' . $s->peserta_id }}</div>
                                    <div class="text-[10px] text-slate-500 mt-0.5 flex items-center gap-2">
                                        <span>NISN: <strong class="text-slate-700">{{ $s->peserta?->nisn ?? '—' }}</strong></span>
                                        <span>&bull;</span>
                                        <span>{{ $s->peserta?->akun?->email ?? '—' }}</span>
                                        @if($s->peserta?->no_whatsapp)
                                            <span>&bull;</span>
                                            <span>WA: {{ $s->peserta->no_whatsapp }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-flex items-center gap-1 font-bold text-blue-800 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200/80">
                                        <span class="iconify text-sm" data-icon="solar:diploma-bold"></span>
                                        {{ $s->ujian?->nama ?? 'Ujian' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="iconify text-sm" data-icon="solar:lock-bold"></span>
                                        <span>Terkunci ({{ $s->pelanggaran_count }}x Pindah Tab)</span>
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-slate-500 text-[11px] whitespace-nowrap">
                                    {{ $s->terkunci_pada ? $s->terkunci_pada->format('d M Y, H:i') . ' WIB' : 'Baru saja' }}
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        <form method="POST" action="{{ route('admin.panel_ujian.buka_kunci', $s->id) }}" onsubmit="return confirm('Buka kunci ujian untuk {{ $s->peserta?->nama }}? Draft jawaban peserta tetap tersimpan dan pelanggaran di-reset ke 0.')">
                                            @csrf
                                            <button type="submit" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider shadow-sm transition-all flex items-center gap-1.5 cursor-pointer">
                                                <span class="iconify text-base" data-icon="solar:lock-unlocked-bold"></span>
                                                <span>Buka Kunci</span>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.panel_ujian.reset_sesi', $s->id) }}" onsubmit="return confirm('Reset ujian untuk {{ $s->peserta?->nama }}? Sesi akan dihapus dan peserta dapat mengulang dari awal.')">
                                            @csrf
                                            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 hover:text-rose-700 text-slate-600 font-bold text-xs uppercase tracking-wider border border-slate-200 transition-all flex items-center gap-1.5 cursor-pointer">
                                                <span class="iconify text-base" data-icon="solar:restart-bold"></span>
                                                <span>Reset Ulang</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/60 flex items-center gap-3 text-xs text-slate-500">
                <span class="iconify text-xl text-emerald-600 shrink-0" data-icon="solar:check-circle-bold-duotone"></span>
                <span>Semua sesi ujian tertib. Saat ini <strong>tidak ada peserta yang sedang terkunci</strong> akibat pelanggaran fokus layar.</span>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-bold text-emerald-800 flex items-center gap-2">
            <span class="iconify text-lg text-emerald-600" data-icon="solar:check-circle-bold"></span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs font-bold text-rose-800 flex items-center gap-2">
            <span class="iconify text-lg text-rose-600" data-icon="solar:danger-triangle-bold"></span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($ujians as $ujian)
            @php
                $status = $ujian->statusSesi();
                $badgeConfig = [
                    'berjalan' => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'bg-emerald-500 animate-pulse', 'Sesi Berjalan'],
                    'selesai' => ['bg-slate-100 text-slate-600 border-slate-200', 'bg-slate-400', 'Jadwal Selesai'],
                    'tertutup' => ['bg-rose-50 text-rose-700 border-rose-200', 'bg-rose-500', 'Belum Dibuka'],
                ][$status];
                $selesaiCount = $ujian->jawaban_ujians_count;
            @endphp
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs hover:border-blue-500/30 hover:shadow-md transition-all p-6 space-y-4 self-start">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="font-black text-slate-900 text-base leading-snug truncate">{{ $ujian->nama }}</h2>
                        <p class="text-xs text-slate-400 font-semibold mt-0.5">
                            ID: #{{ $ujian->id }} &bull; {{ $ujian->durasi }} Menit &bull; {{ $ujian->soals_count }} Soal
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badgeConfig[0] }} shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full {{ $badgeConfig[1] }}"></span>
                        <span>{{ $badgeConfig[2] }}</span>
                    </span>
                </div>

                <div class="text-xs text-slate-600 space-y-1.5 p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Jadwal Sesi:</span>
                        <span class="font-bold text-slate-800 text-right">
                            {{ $ujian->jadwal_mulai?->format('d M, H:i') ?? '—' }} &rarr; {{ $ujian->jadwal_selesai?->format('d M, H:i') ?? '—' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Peserta Selesai:</span>
                        <span class="font-black text-[#0870B8]">{{ $selesaiCount }} / {{ $totalPeserta }}</span>
                    </div>
                    @if($ujian->override_status !== 'auto')
                        <div class="text-amber-700 font-bold flex items-center gap-1 pt-1 border-t border-slate-200/60">
                            <span class="iconify" data-icon="solar:danger-triangle-bold"></span>
                            <span>Override: {{ $ujian->override_status === 'buka' ? 'Dibuka Manual' : 'Ditutup Manual' }}</span>
                        </div>
                    @endif
                </div>

                {{-- Kontrol Sesi Cepat --}}
                <div class="flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('admin.panel_ujian.override', $ujian->id) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="override_status" value="buka">
                        <button class="px-3 py-1.5 rounded-xl text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-colors cursor-pointer">
                            Buka Sekarang
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.panel_ujian.override', $ujian->id) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="override_status" value="tutup">
                        <button class="px-3 py-1.5 rounded-xl text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition-colors cursor-pointer">
                            Tutup Sekarang
                        </button>
                    </form>
                    @if($ujian->override_status !== 'auto')
                    <form method="POST" action="{{ route('admin.panel_ujian.override', $ujian->id) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="override_status" value="auto">
                        <button class="px-3 py-1.5 rounded-xl text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 transition-colors cursor-pointer">
                            Otomatis
                        </button>
                    </form>
                    @endif
                </div>

                {{-- Action Bar --}}
                <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 pt-3">
                    <a href="{{ route('admin.soal.index', ['ujian_id' => $ujian->id]) }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-50 text-[#0870B8] border border-blue-200 hover:bg-blue-100 transition-colors inline-flex items-center gap-1">
                        <span class="iconify" data-icon="solar:pen-new-square-bold"></span>
                        <span>Bank Soal</span>
                    </a>

                    <details class="relative inline-block">
                        <summary class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 transition-colors cursor-pointer list-none inline-flex items-center gap-1">
                            <span class="iconify" data-icon="solar:settings-bold"></span>
                            <span>Edit Jadwal</span>
                        </summary>
                        <div class="absolute z-20 mt-2 w-80 bg-white border border-slate-200 rounded-2xl shadow-xl p-4 space-y-3 left-0">
                            <form method="POST" action="{{ route('admin.panel_ujian.jadwal', $ujian->id) }}" class="space-y-2">
                                @csrf @method('PUT')
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Atur Jadwal Pelaksanaan</p>
                                <input name="jadwal_mulai" type="datetime-local" value="{{ $ujian->jadwal_mulai?->format('Y-m-d\TH:i') }}"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                                <input name="jadwal_selesai" type="datetime-local" value="{{ $ujian->jadwal_selesai?->format('Y-m-d\TH:i') }}"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                                <button class="w-full px-3 py-2 rounded-xl bg-[#0870B8] text-white text-xs font-bold hover:bg-[#065A96] transition-colors">
                                    Simpan Jadwal
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.panel_ujian.update', $ujian->id) }}" class="space-y-2 border-t border-slate-100 pt-3">
                                @csrf @method('PUT')
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Edit Nama & Durasi</p>
                                <input name="nama" value="{{ $ujian->nama }}" required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                                <input name="durasi" type="number" min="1" max="600" value="{{ $ujian->durasi }}" required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                                <button class="w-full px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors">
                                    Simpan Perubahan
                                </button>
                            </form>
                        </div>
                    </details>

                    <form method="POST" action="{{ route('admin.panel_ujian.kirim_undangan', $ujian->id) }}"
                        onsubmit="return confirm('Kirim email undangan ujian ini ke semua peserta yang lulus seleksi administrasi?')">
                        @csrf
                        <button class="px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-colors cursor-pointer">
                            Undangan
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.panel_ujian.destroy', $ujian->id) }}"
                        onsubmit="return confirm('Hapus test \'{{ $ujian->nama }}\' beserta SEMUA soal, jawaban, dan nilainya? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf @method('DELETE')
                        <button class="px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition-colors cursor-pointer">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-3xl border border-dashed border-slate-300 p-12 text-center text-xs font-bold text-slate-400">
                Belum ada paket ujian CBT. Klik "Buat Test Baru" untuk menambahkan modul ujian.
            </div>
        @endforelse
    </div>
</div>
@endsection

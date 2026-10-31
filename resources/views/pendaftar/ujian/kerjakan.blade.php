@extends('pendaftar.layout')

@section('content')
<style type="text/tailwindcss">
    /* Anti-copy: teks soal & jawaban tidak bisa diseleksi/di-drag */
    .noselect, .noselect * {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        -webkit-touch-callout: none;
    }
</style>

<div class="noselect space-y-4 pb-24 lg:pb-6">

    {{-- Header: judul + timer + aksi --}}
    <div class="bg-navy-mnc rounded-2xl shadow-xl p-4 md:p-5 flex items-center justify-between gap-3 sticky top-2 z-30">
        <div class="min-w-0">
            <a href="{{ route('ujian.index') }}" class="text-[10px] font-black text-primary-orange hover:underline">&larr; Daftar Ujian</a>
            <h1 class="text-white font-black text-sm md:text-base leading-tight truncate">{{ $ujian->nama }}</h1>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <div class="px-3 md:px-4 py-2 rounded-xl bg-white/10 border border-white/10 text-center">
                <p class="text-[9px] font-black uppercase tracking-widest text-primary-orange leading-none">Sisa Waktu</p>
                <p id="timer" class="text-lg md:text-xl font-black text-white tabular-nums leading-tight">--:--:--</p>
            </div>
            <button onclick="konfirmasiSelesai()" id="btnSelesai"
                class="px-4 py-2.5 rounded-xl bg-primary-orange hover:bg-orange-600 text-white font-black text-[11px] uppercase tracking-widest transition-all active:scale-95">
                Selesai
            </button>
        </div>
    </div>

    {{-- Info progres --}}
    <div class="flex flex-wrap items-center gap-3 text-[11px] font-black text-slate-500">
        <span class="px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Terjawab: <span id="cntJawab">0</span>/{{ count($soals) }}</span>
        <span class="px-3 py-1.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">Diragukan: <span id="cntRagu">0</span></span>
        <span id="autosaveInfo" class="px-3 py-1.5 rounded-full bg-slate-100 text-slate-500 border border-slate-200">Tersimpan otomatis</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[16rem_1fr] gap-5 items-start">

        {{-- Panel nomor soal --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 lg:sticky lg:top-28 order-1">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-black uppercase tracking-widest text-slate-400">Navigasi Soal</p>
                <button onclick="document.getElementById('panelNomor').classList.toggle('hidden')"
                    class="lg:hidden text-[11px] font-black text-primary-orange">Tutup</button>
            </div>
            <div id="panelNomor" class="grid grid-cols-6 lg:grid-cols-5 gap-2 mt-3">
                {{-- diisi JS --}}
            </div>
            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-4 text-[10px] font-bold text-slate-400">
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-emerald-400 inline-block"></span> Terjawab</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-amber-400 inline-block"></span> Diragukan</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-slate-200 inline-block"></span> Kosong</span>
            </div>
        </div>

        {{-- Kartu soal --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 md:p-8 order-2">
            <p class="text-[11px] font-black uppercase tracking-widest text-primary-orange">Soal <span id="noAktif">1</span> dari {{ count($soals) }}</p>
            <div id="isiSoal" class="mt-4 space-y-5">
                {{-- diisi JS --}}
            </div>

            <div class="flex items-center justify-between gap-3 mt-8 pt-5 border-t border-slate-100">
                <button onclick="keSoal(current - 1)" id="btnPrev"
                    class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-xs uppercase tracking-widest transition-all disabled:opacity-40">
                    &larr; Sebelumnya
                </button>
                <button onclick="toggleRagu()" id="btnRagu"
                    class="px-4 py-3 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 font-black text-xs uppercase tracking-widest transition-all">
                    Ragu-ragu
                </button>
                <button onclick="keSoal(current + 1)" id="btnNext"
                    class="px-5 py-3 rounded-xl bg-navy-mnc hover:bg-slate-800 text-white font-black text-xs uppercase tracking-widest transition-all disabled:opacity-40">
                    Berikutnya &rarr;
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Overlay hasil --}}
<div id="hasilBox" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 text-center space-y-4">
        <div class="w-16 h-16 mx-auto bg-emerald-100 rounded-full flex items-center justify-center">
            <span class="iconify text-4xl text-emerald-600" data-icon="solar:check-circle-bold"></span>
        </div>
        <h2 class="text-xl font-black text-navy-mnc">Jawaban Terkirim!</h2>
        <p class="text-sm font-semibold text-slate-500" id="hasilDetail"></p>
        <a href="{{ route('ujian.index') }}"
            class="block w-full px-5 py-3 rounded-xl bg-primary-orange hover:bg-orange-600 text-white font-black text-xs uppercase tracking-widest">
            Kembali ke Daftar Ujian
        </a>
    </div>
</div>

<script>
const SOALS = @json($soals);
const UJIAN_ID = {{ $ujian->id }};
const DURASI_TOTAL = {{ (int) $ujian->durasi * 60 }};
let answers = @json($savedAnswers);
let ragu = {};
let current = SOALS.length ? Math.max(0, SOALS.findIndex(s => !answers[s.id])) : 0;
if (current < 0) current = 0;
let sisa = {{ (int) $sisaDetik }};
let submitted = false;
let saveTimer = null;
let dirty = false;

const OPSI = ['a', 'b', 'c', 'd', 'e'];

function esc(t) {
    const d = document.createElement('div');
    d.textContent = t == null ? '' : String(t);
    return d.innerHTML;
}

function terjawab() { return Object.values(answers).filter(v => v).length; }

function updateStats() {
    document.getElementById('cntJawab').textContent = terjawab();
    document.getElementById('cntRagu').textContent = Object.values(ragu).filter(Boolean).length;
    renderPanel();
}

// ---------- Panel nomor ----------
function renderPanel() {
    const panel = document.getElementById('panelNomor');
    panel.innerHTML = SOALS.map((s, i) => {
        let cls = 'bg-slate-100 text-slate-500 border-slate-200';
        if (answers[s.id]) cls = 'bg-emerald-100 text-emerald-700 border-emerald-300';
        if (ragu[s.id]) cls = 'bg-amber-100 text-amber-700 border-amber-300';
        if (i === current) cls += ' ring-2 ring-primary-orange';
        return `<button onclick="keSoal(${i})" class="h-9 rounded-lg border ${cls} font-black text-xs transition-all">${i + 1}</button>`;
    }).join('');
}

// ---------- Render soal ----------
function keSoal(i) {
    if (i < 0 || i >= SOALS.length) return;
    current = i;
    simpanSekarang(); // pindah soal → flush autosave
    renderSoal();
    renderPanel();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function renderSoal() {
    const s = SOALS[current];
    document.getElementById('noAktif').textContent = current + 1;

    let html = `<div class="text-sm md:text-base font-semibold text-slate-800 leading-relaxed whitespace-pre-line">${esc(s.pertanyaan)}</div>`;
    if (s.gambar) html += `<img src="${esc(s.gambar)}" alt="Gambar soal" class="rounded-xl border border-slate-200 max-h-72 mt-3" draggable="false">`;

    html += '<div class="space-y-3 mt-5">';
    for (const o of OPSI) {
        const teks = s['opsi_' + o];
        const gambar = s['opsi_' + o + '_image'];
        if (!teks && !gambar) continue;
        const aktif = answers[s.id] === o;
        html += `
        <button onclick="pilih('${o}')" draggable="false"
            class="w-full text-left flex items-start gap-3 p-4 rounded-xl border-2 transition-all ${aktif ? 'border-primary-orange bg-orange-50' : 'border-slate-200 hover:border-slate-300 bg-white'}">
            <span class="shrink-0 w-7 h-7 rounded-lg flex items-center justify-center font-black text-xs ${aktif ? 'bg-primary-orange text-white' : 'bg-slate-100 text-slate-500'}">${o.toUpperCase()}</span>
            <span class="flex-1 text-sm font-semibold text-slate-700">${teks ? esc(teks) : ''}
                ${gambar ? `<img src="${esc(gambar)}" alt="Opsi ${o.toUpperCase()}" class="rounded-lg border border-slate-200 max-h-40 mt-2" draggable="false">` : ''}
            </span>
        </button>`;
    }
    html += '</div>';

    document.getElementById('isiSoal').innerHTML = html;
    document.getElementById('btnPrev').disabled = current === 0;
    document.getElementById('btnNext').disabled = current === SOALS.length - 1;
    document.getElementById('btnRagu').className =
        `px-4 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition-all border ${ragu[s.id] ? 'bg-amber-500 text-white border-amber-500' : 'bg-amber-50 hover:bg-amber-100 text-amber-700 border-amber-200'}`;
}

function pilih(op) {
    const s = SOALS[current];
    answers[s.id] = answers[s.id] === op ? '' : op;
    renderSoal();
    updateStats();
    jadwalSimpan();
}

function toggleRagu() {
    const id = SOALS[current].id;
    ragu[id] = !ragu[id];
    renderSoal();
    updateStats();
}

// ---------- Autosave ----------
function jadwalSimpan() {
    dirty = true;
    document.getElementById('autosaveInfo').textContent = 'Menyimpan...';
    clearTimeout(saveTimer);
    saveTimer = setTimeout(simpanSekarang, 1000);
}

async function simpanSekarang() {
    clearTimeout(saveTimer);
    if (!dirty || sisa <= 0 || submitted) return;
    dirty = false;
    try {
        const res = await fetch(`/pendaftar/ujian/${UJIAN_ID}/draft`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ jawaban: answers }),
        });
        if (res.ok) {
            document.getElementById('autosaveInfo').textContent = 'Tersimpan otomatis ✓';
            dirty = false;
        } else if (res.status === 403 || res.status === 422) {
            // Ujian ditutup admin / waktu habis → finalkan jawaban tersimpan
            document.getElementById('autosaveInfo').textContent = 'Ujian ditutup — mengirim jawaban...';
            autoSubmit();
        } else {
            document.getElementById('autosaveInfo').textContent = 'Gagal menyimpan — ulangi';
        }
    } catch (e) {
        document.getElementById('autosaveInfo').textContent = 'Gagal menyimpan — ulangi';
    }
}

// ---------- Timer ----------
function fmt(d) {
    const h = String(Math.floor(d / 3600)).padStart(2, '0');
    const m = String(Math.floor((d % 3600) / 60)).padStart(2, '0');
    const s = String(d % 60).padStart(2, '0');
    return `${h}:${m}:${s}`;
}

function tick() {
    document.getElementById('timer').textContent = fmt(Math.max(0, sisa));
    if (sisa <= 0 && !submitted) {
        autoSubmit();
        return;
    }
    sisa--;
    setTimeout(tick, 1000);
}

// ---------- Submit ----------
function konfirmasiSelesai() {
    const kosong = SOALS.length - terjawab();
    Swal.fire({
        title: 'Selesaikan ujian?',
        text: kosong > 0 ? `Masih ada ${kosong} soal kosong. Jawaban yang kosong dianggap salah.` : 'Semua jawaban akan dikirim ke panitia.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Kirim Jawaban',
        cancelButtonText: 'Lanjut Kerjakan',
        confirmButtonColor: '#f97316',
    }).then(r => { if (r.isConfirmed) autoSubmit(); });
}

async function autoSubmit() {
    if (submitted) return;
    submitted = true;
    clearTimeout(saveTimer);
    document.getElementById('btnSelesai').disabled = true;

    // Tidak ada jawaban sama sekali → finalisasi sesi tanpa nilai (bukan submit API)
    if (Object.keys(answers).filter(k => answers[k]).length === 0) {
        await fetch(`/pendaftar/ujian/${UJIAN_ID}/selesai`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
        }).catch(() => {});
        window.location = "{{ route('ujian.index') }}";
        return;
    }

    try {
        const res = await fetch(`/pendaftar/ujian/${UJIAN_ID}/submit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ answers: answers }),
        });
        const data = await res.json();

        if (res.ok && data.status === 'success') {
            const r = data.results || {};
            document.getElementById('hasilDetail').textContent =
                `Skor Anda: ${r.score ?? '-'} dari 100 — ${r.correct_answers ?? '-'} benar dari ${r.total_questions ?? SOALS.length} soal.`;
            const box = document.getElementById('hasilBox');
            box.classList.remove('hidden');
            box.classList.add('flex');
        } else {
            submitted = false;
            document.getElementById('btnSelesai').disabled = false;
            Swal.fire('Gagal mengirim', data.message || 'Coba lagi.', 'error');
        }
    } catch (e) {
        submitted = false;
        document.getElementById('btnSelesai').disabled = false;
        Swal.fire('Gagal mengirim', 'Koneksi bermasalah. Jawaban tersimpan — coba lagi.', 'error');
    }
}

// ---------- Anti-copy ----------
document.addEventListener('contextmenu', e => e.preventDefault());
document.addEventListener('copy', e => e.preventDefault());
document.addEventListener('cut', e => e.preventDefault());
document.addEventListener('dragstart', e => e.preventDefault());
document.addEventListener('selectstart', e => {
    if (e.target.closest('.noselect')) e.preventDefault();
});
document.addEventListener('keydown', e => {
    const k = e.key.toLowerCase();
    if ((e.ctrlKey || e.metaKey) && ['c', 'x', 'p', 's'].includes(k)) {
        e.preventDefault();
    }
});

// ---------- Init ----------
renderSoal();
updateStats();
tick();
// Waktu sudah habis saat halaman dibuka → langsung finalkan
if ({{ (int) $sisaDetik }} <= 0) {
    document.getElementById('timer').textContent = '00:00:00';
    autoSubmit();
}
</script>
@endsection

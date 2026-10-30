@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <a href="{{ route('admin.email-templates.index') }}" class="text-xs font-black text-orange-600 hover:underline">&larr; Kembali ke daftar</a>
            <h1 class="text-2xl font-black text-slate-800 mt-1">Edit: {{ $label }}</h1>
            <p class="text-sm text-slate-500 font-semibold mt-1">Hanya isi bagian dalam email yang bisa diubah — logo, tampilan, dan bagian bawah tetap bawaan sistem.</p>
        </div>
    </div>

    @if(!empty($missing))
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
        <p class="font-black text-sm text-amber-800">Peringatan — placeholder wajib tidak ditemukan di isi email:</p>
        <p class="text-sm text-amber-700 font-semibold mt-1">
            @foreach($missing as $ph)<code class="bg-amber-100 px-2 py-0.5 rounded">{{ $ph }}</code> @endforeach
            Placeholder tersebut <strong>tidak akan tampil</strong> di email yang dikirim. Pastikan tetap disisipkan kecuali memang disengaja.
        </p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Editor -->
        <form action="{{ route('admin.email-templates.update', $key) }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5 shadow-sm self-start">
            @csrf

            <div>
                <label for="subject" class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Judul Email (Subject)</label>
                <input type="text" id="subject" name="subject" maxlength="200"
                    value="{{ old('subject', $template?->subject) }}"
                    placeholder="Kosongkan untuk memakai judul bawaan sistem"
                    class="w-full px-4 py-3 bg-slate-50 border {{ $errors->has('subject') ? 'border-red-500' : 'border-slate-200' }} rounded-xl text-sm font-semibold outline-none focus:ring-4 focus:ring-orange-100">
                @error('subject') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="body" class="block text-xs font-black text-slate-500 uppercase tracking-widest">Isi Email (HTML bagian dalam)</label>
                    <span class="text-[11px] text-slate-400 font-semibold">Kosongkan = tampilan bawaan</span>
                </div>

                @if(count($placeholders))
                <div class="flex flex-wrap gap-2 mb-2">
                    @foreach($placeholders as $ph)
                    <button type="button" onclick="insertPlaceholder('{{ '{' . $ph . '}' }}')"
                        class="px-3 py-1.5 rounded-full bg-orange-50 text-orange-700 text-xs font-black border border-orange-200 hover:bg-orange-100 transition-colors">
                        {<span class="select-none">{{ $ph }}</span>}
                    </button>
                    @endforeach
                </div>
                @endif

                <textarea id="body" name="body" rows="18" maxlength="65000"
                    placeholder="<p>Halo {nama}, ...</p>"
                    class="w-full px-4 py-3 bg-slate-900 text-slate-100 border {{ $errors->has('body') ? 'border-red-500' : 'border-slate-700' }} rounded-xl text-xs font-mono leading-relaxed outline-none focus:ring-4 focus:ring-orange-100">{{ old('body', $template?->body) }}</textarea>
                @error('body') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-black text-sm px-6 py-3 rounded-xl transition-colors">
                    Simpan
                </button>
            </div>
        </form>

        @if($template)
        <form action="{{ route('admin.email-templates.destroy', $key) }}" method="POST" id="resetForm" class="hidden">
            @csrf @method('DELETE')
        </form>
        <button type="button" onclick="confirmReset()"
            class="w-full bg-white border border-red-200 text-red-600 font-black text-sm px-6 py-3 rounded-xl hover:bg-red-50 transition-colors">
            Reset ke Default
        </button>
        @endif

        <!-- Preview -->
        <div class="space-y-3 self-start">
            <!-- form preview nembak ke iframe -->
            <form action="{{ route('admin.email-templates.preview', $key) }}" method="POST" target="previewFrame" id="previewForm" class="flex items-center gap-3">
                @csrf
                <input type="hidden" name="subject" id="previewSubject">
                <input type="hidden" name="body" id="previewBody">
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-black text-sm px-6 py-3 rounded-xl transition-colors">
                    Muat Preview
                </button>
                <span class="text-xs text-slate-400 font-semibold">Data contoh: nama Budi Santoso, kode 123456</span>
            </form>

            <iframe name="previewFrame" class="w-full h-[720px] rounded-2xl border border-slate-200 bg-white"
                title="Preview email"></iframe>
        </div>

    </div>
</div>

<script>
function insertPlaceholder(ph) {
    const ta = document.getElementById('body');
    const start = ta.selectionStart;
    ta.value = ta.value.slice(0, start) + ph + ta.value.slice(ta.selectionEnd);
    ta.focus();
    ta.selectionStart = ta.selectionEnd = start + ph.length;
}

// sync draft ke form preview sebelum submit ke iframe
document.getElementById('previewForm').addEventListener('submit', function () {
    document.getElementById('previewSubject').value = document.getElementById('subject').value;
    document.getElementById('previewBody').value = document.getElementById('body').value;
});

function confirmReset() {
    Swal.fire({
        title: 'Kembalikan ke default?',
        text: 'Judul dan isi yang sudah diubah akan dihapus. Email kembali memakai tampilan bawaan sistem.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, reset',
        cancelButtonText: 'Batal',
    }).then((r) => {
        if (r.isConfirmed) document.getElementById('resetForm').submit();
    });
}
</script>
@endsection

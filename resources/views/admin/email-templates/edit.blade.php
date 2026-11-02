@extends('layouts.admin')

@section('content')
<div class="space-y-6 sm:space-y-8">

    <!-- Header Banner -->
    <div class="relative overflow-hidden bg-[#0B1120] text-white p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl shadow-slate-950/20">
        <div class="absolute -top-20 -right-20 w-72 h-72 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('admin.email-templates.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-400 hover:text-blue-300 transition-colors mb-2">
                    <span class="iconify text-sm" data-icon="solar:arrow-left-linear"></span>
                    <span>Kembali ke Daftar Template</span>
                </a>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight">
                    Edit Template: {{ $label }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl">
                    Sunting subjek dan isi dokumen HTML email secara langsung. Anda dapat melihat pratinjau sebelum menyimpan.
                </p>
            </div>
            
            <div class="font-mono text-xs px-3.5 py-2 rounded-xl bg-slate-900/90 border border-slate-800 text-slate-300 shrink-0">
                Key: <strong class="text-blue-400">{{ $key }}</strong>
            </div>
        </div>
    </div>

    @if(!empty($missing))
    <div class="rounded-2xl border border-amber-500/30 bg-amber-500/10 p-5 flex items-start gap-3">
        <span class="iconify text-xl text-amber-500 shrink-0 mt-0.5" data-icon="solar:shield-warning-bold"></span>
        <div>
            <p class="font-bold text-xs sm:text-sm text-amber-900">Peringatan: Placeholder wajib tidak ditemukan dalam template:</p>
            <p class="text-xs text-amber-800 font-medium mt-1 leading-relaxed">
                @foreach($missing as $ph)<code class="bg-amber-200/60 text-amber-950 px-1.5 py-0.5 rounded text-[11px] font-mono mr-1">{{ $ph }}</code>@endforeach
                Placeholder di atas diperlukan agar sistem dapat menyuntikkan data personal peserta (seperti link reset / kode OTP).
            </p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Left: Code Editor -->
        <form action="{{ route('admin.email-templates.update', $key) }}" method="POST" id="saveForm" class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 space-y-5 shadow-xs self-start">
            @csrf

            <div>
                <label for="subject" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Subjek Email (Subject)</label>
                <input type="text" id="subject" name="subject" maxlength="200"
                    value="{{ old('subject', $template?->subject) }}"
                    placeholder="Kosongkan untuk memakai subjek bawaan sistem"
                    class="w-full px-4 py-3 bg-slate-50 hover:bg-white focus:bg-white border {{ $errors->has('subject') ? 'border-rose-500' : 'border-slate-200' }} focus:border-[#0870B8] rounded-xl text-xs sm:text-sm font-semibold outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                @error('subject') <p class="text-rose-500 text-xs mt-1.5 font-semibold">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Kode HTML Lengkap</span>
                    <span class="text-[11px] text-slate-400">Kosongkan untuk reset ke default</span>
                </div>

                @if(count($placeholders))
                <div class="flex flex-wrap items-center gap-1.5 mb-3 p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mr-1">Klik Placeholder:</span>
                    @foreach($placeholders as $ph)
                    <button type="button" onclick="insertPlaceholder('{{ '{' . $ph . '}' }}')"
                        class="px-2.5 py-1 rounded-lg bg-blue-50 text-[#0870B8] hover:bg-blue-100 text-[11px] font-bold border border-blue-200 transition-colors cursor-pointer">
                        {{ '{' . $ph . '}' }}
                    </button>
                    @endforeach
                </div>
                @endif

                <textarea id="body" name="body" rows="24" spellcheck="false"
                    placeholder="<!DOCTYPE html> ..."
                    class="w-full px-4 py-3 bg-[#0B1120] text-slate-100 border {{ $errors->has('body') ? 'border-rose-500' : 'border-slate-800' }} rounded-2xl text-xs font-mono leading-relaxed outline-none focus:ring-4 focus:ring-blue-500/20 transition-all">{{ old('body', $prefill) }}</textarea>
                @error('body') <p class="text-rose-500 text-xs mt-1.5 font-semibold">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="loadPreview()" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                    <span class="iconify text-sm" data-icon="solar:eye-bold"></span>
                    <span>Muat Pratinjau</span>
                </button>

                <div class="flex items-center gap-2">
                    <button type="submit" class="bg-[#0870B8] hover:bg-[#065A96] text-white font-bold text-xs px-5 py-2.5 rounded-xl transition-all shadow-xs cursor-pointer inline-flex items-center gap-1.5">
                        <span class="iconify text-sm" data-icon="solar:diskette-bold"></span>
                        <span>Simpan Template</span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Right: Live Preview Panel -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 space-y-4 shadow-xs self-start flex flex-col">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <span class="iconify text-[#0870B8] text-lg" data-icon="solar:monitor-smartphone-bold-duotone"></span>
                        Pratinjau Tampilan Email
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Render visual langsung sebelum disiarkan ke pendaftar.</p>
                </div>
                @if($template)
                <form action="{{ route('admin.email-templates.destroy', $key) }}" method="POST" onsubmit="return confirm('Kembalikan template email ini ke format asli bawaan sistem?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-rose-600 hover:text-rose-700 font-bold px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors cursor-pointer">
                        Reset Default
                    </button>
                </form>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 overflow-hidden bg-slate-100/50 min-h-[500px]">
                <iframe id="previewFrame" class="w-full h-[600px] bg-white border-0" srcdoc="<p style='font-family:sans-serif;font-size:13px;color:#94a3b8;padding:20px;text-align:center;'>Klik tombol 'Muat Pratinjau' untuk melihat tampilan email.</p>"></iframe>
            </div>
        </div>

    </div>

</div>

<script>
function insertPlaceholder(text) {
    const textarea = document.getElementById('body');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const value = textarea.value;
    textarea.value = value.substring(0, start) + text + value.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + text.length;
    textarea.focus();
}

function loadPreview() {
    const form = document.getElementById('saveForm');
    const body = document.getElementById('body').value;
    const subject = document.getElementById('subject').value;
    const frame = document.getElementById('previewFrame');

    fetch("{{ route('admin.email-templates.preview', $key) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ body: body, subject: subject })
    })
    .then(r => r.json())
    .then(data => {
        frame.srcdoc = data.html || "<p style='padding:20px;'>Gagal merender preview</p>";
    })
    .catch(err => {
        frame.srcdoc = "<p style='padding:20px;color:red;'>Terjadi kesalahan saat memuat preview</p>";
    });
}

document.addEventListener('DOMContentLoaded', function() {
    loadPreview();
});
</script>
@endsection

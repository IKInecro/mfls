@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-black text-slate-800">Kelola Template Email</h1>
        <p class="text-sm text-slate-500 font-semibold mt-1">Atur isi dan judul email yang dikirim otomatis ke pendaftar. Template yang belum diubah memakai tampilan asli bawaan sistem.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs font-black uppercase tracking-widest">
                    <th class="text-left px-6 py-4">Email</th>
                    <th class="text-left px-6 py-4">Status</th>
                    <th class="px-6 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($templates as $t)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-4">
                        <span class="font-black text-slate-800">{{ $t['label'] }}</span>
                        <span class="block text-xs text-slate-400 font-semibold">{{ $t['key'] }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($t['row'])
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-orange-100 text-orange-700">
                                Diubah @if($t['row']->updated_at) &middot; {{ $t['row']->updated_at->format('d/m/Y H:i') }} @endif
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-slate-100 text-slate-500">Default</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.email-templates.edit', $t['key']) }}"
                            class="bg-orange-500 hover:bg-orange-600 text-white font-black text-xs px-5 py-2.5 rounded-xl transition-colors">
                            Edit
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection

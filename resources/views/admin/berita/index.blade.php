@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800">Manajemen Berita</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola publikasi berita dan artikel</p>
        </div>
        <a href="{{ route('admin.berita.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0870B8] text-white rounded-xl hover:bg-[#065A96] transition-all font-bold text-xs shadow-xs">
            <span class="iconify text-base" data-icon="solar:add-circle-bold"></span>
            <span>Tambah Berita Baru</span>
        </a>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-4">Judul Berita</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Tanggal Publikasi</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($beritas as $berita)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($berita->thumbnail)
                                <img src="{{ asset('storage/' . $berita->thumbnail) }}" alt="{{ $berita->judul }}" class="w-12 h-12 rounded-lg object-cover">
                                @else
                                <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400">
                                    <span class="iconify text-2xl" data-icon="solar:gallery-bold-duotone"></span>
                                </div>
                                @endif
                                <div>
                                    <p class="font-bold text-slate-800">{{ Str::limit($berita->judul, 50) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($berita->is_published)
                            <span class="px-2.5 py-1 bg-green-50 text-green-600 rounded-md text-[10px] font-black uppercase tracking-widest border border-green-100">Dipublikasi</span>
                            @else
                            <span class="px-2.5 py-1 bg-slate-50 text-slate-500 rounded-md text-[10px] font-black uppercase tracking-widest border border-slate-200">Draf</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500">
                            {{ $berita->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.berita.edit', $berita->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors border border-transparent hover:border-blue-100" title="Edit">
                                    <span class="iconify text-lg" data-icon="solar:pen-bold-duotone"></span>
                                </a>
                                <form action="{{ route('admin.berita.destroy', $berita->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berita ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors border border-transparent hover:border-red-100" title="Hapus">
                                        <span class="iconify text-lg" data-icon="solar:trash-bin-trash-bold-duotone"></span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                    <span class="iconify text-3xl text-slate-400" data-icon="solar:document-text-bold-duotone"></span>
                                </div>
                                <p class="text-slate-500 font-medium">Belum ada berita yang ditambahkan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($beritas->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $beritas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

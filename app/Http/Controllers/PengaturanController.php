<?php

namespace App\Http\Controllers;

use App\Models\PengaturanPendaftaran;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function edit()
    {
        $pengaturan = PengaturanPendaftaran::instance();

        return view('admin.pengaturan.pendaftaran', [
            'pengaturan' => $pengaturan,
            'sedangBuka' => PengaturanPendaftaran::aktif(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'mode' => 'required|in:auto,buka,tutup',
            'pendaftaran_mulai' => ['required_unless:mode,buka,tutup', 'nullable', 'date'],
            'pendaftaran_selesai' => ['required_unless:mode,buka,tutup', 'nullable', 'date', 'after_or_equal:pendaftaran_mulai'],
        ]);

        PengaturanPendaftaran::instance()->update($validated);

        return back()->with('success', 'Pengaturan pendaftaran berhasil disimpan.');
    }
}

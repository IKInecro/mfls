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

    public function timeline()
    {
        $pengaturan = PengaturanPendaftaran::instance();
        $timeline = $pengaturan->getTimelineOrDefault();

        return view('admin.pengaturan.timeline', [
            'pengaturan' => $pengaturan,
            'timeline' => $timeline,
        ]);
    }

    public function updateTimeline(Request $request)
    {
        $request->validate([
            'steps' => 'required|array|min:1',
            'steps.*.step' => 'required|string|max:20',
            'steps.*.title' => 'required|string|max:150',
            'steps.*.date' => 'required|string|max:100',
            'steps.*.desc' => 'required|string|max:1000',
            'steps.*.status' => 'required|string|max:50',
            'active_index' => 'nullable|integer',
        ]);

        $rawSteps = array_values($request->input('steps', []));
        $activeIndex = $request->input('active_index');

        $processedSteps = [];
        foreach ($rawSteps as $idx => $step) {
            $processedSteps[] = [
                'step' => trim($step['step'] ?? str_pad($idx + 1, 2, '0', STR_PAD_LEFT)),
                'title' => trim($step['title'] ?? ''),
                'date' => trim($step['date'] ?? ''),
                'desc' => trim($step['desc'] ?? ''),
                'status' => trim($step['status'] ?? 'Mendatang'),
                'active' => ($activeIndex !== null && (int)$activeIndex === $idx),
            ];
        }

        PengaturanPendaftaran::instance()->update([
            'timeline' => $processedSteps,
        ]);

        return back()->with('success', 'Jadwal linimasa seleksi berhasil disimpan dan langsung diperbarui di halaman beranda.');
    }

    public function resetTimeline()
    {
        PengaturanPendaftaran::instance()->update([
            'timeline' => PengaturanPendaftaran::defaultTimeline(),
        ]);

        return back()->with('success', 'Jadwal linimasa seleksi telah di-reset ke pengaturan standar.');
    }
}

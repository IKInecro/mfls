<?php

namespace App\Http\Controllers;

use App\Models\RiwayatAktivitas;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'palugada', 'panitia', 'god'])) {
            return abort(403, 'Akses ditolak.');
        }

        $query = RiwayatAktivitas::with('pelaku');

        // Sembunyikan aktivitas akun GOD dari non-god untuk menjaga kerahasiaan developer
        if (auth()->user()->role !== 'god') {
            $query->whereDoesntHave('pelaku', function($q) {
                $q->where('role', 'god');
            });
        }

        // Filtering by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filtering by activity type (aksi)
        if ($request->filled('aksi')) {
            $query->where('aksi', $request->aksi);
        }

        // Search in description or perpetrator name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('pelaku', function($q2) use ($search) {
                      $q2->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $riwayats = $query->latest()->paginate(25)->withQueryString();
        
        // Get unique activities for filter dropdown
        $activities = RiwayatAktivitas::select('aksi')->distinct()->pluck('aksi');

        return view('admin.activity_log.index', compact('riwayats', 'activities'));
    }
}

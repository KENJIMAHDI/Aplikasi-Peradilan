<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalSidang;

class RelaasController extends Controller
{
    public function index(Request $request)
    {
        $totalPanggilan = JadwalSidang::count();
        $panggilanPatut = JadwalSidang::where('status_relaas', 'Relaas Siap/Patut')->count();
        $panggilanBelum = JadwalSidang::where('status_relaas', '!=', 'Relaas Siap/Patut')->count();

        $daftarRelaas = JadwalSidang::with(['hakim', 'ruangSidang'])
            ->orderBy('waktu_mulai', 'desc')
            ->get();

        return view('relaas.index', compact(
            'totalPanggilan',
            'panggilanPatut',
            'panggilanBelum',
            'daftarRelaas'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-perkara');

        $request->validate([
            'status_relaas' => 'required|string|in:Relaas Siap/Patut,Belum Dipanggil,Dalam Perjalanan,Tidak Bertemu'
        ]);

        $jadwal = JadwalSidang::findOrFail($id);
        $jadwal->update([
            'status_relaas' => $request->status_relaas
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status relaas berhasil diperbarui.',
                'data' => $jadwal
            ]);
        }

        return redirect()->route('relaas.index')->with('success', 'Status relaas panggilan berhasil diperbarui secara realtime.');
    }
}

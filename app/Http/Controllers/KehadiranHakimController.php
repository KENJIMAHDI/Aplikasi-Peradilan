<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KehadiranHakimController extends Controller
{
    public function index()
    {
        $presensi = \App\Models\PresensiHakim::with('hakim')->latest()->get();
        $hakims = \App\Models\Hakim::all();
        return view('kehadiran.index', compact('presensi', 'hakims'));
    }

    public function storeHakim(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|unique:hakims,nip',
            'nama' => 'required|string|max:255',
            'chat_id_telegram' => 'nullable|string|max:255',
        ]);

        \App\Models\Hakim::create([
            'nip' => $request->nip,
            'nama' => $request->nama,
            'chat_id_telegram' => $request->chat_id_telegram,
        ]);

        return redirect()->back()->with('success', 'Hakim baru berhasil ditambahkan.');
    }

    public function destroyHakim($id)
    {
        $hakim = \App\Models\Hakim::findOrFail($id);
        $hakim->delete();

        return redirect()->back()->with('success', 'Data Hakim berhasil dihapus dari sistem.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'hakim_id' => 'required|exists:hakims,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:Hadir,Cuti,Dinas Luar,Sakit,Izin',
        ]);

        $presensi = \App\Models\PresensiHakim::create($request->all());
        $presensi->load('hakim');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data kehadiran berhasil ditambahkan.',
                'data' => $presensi
            ]);
        }

        return redirect()->back()->with('success', 'Data kehadiran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Hadir,Cuti,Dinas Luar,Sakit,Izin',
        ]);

        $presensi = \App\Models\PresensiHakim::findOrFail($id);
        $presensi->update(['status' => $request->status]);
        $presensi->load('hakim');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data kehadiran berhasil diperbarui.',
                'data' => $presensi
            ]);
        }

        return redirect()->back()->with('success', 'Data kehadiran berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $presensi = \App\Models\PresensiHakim::findOrFail($id);
        $presensi->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data kehadiran berhasil dihapus.'
            ]);
        }

        return redirect()->back()->with('success', 'Data kehadiran berhasil dihapus.');
    }
}

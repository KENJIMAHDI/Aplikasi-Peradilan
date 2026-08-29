<?php

namespace App\Http\Controllers;

use App\Models\DelegasiPerkara;
use Illuminate\Http\Request;

class DelegasiController extends Controller
{
    public function index()
    {
        $delegasi = DelegasiPerkara::latest()->get();
        return view('delegasi.index', compact('delegasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_perkara' => 'required|string|max:255',
            'pn_pengirim' => 'required|string|max:255',
            'pn_penerima' => 'required|string|max:255',
            'tujuan_delegasi' => 'required|string|max:255',
            'status' => 'required|in:Selesai,Dalam Proses'
        ]);

        $delegasi = DelegasiPerkara::create($request->all());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data delegasi berhasil ditambahkan.',
                'data' => $delegasi
            ], 201);
        }

        return redirect()->route('delegasi.index')->with('success', 'Data delegasi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $delegasi = DelegasiPerkara::findOrFail($id);

        $request->validate([
            'nomor_perkara' => 'required|string|max:255',
            'pn_pengirim' => 'required|string|max:255',
            'pn_penerima' => 'required|string|max:255',
            'tujuan_delegasi' => 'required|string|max:255',
            'status' => 'required|in:Selesai,Dalam Proses'
        ]);

        $delegasi->update($request->all());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data delegasi berhasil diperbarui.',
                'data' => $delegasi
            ]);
        }

        return redirect()->route('delegasi.index')->with('success', 'Data delegasi berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $delegasi = DelegasiPerkara::findOrFail($id);
        $delegasi->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data delegasi berhasil dihapus.'
            ]);
        }

        return redirect()->route('delegasi.index')->with('success', 'Data delegasi berhasil dihapus.');
    }
}

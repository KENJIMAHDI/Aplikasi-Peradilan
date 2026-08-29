<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EBerpadu;

class EBerpaduController extends Controller
{
    // Halaman Panel Hakim
    public function index()
    {
        $permohonans = EBerpadu::latest()->get();
        return view('e_berpadu.index', compact('permohonans'));
    }

    // Form Pengajuan oleh Penyidik/Penuntut
    public function create()
    {
        return view('e_berpadu.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'instansi_pengaju' => 'required|string',
            'jenis_permohonan' => 'required|string',
            'nama_tersangka' => 'required|string',
        ]);

        $berpadu = EBerpadu::create([
            'nomor_surat' => 'BPD/' . date('Y/m/') . rand(100, 999),
            'instansi_pengaju' => $request->instansi_pengaju,
            'jenis_permohonan' => $request->jenis_permohonan,
            'nama_tersangka' => $request->nama_tersangka,
            'status_persetujuan_hakim' => 'Menunggu'
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Permohonan e-Berpadu berhasil diajukan.',
                'data' => $berpadu
            ]);
        }

        return redirect()->route('e-berpadu.create')->with('success', 'Permohonan e-Berpadu berhasil diajukan dan sedang menunggu persetujuan Hakim.');
    }

    // Fungsi approve/reject oleh hakim
    public function update(Request $request, $id)
    {
        $permohonan = EBerpadu::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak'
        ]);

        $permohonan->update([
            'status_persetujuan_hakim' => $request->status
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status permohonan berhasil diperbarui.',
                'data' => $permohonan
            ]);
        }

        return redirect()->route('e-berpadu.index')->with('success', 'Status permohonan berhasil diperbarui.');
    }
}

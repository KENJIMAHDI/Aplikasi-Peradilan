<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ERaterang;

class ERaterangController extends Controller
{
    // Halaman Panel Verifikasi Petugas
    public function index()
    {
        $permohonans = ERaterang::latest()->get();
        return view('e_raterang.index', compact('permohonans'));
    }

    // Form Pengajuan oleh Masyarakat
    public function create()
    {
        return view('e_raterang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik_pemohon' => 'required|string|size:16',
            'nama_pemohon' => 'required|string',
            'jenis_surat' => 'required|string',
        ]);

        $raterang = ERaterang::create([
            'nomor_permohonan' => 'SK/' . date('Y/m/d/') . rand(100, 999),
            'nik_pemohon' => $request->nik_pemohon,
            'nama_pemohon' => $request->nama_pemohon,
            'jenis_surat' => $request->jenis_surat,
            'status_verifikasi' => 'Belum Diverifikasi'
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Permohonan Surat Keterangan berhasil diajukan.',
                'data' => $raterang
            ]);
        }

        return redirect()->route('e-raterang.create')->with('success', 'Permohonan Surat Keterangan berhasil diajukan. Silakan cek status secara berkala.');
    }

    // Fungsi verifikasi oleh petugas
    public function update(Request $request, $id)
    {
        $permohonan = ERaterang::findOrFail($id);
        
        $permohonan->update([
            'status_verifikasi' => 'Selesai'
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Surat Keterangan berhasil diverifikasi.',
                'data' => $permohonan
            ]);
        }

        return redirect()->route('e-raterang.index')->with('success', 'Surat Keterangan berhasil diverifikasi.');
    }

    // Halaman cetak/print
    public function show($id)
    {
        $permohonan = ERaterang::findOrFail($id);
        if($permohonan->status_verifikasi != 'Selesai') {
            return back()->with('error', 'Surat belum diverifikasi!');
        }
        // return view khusus cetak
        return view('e_raterang.print', compact('permohonan'));
    }
}

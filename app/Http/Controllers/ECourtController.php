<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ECourtPerkara;

class ECourtController extends Controller
{
    public function index()
    {
        $perkaras = ECourtPerkara::latest()->get();
        return view('e_court.index', compact('perkaras'));
    }

    public function create()
    {
        return view('e_court.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_perdata' => 'required|string',
            'penggugat' => 'required|string',
            'tergugat' => 'required|string',
        ]);

        // Kalkulator sederhana: biaya dasar 500k + 200k admin
        $panjar = 700000;
        
        $perkara = ECourtPerkara::create([
            'nomor_register' => 'PDT/' . date('Y/m/d/') . rand(100, 999),
            'jenis_perdata' => $request->jenis_perdata,
            'penggugat' => $request->penggugat,
            'tergugat' => $request->tergugat,
            'nominal_panjar' => $panjar,
            'status_pembayaran' => 'Belum Dibayar'
        ]);

        return redirect()->route('e-court.index')->with('success', 'Perkara e-Court berhasil didaftarkan. Tagihan panjar: Rp ' . number_format($panjar, 0, ',', '.'));
    }
}

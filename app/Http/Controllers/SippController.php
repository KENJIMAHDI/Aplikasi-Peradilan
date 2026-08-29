<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalSidang;
use App\Models\RiwayatPerkara;

class SippController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $jadwals = collect();

        if ($query) {
            $jadwals = JadwalSidang::with(['hakim', 'ruangSidang', 'riwayatPerkaras'])
                ->where('nomor_perkara', 'LIKE', "%{$query}%")
                ->orWhereHas('hakim', fn($q) => $q->where('nama', 'LIKE', "%{$query}%"))
                ->get();
        }

        return view('sipp.index', compact('jadwals', 'query'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiwayatPerkara;

class PidanaController extends Controller
{
    public function biasa()
    {
        $perkara = \App\Models\PerkaraPidana::all();
        return view('pidana.index', compact('perkara'));
    }

    public function khusus()
    {
        $perkara = \App\Models\PerkaraPidana::where('status', 'Khusus')->get();
        return view('pidana.khusus', compact('perkara'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_perkara' => 'required',
            'terdakwa' => 'required',
            'jaksa' => 'required',
        ]);

        $perkara = \App\Models\PerkaraPidana::create([
            'nomor_perkara' => $request->nomor_perkara,
            'terdakwa' => $request->terdakwa,
            'jaksa' => $request->jaksa,
            'pasal' => $request->pasal,
            'status' => $request->status ?? 'Proses'
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Perkara Pidana berhasil ditambahkan.',
                'data' => $perkara
            ]);
        }

        return redirect()->back()->with('success', 'Perkara Pidana berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nomor_perkara' => 'required',
            'terdakwa' => 'required',
            'jaksa' => 'required',
        ]);

        $perkara = \App\Models\PerkaraPidana::findOrFail($id);
        $perkara->update([
            'nomor_perkara' => $request->nomor_perkara,
            'terdakwa' => $request->terdakwa,
            'jaksa' => $request->jaksa,
            'pasal' => $request->pasal,
            'status' => $request->status ?? $perkara->status
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Perkara Pidana berhasil diperbarui.',
                'data' => $perkara
            ]);
        }

        return redirect()->back()->with('success', 'Perkara Pidana berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $perkara = \App\Models\PerkaraPidana::findOrFail($id);
        $perkara->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Perkara Pidana berhasil dihapus.'
            ]);
        }

        return redirect()->back()->with('success', 'Perkara Pidana berhasil dihapus.');
    }
}

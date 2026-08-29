<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiwayatPerkara; // Assuming we use RiwayatPerkara as a base model

class PerdataController extends Controller
{
    public function umum()
    {
        $perkara = \App\Models\ECourtPerkara::whereIn('jenis_perdata', ['Gugatan', 'Permohonan'])->latest('id')->get();
        return view('perdata.umum', compact('perkara'));
    }

    public function khusus()
    {
        $perkara = \App\Models\ECourtPerkara::whereIn('jenis_perdata', ['PHI', 'Niaga'])->latest('id')->get();
        return view('perdata.khusus', compact('perkara'));
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-perkara');

        if ($request->has('kategori')) {
            $kat = strtolower(trim($request->kategori));
            if ($kat === 'gugatan') {
                $request->merge(['jenis_perdata' => 'Gugatan']);
            } elseif ($kat === 'permohonan') {
                $request->merge(['jenis_perdata' => 'Permohonan']);
            } elseif ($kat === 'phi') {
                $request->merge(['jenis_perdata' => 'PHI']);
            } elseif ($kat === 'niaga') {
                $request->merge(['jenis_perdata' => 'Niaga']);
            }
        }

        $request->validate([
            'nomor_perkara' => 'required|unique:e_court_perkaras,nomor_register',
            'jenis_perdata' => 'required',
            'penggugat' => 'required',
            'tergugat' => 'nullable',
            'tanggal_daftar' => 'nullable|date',
            'status' => 'nullable|string',
            'nominal_panjar' => 'nullable|numeric',
            'status_pembayaran' => 'nullable|string',
            'jadwal_sidang_online' => 'nullable|date',
            'link_litigasi_online' => 'nullable|string',
        ]);
        
        $perkara = \App\Models\ECourtPerkara::create([
            'nomor_register' => $request->nomor_perkara,
            'tanggal_daftar' => $request->tanggal_daftar ?? now()->format('Y-m-d'),
            'status' => $request->status ?? 'Diajukan',
            'jenis_perdata' => $request->jenis_perdata,
            'penggugat' => $request->penggugat,
            'tergugat' => $request->tergugat,
            'nominal_panjar' => $request->nominal_panjar ?? 0,
            'status_pembayaran' => $request->status_pembayaran ?? 'Belum Dibayar',
            'jadwal_sidang_online' => $request->jadwal_sidang_online,
            'link_litigasi_online' => $request->link_litigasi_online,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Perkara ' . $request->jenis_perdata . ' berhasil ditambahkan.',
                'data' => $perkara
            ]);
        }

        if (in_array($request->jenis_perdata, ['PHI', 'Niaga'])) {
            $tab = strtolower($request->jenis_perdata);
            return redirect()->to(route('perdata.khusus') . '?tab=' . $tab)->with([
                'success' => 'Data Register ' . $request->jenis_perdata . ' berhasil ditambahkan.',
                'active_tab' => $tab
            ]);
        }

        $tab = strtolower($request->jenis_perdata) === 'permohonan' ? 'permohonan' : 'gugatan';
        return redirect()->to(route('perdata.umum') . '?tab=' . $tab)->with([
            'success' => 'Data Register ' . $request->jenis_perdata . ' berhasil ditambahkan.',
            'active_tab' => $tab
        ]);
    }

    public function update(Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-perkara');

        if ($request->has('kategori')) {
            $kat = strtolower(trim($request->kategori));
            if ($kat === 'gugatan') {
                $request->merge(['jenis_perdata' => 'Gugatan']);
            } elseif ($kat === 'permohonan') {
                $request->merge(['jenis_perdata' => 'Permohonan']);
            } elseif ($kat === 'phi') {
                $request->merge(['jenis_perdata' => 'PHI']);
            } elseif ($kat === 'niaga') {
                $request->merge(['jenis_perdata' => 'Niaga']);
            }
        }

        $request->validate([
            'nomor_perkara' => 'required|unique:e_court_perkaras,nomor_register,' . $id,
            'jenis_perdata' => 'required',
            'penggugat' => 'required',
            'tergugat' => 'nullable',
            'tanggal_daftar' => 'nullable|date',
            'status' => 'nullable|string',
            'nominal_panjar' => 'nullable|numeric',
            'status_pembayaran' => 'nullable|string',
            'jadwal_sidang_online' => 'nullable|date',
            'link_litigasi_online' => 'nullable|string',
        ]);

        $perkara = \App\Models\ECourtPerkara::findOrFail($id);
        $perkara->update([
            'nomor_register' => $request->nomor_perkara,
            'tanggal_daftar' => $request->tanggal_daftar ?? $perkara->tanggal_daftar ?? now()->format('Y-m-d'),
            'status' => $request->status ?? $perkara->status ?? 'Diajukan',
            'jenis_perdata' => $request->jenis_perdata,
            'penggugat' => $request->penggugat,
            'tergugat' => $request->tergugat,
            'nominal_panjar' => $request->nominal_panjar ?? $perkara->nominal_panjar ?? 0,
            'status_pembayaran' => $request->status_pembayaran ?? $perkara->status_pembayaran ?? 'Belum Dibayar',
            'jadwal_sidang_online' => $request->jadwal_sidang_online,
            'link_litigasi_online' => $request->link_litigasi_online,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Register ' . $perkara->jenis_perdata . ' berhasil diperbarui.',
                'data' => $perkara
            ]);
        }

        if (in_array($perkara->jenis_perdata, ['PHI', 'Niaga'])) {
            $tab = strtolower($perkara->jenis_perdata);
            return redirect()->to(route('perdata.khusus') . '?tab=' . $tab)->with([
                'success' => 'Data Register ' . $perkara->jenis_perdata . ' berhasil diperbarui.',
                'active_tab' => $tab
            ]);
        }

        $tab = strtolower($perkara->jenis_perdata) === 'permohonan' ? 'permohonan' : 'gugatan';
        return redirect()->to(route('perdata.umum') . '?tab=' . $tab)->with([
            'success' => 'Data Register ' . $perkara->jenis_perdata . ' berhasil diperbarui.',
            'active_tab' => $tab
        ]);
    }

    public function destroy(Request $request, $id)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-perkara');

        $perkara = \App\Models\ECourtPerkara::findOrFail($id);
        $jenis = $perkara->jenis_perdata;
        $perkara->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Register ' . $jenis . ' berhasil dihapus.'
            ]);
        }

        if (in_array($jenis, ['PHI', 'Niaga'])) {
            $tab = strtolower($jenis);
            return redirect()->to(route('perdata.khusus') . '?tab=' . $tab)->with([
                'success' => 'Data Register ' . $jenis . ' berhasil dihapus.',
                'active_tab' => $tab
            ]);
        }

        $tab = strtolower($jenis) === 'permohonan' ? 'permohonan' : 'gugatan';
        return redirect()->to(route('perdata.umum') . '?tab=' . $tab)->with([
            'success' => 'Data Register ' . $jenis . ' berhasil dihapus.',
            'active_tab' => $tab
        ]);
    }
}

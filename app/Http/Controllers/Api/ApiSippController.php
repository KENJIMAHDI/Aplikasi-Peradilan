<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ECourtPerkara;
use App\Models\PerkaraPidana;

class ApiSippController extends Controller {
    public function perdataUmum() {
        return response()->json(['data' => ECourtPerkara::whereIn('jenis_perdata', ['Gugatan', 'Permohonan'])->latest('id')->get()]);
    }
    public function perdataKhusus() {
        return response()->json(['data' => ECourtPerkara::whereIn('jenis_perdata', ['PHI', 'Niaga'])->latest('id')->get()]);
    }
    public function storePerdata(Request $request) {
        $perkara = ECourtPerkara::create($request->all());
        return response()->json(['message' => 'Berhasil ditambahkan', 'data' => $perkara], 201);
    }
    public function updatePerdata(Request $request, $id) {
        $perkara = ECourtPerkara::findOrFail($id);
        $perkara->update($request->all());
        return response()->json(['message' => 'Berhasil diperbarui', 'data' => $perkara]);
    }
    public function destroyPerdata($id) {
        ECourtPerkara::findOrFail($id)->delete();
        return response()->json(['message' => 'Berhasil dihapus']);
    }
    public function pidanaBiasa() {
        return response()->json(['data' => PerkaraPidana::where('status', '!=', 'Khusus')->get()]);
    }
    public function pidanaKhusus() {
        return response()->json(['data' => PerkaraPidana::where('status', 'Khusus')->get()]);
    }
    public function storePidana(Request $request) {
        $perkara = PerkaraPidana::create($request->all());
        return response()->json(['message' => 'Berhasil', 'data' => $perkara], 201);
    }
    public function updatePidana(Request $request, $id) {
        $perkara = PerkaraPidana::findOrFail($id);
        $perkara->update($request->all());
        return response()->json(['message' => 'Berhasil', 'data' => $perkara]);
    }
    public function destroyPidana($id) {
        PerkaraPidana::findOrFail($id)->delete();
        return response()->json(['message' => 'Berhasil']);
    }
}

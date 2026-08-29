<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalSidang;

class ApiRelaasController extends Controller {
    public function index() {
        $daftarRelaas = JadwalSidang::with(['hakim', 'ruangSidang'])->orderBy('waktu_mulai', 'desc')->get();
        return response()->json(['data' => $daftarRelaas]);
    }
    public function updateStatus(Request $request, $id) {
        $jadwal = JadwalSidang::findOrFail($id);
        $jadwal->update(['status_relaas' => $request->status_relaas]);
        return response()->json(['message' => 'Berhasil', 'data' => $jadwal]);
    }
}

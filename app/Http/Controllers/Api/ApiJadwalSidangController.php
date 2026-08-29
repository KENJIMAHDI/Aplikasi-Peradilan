<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalSidang;
use App\Services\JadwalSidangService;

class ApiJadwalSidangController extends Controller {
    public function index() {
        return response()->json(['data' => JadwalSidang::with(['hakim', 'ruangSidang'])->orderBy('waktu_mulai', 'asc')->get()]);
    }
    public function store(Request $request, JadwalSidangService $service) {
        try {
            $service->cekKonflik($request->waktu_mulai, $request->waktu_selesai, $request->ruang_sidang_id, $request->hakim_id);
            $jadwal = JadwalSidang::create($request->all());
            return response()->json(['message' => 'Berhasil', 'data' => $jadwal], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
    public function update(Request $request, $id, JadwalSidangService $service) {
        $jadwal = JadwalSidang::findOrFail($id);
        try {
            $service->cekKonflik($request->waktu_mulai, $request->waktu_selesai, $request->ruang_sidang_id, $request->hakim_id, $id);
            $jadwal->update($request->all());
            return response()->json(['message' => 'Berhasil', 'data' => $jadwal]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
    public function destroy($id) {
        JadwalSidang::findOrFail($id)->delete();
        return response()->json(['message' => 'Berhasil']);
    }
    public function panggil($id) {
        // Mock blast
        return response()->json(['message' => 'Panggilan dikirim']);
    }
}

<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PresensiHakim;

class ApiKehadiranHakimController extends Controller {
    public function index() {
        return response()->json(['data' => PresensiHakim::with('hakim')->latest()->get()]);
    }
    public function store(Request $request) {
        $presensi = PresensiHakim::create($request->all());
        return response()->json(['message' => 'Berhasil', 'data' => $presensi], 201);
    }
    public function update(Request $request, $id) {
        $presensi = PresensiHakim::findOrFail($id);
        $presensi->update($request->all());
        return response()->json(['message' => 'Berhasil', 'data' => $presensi]);
    }
    public function destroy($id) {
        PresensiHakim::findOrFail($id)->delete();
        return response()->json(['message' => 'Berhasil']);
    }
}

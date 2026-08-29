<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalSidang;

class ApiAntreanController extends Controller {
    public function index() {
        return response()->json(['data' => JadwalSidang::whereDate('waktu_mulai', today())->orderBy('waktu_mulai', 'asc')->get()]);
    }
    public function store(Request $request) {
        $jadwal = JadwalSidang::firstOrCreate(['nomor_perkara' => $request->no_perkara], [
            'waktu_mulai' => now(), 'waktu_selesai' => now()->addHour(), 'ruang_sidang_id' => 1, 'hakim_id' => 1, 'status' => 'TERJADWAL'
        ]);
        if (in_array($request->peran, ['penggugat', 'kuasa_hukum'])) {
            $jadwal->status_penggugat = 'Hadir';
        } else {
            $jadwal->status_tergugat = 'Hadir';
        }
        $jadwal->save();
        return response()->json(['message' => 'Checkin berhasil', 'data' => $jadwal]);
    }
}

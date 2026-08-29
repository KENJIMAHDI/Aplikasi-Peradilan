<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalSidang;
use App\Models\ECourtPerkara;
use App\Models\PerkaraPidana;

class ApiDashboardController extends Controller {
    public function index(Request $request) {
        $masuk = ECourtPerkara::count();
        $putus = ECourtPerkara::where('status', 'Selesai')->count();
        $stats = [
            'masuk' => $masuk,
            'putus' => $putus,
            'sisa' => max(0, $masuk - $putus)
        ];
        $jadwalHariIni = JadwalSidang::with(['hakim', 'ruangSidang'])->whereDate('waktu_mulai', today())->orderBy('waktu_mulai', 'asc')->get();
        return response()->json(compact('stats', 'jadwalHariIni'));
    }
}

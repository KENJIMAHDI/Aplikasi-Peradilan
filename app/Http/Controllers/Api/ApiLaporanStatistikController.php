<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\ECourtPerkara;
use App\Models\PerkaraPidana;

class ApiLaporanStatistikController extends Controller {
    public function index() {
        $gugatanMasuk = ECourtPerkara::where('jenis_perdata', 'Gugatan')->count();
        $gugatanPutus = ECourtPerkara::where('jenis_perdata', 'Gugatan')->where('status', 'Selesai')->count();
        $pidanaMasuk = PerkaraPidana::count();
        $pidanaPutus = PerkaraPidana::where('status', 'Putus')->count();
        $totalMasuk = $gugatanMasuk + $pidanaMasuk;
        $totalPutus = $gugatanPutus + $pidanaPutus;
        return response()->json([
            'totalMasuk' => $totalMasuk,
            'totalPutus' => $totalPutus,
            'clearanceRate' => $totalMasuk > 0 ? round(($totalPutus / $totalMasuk) * 100, 1) : 100.0
        ]);
    }
}

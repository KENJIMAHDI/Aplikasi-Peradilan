<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ECourtPerkara;
use App\Models\PerkaraPidana;
use App\Models\JadwalSidang;
use App\Models\Hakim;

class LaporanStatistikController extends Controller
{
    public function index()
    {
        // Perdata Stats
        $gugatanMasuk = ECourtPerkara::where('jenis_perdata', 'Gugatan')->count();
        $gugatanPutus = ECourtPerkara::where('jenis_perdata', 'Gugatan')->where('status', 'Selesai')->count();
        $gugatanSisa = max(0, $gugatanMasuk - $gugatanPutus);

        $permohonanMasuk = ECourtPerkara::where('jenis_perdata', 'Permohonan')->count();
        $permohonanPutus = ECourtPerkara::where('jenis_perdata', 'Permohonan')->where('status', 'Selesai')->count();
        $permohonanSisa = max(0, $permohonanMasuk - $permohonanPutus);

        $khususMasuk = ECourtPerkara::whereIn('jenis_perdata', ['PHI', 'Niaga'])->count();
        $khususPutus = ECourtPerkara::whereIn('jenis_perdata', ['PHI', 'Niaga'])->where('status', 'Selesai')->count();
        $khususSisa = max(0, $khususMasuk - $khususPutus);

        // Pidana Stats
        $pidanaMasuk = PerkaraPidana::count();
        $pidanaPutus = PerkaraPidana::where('status', 'Putus')->count();
        $pidanaSisa = max(0, $pidanaMasuk - $pidanaPutus);

        // Global Totals
        $totalMasuk = $gugatanMasuk + $permohonanMasuk + $khususMasuk + $pidanaMasuk;
        $totalPutus = $gugatanPutus + $permohonanPutus + $khususPutus + $pidanaPutus;
        $totalSisa = max(0, $totalMasuk - $totalPutus);

        // Clearance Rate
        $clearanceRate = $totalMasuk > 0 ? round(($totalPutus / $totalMasuk) * 100, 1) : 100.0;

        // Distribusi Perkara
        $distribusi = [
            [
                'kategori' => 'Perdata Gugatan',
                'sisa_lalu' => ECourtPerkara::where('jenis_perdata', 'Gugatan')->where('status', 'Diajukan')->count(),
                'masuk' => $gugatanMasuk,
                'putus' => $gugatanPutus,
                'sisa' => $gugatanSisa,
            ],
            [
                'kategori' => 'Perdata Permohonan',
                'sisa_lalu' => ECourtPerkara::where('jenis_perdata', 'Permohonan')->where('status', 'Diajukan')->count(),
                'masuk' => $permohonanMasuk,
                'putus' => $permohonanPutus,
                'sisa' => $permohonanSisa,
            ],
            [
                'kategori' => 'Perdata Khusus (PHI/Niaga)',
                'sisa_lalu' => ECourtPerkara::whereIn('jenis_perdata', ['PHI', 'Niaga'])->where('status', 'Diajukan')->count(),
                'masuk' => $khususMasuk,
                'putus' => $khususPutus,
                'sisa' => $khususSisa,
            ],
            [
                'kategori' => 'Pidana (Biasa & Khusus)',
                'sisa_lalu' => 0,
                'masuk' => $pidanaMasuk,
                'putus' => $pidanaPutus,
                'sisa' => $pidanaSisa,
            ],
        ];

        // Beban Sidang Hakim
        $hakims = Hakim::withCount('jadwalSidangs')->get();

        return view('laporan.index', compact(
            'totalMasuk',
            'totalPutus',
            'totalSisa',
            'clearanceRate',
            'distribusi',
            'hakims'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalSidang;
use App\Models\ECourtPerkara;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $searchResults = null;

        if ($q) {
            $perdataResults = ECourtPerkara::where('nomor_register', 'like', "%{$q}%")
                ->orWhere('penggugat', 'like', "%{$q}%")
                ->orWhere('tergugat', 'like', "%{$q}%")
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => 'Perdata',
                        'kategori' => $item->jenis_perdata,
                        'nomor' => $item->nomor_register,
                        'pihak1' => $item->penggugat,
                        'pihak2' => $item->tergugat ?: '-',
                        'status' => $item->status,
                        'tanggal' => $item->tanggal_daftar ? $item->tanggal_daftar->format('d/m/Y') : '-'
                    ];
                });

            $pidanaResults = \App\Models\PerkaraPidana::where('nomor_perkara', 'like', "%{$q}%")
                ->orWhere('terdakwa', 'like', "%{$q}%")
                ->orWhere('jaksa', 'like', "%{$q}%")
                ->orWhere('pasal', 'like', "%{$q}%")
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => 'Pidana',
                        'kategori' => $item->status === 'Khusus' ? 'Pidana Khusus' : 'Pidana Biasa',
                        'nomor' => $item->nomor_perkara,
                        'pihak1' => $item->terdakwa,
                        'pihak2' => $item->jaksa ?: '-',
                        'status' => $item->status,
                        'tanggal' => $item->created_at ? $item->created_at->format('d/m/Y') : '-'
                    ];
                });

            $searchResults = $perdataResults->concat($pidanaResults);
        }

        $masuk = ECourtPerkara::count();
        $putus = ECourtPerkara::where('status', 'Selesai')->count();
        $sedangProses = ECourtPerkara::where('status', 'Sedang Di Proses')->count();
        $diajukan = ECourtPerkara::where('status', 'Diajukan')->count();
        $sisa = max(0, $masuk - $putus);

        $stats = [
            'sisa_lalu' => $diajukan,
            'masuk' => $masuk,
            'putus' => $putus,
            'minutasi' => $sedangProses,
            'sisa' => $sisa
        ];

        // Data jadwal sidang riil dari database
        $jadwalHariIni = JadwalSidang::with(['hakim', 'ruangSidang'])
            ->whereDate('waktu_mulai', today())
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        // Load WA Replies
        $repliesFile = storage_path('app/wa_replies.json');
        $waReplies = [];
        if (file_exists($repliesFile)) {
            $waReplies = json_decode(file_get_contents($repliesFile), true) ?: [];
            // Sort by timestamp desc
            usort($waReplies, function($a, $b) {
                return strcmp($b['timestamp'], $a['timestamp']);
            });
        }

        return view('dashboard', compact('stats', 'jadwalHariIni', 'searchResults', 'q', 'waReplies'));
    }

    public function getWaRepliesJson()
    {
        $repliesFile = storage_path('app/wa_replies.json');
        $waReplies = [];
        if (file_exists($repliesFile)) {
            $waReplies = json_decode(file_get_contents($repliesFile), true) ?: [];
            usort($waReplies, function($a, $b) {
                return strcmp($b['timestamp'], $a['timestamp']);
            });
        }
        return response()->json($waReplies);
    }
}

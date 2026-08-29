<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ECourtPerkara;
use App\Models\JadwalSidang;
use App\Models\BerkasPutusan;
use Illuminate\Support\Carbon;

class ApiPerkaraController extends Controller {
    public function index() {
        $perkaras = ECourtPerkara::where('penggugat', auth()->user()->name)->orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $perkaras]);
    }
    public function storeMandiri(Request $request) {
        // Simplified for mobile
        $perkara = ECourtPerkara::create([
            'nomor_register' => 'REG-EC-' . now()->format('Ymd') . '-' . rand(1000, 9999),
            'tanggal_daftar' => now(),
            'jenis_perdata' => $request->kategori ?? 'Gugatan',
            'penggugat' => auth()->user()->name,
            'tergugat' => $request->nama_tergugat,
            'status' => 'Diajukan',
            'nominal_panjar' => 150000.00,
            'status_pembayaran' => 'Belum Dibayar',
        ]);
        return response()->json(['message' => 'Berhasil', 'data' => $perkara], 201);
    }
    public function adminIndex() {
        return response()->json(['data' => ECourtPerkara::orderBy('created_at', 'desc')->get()]);
    }
    public function adminConfirmPembayaran($id) {
        $perkara = ECourtPerkara::findOrFail($id);
        $perkara->update(['status_bayar' => 'lunas', 'status_pembayaran' => 'Lunas']);
        return response()->json(['message' => 'Pembayaran lunas', 'data' => $perkara]);
    }
    public function adminVerify(Request $request, $id) {
        $perkara = ECourtPerkara::findOrFail($id);
        $perkara->update(['nomor_register' => $request->nomor_perkara_resmi, 'status' => 'Sidang', 'status_verifikasi' => 'terverifikasi']);
        JadwalSidang::create([
            'nomor_perkara' => $request->nomor_perkara_resmi,
            'waktu_mulai' => Carbon::parse($request->tanggal_sidang_pertama),
            'waktu_selesai' => Carbon::parse($request->tanggal_sidang_pertama)->addHour(),
            'ruang_sidang_id' => $request->ruang_sidang_id,
            'hakim_id' => $request->hakim_id,
            'status' => 'TERJADWAL',
        ]);
        return response()->json(['message' => 'Terverifikasi', 'data' => $perkara]);
    }
    public function hakimIndex() {
        $schedules = JadwalSidang::where('hakim_id', auth()->user()->hakim_id)->with('ruangSidang')->orderBy('waktu_mulai', 'desc')->get();
        return response()->json(['data' => $schedules]);
    }
    public function hakimPutusan(Request $request, $id) {
        $schedule = JadwalSidang::findOrFail($id);
        $schedule->update(['status' => 'PUTUS']);
        return response()->json(['message' => 'Putusan diunggah', 'data' => $schedule]);
    }
}

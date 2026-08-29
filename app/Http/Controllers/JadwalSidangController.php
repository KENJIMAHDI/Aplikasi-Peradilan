<?php

namespace App\Http\Controllers;

use App\Models\JadwalSidang;
use App\Models\Hakim;
use App\Models\RuangSidang;
use Illuminate\Http\Request;
use App\Services\JadwalSidangService;
use App\Services\TelegramService;

class JadwalSidangController extends Controller
{
    protected $jadwalService;
    protected $telegramService;

    public function __construct(JadwalSidangService $jadwalService, TelegramService $telegramService)
    {
        $this->jadwalService = $jadwalService;
        $this->telegramService = $telegramService;
    }

    public function index()
    {
        $jadwals = JadwalSidang::with(['hakim', 'ruangSidang'])->orderBy('waktu_mulai', 'asc')->get();
        $hakims = Hakim::all();
        $ruangSidangs = RuangSidang::all();
        return view('jadwal_sidang.index', compact('jadwals', 'hakims', 'ruangSidangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hakim_id' => 'required|exists:hakims,id',
            'ruang_sidang_id' => 'required|exists:ruang_sidangs,id',
            'nomor_perkara' => 'required|string',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
        ]);

        try {
            // Cek konflik dengan Smart Conflict Detector
            $this->jadwalService->cekKonflik(
                $request->waktu_mulai, 
                $request->waktu_selesai, 
                $request->ruang_sidang_id, 
                $request->hakim_id
            );

            // Simpan Jadwal
            $jadwal = JadwalSidang::create($request->all());
            $jadwal->load(['hakim', 'ruangSidang']);

            // Kirim Notifikasi Telegram ke Hakim
            $hakim = Hakim::find($request->hakim_id);
            $ruang = RuangSidang::find($request->ruang_sidang_id);
            if ($hakim && $hakim->chat_id_telegram) {
                $pesan = "🔔 *PENGINGAT JADWAL SIDANG*\n\n"
                       . "Yth. Hakim {$hakim->nama},\n"
                       . "Anda memiliki jadwal persidangan baru:\n\n"
                       . "📄 *Perkara:* {$jadwal->nomor_perkara}\n"
                       . "🏢 *Ruangan:* {$ruang->nama_ruangan}\n"
                       . "🕒 *Waktu:* " . \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('d M Y, H:i') . " WIB\n\n"
                       . "Harap hadir tepat waktu. Terima kasih.";
                
                $this->telegramService->kirimPengingatSidang($hakim->chat_id_telegram, $pesan);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jadwal sidang berhasil ditambahkan.',
                    'data' => $jadwal
                ]);
            }

            return redirect()->route('jadwal.index')->with('success', 'Jadwal sidang berhasil ditambahkan dan notifikasi terkirim (jika Hakim terhubung ke Telegram).');

        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            return back()->withErrors(['conflict' => $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalSidang::findOrFail($id);

        $request->validate([
            'hakim_id' => 'required|exists:hakims,id',
            'ruang_sidang_id' => 'required|exists:ruang_sidangs,id',
            'nomor_perkara' => 'required|string',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
        ]);

        try {
            // Cek konflik dengan mengabaikan jadwal yang sedang diubah
            $konflikRuang = JadwalSidang::where('ruang_sidang_id', $request->ruang_sidang_id)
                ->where('id', '!=', $jadwal->id)
                ->where(function($q) use ($request) {
                    $q->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                      ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai]);
                })->exists();

            if ($konflikRuang) {
                throw new \Exception("Ruang sidang sudah terpakai pada waktu tersebut.");
            }

            $konflikHakim = JadwalSidang::where('hakim_id', $request->hakim_id)
                ->where('id', '!=', $jadwal->id)
                ->where(function($q) use ($request) {
                    $q->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                      ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai]);
                })->exists();

            if ($konflikHakim) {
                throw new \Exception("Hakim sudah memiliki jadwal sidang pada waktu tersebut.");
            }

            $jadwal->update($request->all());
            $jadwal->load(['hakim', 'ruangSidang']);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jadwal sidang berhasil diperbarui.',
                    'data' => $jadwal
                ]);
            }

            return redirect()->route('jadwal.index')->with('success', 'Jadwal sidang berhasil diperbarui.');

        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            return back()->withErrors(['conflict' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        $jadwal = JadwalSidang::findOrFail($id);
        $jadwal->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Jadwal sidang berhasil dihapus.'
            ]);
        }

        return redirect()->route('jadwal.index')->with('success', 'Jadwal sidang berhasil dihapus.');
    }

    public function panggil(Request $request, $id)
    {
        $jadwal = JadwalSidang::findOrFail($id);
        $ruangan = $jadwal->ruangSidang->nama_ruangan ?? 'Ruang Sidang';

        $message = "PANGGILAN SIDANG! Perkara No. {$jadwal->nomor_perkara} dipanggil masuk ke {$ruangan} SEKARANG.";

        $sentCount = 0;
        if (!empty($jadwal->no_hp_penggugat)) {
            \App\Services\WhatsAppService::send($jadwal->no_hp_penggugat, $message);
            $sentCount++;
        }
        if (!empty($jadwal->no_hp_tergugat)) {
            \App\Services\WhatsAppService::send($jadwal->no_hp_tergugat, $message);
            $sentCount++;
        }

        if ($sentCount === 0) {
            return redirect()->route('jadwal.index')->withErrors(['error' => 'Gagal mengirim panggilan: Nomor WhatsApp Penggugat dan Tergugat tidak terisi / belum check-in.']);
        }

        return redirect()->route('jadwal.index')->with('success', "Panggilan sidang dikirim ke {$sentCount} pihak.");
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalSidang;
use App\Services\WhatsAppService;

class AntreanController extends Controller
{
    public function index()
    {
        $jadwals = JadwalSidang::with(['hakim', 'ruangSidang'])
            ->whereDate('waktu_mulai', today())
            ->orderBy('waktu_mulai', 'asc')
            ->get();
        return view('antrean.index', compact('jadwals'));
    }

    public function publicCheckin()
    {
        return view('antrean.checkin');
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_perkara'      => 'required',
            'peran'           => 'required|in:penggugat,tergugat,kuasa_hukum',
            'status_kehadiran'=> 'required|in:hadir,sakit,izin,tidak_dapat_hadir,agenda_kesibukan,luar_kota',
            'no_whatsapp'     => 'required',
            'catatan_khusus'  => 'nullable|string',
        ]);

        // Cari atau buat jadwal otomatis jika belum tersedia
        $jadwal = JadwalSidang::firstOrCreate(
            ['nomor_perkara' => $request->no_perkara],
            [
                'waktu_mulai' => now()->format('Y-m-d H:i:s'),
                'waktu_selesai' => now()->addHour()->format('Y-m-d H:i:s'),
                'ruang_sidang_id' => 1,
                'hakim_id' => 1,
                'status' => 'TERJADWAL',
                'status_relaas' => 'Belum Dipanggil',
            ]
        );

        // Format tampilan label status
        $catatan = $request->catatan_khusus ? " ({$request->catatan_khusus})" : "";
        
        $statusText = match($request->status_kehadiran) {
            'hadir'             => 'Hadir & Siap Sidang',
            'sakit'             => 'Sakit',
            'izin'              => 'Izin (Keluar/Makan)',
            'tidak_dapat_hadir' => 'Tidak Dapat Hadir' . $catatan,
            'agenda_kesibukan'  => 'Agenda Kesibukan' . $catatan,
            'luar_kota'         => 'Di Luar Kota/Provinsi',
            default             => 'Hadir & Siap Sidang',
        };

        // Update kolom status sesuai peran pihak
        if ($request->peran === 'penggugat' || $request->peran === 'kuasa_hukum') {
            $jadwal->status_penggugat = $statusText;
            $jadwal->no_hp_penggugat = $request->no_whatsapp;
        } else {
            $jadwal->status_tergugat = $statusText;
            $jadwal->no_hp_tergugat = $request->no_whatsapp;
        }

        // Cek kelengkapan
        if ($jadwal->status_penggugat === 'Hadir & Siap Sidang' && $jadwal->status_tergugat === 'Hadir & Siap Sidang') {
            $jadwal->status_kelengkapan = 'siap_sidang';
        } else {
            $jadwal->status_kelengkapan = 'belum_lengkap';
        }

        $jadwal->save();

        // Otomatis Blast WA ke Hakim jika kelengkapan siap_sidang
        if ($jadwal->status_kelengkapan === 'siap_sidang') {
            $hakim = $jadwal->hakim;
            if ($hakim && !empty($hakim->no_hp)) {
                $ruangan = $jadwal->ruangSidang->nama_ruangan ?? 'Ruang Sidang';
                $message = "Bapak/Ibu Hakim {$hakim->nama}, Perkara No. {$jadwal->nomor_perkara} di Ruang {$ruangan} pihak Penggugat & Tergugat SUDAH LENGKAP & SIAP DIPANGGIL.";
                WhatsAppService::send($hakim->no_hp, $message);
            }
        }

        return redirect()->back()->with('success', "Konfirmasi kehadiran berhasil dikirim sebagai: {$statusText}");
    }

    public function handleWaReply(Request $request)
    {
        $sender = $request->input('sender') ?: $request->input('from');
        $message = $request->input('message') ?: $request->input('text');

        if (empty($sender) || empty($message)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid data'], 400);
        }

        $repliesFile = storage_path('app/wa_replies.json');
        $replies = [];
        if (file_exists($repliesFile)) {
            $replies = json_decode(file_get_contents($repliesFile), true) ?: [];
        }

        $replies[] = [
            'sender' => $sender,
            'message' => $message,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ];

        // Keep only last 20 replies
        if (count($replies) > 20) {
            $replies = array_slice($replies, -20);
        }

        file_put_contents($repliesFile, json_encode($replies));

        return response()->json(['status' => 'success']);
    }
}

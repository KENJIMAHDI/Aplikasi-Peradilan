<?php

namespace App\Services;

use App\Models\JadwalSidang;
use App\Models\PresensiHakim;
use Exception;
use Illuminate\Support\Carbon;

class JadwalSidangService
{
    /**
     * Memeriksa bentrok ruang sidang, bentrok jadwal hakim, dan cuti hakim.
     * @param string $tanggal_sidang
     * @param string $jam_sidang
     * @param int $ruangan_id
     * @param int $hakim_id
     * @throws Exception
     */
    public function cekKonflik($waktu_mulai, $waktu_selesai, $ruangan_id, $hakim_id, $ignoreId = null)
    {
        $mulai = Carbon::parse($waktu_mulai);
        $selesai = Carbon::parse($waktu_selesai);
        $tanggal_sidang = $mulai->format('Y-m-d');

        // 1. Cek Cuti / Izin Hakim
        $presensi = PresensiHakim::where('hakim_id', $hakim_id)
            ->whereDate('tanggal', $tanggal_sidang)
            ->first();

        if ($presensi && in_array($presensi->status, ['Cuti', 'Sakit', 'Dinas Luar'])) {
            throw new Exception("Smart Conflict Detector: Tidak dapat menjadwalkan sidang karena Hakim berstatus '{$presensi->status}' pada tanggal tersebut.");
        }

        // 2. Cek bentrok jadwal Hakim
        $konflikHakim = JadwalSidang::where('hakim_id', $hakim_id)
            ->when($ignoreId, function($q) use ($ignoreId) {
                $q->where('id', '!=', $ignoreId);
            })
            ->where(function($query) use ($mulai, $selesai) {
                $query->whereBetween('waktu_mulai', [$mulai, $selesai])
                      ->orWhereBetween('waktu_selesai', [$mulai, $selesai])
                      ->orWhere(function($q) use ($mulai, $selesai) {
                          $q->where('waktu_mulai', '<=', $mulai)
                            ->where('waktu_selesai', '>=', $selesai);
                      });
            })
            ->first();

        if ($konflikHakim) {
            throw new Exception("Smart Conflict Detector: Hakim sudah memiliki jadwal sidang (Perkara: {$konflikHakim->nomor_perkara}) pada rentang waktu tersebut.");
        }

        // 3. Cek bentrok Ruang Sidang
        $konflikRuangan = JadwalSidang::where('ruang_sidang_id', $ruangan_id)
            ->when($ignoreId, function($q) use ($ignoreId) {
                $q->where('id', '!=', $ignoreId);
            })
            ->where(function($query) use ($mulai, $selesai) {
                $query->whereBetween('waktu_mulai', [$mulai, $selesai])
                      ->orWhereBetween('waktu_selesai', [$mulai, $selesai])
                      ->orWhere(function($q) use ($mulai, $selesai) {
                          $q->where('waktu_mulai', '<=', $mulai)
                            ->where('waktu_selesai', '>=', $selesai);
                      });
            })
            ->first();

        if ($konflikRuangan) {
            throw new Exception("Smart Conflict Detector: Ruang sidang sudah digunakan pada rentang waktu tersebut.");
        }
        
        return true;
    }
}

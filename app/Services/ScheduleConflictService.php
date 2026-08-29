<?php

namespace App\Services;

use App\Models\JadwalSidang;
use App\Models\PresensiHakim;
use Carbon\Carbon;

class ScheduleConflictService
{
    /**
     * Cek apakah ada konflik jadwal untuk hakim atau ruang sidang tertentu.
     *
     * @param int $hakimId
     * @param int $ruangSidangId
     * @param string $waktuMulai
     * @param string $waktuSelesai
     * @return array
     */
    public function checkConflict($hakimId, $ruangSidangId, $waktuMulai, $waktuSelesai): array
    {
        $mulai = Carbon::parse($waktuMulai);
        $selesai = Carbon::parse($waktuSelesai);

        // 1. Cek Cuti Hakim pada tanggal tersebut
        $isCuti = PresensiHakim::where('hakim_id', $hakimId)
            ->whereDate('tanggal', $mulai->toDateString())
            ->whereIn('status', ['Cuti', 'Dinas'])
            ->exists();

        if ($isCuti) {
            return [
                'has_conflict' => true,
                'message' => 'Hakim sedang Cuti atau Dinas pada tanggal tersebut.'
            ];
        }

        // 2. Cek Jadwal bentrok di ruang sidang
        $conflictRuang = JadwalSidang::where('ruang_sidang_id', $ruangSidangId)
            ->where(function ($query) use ($mulai, $selesai) {
                $query->whereBetween('waktu_mulai', [$mulai, $selesai])
                      ->orWhereBetween('waktu_selesai', [$mulai, $selesai])
                      ->orWhere(function ($q) use ($mulai, $selesai) {
                          $q->where('waktu_mulai', '<=', $mulai)
                            ->where('waktu_selesai', '>=', $selesai);
                      });
            })
            ->exists();

        if ($conflictRuang) {
            return [
                'has_conflict' => true,
                'message' => 'Ruang sidang sudah digunakan pada rentang waktu tersebut.'
            ];
        }

        // 3. Cek Jadwal bentrok untuk Hakim
        $conflictHakim = JadwalSidang::where('hakim_id', $hakimId)
            ->where(function ($query) use ($mulai, $selesai) {
                $query->whereBetween('waktu_mulai', [$mulai, $selesai])
                      ->orWhereBetween('waktu_selesai', [$mulai, $selesai])
                      ->orWhere(function ($q) use ($mulai, $selesai) {
                          $q->where('waktu_mulai', '<=', $mulai)
                            ->where('waktu_selesai', '>=', $selesai);
                      });
            })
            ->exists();

        if ($conflictHakim) {
            return [
                'has_conflict' => true,
                'message' => 'Hakim sudah memiliki jadwal sidang lain pada rentang waktu tersebut.'
            ];
        }

        return [
            'has_conflict' => false,
            'message' => 'Jadwal tersedia.'
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalSidang extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }

    public function hakim()
    {
        return $this->belongsTo(Hakim::class);
    }

    public function ruangSidang()
    {
        return $this->belongsTo(RuangSidang::class);
    }

    public function riwayatPerkaras()
    {
        return $this->hasMany(\App\Models\RiwayatPerkara::class);
    }
}

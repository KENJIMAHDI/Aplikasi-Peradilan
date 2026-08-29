<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPerkara extends Model
{
    protected $guarded = [];

    public function jadwalSidang()
    {
        return $this->belongsTo(\App\Models\JadwalSidang::class);
    }
}

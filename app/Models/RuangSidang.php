<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuangSidang extends Model
{
    protected $guarded = [];

    public function jadwalSidang()
    {
        return $this->hasMany(JadwalSidang::class);
    }
}

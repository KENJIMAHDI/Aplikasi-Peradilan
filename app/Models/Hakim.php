<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hakim extends Model
{
    protected $guarded = [];

    public function jadwalSidang()
    {
        return $this->hasMany(JadwalSidang::class);
    }

    public function jadwalSidangs()
    {
        return $this->hasMany(JadwalSidang::class);
    }

    public function presensiHakims()
    {
        return $this->hasMany(PresensiHakim::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}

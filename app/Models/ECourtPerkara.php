<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ECourtPerkara extends Model
{
    use HasFactory, \App\Traits\Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'jadwal_sidang_online' => 'datetime',
            'tanggal_daftar' => 'date',
        ];
    }
}

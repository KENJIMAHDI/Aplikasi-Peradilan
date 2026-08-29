<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresensiHakim extends Model
{
    use HasFactory, \App\Traits\Auditable;

    protected $guarded = [];

    public function hakim(): BelongsTo
    {
        return $this->belongsTo(Hakim::class);
    }
}

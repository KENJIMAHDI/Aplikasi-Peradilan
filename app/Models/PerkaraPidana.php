<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PerkaraPidana extends Model
{
    use HasFactory, \App\Traits\Auditable;

    protected $guarded = [];
}

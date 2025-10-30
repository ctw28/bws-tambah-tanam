<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Komir extends Model
{
    use HasFactory;

    protected $fillable = [
        'sesi_id',
        'nama',
        'no_hp',
        'kode',
    ];

    public function sesi()
    {
        return $this->belongsTo(Sesi::class);
    }
}

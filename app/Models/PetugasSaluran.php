<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetugasSaluran extends Model
{
    protected $fillable = [
        'petugas_id',
        'saluran_id',
    ];

    public function petugas()
    {
        return $this->belongsTo(Petugas::class);
    }

    public function saluran()
    {
        return $this->belongsTo(Saluran::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengamat extends Model
{
    protected $fillable = [
        'sesi_id',
        'daerah_irigasi_id',
        'nama',
        'nomor_hp',
        'kode',
    ];

    public function sesi()
    {
        return $this->belongsTo(Sesi::class);
    }

    public function daerahIrigasi()
    {
        return $this->belongsTo(DaerahIrigasi::class);
    }
}

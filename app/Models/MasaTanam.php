<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasaTanam extends Model
{
    protected $table = 'masa_tanams';

    protected $fillable = [
        'daerah_irigasi_id',
        'tahun',
        'nama',
        'bulan_mulai',
        'bulan_selesai',
    ];

    public function daerahIrigasi()
    {
        return $this->belongsTo(DaerahIrigasi::class);
    }
}

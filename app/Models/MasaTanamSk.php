<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasaTanamSk extends Model
{
    protected $table = 'masa_tanam_sks';

    protected $fillable = [
        'daerah_irigasi_id',
        'sk_dari',
        'no_sk',
        'tahun_sk',
        'tanggal_terbit_sk',
    ];

    public function daerahIrigasi()
    {
        return $this->belongsTo(DaerahIrigasi::class);
    }
}

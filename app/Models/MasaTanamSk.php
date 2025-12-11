<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasaTanamSk extends Model
{
    protected $table = 'masa_tanam_sks';

    protected $fillable = [
        'daerah_irigasi_id',
        'nama_sk',
        'tahun_sk',
    ];

    public function daerahIrigasi()
    {
        return $this->belongsTo(DaerahIrigasi::class);
    }
}

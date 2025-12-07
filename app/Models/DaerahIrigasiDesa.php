<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaerahIrigasiDesa extends Model
{
    protected $table = 'daerah_irigasi_desas';

    protected $fillable = [
        'daerah_irigasi_kecamatan_id',
        'nama'
    ];

    // Relasi ke kecamatan
    public function kecamatan()
    {
        return $this->belongsTo(DaerahIrigasiKecamatan::class, 'daerah_irigasi_kecamatan_id');
    }
}

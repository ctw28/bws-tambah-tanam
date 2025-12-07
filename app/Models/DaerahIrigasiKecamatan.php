<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaerahIrigasiKecamatan extends Model
{
    protected $table = 'daerah_irigasi_kecamatans';

    protected $fillable = [
        'daerah_irigasi_id',
        'nama'
    ];

    // Relasi ke DI (parent)
    public function daerahIrigasi()
    {
        return $this->belongsTo(DaerahIrigasi::class, 'daerah_irigasi_id');
    }

    // Relasi ke desa
    public function desas()
    {
        return $this->hasMany(DaerahIrigasiDesa::class, 'daerah_irigasi_kecamatan_id');
    }
}

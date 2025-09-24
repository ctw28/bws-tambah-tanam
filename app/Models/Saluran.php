<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saluran extends Model
{
    protected $fillable = ['daerah_irigasi_id', 'nama'];

    public function daerahIrigasi()
    {
        return $this->belongsTo(DaerahIrigasi::class);
    }

    public function bangunan()
    {
        return $this->hasMany(Bangunan::class);
    }
    public function petugas()
    {
        return $this->belongsToMany(Petugas::class, 'petugas_salurans');
    }
    public function petugasAktif()
    {
        return $this->hasOne(Petugas::class)->where('is_aktif', true);
    }
}
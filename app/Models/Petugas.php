<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    protected $fillable = [
        'sesi_id',
        'nama',
        'kode',
        'hp',
        'is_aktif'
    ];

    public function sesi()
    {
        return $this->belongsTo(sesi::class);
    }
    public function salurans()
    {
        return $this->belongsToMany(Saluran::class, 'petugas_salurans');
    }
    public function formPengisian()
    {
        return $this->hasMany(FormPengisian::class);
    }
}
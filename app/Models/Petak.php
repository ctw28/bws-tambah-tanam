<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Petak extends Model
{
    protected $fillable = ['bangunan_id', 'nama', 'luas', 'gambar_skema'];

    public function bangunan()
    {
        return $this->belongsTo(Bangunan::class);
    }

    public function formPengisian()
    {
        return $this->hasMany(FormPengisian::class, 'petak_id');
    }
}

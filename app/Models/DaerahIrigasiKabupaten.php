<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaerahIrigasiKabupaten extends Model
{
    protected $fillable = ['daerah_irigasi_id', 'kabupaten_id'];

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }

    public function daerahIrigasi()
    {
        return $this->belongsTo(DaerahIrigasi::class);
    }
}
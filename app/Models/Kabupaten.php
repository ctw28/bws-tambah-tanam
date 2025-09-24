<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    protected $fillable = ['nama'];

    public function daerahIrigasis()
    {
        return $this->belongsToMany(DaerahIrigasi::class, 'daerah_irigasi_kabupatens');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPermasalahan extends Model
{
    protected $fillable = [
        'nama',
    ];

    // Relasi: 1 kabupaten punya banyak daerah irigasi
    public function formPermasalahan()
    {
        return $this->hasMany(FormPermasalahan::class);
    }
}
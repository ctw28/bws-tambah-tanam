<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasaTanam extends Model
{
    protected $table = 'masa_tanams';

    protected $fillable = [
        'tahun',
        'nama',
        'bulan_mulai',
        'bulan_selesai',
    ];
}

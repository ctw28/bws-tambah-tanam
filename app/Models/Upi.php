<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Upi extends Model
{
    use HasFactory;

    protected $fillable = [
        'sesi_id',
        'nama',
        'no_hp',
        'kode',
    ];

    public function sesi()
    {
        return $this->belongsTo(Sesi::class);
    }

    public function daerahIrigasis()
    {
        return $this->belongsToMany(DaerahIrigasi::class, 'daerah_irigasi_upis')
            ->withTimestamps();
    }
}

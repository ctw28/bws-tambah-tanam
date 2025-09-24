<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bangunan extends Model
{
    protected $fillable = ['saluran_id', 'nama'];

    public function saluran()
    {
        return $this->belongsTo(Saluran::class);
    }

    public function petak()
    {
        return $this->hasMany(Petak::class);
    }
}
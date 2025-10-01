<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaerahIrigasi extends Model
{
    protected $fillable = ['nama', 'parent_id'];
    public function parent()
    {
        return $this->belongsTo(DaerahIrigasi::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(DaerahIrigasi::class, 'parent_id');
    }
    public function kabupatens()
    {
        return $this->belongsToMany(Kabupaten::class, 'daerah_irigasi_kabupatens');
    }

    public function saluran()
    {
        return $this->hasMany(Saluran::class);
    }
    public function upis()
    {
        return $this->belongsToMany(Upi::class, 'daerah_irigasi_upis')
            ->withTimestamps();
    }
}
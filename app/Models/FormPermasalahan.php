<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormPermasalahan extends Model
{
    protected $fillable = [
        'form_pengisian_id',
        'master_permasalahan_id',
        'status',
        'keterangan',
    ];

    // Relasi: 1 kabupaten punya banyak daerah irigasi
    public function formPengisian()
    {
        return $this->hasMany(FormPengisian::class);
    }
    public function masterPermasalahan()
    {
        return $this->belongsTo(MasterPermasalahan::class);
    }
}
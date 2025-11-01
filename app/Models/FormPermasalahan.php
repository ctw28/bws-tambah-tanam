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
        'foto_permasalahan',
    ];

    // Relasi: 1 kabupaten punya banyak daerah irigasi
    public function formPengisian()
    {
        // Karena form_permasalahans memiliki kolom form_pengisian_id
        return $this->belongsTo(FormPengisian::class, 'form_pengisian_id');
    }

    public function masterPermasalahan()
    {
        return $this->belongsTo(MasterPermasalahan::class);
    }
}

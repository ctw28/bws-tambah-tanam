<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormValidasi extends Model
{
    protected $fillable = [
        'form_pengisian_id',
        'pengamat_id',
        'pengamat_valid',
        'upi_valid'
    ];

    public function formPengisian()
    {
        return $this->belongsTo(FormPengisian::class);
    }

    public function pengamat()
    {
        return $this->belongsTo(Pengamat::class);
    }
}
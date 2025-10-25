<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormPengisianP3a extends Model
{
    protected $table = 'form_pengisian_p3as';

    protected $fillable = [
        'form_pengisian_id',
        'p3a_id',
    ];

    /**
     * Relasi ke model FormPengisian
     */
    public function formPengisian()
    {
        return $this->belongsTo(FormPengisian::class);
    }

    /**
     * Relasi ke model P3a
     */
    public function p3a()
    {
        return $this->belongsTo(P3a::class);
    }
}

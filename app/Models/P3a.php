<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class P3a extends Model
{
    protected $table = 'p3as';

    protected $fillable = [
        'nama',
        'keterangan',
    ];

    /**
     * Relasi ke FormPengisianP3a (many-to-many via pivot)
     */
    public function formPengisianP3as()
    {
        return $this->hasMany(FormPengisianP3a::class, 'p3a_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormPengisian extends Model
{
    use HasFactory;

    protected $fillable = [
        'petugas_id',
        'tanggal_pantau',
        'kabupaten_id',
        'daerah_irigasi_id',
        'saluran_id',
        'bangunan_id',
        'petak_id',
        'kecamatan',
        'desa',
        'koordinat',
        'debit_air',
        'masa_tanam',
        'luas_padi',
        'luas_palawija',
        'luas_lainnya',
        'foto_pemantauan',
        'sesi_id'
    ];

    // 🔗 Relasi ke detail permasalahan
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }
    public function permasalahan()
    {
        return $this->hasMany(FormPermasalahan::class);
    }
    public function formPengisianP3a()
    {
        return $this->hasMany(FormPengisianP3a::class);
    }

    public function validasi()
    {
        return $this->hasOne(FormValidasi::class);
    }
    // 🔗 Relasi ke master daerah irigasi
    public function daerahIrigasi()
    {
        return $this->belongsTo(DaerahIrigasi::class);
    }
    // 🔗 Relasi ke master daerah petugas
    public function petugas()
    {
        return $this->belongsTo(Petugas::class);
    }
    // 🔗 Relasi ke master saluran
    public function saluran()
    {
        return $this->belongsTo(Saluran::class);
    }

    // 🔗 Relasi ke master bangunan
    public function bangunan()
    {
        return $this->belongsTo(Bangunan::class);
    }
    // 🔗 Relasi ke master petak
    public function petak()
    {
        return $this->belongsTo(Petak::class);
    }
    public function sesi() {}
}

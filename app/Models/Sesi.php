<?php
// app/Models/Sesi.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sesi extends Model
{
    protected $fillable = [
        'nama',
        'is_aktif'
    ];
}

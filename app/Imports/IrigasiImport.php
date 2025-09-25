<?php

namespace App\Imports;

use App\Models\DaerahIrigasi;
use App\Models\Saluran;
use App\Models\Bangunan;
use App\Models\Kabupaten;
use App\Models\Petak;
use App\Models\Petugas;
use App\Models\PetugasSaluran;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IrigasiImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Cari kabupaten
        $kabupaten = Kabupaten::firstOrCreate(['nama' => $row['kabupaten']]);

        // 2. Daerah irigasi
        $daerah = DaerahIrigasi::firstOrCreate([
            'nama' => $row['nama_daerah_irigasi']
        ]);

        // Hubungkan ke kabupaten via pivot
        $daerah->kabupatens()->syncWithoutDetaching([$kabupaten->id]);

        // 3. Saluran
        $saluran = Saluran::firstOrCreate([
            'daerah_irigasi_id' => $daerah->id,
            'nama' => $row['nama_saluran']
        ]);

        // 4. Bangunan
        $bangunan = Bangunan::firstOrCreate([
            'saluran_id' => $saluran->id,
            'nama' => $row['nama_bangunan_bagi_sadap'] // hati-hati nama kolom heading
        ]);

        // 5. Petak
        Petak::updateOrCreate(
            [
                'bangunan_id' => $bangunan->id,
                'nama' => $row['kode_petak'],
            ],
            [
                'luas' => $row['luas_petak_skema'],
                'gambar_skema' => '',
            ]
        );

        // Petugas
        $namaPetugas = trim(explode('/', $row['nama_wilayah_juru'])[1] ?? $row['nama_wilayah_juru']);

        $petugas = Petugas::firstOrCreate([
            'nama' => $namaPetugas,
        ], [
            'sesi_id' => 1,
            'hp' => $row['hp'],
            'kode' => Str::slug($namaPetugas) . rand(100, 999),
            'is_aktif' => true
        ]);

        // Hubungkan petugas ke saluran
        PetugasSaluran::firstOrCreate([
            'petugas_id' => $petugas->id,
            'saluran_id' => $saluran->id
        ]);

        return null; // kita tidak return Petak, karena banyak relasi
    }
}

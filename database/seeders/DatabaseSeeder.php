<?php

namespace Database\Seeders;

use App\Models\DaerahIrigasi;
use App\Models\Kabupaten;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        DB::beginTransaction();

        try {
            $data = [
                'Bombana',
                'Buton',
                'Buton Selatan',
                'Buton Tengah',
                'Buton Utara',
                'Kolaka',
                'Kolaka Timur',
                'Kolaka Utara',
                'Konawe', //9
                'Konawe Kepulauan',
                'Konawe Selatan',
                'Konawe Utara', //12
                'Muna',
                'Muna Barat',
                'Wakatobi',
                'Kota Bau-Bau',
                'Kota Kendari', //17
            ];

            foreach ($data as $nama) {
                Kabupaten::firstOrCreate(['nama' => $nama]);
            }

            Role::updateOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
            Role::updateOrCreate(['name' => 'koordinator'], ['label' => 'Koordinator']);
            Role::updateOrCreate(['name' => 'juru'], ['label' => 'Juru']);
            Role::updateOrCreate(['name' => 'pengamat'], ['label' => 'Pengamat Lapangan']);
            Role::updateOrCreate(['name' => 'upi'], ['label' => 'Unit Pengelola Irigasi']);
            // Buat user
            $user = User::create([
                'name' => 'Ellen Ambar Winarsih',
                'email' => 'ellen@mail.com',
                'password' => bcrypt('ell3n*123#'), // default password
                'role_id' => 2
            ]);
            // Ambil beberapa kabupaten contoh
            $kabupatenIds = [9, 12, 17]; // ambil 2 kabupaten pertama

            // Attach ke user
            $user->kabupatens()->attach($kabupatenIds);
            // ============================
            // SESI
            // ============================
            $sesiId = DB::table('sesis')->insertGetId([
                'nama' => 'Tahun 2025',
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            // ============================
            // buat DI Walay
            // $di = DaerahIrigasi::firstOrCreate(['nama' => 'Walay']);

            // hubungkan DI dengan kabupaten konawe
            // $di->kabupatens()->syncWithoutDetaching([9]);
            // ============================
            // DAERAH IRIGASI
            // ============================
            // $diId = DB::table('daerah_irigasis')->insertGetId([
            //     'nama' => 'DI Walay',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);
            //     'kabupaten_id' => $kabupatenId,


            // ============================
            // SALURAN
            // ============================
            // $saluranId = DB::table('salurans')->insertGetId([
            //     'daerah_irigasi_id' => 1,
            //     'nama' => 'Saluran Lahumbuti',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);
            // ============================
            // PETUGAS
            // ============================
            // $petugasId = DB::table('petugas')->insertGetId([
            //     'sesi_id' => $sesiId,
            //     'nama' => 'Mustafa',
            //     'kode' => "ABCDE", // contoh kode
            //     // 'kode' => Hash::make('123'), // contoh kode
            //     'hp' => "85241800852",
            //     'is_aktif' => true,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);

            // $petugasSaluran = DB::table('petugas_salurans')->insertGetId([
            //     'petugas_id' => $petugasId,
            //     'saluran_id' => $saluranId,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);
            // ============================
            // BANGUNAN
            // ============================
            // $bangunanId = DB::table('bangunans')->insertGetId([
            //     'saluran_id' => $saluranId,
            //     'nama' => 'BLH.1',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);

            // ============================
            // PETAK
            // ============================
            // DB::table('petaks')->insertGetId([
            //     'bangunan_id' => $bangunanId,
            //     'nama' => 'LH 1 Kr',
            //     'luas' => 62.12,
            //     'gambar_skema' => "",
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);

            // ============================
            // SALURAN
            // ============================
            // $saluranId = DB::table('salurans')->insertGetId([
            //     'daerah_irigasi_id' => 1,
            //     'nama' => 'Saluran Sekunder Ambepe',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);
            // $petugasSaluran = DB::table('petugas_salurans')->insertGetId([
            //     'petugas_id' => $petugasId,
            //     'saluran_id' => $saluranId,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);
            // ============================
            // BANGUNAN
            // ============================
            // $bangunanId = DB::table('bangunans')->insertGetId([
            //     'saluran_id' => $saluranId,
            //     'nama' => 'BAM.1',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);

            // ============================
            // PETAK
            // ============================
            // DB::table('petaks')->insertGetId([
            //     'bangunan_id' => $bangunanId,
            //     'nama' => 'AM 1 Kr',
            //     'luas' => 63.94,
            //     'gambar_skema' => "",
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);
            // ============================
            // MASTER PEMANTAUAN PERMASALAHAN
            // ============================
            $permasalahan = [
                'Ketersediaan Air di Sekunder',
                'Ketersediaan Air di Tersier',
                'Ketersediaan Lahan',
                'Penyediaan Saprodi',
                'Faktor Sosial',
                'Lainnya',
            ];
            foreach ($permasalahan as $p) {
                DB::table('master_permasalahans')->insert([
                    'nama' => $p,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            // Tambah UPI (jika ada untuk DI ini)
            // $upiId = DB::table('upis')->insertGetId([
            //     'sesi_id' => 1,
            //     'nama' => 'UPI Kendari',
            //     'no_hp' => '085241800852',
            //     'kode' => '888', // kode login pengamat
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);
            // // Tambah UPI (jika ada untuk DI ini)
            // $upiId = DB::table('daerah_irigasi_upis')->insertGetId([
            //     'upi_id' => 1,
            //     'daerah_irigasi_id' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);
            // // Tambah Pengamat
            // $pengamatId = DB::table('pengamats')->insertGetId([
            //     'sesi_id' => 1,
            //     'daerah_irigasi_id' => 1,
            //     'nama' => 'Pengamat Ali',
            //     'nomor_hp' => '08524180052',
            //     'kode' => '1234', // kode login pengamat
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);
            // // ============================
            // // FORM PENGISIAN (contoh transaksi)
            // // ============================
            // $formId = DB::table('form_pengisians')->insertGetId([
            //     'tanggal_pantau' => now()->toDateString(),
            //     'petugas_id' => $petugasId,
            //     'daerah_irigasi_id' => $diId,
            //     'saluran_id' => $saluranId,
            //     'petak_id' => $petakId,
            //     'kecamatan' => 'Kec. Unaaha',
            //     'desa' => 'Desa Mekar Jaya',
            //     'koordinat' => '-3.9901, 122.5123',
            //     'debit_air' => 25.5,
            //     'masa_tanam' => 'I',
            //     'luas_padi' => 10,
            //     'luas_palawija' => 2,
            //     'luas_lainnya' => 1,
            //     'foto_pemantauan' => 'pantau.jpeg',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);

            // // ============================
            // // DETAIL PERMASALAHAN (ambil salah satu)
            // // ============================
            // DB::table('form_permasalahans')->insert([
            //     'form_pengisian_id' => $formId,
            //     'master_permasalahan_id' => 1, // Kerusakan Saluran
            //     'status' => true,
            //     'keterangan' => 'Terdapat retakan kecil',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);
            DB::commit();
            $this->command->info('Seeder berhasil');
        } catch (Exception $e) {
            // kalau ada error → rollback
            DB::rollBack();
            $this->command->error('Seeder gagal: ' . $e->getMessage());
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {



        // DAERAH IRIGASI
        Schema::create('kabupatens', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });
        // USER KABUPATEN
        Schema::create('user_kabupatens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('kabupaten_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
        // DAERAH IRIGASI
        Schema::create('daerah_irigasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        // KABUPATEN
        Schema::create('daerah_irigasi_kabupatens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daerah_irigasi_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kabupaten_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // SALURAN
        Schema::create('salurans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daerah_irigasi_id')->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->timestamps();
        });

        // BANGUNAN
        Schema::create('bangunans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saluran_id')->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->timestamps();
        });

        // PETAK
        Schema::create('petaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bangunan_id')->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->decimal('luas', 10, 2); // hektar
            $table->string('gambar_skema');
            $table->timestamps();
        });
        // SESI PENGISIAN
        Schema::create('sesis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
        // tabel upis
        Schema::create('upis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->string('no_hp')->nullable();
            $table->string('kode')->unique();
            $table->timestamps();
        });
        // pivot upi - daerah_irigasi
        Schema::create('daerah_irigasi_upis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upi_id')->constrained()->cascadeOnDelete();
            $table->foreignId('daerah_irigasi_id')->constrained()->cascadeOnDelete();
            $table->unique(['upi_id', 'daerah_irigasi_id']);
            $table->timestamps();
        });

        // PETUGAS
        Schema::create('petugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained();
            $table->string('nama');
            $table->string('hp')->nullable(); // nomor HP petugas

            $table->string('kode')->unique(); // wajib unik
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });


        // Pivot petugas_saluran
        Schema::create('petugas_salurans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petugas_id')->constrained('petugas')->cascadeOnDelete();
            $table->foreignId('saluran_id')->constrained('salurans')->cascadeOnDelete();
            $table->timestamps();

            // enforce 1 saluran hanya bisa 1 petugas
            $table->unique('saluran_id');
        });

        // MASTER PEMANTAUAN PERMASALAHAN
        Schema::create('master_permasalahans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        // FORM PENGISIAN (TRANSAKSI)
        Schema::create('form_pengisians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained();
            $table->date('tanggal_pantau');
            $table->foreignId('daerah_irigasi_id')->constrained();
            $table->foreignId('kabupaten_id')->constrained();
            $table->foreignId('saluran_id')->constrained();
            $table->foreignId('bangunan_id')->constrained();
            $table->foreignId('petak_id')->constrained();
            $table->foreignId('petugas_id')->constrained();
            $table->string('kecamatan');
            $table->string('desa');
            $table->string('koordinat');
            $table->decimal('debit_air', 10, 2);
            $table->enum('masa_tanam', ['I', 'II', 'III']);
            $table->decimal('luas_padi', 10, 2)->default(0);
            $table->decimal('luas_palawija', 10, 2)->default(0);
            $table->decimal('luas_lainnya', 10, 2)->default(0);
            $table->string('foto_pemantauan');
            $table->timestamps();
        });

        // DETAIL PERMASALAHAN YANG DITEMUKAN
        Schema::create('form_permasalahans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_pengisian_id')->constrained()->cascadeOnDelete();
            $table->foreignId('master_permasalahan_id')->constrained()->cascadeOnDelete();
            $table->boolean('status')->default(false);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        // PENGAMAT
        Schema::create('pengamats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained()->cascadeOnDelete();
            $table->foreignId('daerah_irigasi_id')->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->string('nomor_hp')->nullable();
            $table->string('kode')->unique(); // kalau mau unik global
            $table->timestamps();

            // kombinasi unik sesi + DI
            $table->unique(['sesi_id', 'daerah_irigasi_id']);
        });

        // UNTUK VALIDASI
        Schema::create('form_validasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_pengisian_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pengamat_id')->nullable()->constrained()->cascadeOnDelete();
            $table->boolean('pengamat_valid')->default(false);
            $table->boolean('upi_valid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('form_permasalahans');
        Schema::dropIfExists('form_pengisians');
        Schema::dropIfExists('master_permasalahans');
        Schema::dropIfExists('petaks');
        Schema::dropIfExists('bangunans');
        Schema::dropIfExists('salurans');
        Schema::dropIfExists('daerah_irigasis');
        Schema::dropIfExists('kabupatens');
        Schema::dropIfExists('sesis');
    }
};

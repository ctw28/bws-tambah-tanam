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
        Schema::create('masa_tanams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daerah_irigasi_id')
                ->constrained('daerah_irigasis')
                ->cascadeOnDelete();

            $table->integer('tahun');                     // contoh: 2025
            $table->string('nama');                       // contoh: Masa Tanam I
            $table->unsignedTinyInteger('bulan_mulai');   // 1–12
            $table->unsignedTinyInteger('bulan_selesai'); // 1–12
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masa_tanams');
    }
};

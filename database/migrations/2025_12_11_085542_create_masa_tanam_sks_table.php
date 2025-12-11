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
        Schema::create('masa_tanam_sks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('daerah_irigasi_id');
            $table->string('nama_sk');
            $table->string('tahun_sk', 4);
            $table->timestamps();

            // Jika ingin foreign key
            $table->foreign('daerah_irigasi_id')
                ->references('id')
                ->on('daerah_irigasis')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masa_tanam_sks');
    }
};

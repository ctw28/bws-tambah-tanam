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
        Schema::create('form_pengisian_p3as', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_pengisian_id')->constrained()->cascadeOnDelete();
            $table->foreignId('p3a_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_pengisian_p3as');
    }
};

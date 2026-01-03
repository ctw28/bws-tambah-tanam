<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('p3as', function (Blueprint $table) {
            $table->foreignId('daerah_irigasi_id')
                ->nullable()
                ->after('keterangan')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('p3as', function (Blueprint $table) {
            $table->dropForeign(['daerah_irigasi_id']);
            $table->dropColumn('daerah_irigasi_id');
        });
    }
};

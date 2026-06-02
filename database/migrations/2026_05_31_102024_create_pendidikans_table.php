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
        Schema::create('pendidikans', function (Blueprint $table) {
            $table->id();

             // Relasi ke pegawai
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('jenjang');
            $table->string('gelar')->nullable();        // kosong untuk SD-SMA
            $table->string('bidang_studi')->nullable(); // kosong untuk SD/SMP
            $table->string('nama_institusi');
            $table->year('tahun_lulus')->nullable();



            $table->string('file_ijazah')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendidikans');
    }
};

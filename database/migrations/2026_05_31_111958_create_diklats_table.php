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
        Schema::create('diklats', function (Blueprint $table) {
            $table->id();

            // Relasi ke pegawai
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('nama_diklat');
            $table->string('jenis_diklat')->nullable();
            $table->string('penyelenggara')->nullable();

            $table->year('tahun')->nullable();

            $table->string('status')->nullable();

            // Optional
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('nomor_sertifikat')->nullable();
            $table->date('tanggal_sertifikat')->nullable();
            $table->string('file_sertifikat')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diklats');
    }
};

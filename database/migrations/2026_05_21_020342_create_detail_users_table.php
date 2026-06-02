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
        Schema::create('detail_users', function (Blueprint $table) {
            $table->id();

            // relasi ke tabel users
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // profil
            $table->string('nip')->nullable();
            $table->string('nama')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('foto')->nullable();

            // alamat & kontak
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();

            // kependudukan
            $table->string('nik')->nullable();
            $table->string('agama')->nullable();
            $table->string('kewarganegaraan')->nullable();


            // kepegawaian
            $table->string('nomor_sk')->nullable();
            $table->date('tmt_sk')->nullable();
            $table->string('pangkat_golongan')->nullable();
            $table->date('tanggal_sk')->nullable();
            $table->integer('masa_kerja_tahun')->nullable();
            $table->integer('masa_kerja_bulan')->nullable();
            $table->string('status_kepegawaian')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_users');
    }
};
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
        Schema::create('jabatan_fungsionals', function (Blueprint $table) {
            $table->id();

            // relasi user
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // data jabatan
            $table->string('jabatan_fungsional');

            // data SK
            $table->string('nomor_sk');
            $table->date('terhitung_mulai_tanggal');

            // status pegawai
            $table->enum('status_pegawai', [
                'PNS',
                'PPPK',
                'Non ASN'
            ])->nullable();

            // upload file SK
            $table->string('file_sk')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatan_fungsionals');
    }
};
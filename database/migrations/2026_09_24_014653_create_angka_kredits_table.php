<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('angka_kredits', function (Blueprint $table) {
            $table->id();

            // Relasi ke pegawai
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('no_sk');
            $table->date('tgl_sk');

            // $table->unsignedTinyInteger('bulan_mulai');
            // $table->year('tahun_mulai');
            $table->date('tgl_mulai');

            // $table->unsignedTinyInteger('bulan_selesai');
            // $table->year('tahun_selesai');
            $table->date('tgl_selesai');

            $table->decimal('kredit_utama')->nullable();
            $table->decimal('kredit_penunjang')->nullable();
            $table->decimal('total_kredit')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('angka_kredits');
    }
};
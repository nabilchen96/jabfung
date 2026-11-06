<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('bulan', 7);
            $table->unsignedTinyInteger('total_terlambat')->default(0);
            $table->unsignedTinyInteger('total_cuti_izin_dinas')->default(0);
            $table->unsignedTinyInteger('total_pulang_cepat')->default(0);
            $table->unsignedTinyInteger('total_kehadiran')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'bulan']);
            $table->index('bulan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};

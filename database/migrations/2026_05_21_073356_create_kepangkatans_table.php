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
        Schema::create('kepangkatans', function (Blueprint $table) {
            $table->id();

            // relasi user
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->string('golongan')->nullable();
            $table->string('pangkat')->nullable();
            $table->string('nomor_sk')->nullable();
            $table->string('terhitung_mulai_tanggal')->nullable();
            $table->string('file_sk')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepangkatans');
    }
};

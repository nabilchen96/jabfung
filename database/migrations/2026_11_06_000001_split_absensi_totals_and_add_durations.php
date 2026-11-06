<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keep the original units and combined totals; their new values cannot be inferred.
        Schema::table('absensis', function (Blueprint $table) {
            $table->renameColumn('total_terlambat', 'lama_terlambat_hari');
            $table->renameColumn('total_pulang_cepat', 'lama_pulang_cepat_hari');
            $table->renameColumn('total_cuti_izin_dinas', 'lama_cuti_izin_dinas');
        });
        Schema::table('absensis', function (Blueprint $table) {
            $table->string('total_terlambat', 9)->default('00:00:00');
            $table->string('total_pulang_cepat', 9)->default('00:00:00');
            $table->unsignedTinyInteger('total_izin')->default(0);
            $table->unsignedTinyInteger('total_cuti')->default(0);
            $table->unsignedTinyInteger('total_dinas')->default(0);
            $table->boolean('perlu_penyesuaian')->default(false);
        });
        DB::table('absensis')->where(function ($query) {
            $query->where('lama_terlambat_hari', '>', 0)
                ->orWhere('lama_pulang_cepat_hari', '>', 0)
                ->orWhere('lama_cuti_izin_dinas', '>', 0);
        })->update(['perlu_penyesuaian' => true]);
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['total_terlambat', 'total_pulang_cepat', 'total_izin', 'total_cuti', 'total_dinas', 'perlu_penyesuaian']);
        });
        Schema::table('absensis', function (Blueprint $table) {
            $table->renameColumn('lama_terlambat_hari', 'total_terlambat');
            $table->renameColumn('lama_pulang_cepat_hari', 'total_pulang_cepat');
            $table->renameColumn('lama_cuti_izin_dinas', 'total_cuti_izin_dinas');
        });
    }
};

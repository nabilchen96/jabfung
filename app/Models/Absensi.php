<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    protected $fillable = [
        'user_id', 'bulan', 'total_terlambat', 'total_izin', 'total_cuti', 'total_dinas',
        'total_pulang_cepat', 'total_kehadiran',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'total_izin' => 'integer',
        'total_cuti' => 'integer',
        'total_dinas' => 'integer',
        'perlu_penyesuaian' => 'boolean',
        'total_kehadiran' => 'integer',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

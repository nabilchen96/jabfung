<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    protected $fillable = [
        'user_id', 'bulan', 'total_terlambat', 'total_cuti_izin_dinas',
        'total_pulang_cepat', 'total_kehadiran',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'total_terlambat' => 'integer',
        'total_cuti_izin_dinas' => 'integer',
        'total_pulang_cepat' => 'integer',
        'total_kehadiran' => 'integer',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

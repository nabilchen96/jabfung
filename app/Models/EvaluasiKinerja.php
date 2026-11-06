<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluasiKinerja extends Model
{
    public const NILAI = ['Sangat Baik', 'Baik', 'Cukup Baik'];

    protected $fillable = ['user_id', 'nilai', 'keterangan'];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

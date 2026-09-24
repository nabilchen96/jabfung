<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AngkaKredit extends Model
{
    use HasFactory;

    protected $table = 'angka_kredits';

    protected $fillable = [
        'user_id',
        'no_sk',
        'tgl_sk',
        // 'bulan_mulai',
        // 'tahun_mulai',
        // 'bulan_selesai',
        // 'tahun_selesai',
        'tgl_mulai',
        'tgl_selesai',
        'kredit_utama',
        'kredit_penunjang',
        'total_kredit',
    ];

    // protected $casts = [
    //     'tgl_sk' => 'date',
    //     'kredit_utama' => 'decimal:3',
    //     'kredit_penunjang' => 'decimal:3',
    //     'total_kredit' => 'decimal:3',
    // ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $admin = $request->user()->role === 'Admin';
        $query = function (string $table) use ($admin, $request) {
            $builder = DB::table($table);
            if (!$admin) {
                $builder->where($table === 'users' ? 'id' : 'user_id', $request->user()->id);
            }

            return $builder;
        };

        $cards = [];
        foreach ([
            'users' => ['Pengguna', 'user'],
            'jabatan_fungsionals' => ['Riwayat Jabatan', 'jabatan-fungsional'],
            'kepangkatans' => ['Riwayat Kepangkatan', 'kepangkatan'],
            'pendidikans' => ['Riwayat Pendidikan', 'pendidikan'],
            'diklats' => ['Riwayat Diklat', 'diklat'],
            'angka_kredits' => ['Catatan Angka Kredit', 'angka-kredit'],
        ] as $table => [$label, $path]) {
            $cards[] = ['label' => $label, 'count' => $query($table)->count(), 'url' => url($path)];
        }

        $education = $query('pendidikans')
            ->select('jenjang')->selectRaw('COUNT(*) as jumlah')
            ->groupBy('jenjang')->orderBy('jenjang')->get();
        $years = range(now()->year - 4, now()->year);
        $training = $query('diklats')->whereBetween('tahun', [$years[0], $years[4]])
            ->select('tahun')->selectRaw('COUNT(*) as jumlah')->groupBy('tahun')->pluck('jumlah', 'tahun');
        $credits = $query('angka_kredits')
            ->whereBetween('tgl_mulai', [$years[0].'-01-01', $years[4].'-12-31'])
            ->select('tgl_mulai', 'total_kredit')->get()
            ->groupBy(fn ($row) => (int) substr($row->tgl_mulai, 0, 4))
            ->map(fn ($rows) => (float) $rows->sum('total_kredit'));

        return view('backend.dashboard.index', [
            'cards' => $cards,
            'admin' => $admin,
            'totalCredits' => (float) $query('angka_kredits')->sum('total_kredit'),
            'hasCreditRecords' => $credits->isNotEmpty(),
            'charts' => [
                'years' => $years,
                'education' => [
                    'labels' => $education->map(fn ($row) => $row->jenjang ?: 'Belum diisi')->values(),
                    'values' => $education->pluck('jumlah')->map(fn ($count) => (int) $count)->values(),
                ],
                'training' => array_map(fn ($year) => (int) ($training[$year] ?? 0), $years),
                'credits' => array_map(fn ($year) => (float) ($credits[$year] ?? 0), $years),
            ],
        ]);
    }
}

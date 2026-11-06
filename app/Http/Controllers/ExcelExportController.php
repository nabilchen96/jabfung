<?php

namespace App\Http\Controllers;

use App\Support\ExcelDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExcelExportController extends Controller
{
    private const EXPORTS = [
        'absensi' => ['absensis', [
            'bulan' => 'Bulan', 'total_terlambat' => 'Total Terlambat (Hari)',
            'total_cuti_izin_dinas' => 'Total Cuti/Izin/Dinas (Hari)',
            'total_pulang_cepat' => 'Total Pulang Cepat (Hari)', 'total_kehadiran' => 'Total Kehadiran (Hari)',
        ]],
        'user' => ['users', ['name' => 'Nama', 'email' => 'Email', 'role' => 'Peran']],
        'detail-user' => ['detail_users', [
            'nip' => 'NIP', 'nama' => 'Nama Lengkap', 'jenis_kelamin' => 'Jenis Kelamin',
            'tempat_lahir' => 'Tempat Lahir', 'tanggal_lahir' => 'Tanggal Lahir',
            'email' => 'Email', 'alamat' => 'Alamat', 'nik' => 'NIK', 'agama' => 'Agama',
            'kewarganegaraan' => 'Kewarganegaraan', 'nomor_sk' => 'Nomor SK',
            'tmt_sk' => 'TMT SK', 'pangkat_golongan' => 'Pangkat/Golongan',
            'tanggal_sk' => 'Tanggal SK', 'masa_kerja_tahun' => 'Masa Kerja (Tahun)',
            'masa_kerja_bulan' => 'Masa Kerja (Bulan)', 'status_kepegawaian' => 'Status Kepegawaian',
        ]],
        'jabatan-fungsional' => ['jabatan_fungsionals', [
            'jabatan_fungsional' => 'Jabatan Fungsional', 'nomor_sk' => 'Nomor SK',
            'terhitung_mulai_tanggal' => 'TMT', 'status_pegawai' => 'Status Pegawai',
        ]],
        'kepangkatan' => ['kepangkatans', [
            'golongan' => 'Golongan', 'pangkat' => 'Pangkat', 'nomor_sk' => 'Nomor SK',
            'terhitung_mulai_tanggal' => 'TMT',
        ]],
        'angka-kredit' => ['angka_kredits', [
            'no_sk' => 'Nomor SK', 'tgl_sk' => 'Tanggal SK', 'tgl_mulai' => 'Tanggal Mulai',
            'tgl_selesai' => 'Tanggal Selesai', 'kredit_utama' => 'Kredit Utama',
            'kredit_penunjang' => 'Kredit Penunjang', 'total_kredit' => 'Total Kredit',
        ]],
        'pendidikan' => ['pendidikans', [
            'jenjang' => 'Jenjang', 'gelar' => 'Gelar', 'bidang_studi' => 'Bidang Studi',
            'nama_institusi' => 'Nama Institusi', 'tahun_lulus' => 'Tahun Lulus',
        ]],
        'diklat' => ['diklats', [
            'nama_diklat' => 'Nama Diklat', 'jenis_diklat' => 'Jenis Diklat',
            'penyelenggara' => 'Penyelenggara', 'tahun' => 'Tahun', 'status' => 'Status',
            'tanggal_mulai' => 'Tanggal Mulai', 'tanggal_selesai' => 'Tanggal Selesai',
            'nomor_sertifikat' => 'Nomor Sertifikat', 'tanggal_sertifikat' => 'Tanggal Sertifikat',
        ]],
    ];

    public function __invoke(Request $request, string $page, ExcelDownload $excel)
    {
        abort_unless($page === 'dashboard' || isset(self::EXPORTS[$page]), 404);
        $admin = $request->user()->role === 'Admin';
        $userId = $request->user()->id;

        if ($page === 'dashboard') {
            $rows = [];
            foreach (self::EXPORTS as $key => [$table, $fields]) {
                $query = DB::table($table);
                if (!$admin) {
                    $query->where($table === 'users' ? 'id' : 'user_id', $userId);
                }
                $rows[] = [ucwords(str_replace('-', ' ', $key)), $query->count()];
            }

            return $excel->download('dashboard.xlsx', ['Kategori', 'Jumlah Data'], $rows);
        }

        [$table, $fields] = self::EXPORTS[$page];
        $query = DB::table($table);
        if ($table === 'users') {
            $query->select(array_keys($fields));
            if (!$admin) {
                $query->where('id', $userId);
            }
        } else {
            $query->leftJoin('users', 'users.id', '=', "$table.user_id")
                ->select('users.name as nama_pengguna');
            foreach (array_keys($fields) as $field) {
                $query->addSelect("$table.$field");
            }
            if (!$admin) {
                $query->where("$table.user_id", $userId);
            }
        }
        if ($page === 'absensi') {
            $validated = $request->validate(['bulan' => 'required|date_format:Y-m']);
            $query->where('absensis.bulan', $validated['bulan']);
        }
        if ($page === 'detail-user') {
            $validated = $request->validate(['id' => 'required|integer|exists:users,id']);
            abort_unless($admin || (int) $validated['id'] === $userId, 403);
            $query->where('detail_users.user_id', $validated['id']);
        }
        $headings = array_values($fields);
        if ($table !== 'users') {
            array_unshift($headings, 'Nama Pengguna');
        }
        $rows = $query->orderBy("$table.id")->cursor()->map(fn ($row) => array_values((array) $row));

        return $excel->download($page.'.xlsx', $headings, $rows);
    }
}

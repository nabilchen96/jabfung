<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AngkaKredit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AngkaKreditController extends Controller
{
    public function index()
    {
        return view('backend.angka_kredit.index');
    }


    /**
     * DATA DATATABLE
     */
    public function data(Request $request)
    {
        $query = DB::table('angka_kredits')
            ->leftJoin('users', 'users.id', '=', 'angka_kredits.user_id')
            ->select(
                'users.name',
                'angka_kredits.*'
            );

        // Jika bukan Admin, hanya melihat data sendiri
        if (Auth::user()->role != 'Admin') {
            $query->where('angka_kredits.user_id', Auth::id());
        }

        $data = $query
            ->orderBy('tgl_mulai', 'desc')
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }


    /**
     * STORE
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'no_sk' => 'required|string|max:255',
            'tgl_sk' => 'required|date',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date',

            // 'bulan_mulai' => 'required|integer|min:1|max:12',
            // 'tahun_mulai' => 'required|integer|min:1900|max:2100',

            // 'bulan_selesai' => 'required|integer|min:1|max:12',
            // 'tahun_selesai' => 'required|integer|min:1900|max:2100',

            'kredit_utama' => 'nullable|numeric|min:0',
            'kredit_penunjang' => 'nullable|numeric|min:0',
            'total_kredit' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responCode' => 0,
                'respon' => $validator->errors()
            ]);
        }

        // Non Admin hanya boleh menyimpan untuk dirinya sendiri
        $userId = Auth::user()->role == 'Admin'
            ? $request->user_id
            : Auth::id();

        AngkaKredit::create([
            'user_id' => $userId,
            'no_sk' => $request->no_sk,
            'tgl_sk' => $request->tgl_sk,

            // 'bulan_mulai' => $request->bulan_mulai,
            // 'tahun_mulai' => $request->tahun_mulai,

            // 'bulan_selesai' => $request->bulan_selesai,
            // 'tahun_selesai' => $request->tahun_selesai,

            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,

            'kredit_utama' => $request->kredit_utama,
            'kredit_penunjang' => $request->kredit_penunjang,
            'total_kredit' => $request->total_kredit,
        ]);

        return response()->json([
            'responCode' => 1,
            'respon' => 'Data Angka Kredit berhasil disimpan'
        ]);
    }


    /**
     * UPDATE
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:angka_kredits,id',
            'user_id' => 'required|exists:users,id',
            'no_sk' => 'required|string|max:255',
            'tgl_sk' => 'required|date',
            
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date',

            // 'bulan_mulai' => 'required|integer|min:1|max:12',
            // 'tahun_mulai' => 'required|integer|min:1900|max:2100',

            // 'bulan_selesai' => 'required|integer|min:1|max:12',
            // 'tahun_selesai' => 'required|integer|min:1900|max:2100',

            'kredit_utama' => 'nullable|numeric|min:0',
            'kredit_penunjang' => 'nullable|numeric|min:0',
            'total_kredit' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responCode' => 0,
                'respon' => $validator->errors()
            ]);
        }

        $angkaKredit = AngkaKredit::find($request->id);

        // Non Admin hanya boleh mengubah data miliknya
        if (
            Auth::user()->role != 'Admin' &&
            $angkaKredit->user_id != Auth::id()
        ) {
            return response()->json([
                'responCode' => 0,
                'respon' => 'Anda tidak memiliki akses untuk mengubah data ini'
            ]);
        }

        $userId = Auth::user()->role == 'Admin'
            ? $request->user_id
            : Auth::id();

        $angkaKredit->update([
            'user_id' => $userId,
            'no_sk' => $request->no_sk,
            'tgl_sk' => $request->tgl_sk,

            // 'bulan_mulai' => $request->bulan_mulai,
            // 'tahun_mulai' => $request->tahun_mulai,

            // 'bulan_selesai' => $request->bulan_selesai,
            // 'tahun_selesai' => $request->tahun_selesai,

            
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,

            'kredit_utama' => $request->kredit_utama,
            'kredit_penunjang' => $request->kredit_penunjang,
            'total_kredit' => $request->total_kredit,
        ]);

        return response()->json([
            'responCode' => 1,
            'respon' => 'Data Angka Kredit berhasil diupdate'
        ]);
    }


    /**
     * DELETE
     */
    public function delete(Request $request)
    {
        $angkaKredit = AngkaKredit::find($request->id);

        if (!$angkaKredit) {
            return response()->json([
                'responCode' => 0,
                'respon' => 'Data tidak ditemukan'
            ]);
        }

        // Non Admin hanya boleh menghapus data miliknya
        if (
            Auth::user()->role != 'Admin' &&
            $angkaKredit->user_id != Auth::id()
        ) {
            return response()->json([
                'responCode' => 0,
                'respon' => 'Anda tidak memiliki akses untuk menghapus data ini'
            ]);
        }

        $angkaKredit->delete();

        return response()->json([
            'responCode' => 1,
            'respon' => 'Data Angka Kredit berhasil dihapus'
        ]);
    }
}
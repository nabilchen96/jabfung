<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kepangkatan;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;


class KepangkatanController extends Controller
{
    public function index(){

        return view('backend.kepangkatan.index');
    }

    public function data(Request $request)
    {
        $data = DB::table('kepangkatans')
                ->leftjoin('users', 'users.id', '=', 'kepangkatans.user_id')
                ->select(
                    'users.name',
                    'kepangkatans.*'
                )
                ->get();

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'file_sk' => 'required|mimes:pdf|max:1048',
            'golongan' => 'required',
            'pangkat'  => 'required',
            'nomor_sk' => 'required',
            'terhitung_mulai_tanggal' => 'required'
        ]);

        if ($validator->fails()) {

             $data = [
                'responCode' => 0,
                'respon' => $validator->errors()
            ];

        }

        // upload file_sk
        $namafilesk = null;

        if ($request->hasFile('file_sk')) {

            $file = $request->file('file_sk');

            $namafilesk = time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('upload/file_sk_pangkat'), $namafilesk);
        }

        // simpan database
        Kepangkatan::Create([

            'user_id' => $request->user_id,
            'golongan' => $request->golongan,
            'pangkat' => $request->pangkat,
            'nomor_sk' => $request->nomor_sk,
            'terhitung_mulai_tanggal' => $request->terhitung_mulai_tanggal,
            'file_sk' => $namafilesk ?? DB::table('kepangkatans')->where('user_id', Request('id'))->value('file_sk'),
        
        ]);

        $data = [
            'responCode' => 1,
            'respon' => 'Data Sukses Disimpan'
        ];

        return response()->json($data);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'id' => 'required|exists:kepangkatans,id',
            'user_id' => 'required|exists:users,id',
            'golongan' => 'required',
            'pangkat' => 'required',
            'nomor_sk' => 'required',
            'terhitung_mulai_tanggal' => 'required',

            // file optional saat update
            'file_sk' => 'nullable|mimes:pdf|max:1048',

        ]);

        if ($validator->fails()) {

            return response()->json([
                'responCode' => 0,
                'respon' => $validator->errors()
            ]);
        }

        // ambil data lama
        $kepangkatan = Kepangkatan::find($request->id);

        // default gunakan file lama
        $namafilesk = $kepangkatan->file_sk;

        // jika upload file baru
        if ($request->hasFile('file_sk')) {

            // hapus file lama
            if (
                $kepangkatan->file_sk &&
                file_exists(public_path('upload/file_sk_pangkat/' . $kepangkatan->file_sk))
            ) {

                unlink(public_path('upload/file_sk_pangkat/' . $kepangkatan->file_sk));
            }

            // upload file baru
            $file = $request->file('file_sk');

            $namafilesk = time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('upload/file_sk_pangkat'), $namafilesk);
        }

        // update database
        $kepangkatan->update([

            'user_id' => $request->user_id,
            'golongan' => $request->golongan,
            'pangkat' => $request->pangkat,
            'nomor_sk' => $request->nomor_sk,
            'terhitung_mulai_tanggal' => $request->terhitung_mulai_tanggal,
            'file_sk' => $namafilesk,

        ]);

        return response()->json([
            'responCode' => 1,
            'respon' => 'Data berhasil diupdate'
        ]);
    }

    public function delete(Request $request)
    {

        $data = Kepangkatan::find($request->id)->delete();

        $data = [
            'responCode' => 1,
            'respon' => 'Data Sukses Dihapus'
        ];

        return response()->json($data);
    }
}

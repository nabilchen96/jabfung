<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\JabatanFungsional;
use Illuminate\Support\Facades\Validator;
use Auth;

class JabatanFungsionalController extends Controller
{
    public function index(){

        return view('backend.jabatan_fungsional.index');
    }

    public function data(Request $request)
    {
        $data = DB::table('jabatan_fungsionals')
                ->leftjoin('users', 'users.id', '=', 'jabatan_fungsionals.user_id')
                ->select(
                    'users.name',
                    'jabatan_fungsionals.*'
                )
                ->get();

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'file_sk' => 'required|mimes:pdf|max:1048',
            'jabatan_fungsional' => 'required',
            'nomor_sk'  => 'required',
            'terhitung_mulai_tanggal' => 'required',
            'status_pegawai' => 'required'
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

            $file->move(public_path('upload/file_sk'), $namafilesk);
        }

        // simpan database
        JabatanFungsional::Create([

            'user_id' => $request->user_id,
            'jabatan_fungsional' => $request->jabatan_fungsional,
            'nomor_sk' => $request->nomor_sk,
            'terhitung_mulai_tanggal' => $request->terhitung_mulai_tanggal,
            'file_sk' => $namafilesk ?? DB::table('jabatan_fungsionals')->where('user_id', Request('id'))->value('file_sk'),
            'status_pegawai' => $request->status_pegawai,
        
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

            'id' => 'required|exists:jabatan_fungsionals,id',
            'user_id' => 'required|exists:users,id',
            'jabatan_fungsional' => 'required',
            'nomor_sk' => 'required',
            'terhitung_mulai_tanggal' => 'required',
            'status_pegawai' => 'required',

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
        $jabatan = JabatanFungsional::find($request->id);

        // default gunakan file lama
        $namafilesk = $jabatan->file_sk;

        // jika upload file baru
        if ($request->hasFile('file_sk')) {

            // hapus file lama
            if (
                $jabatan->file_sk &&
                file_exists(public_path('upload/file_sk/' . $jabatan->file_sk))
            ) {

                unlink(public_path('upload/file_sk/' . $jabatan->file_sk));
            }

            // upload file baru
            $file = $request->file('file_sk');

            $namafilesk = time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('upload/file_sk'), $namafilesk);
        }

        // update database
        $jabatan->update([

            'user_id' => $request->user_id,
            'jabatan_fungsional' => $request->jabatan_fungsional,
            'nomor_sk' => $request->nomor_sk,
            'terhitung_mulai_tanggal' => $request->terhitung_mulai_tanggal,
            'status_pegawai' => $request->status_pegawai,
            'file_sk' => $namafilesk,

        ]);

        return response()->json([
            'responCode' => 1,
            'respon' => 'Data berhasil diupdate'
        ]);
    }

    public function delete(Request $request)
    {

        $data = JabatanFungsional::find($request->id)->delete();

        $data = [
            'responCode' => 1,
            'respon' => 'Data Sukses Dihapus'
        ];

        return response()->json($data);
    }
}

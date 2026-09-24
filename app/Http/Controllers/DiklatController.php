<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Diklat;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;

class DiklatController extends Controller
{
    public function index(){

        return view('backend.diklat.index');
    }

    public function data(Request $request)
    {
        $data = DB::table('diklats')
                ->leftjoin('users', 'users.id', '=', 'diklats.user_id')
                ->select(
                    'users.name',
                    'diklats.*'
                )
                ->get();

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'nama_diklat' => 'required',
            'jenis_diklat'  => 'required',
            'penyelenggara' => 'required',
            'tahun' => 'required',
            'file_sertifikat' => 'required|mimes:pdf|max:1048',
        ]);

        if ($validator->fails()) {

             $data = [
                'responCode' => 0,
                'respon' => $validator->errors()
            ];

        }

        // upload file_sertifikat
        $namafilesertifikat = null;

        if ($request->hasFile('file_sertifikat')) {

            $file = $request->file('file_sertifikat');

            $namafilesertifikat = time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('upload/file_sertifikat'), $namafilesertifikat);
        }

        // simpan database
        Diklat::Create([

            'user_id' => $request->user_id,
            'nama_diklat' => $request->nama_diklat,
            'jenis_diklat' => $request->jenis_diklat,
            'penyelenggara' => $request->penyelenggara,
            'tahun' => $request->tahun,
            'file_sertifikat' => $namafilesertifikat ?? DB::table('diklats')->where('user_id', Request('id'))->value('file_sertifikat'),
        
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

            'id' => 'required|exists:diklats,id',
            'user_id' => 'required|exists:users,id',

            'nama_diklat' => 'required',
            'jenis_diklat'  => 'required',
            'penyelenggara' => 'required',
            'tahun' => 'required',
            
            'file_sertifikat' => 'mimes:pdf|max:1048',

        ]);

        if ($validator->fails()) {

            return response()->json([
                'responCode' => 0,
                'respon' => $validator->errors()
            ]);
        }

        // ambil data lama
        $diklat = Diklat::find($request->id);

        // default gunakan file lama
        $namafilesertifikat = $diklat->file_sertifikat;

        // jika upload file baru
        if ($request->hasFile('file_sertifikat')) {

            // hapus file lama
            if (
                $diklat->file_sertifikat &&
                file_exists(public_path('upload/file_sertifikat/' . $diklat->file_sertifikat))
            ) {

                unlink(public_path('upload/file_sertifikat/' . $diklat->file_sertifikat));
            }

            // upload file baru
            $file = $request->file('file_sertifikat');

            $namafilesertifikat = time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('upload/file_sertifikat'), $namafilesertifikat);
        }

        // update database
        $diklat->update([

            'user_id' => $request->user_id,
            'nama_diklat' => $request->nama_diklat,
            'jenis_diklat' => $request->jenis_diklat,
            'penyelenggara' => $request->penyelenggara,
            'tahun' => $request->tahun,
            'file_sertifikat' => $namafilesertifikat,

        ]);

        return response()->json([
            'responCode' => 1,
            'respon' => 'Data berhasil diupdate'
        ]);
    }

    public function delete(Request $request)
    {

        $data = Diklat::find($request->id)->delete();

        $data = [
            'responCode' => 1,
            'respon' => 'Data Sukses Dihapus'
        ];

        return response()->json($data);
    }
}

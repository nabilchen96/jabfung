<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pendidikan;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;

class PendidikanController extends Controller
{
    public function index(){

        return view('backend.pendidikan.index');
    }

    public function data(Request $request)
    {
        $data = DB::table('pendidikans')
                ->leftjoin('users', 'users.id', '=', 'pendidikans.user_id')
                ->select(
                    'users.name',
                    'pendidikans.*'
                )
                ->get();

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'jenjang' => 'required',
            'gelar'  => 'required',
            'bidang_studi' => 'required',
            'nama_institusi' => 'required',
            'tahun_lulus' => 'required',

            'file_ijazah' => 'required|mimes:pdf|max:1048',
        ]);

        if ($validator->fails()) {

             $data = [
                'responCode' => 0,
                'respon' => $validator->errors()
            ];

        }

        // upload file_ijazah
        $namafileijazah = null;

        if ($request->hasFile('file_ijazah')) {

            $file = $request->file('file_ijazah');

            $namafileijazah = time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('upload/file_ijazah'), $namafileijazah);
        }

        // simpan database
        Pendidikan::Create([

            'user_id' => $request->user_id,
            'jenjang' => $request->jenjang,
            'gelar' => $request->gelar,
            'bidang_studi' => $request->bidang_studi,
            'nama_institusi' => $request->nama_institusi,
            'tahun_lulus' => $request->tahun_lulus,
            'file_ijazah' => $namafileijazah ?? DB::table('pendidikans')->where('user_id', Request('id'))->value('file_ijazah'),
        
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

            'id' => 'required|exists:pendidikans,id',
            'user_id' => 'required|exists:users,id',

            'jenjang' => 'required',
            'gelar'  => 'required',
            'bidang_studi' => 'required',
            'nama_institusi' => 'required',
            'tahun_lulus' => 'required',

            'file_ijazah' => 'required|mimes:pdf|max:1048',

        ]);

        if ($validator->fails()) {

            return response()->json([
                'responCode' => 0,
                'respon' => $validator->errors()
            ]);
        }

        // ambil data lama
        $pendidikan = Pendidikan::find($request->id);

        // default gunakan file lama
        $namafileijazah = $pendidikan->file_ijazah;

        // jika upload file baru
        if ($request->hasFile('file_ijazah')) {

            // hapus file lama
            if (
                $pendidikan->file_ijazah &&
                file_exists(public_path('upload/file_ijazah/' . $pendidikan->file_ijazah))
            ) {

                unlink(public_path('upload/file_ijazah/' . $pendidikan->file_ijazah));
            }

            // upload file baru
            $file = $request->file('file_ijazah');

            $namafileijazah = time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('upload/file_ijazah'), $namafileijazah);
        }

        // update database
        $pendidikan->update([

            'user_id' => $request->user_id,
            'jenjang' => $request->jenjang,
            'gelar' => $request->gelar,
            'bidang_studi' => $request->bidang_studi,
            'nama_institusi' => $request->nama_institusi,
            'tahun_lulus' => $request->tahun_lulus,
            'file_ijazah' => $namafileijazah,

        ]);

        return response()->json([
            'responCode' => 1,
            'respon' => 'Data berhasil diupdate'
        ]);
    }

    public function delete(Request $request)
    {

        $data = Pendidikan::find($request->id)->delete();

        $data = [
            'responCode' => 1,
            'respon' => 'Data Sukses Dihapus'
        ];

        return response()->json($data);
    }
}

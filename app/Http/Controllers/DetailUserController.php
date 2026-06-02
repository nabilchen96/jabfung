<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\DetailUser;
use DB;

class DetailUserController extends Controller
{
    public function index(){
        $id = Request('id');
        $data = DB::table('users')
                ->leftjoin('detail_users', 'detail_users.user_id', '=', 'users.id')
                ->select(
                    'users.name',
                    'detail_users.*'
                )
                ->where('users.id', Request('id'))
                ->first();

        return view('backend.detail_user.index', [
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {

            return redirect()
            ->back()
            ->withErrors($validator)
            ->withInput();

        }

        // upload foto
        $namaFoto = null;

        if ($request->hasFile('foto')) {

            $file = $request->file('foto');

            $namaFoto = time() . '_' . $file->getClientOriginalName();

            $file->move(public_path('upload/detail-user'), $namaFoto);
        }

        // simpan database
        DetailUser::updateOrCreate(
            [
                // kondisi pencarian
                'user_id' => $request->id,
            ], 
            [
            'user_id' => $request->id,

            // profil
            'nip' => $request->nip,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'foto' => $namaFoto ?? DB::table('detail_users')->where('user_id', Request('id'))->value('foto'),

            // alamat & kontak
            'email' => $request->email,
            'alamat' => $request->alamat,

            // kependudukan
            'nik' => $request->nik,
            'agama' => $request->agama,
            'kewarganegaraan' => $request->kewarganegaraan,

            // kepegawaian
            'nomor_sk' => $request->nomor_sk,
            'tmt_sk' => $request->tmt_sk,
            'pangkat_golongan' => $request->pangkat_golongan,
            'tanggal_sk' => $request->tanggal_sk,
            'masa_kerja_tahun' => $request->masa_kerja_tahun,
            'masa_kerja_bulan' => $request->masa_kerja_bulan,
            'status_kepegawaian' => $request->status_kepegawaian,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Data berhasil disimpan');
    }
}

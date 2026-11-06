<?php

namespace App\Http\Controllers;

use App\Http\Requests\EvaluasiKinerjaRequest;
use App\Models\EvaluasiKinerja;
use App\Models\User;
use Illuminate\Http\Request;

class EvaluasiKinerjaController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->role === 'Admin', 403);

            return $next($request);
        })->except('index');
    }

    public function index(Request $request)
    {
        $admin = $request->user()->role === 'Admin';
        $query = EvaluasiKinerja::with('pegawai:id,name');
        if (!$admin) {
            $query->where('user_id', $request->user()->id);
        }

        return view('backend.evaluasi_kinerja.index', [
            'evaluasis' => $query->latest('id')->paginate(20),
            'pegawai' => $admin ? User::orderBy('name')->get(['id', 'name', 'email']) : collect(),
            'nilaiOptions' => EvaluasiKinerja::NILAI,
            'admin' => $admin,
        ]);
    }

    public function store(EvaluasiKinerjaRequest $request)
    {
        EvaluasiKinerja::create($request->validated());

        return redirect()->route('evaluasi-kinerja.index')->with('success', 'Evaluasi kinerja berhasil ditambahkan.');
    }

    public function update(EvaluasiKinerjaRequest $request, EvaluasiKinerja $evaluasiKinerja)
    {
        $evaluasiKinerja->update($request->validated());

        return redirect()->route('evaluasi-kinerja.index')->with('success', 'Evaluasi kinerja berhasil diperbarui.');
    }

    public function destroy(EvaluasiKinerja $evaluasiKinerja)
    {
        $evaluasiKinerja->delete();

        return redirect()->route('evaluasi-kinerja.index')->with('success', 'Evaluasi kinerja berhasil dihapus.');
    }
}

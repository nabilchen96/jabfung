<?php

namespace App\Http\Controllers;

use App\Http\Requests\AbsensiRequest;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;

class AbsensiController extends Controller
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
        $request->validate(['bulan' => ['nullable', 'date_format:Y-m']]);
        $bulan = $request->input('bulan') ?: now()->format('Y-m');
        $admin = $request->user()->role === 'Admin';
        $query = Absensi::with('pegawai:id,name')->where('bulan', $bulan);
        if (!$admin) {
            $query->where('user_id', $request->user()->id);
        }

        return view('backend.absensi.index', [
            'absensis' => $query->orderBy('user_id')->paginate(20)->withQueryString(),
            'pegawai' => $admin ? User::orderBy('name')->get(['id', 'name', 'email']) : collect(),
            'admin' => $admin,
            'bulan' => $bulan,
        ]);
    }

    public function store(AbsensiRequest $request)
    {
        $data = $request->validated();
        Absensi::create($data);

        return redirect()->route('absensi.index', ['bulan' => $data['bulan']])->with('success', 'Data absensi berhasil ditambahkan.');
    }

    public function update(AbsensiRequest $request, Absensi $absensi)
    {
        $data = $request->validated();
        $absensi->update($data);

        return redirect()->route('absensi.index', ['bulan' => $data['bulan']])->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function destroy(Absensi $absensi)
    {
        $bulan = $absensi->bulan;
        $absensi->delete();

        return redirect()->route('absensi.index', ['bulan' => $bulan])->with('success', 'Data absensi berhasil dihapus.');
    }
}

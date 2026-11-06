@extends('backend.app')

@push('style')
    <style>
        @media (max-width: 768px) {
            #absensiTable td.absensi-empty { display: table-cell !important; }
            #absensiTable td[data-label]::before { content: attr(data-label); font-weight: bold; }
        }
    </style>
@endpush

@section('content')
    <div class="row page-heading">
        <div class="col-12 page-heading-content">
            <h3 class="font-weight-bold">Data Absensi</h3>
            <p>Rekap bulanan {{ $admin ? 'pegawai' : 'Anda' }}. Izin, cuti, dinas, dan kehadiran dalam hari; terlambat dan pulang cepat dalam durasi.</p>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="status">{{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif
                    <div class="d-flex flex-wrap align-items-center mb-4">
                        @if ($admin)
                            <button type="button" class="btn btn-primary btn-md page-action mr-2 mb-2" data-toggle="modal" data-target="#absensiModal">Tambah</button>
                        @endif
                        <a href="{{ route('export.excel', ['page' => 'absensi', 'bulan' => $bulan]) }}" class="btn btn-success btn-md page-action mb-2" title="Ekspor absensi pada bulan yang dipilih">Export</a>
                    </div>
                    <form action="{{ route('absensi.index') }}" method="GET" class="mb-4">
                        <label for="filter-bulan">Bulan absensi</label>
                        <div class="d-flex align-items-center flex-wrap">
                            <input type="month" id="filter-bulan" name="bulan" value="{{ $bulan }}" required class="form-control mr-2 mb-2" style="max-width:220px;">
                            <button class="btn btn-primary page-action mb-2" type="submit">Tampilkan</button>
                        </div>
                    </form>
                    <p class="text-muted">Satu catatan per pegawai per bulan. Izin, cuti, dan dinas dicatat terpisah. Durasi memakai format HH:MM:SS dan dapat melebihi 24 jam.</p>
                    <div class="table-responsive">
                        <table id="absensiTable" class="table table-striped">
                            <thead class="bg-info text-white"><tr>
                                <th>No</th><th>Nama Pegawai</th><th>Bulan</th><th>Total Terlambat (HH:MM:SS)</th>
                                <th>Total Izin (Hari)</th><th>Total Cuti (Hari)</th><th>Total Dinas (Hari)</th><th>Total Pulang Cepat (HH:MM:SS)</th><th>Total Kehadiran (Hari)</th>
                                @if ($admin)<th>Aksi</th>@endif
                            </tr></thead>
                            <tbody>
                                @forelse ($absensis as $absensi)
                                    <tr>
                                        <td data-label="No">{{ $absensis->firstItem() + $loop->index }}</td>
                                        <td data-label="Nama Pegawai">
                                            {{ $absensi->pegawai->name }}
                                            @if ($absensi->perlu_penyesuaian)
                                                <div class="small text-warning mt-1">Perlu penyesuaian data lama</div>
                                                <div class="small text-muted">Rekap lama: terlambat {{ $absensi->lama_terlambat_hari }} hari; pulang cepat {{ $absensi->lama_pulang_cepat_hari }} hari; cuti/izin/dinas {{ $absensi->lama_cuti_izin_dinas }} hari.</div>
                                            @endif
                                        </td>
                                        <td data-label="Bulan">{{ \Carbon\Carbon::createFromFormat('!Y-m', $absensi->bulan)->locale('id')->translatedFormat('F Y') }}</td>
                                        <td data-label="Total Terlambat">{{ $absensi->perlu_penyesuaian ? 'Perlu penyesuaian' : $absensi->total_terlambat }}</td>
                                        <td data-label="Total Izin">{{ $absensi->perlu_penyesuaian ? 'Perlu penyesuaian' : $absensi->total_izin }}</td>
                                        <td data-label="Total Cuti">{{ $absensi->perlu_penyesuaian ? 'Perlu penyesuaian' : $absensi->total_cuti }}</td>
                                        <td data-label="Total Dinas">{{ $absensi->perlu_penyesuaian ? 'Perlu penyesuaian' : $absensi->total_dinas }}</td>
                                        <td data-label="Total Pulang Cepat">{{ $absensi->perlu_penyesuaian ? 'Perlu penyesuaian' : $absensi->total_pulang_cepat }}</td>
                                        <td data-label="Total Kehadiran">{{ $absensi->total_kehadiran }}</td>
                                        @if ($admin)
                                            <td data-label="Aksi">
                                                <div class="d-flex align-items-center">
                                                    <button type="button" class="btn btn-primary btn-sm d-inline-flex align-items-center mr-2" data-toggle="modal" data-target="#absensiModal" data-id="{{ $absensi->id }}">Edit</button>
                                                    <form action="{{ route('absensi.destroy', $absensi) }}" method="POST" class="absensi-delete-form">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm d-inline-flex align-items-center">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr><td class="absensi-empty" colspan="{{ $admin ? 10 : 9 }}">Belum ada data absensi untuk bulan ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $absensis->links('pagination::bootstrap-4') }}</div>
                </div>
            </div>
        </div>
    </div>

    @if ($admin)
        <div class="modal fade" id="absensiModal" tabindex="-1" aria-labelledby="absensiModalTitle" aria-hidden="true">
            <div class="modal-dialog"><div class="modal-content">
                <form id="absensiForm" action="{{ old('absensi_id') ? route('absensi.update', (int) old('absensi_id')) : route('absensi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="absensiMethod" value="{{ old('absensi_id') ? 'PUT' : 'POST' }}">
                    <input type="hidden" name="absensi_id" id="absensi-id" value="{{ old('absensi_id') }}">
                    <div class="modal-header"><h5 class="modal-title" id="absensiModalTitle">{{ old('absensi_id') ? 'Edit' : 'Tambah' }} Absensi</h5></div>
                    <div class="modal-body">
                        @php($legacyRecord = $absensis->getCollection()->firstWhere('id', (int) old('absensi_id')))
                        <div id="absensi-legacy" class="alert alert-warning" role="status" @if (!$legacyRecord?->perlu_penyesuaian) hidden @endif>
                            @if ($legacyRecord?->perlu_penyesuaian)
                                Data lama: terlambat {{ $legacyRecord->lama_terlambat_hari }} hari, pulang cepat {{ $legacyRecord->lama_pulang_cepat_hari }} hari, cuti/izin/dinas gabungan {{ $legacyRecord->lama_cuti_izin_dinas }} hari. Isi durasi dan pisahkan jumlah hari berdasarkan rekap asli, lalu simpan.
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="absensi-user">Nama Pegawai</label>
                            <select id="absensi-user" name="user_id" class="form-control" required>
                                <option value="">Pilih pegawai</option>
                                @foreach ($pegawai as $user)
                                    <option value="{{ $user->id }}" @selected((string) old('user_id') === (string) $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="absensi-bulan">Bulan</label>
                            <input id="absensi-bulan" name="bulan" type="month" value="{{ old('bulan', $bulan) }}" class="form-control" required>
                        </div>
                        @foreach (['total_izin' => 'Total Izin', 'total_cuti' => 'Total Cuti', 'total_dinas' => 'Total Dinas', 'total_kehadiran' => 'Total Kehadiran'] as $field => $label)
                            <div class="form-group">
                                <label for="absensi-{{ $field }}">{{ $label }} (hari)</label>
                                <input id="absensi-{{ $field }}" name="{{ $field }}" type="number" min="0" max="31" step="1" value="{{ old($field, 0) }}" class="form-control absensi-total" required>
                            </div>
                        @endforeach
                        @foreach (['total_terlambat' => 'Total Terlambat', 'total_pulang_cepat' => 'Total Pulang Cepat'] as $field => $label)
                            <div class="form-group">
                                <label for="absensi-{{ $field }}">{{ $label }} (HH:MM:SS)</label>
                                <input id="absensi-{{ $field }}" name="{{ $field }}" type="text" inputmode="text" maxlength="9" pattern="([0-9]{2}|[1-9][0-9]{2}):[0-5][0-9]:[0-5][0-9]" placeholder="00:00:00" value="{{ old($field, '00:00:00') }}" class="form-control" aria-describedby="absensi-duration-help" required>
                            </div>
                        @endforeach
                        <p class="text-muted" id="absensi-duration-help">Isi 0 untuk jumlah hari dan 00:00:00 untuk durasi kosong. Durasi dapat melebihi 24 jam, misalnya 27:15:00. Jumlah hari maksimal sesuai bulan yang dipilih.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary page-action" data-dismiss="modal">Batal</button>
                        <button type="submit" id="absensiSubmit" class="btn btn-primary page-action">Simpan</button>
                    </div>
                </form>
            </div></div>
        </div>
    @endif
@endsection

@push('script')
    @if ($admin)
        <script>
            window.absensiConfig = {{ \Illuminate\Support\Js::from([
                'rows' => $absensis->getCollection()->keyBy('id'),
                'storeUrl' => route('absensi.store'),
                'updateUrl' => route('absensi.update', ['absensi' => '__ID__']),
                'bulan' => $bulan,
                'hasErrors' => $errors->any() && old('_token') !== null,
            ]) }};
        </script>
        <script src="{{ asset('js/backend/absensi/index.js') }}"></script>
    @endif
@endpush

@extends('backend.app')

@push('style')
    <style>
        #evaluasiTable .evaluasi-keterangan { white-space: pre-wrap; overflow-wrap: anywhere; }
        @media (max-width: 768px) {
            #evaluasiTable td.evaluasi-empty { display: table-cell !important; }
            #evaluasiTable td[data-label]::before { content: attr(data-label); font-weight: bold; }
        }
    </style>
@endpush

@section('content')
    <div class="row page-heading">
        <div class="col-12 page-heading-content">
            <h3 class="font-weight-bold">Evaluasi Kinerja</h3>
            <p>Penilaian kinerja {{ $admin ? 'pegawai' : 'Anda' }}.</p>
        </div>
    </div>
    <div class="row mt-4"><div class="col-12"><div class="card"><div class="card-body">
        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><ul class="mb-0">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul></div>
        @endif
        <div class="d-flex flex-wrap align-items-center mb-4">
            @if ($admin)
                <button type="button" class="btn btn-primary btn-md page-action mr-2" data-toggle="modal" data-target="#evaluasiModal">Tambah</button>
            @endif
            <a href="{{ route('export.excel', ['page' => 'evaluasi-kinerja']) }}" class="btn btn-success btn-md page-action">Export</a>
        </div>
        <div class="table-responsive">
            <table id="evaluasiTable" class="table table-striped">
                <thead class="bg-info text-white"><tr>
                    <th>No</th><th>Nama Pegawai</th><th>Nilai</th><th>Keterangan</th>@if ($admin)<th>Aksi</th>@endif
                </tr></thead>
                <tbody>
                    @forelse ($evaluasis as $evaluasi)
                        <tr>
                            <td data-label="No">{{ $evaluasis->firstItem() + $loop->index }}</td>
                            <td data-label="Nama Pegawai">{{ $evaluasi->pegawai->name }}</td>
                            <td data-label="Nilai">{{ $evaluasi->nilai }}</td>
                            <td data-label="Keterangan" class="evaluasi-keterangan">{{ $evaluasi->keterangan ?? '—' }}</td>
                            @if ($admin)
                                <td data-label="Aksi"><div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-primary btn-sm d-inline-flex align-items-center mr-2" data-toggle="modal" data-target="#evaluasiModal" data-id="{{ $evaluasi->id }}">Edit</button>
                                    <form action="{{ route('evaluasi-kinerja.destroy', $evaluasi) }}" method="POST" class="evaluasi-delete-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm d-inline-flex align-items-center">Hapus</button>
                                    </form>
                                </div></td>
                            @endif
                        </tr>
                    @empty
                        <tr><td class="evaluasi-empty" colspan="{{ $admin ? 5 : 4 }}">Belum ada data evaluasi kinerja.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $evaluasis->links('pagination::bootstrap-4') }}</div>
    </div></div></div></div>

    @if ($admin)
        <div class="modal fade" id="evaluasiModal" tabindex="-1" aria-labelledby="evaluasiModalTitle" aria-hidden="true">
            <div class="modal-dialog"><div class="modal-content">
                <form id="evaluasiForm" action="{{ old('evaluasi_id') ? route('evaluasi-kinerja.update', (int) old('evaluasi_id')) : route('evaluasi-kinerja.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="evaluasiMethod" value="{{ old('evaluasi_id') ? 'PUT' : 'POST' }}">
                    <input type="hidden" name="evaluasi_id" id="evaluasi-id" value="{{ old('evaluasi_id') }}">
                    <div class="modal-header"><h5 class="modal-title" id="evaluasiModalTitle">{{ old('evaluasi_id') ? 'Edit' : 'Tambah' }} Evaluasi Kinerja</h5></div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="evaluasi-user">Nama Pegawai</label>
                            <select id="evaluasi-user" name="user_id" class="form-control" required>
                                <option value="">Pilih pegawai</option>
                                @foreach ($pegawai as $user)
                                    <option value="{{ $user->id }}" @selected((string) old('user_id') === (string) $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="evaluasi-nilai">Nilai</label>
                            <select id="evaluasi-nilai" name="nilai" class="form-control" required>
                                <option value="">Pilih nilai</option>
                                @foreach ($nilaiOptions as $nilai)<option @selected(old('nilai') === $nilai)>{{ $nilai }}</option>@endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="evaluasi-keterangan">Keterangan (opsional)</label>
                            <textarea id="evaluasi-keterangan" name="keterangan" class="form-control" rows="4" maxlength="5000" style="height:auto;">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary page-action" data-dismiss="modal">Batal</button>
                        <button type="submit" id="evaluasiSubmit" class="btn btn-primary page-action">Simpan</button>
                    </div>
                </form>
            </div></div>
        </div>
    @endif
@endsection

@push('script')
    @if ($admin)
        <script>
            window.evaluasiConfig = {{ \Illuminate\Support\Js::from([
                'rows' => $evaluasis->getCollection()->keyBy('id'),
                'storeUrl' => route('evaluasi-kinerja.store'),
                'updateUrl' => route('evaluasi-kinerja.update', ['evaluasiKinerja' => '__ID__']),
                'hasErrors' => $errors->any() && old('_token') !== null,
            ]) }};
        </script>
        <script src="{{ asset('js/backend/evaluasi_kinerja/index.js') }}"></script>
    @endif
@endpush

@extends('backend.app')
@section('content')
    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <div class="row">
                <div class="col-12 col-xl-8 mb-xl-0">
                    <h3 class="font-weight-bold">Data Jabatan Fungsional</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mt-4">
            <div class="card w-100">
                <div class="card-body">
                    <button type="button"
                        class="btn btn-primary btn-md mb-4 d-none d-md-inline-block" data-toggle="modal"
                        data-target="#modal">
                        Tambah
                    </button>


                    <div class="table-responsive">
                        <table id="myTable" class="table table-striped" style="width: 100%;">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama</th>
                                    <th>Jabatan Fungsional</th>
                                    <th>Nomor SK</th>
                                    <th>Terhitung Mulai Tanggal</th>
                                    <th>Status Pegawai</th>
                                    <th>File SK</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="form" enctype="multipart/form-data">
                    <div class="modal-header p-3">
                        <h5 class="modal-title m-2" id="exampleModalLabel">
                            Jabatan Fungsional Form
                        </h5>
                    </div>

                    <div class="modal-body">

                        <div id="respon_error" class="text-danger mb-4"></div>

                        <input type="hidden" name="id" id="id">

                        {{-- USER --}}
                        <div class="form-group">
                            <label>User</label>

                            <select name="user_id" id="user_id" class="form-control form-control-sm" required>

                                <option value="">PILIH USER</option>

                                @php
                                    $user = DB::table('users')->where('role', '!=', 'Admin');
                                    
                                    if (Auth::user()->role == 'Admin') {
                                        $user = $user->get();
                                    } else {
                                        $user = $user->where('id', Auth::id())->get();
                                    }
                                @endphp

                                @foreach ($user as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- JABATAN FUNGSIONAL --}}
                        <div class="form-group">
                            <label>Jabatan Fungsional</label>

                            <input type="text" name="jabatan_fungsional" id="jabatan_fungsional"
                                class="form-control form-control-sm" placeholder="Contoh : Lektor" required>
                        </div>

                        {{-- NOMOR SK --}}
                        <div class="form-group">
                            <label>Nomor SK</label>

                            <input type="text" name="nomor_sk" id="nomor_sk" class="form-control form-control-sm"
                                placeholder="Masukkan Nomor SK" required>
                        </div>

                        {{-- TERHITUNG MULAI --}}
                        <div class="form-group">
                            <label>Terhitung Mulai Tanggal</label>

                            <input type="date" name="terhitung_mulai_tanggal" id="terhitung_mulai_tanggal"
                                class="form-control form-control-sm" required>
                        </div>

                        {{-- STATUS PEGAWAI --}}
                        <div class="form-group">
                            <label>Status Pegawai</label>

                            <select name="status_pegawai" id="status_pegawai" class="form-control form-control-sm">

                                <option value="">PILIH STATUS</option>
                                <option value="PNS">PNS</option>
                                <option value="PPPK">PPPK</option>
                                <option value="Non ASN">Non ASN</option>
                            </select>
                        </div>

                        {{-- FILE SK --}}
                        <div class="form-group">
                            <label>Upload File SK</label>

                            <input type="file" name="file_sk" id="file_sk" class="form-control form-control-sm"
                                accept=".pdf">

                            <small class="text-muted">
                                Format: PDF
                            </small>
                        </div>

                    </div>

                    <div class="modal-footer p-3">
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">
                            Close
                        </button>

                        <button id="tombol_kirim" class="btn btn-primary btn-sm">
                            Submit
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ asset('js/backend/jabatan_fungsional/index.js') }}"></script>
@endpush

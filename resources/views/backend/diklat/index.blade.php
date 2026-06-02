@extends('backend.app')
@section('content')
    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <div class="row">
                <div class="col-12 col-xl-8 mb-xl-0">
                    <h3 class="font-weight-bold">Data Diklat</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mt-4">
            <div class="card w-100">
                <div class="card-body">
                    <button type="button" class="btn btn-primary btn-md mb-4 d-none d-md-inline-block" data-toggle="modal"
                        data-target="#modal">
                        Tambah
                    </button>


                    <div class="table-responsive">
                        <table id="myTable" class="table table-striped" style="width: 100%;">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama</th>
                                    <th>Diklat</th>
                                    <th>Jenis</th>
                                    <th>Penyelenggara</th>
                                    <th>Tahun</th>
                                    <th>Sertifikat</th>
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
                            Sertifikat Form
                        </h5>
                    </div>

                    <div class="modal-body">

                        <div id="respon_error" class="text-danger mb-4"></div>

                        <input type="hidden" name="id" id="id">

                        {{-- USER --}}
                        <div class="form-group">
                            <label>User <sup class="text-danger">*</sup></label>

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

                        {{-- NAMA DIKLAT --}}
                        <div class="form-group">
                            <label>Nama Diklat <sup class="text-danger">*</sup></label>

                            <input type="text" name="nama_diklat" id="nama_diklat" class="form-control form-control-sm"
                                placeholder="Contoh: Pelatihan Artificial Intelligence (AI)" required>
                        </div>

                        {{-- JENIS DIKLAT --}}
                        <div class="form-group">
                            <label>Jenis Diklat <sup class="text-danger">*</sup></label>

                            <select name="jenis_diklat" id="jenis_diklat" class="form-control form-control-sm" required>

                                <option value="">-- Pilih Jenis Diklat --</option>
                                <option value="Pelatihan Profesional">Pelatihan Profesional</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Seminar">Seminar</option>
                                <option value="Bimbingan Teknis">Bimbingan Teknis</option>
                                <option value="Sertifikasi">Sertifikasi</option>
                            </select>
                        </div>

                        {{-- PENYELENGGARA --}}
                        <div class="form-group">
                            <label>Penyelenggara <sup class="text-danger">*</sup></label>

                            <input type="text" name="penyelenggara" id="penyelenggara"
                                class="form-control form-control-sm" placeholder="Contoh: LAN RI, Edukasa" required>
                        </div>

                        {{-- TAHUN --}}
                        <div class="form-group">
                            <label>Tahun <sup class="text-danger">*</sup></label>

                            <input type="number" name="tahun" id="tahun" class="form-control form-control-sm"
                                min="2000" max="{{ date('Y') + 5 }}" placeholder="Contoh: 2025" required>
                        </div>

                        {{-- FILE SERTIFIKAT --}}
                        <div class="form-group">
                            <label>Upload Sertifikat</label>

                            <input type="file" name="file_sertifikat" id="file_sertifikat"
                                class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">

                            <small class="text-muted">
                                Format: PDF, JPG, JPEG, PNG
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
    <script src="{{ asset('js/backend/diklat/index.js') }}"></script>
@endpush

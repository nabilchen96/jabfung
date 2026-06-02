@extends('backend.app')
@section('content')
    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <div class="row">
                <div class="col-12 col-xl-8 mb-xl-0">
                    <h3 class="font-weight-bold">Data Pendidikan</h3>
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
                                    <th>Jenjang / Gelar</th>
                                    <th>Bidang Studi / Institusi</th>
                                    <th>Tahun Lulus / Ijazah</th>
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
                            Pendidikan Form
                        </h5>
                    </div>

                    <div class="modal-body">

                        <div id="respon_error" class="text-danger mb-4"></div>

                        <input type="hidden" name="id" id="id">

                        {{-- USER --}}
                        <div class="form-group">
                            <label>User<sup class="text-danger">*</sup></label>

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

                        {{-- JENJANG --}}
                        <div class="form-group">
                            <label>Jenjang Pendidikan<sup class="text-danger">*</sup></label>

                            <select name="jenjang" id="jenjang" class="form-control form-control-sm" required>
                                <option value="">-- Pilih Jenjang --</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA">SMA</option>
                                <option value="SMK">SMK</option>
                                <option value="D1">D1</option>
                                <option value="D2">D2</option>
                                <option value="D3">D3</option>
                                <option value="D4">D4</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="S3">S3</option>
                            </select>
                        </div>

                        {{-- NAMA INSTITUSI --}}
                        <div class="form-group">
                            <label>Nama Institusi<sup class="text-danger">*</sup></label>

                            <input type="text" name="nama_institusi" id="nama_institusi"
                                class="form-control form-control-sm"
                                placeholder="Contoh: Universitas Sriwijaya / SMA Negeri 1" required>
                        </div>

                        {{-- BIDANG STUDI --}}
                        <div class="form-group">
                            <label>Bidang Studi</label>

                            <input type="text" name="bidang_studi" id="bidang_studi" class="form-control form-control-sm"
                                placeholder="Contoh: Manajemen, Teknik Informatika, Atau kosongkan">
                        </div>

                        {{-- GELAR --}}
                        <div class="form-group">
                            <label>Gelar</label>

                            <input type="text" name="gelar" id="gelar" class="form-control form-control-sm"
                                placeholder="Contoh: S.ST, S.Kom, M.M, Atau kosongkan">
                        </div>

                        {{-- TAHUN LULUS --}}
                        <div class="form-group">
                            <label>Tahun Lulus<sup class="text-danger">*</sup></label>

                            <input type="number" name="tahun_lulus" id="tahun_lulus" class="form-control form-control-sm"
                                min="1900" max="{{ date('Y') }}" placeholder="Contoh: 2020" required>
                        </div>

                        {{-- FILE IJAZAH --}}
                        <div class="form-group">
                            <label>Upload Ijazah</label>

                            <input type="file" name="file_ijazah" id="file_ijazah"
                                class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">

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
    <script src="{{ asset('js/backend/pendidikan/index.js') }}"></script>
@endpush

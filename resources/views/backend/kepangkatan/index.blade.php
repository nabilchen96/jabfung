@extends('backend.app')
@section('content')
    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <div class="row">
                <div class="col-12 col-xl-8 mb-xl-0">
                    <h3 class="font-weight-bold">Data Kepangkatan</h3>
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
                                    <th>Golongan</th>
                                    <th>Pangkat</th>
                                    <th>Nomor SK</th>
                                    <th>Terhitung Mulai Tanggal</th>
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
                            Kepangkatan Form
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

                        {{-- GOLONGAN --}}
                        <div class="form-group">
                            <label>Golongan</label>
                            <select name="golongan" id="golongan" class="form-control form-control-sm" required>
                                <option value="">-- Pilih Golongan --</option>

                                <optgroup label="Golongan I">
                                    <option value="I/a">I/a</option>
                                    <option value="I/b">I/b</option>
                                    <option value="I/c">I/c</option>
                                    <option value="I/d">I/d</option>
                                </optgroup>

                                <optgroup label="Golongan II">
                                    <option value="II/a">II/a</option>
                                    <option value="II/b">II/b</option>
                                    <option value="II/c">II/c</option>
                                    <option value="II/d">II/d</option>
                                </optgroup>

                                <optgroup label="Golongan III">
                                    <option value="III/a">III/a</option>
                                    <option value="III/b">III/b</option>
                                    <option value="III/c">III/c</option>
                                    <option value="III/d">III/d</option>
                                </optgroup>

                                <optgroup label="Golongan IV">
                                    <option value="IV/a">IV/a</option>
                                    <option value="IV/b">IV/b</option>
                                    <option value="IV/c">IV/c</option>
                                    <option value="IV/d">IV/d</option>
                                    <option value="IV/e">IV/e</option>
                                </optgroup>
                            </select>
                        </div>

                        {{-- PANGKAT --}}
                        <div class="form-group">
                            <label>Pangkat</label>
                            <select name="pangkat" id="pangkat" class="form-control form-control-sm" required>
                                <option value="">-- Pilih Pangkat --</option>
                                <option value="Juru Muda">Juru Muda (I/a)</option>
                                <option value="Juru Muda Tingkat I">Juru Muda Tingkat I (I/b)</option>
                                <option value="Juru">Juru (I/c)</option>
                                <option value="Juru Tingkat I">Juru Tingkat I (I/d)</option>

                                <option value="Pengatur Muda">Pengatur Muda (II/a)</option>
                                <option value="Pengatur Muda Tingkat I">Pengatur Muda Tingkat I (II/b)</option>
                                <option value="Pengatur">Pengatur (II/c)</option>
                                <option value="Pengatur Tingkat I">Pengatur Tingkat I (II/d)</option>

                                <option value="Penata Muda">Penata Muda (III/a)</option>
                                <option value="Penata Muda Tingkat I">Penata Muda Tingkat I (III/b)</option>
                                <option value="Penata">Penata (III/c)</option>
                                <option value="Penata Tingkat I">Penata Tingkat I (III/d)</option>

                                <option value="Pembina">Pembina (IV/a)</option>
                                <option value="Pembina Tingkat I">Pembina Tingkat I (IV/b)</option>
                                <option value="Pembina Utama Muda">Pembina Utama Muda (IV/c)</option>
                                <option value="Pembina Utama Madya">Pembina Utama Madya (IV/d)</option>
                                <option value="Pembina Utama">Pembina Utama (IV/e)</option>
                            </select>
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
    <script src="{{ asset('js/backend/kepangkatan/index.js') }}"></script>
@endpush

@extends('backend.app')

@section('content')

<div class="row" style="margin-top: -200px;">

    <div class="col-md-12 text-white">

        <div class="row">

            <div class="col-12 col-xl-8 mb-xl-0">
                <h3 class="font-weight-bold">Data Angka Kredit</h3>
            </div>

        </div>

    </div>

</div>


<div class="row">

    <div class="col-12 mt-4">

        <div class="card w-100">

            <div class="card-body">

                <button type="button"
                    class="btn btn-primary btn-md mb-4 d-none d-md-inline-block"
                    data-toggle="modal"
                    data-target="#modal">

                    Tambah

                </button>


                <div class="table-responsive">

                    <table id="myTable"
                        class="table table-striped"
                        style="width: 100%;">

                        <thead class="bg-info text-white">

                            <tr>

                                <th width="5%">No</th>

                                <th>Nama</th>

                                <th>No SK / Tgl SK</th>

                                <th>Mulai</th>

                                <th>Selesai</th>

                                <th>Kredit Utama</th>

                                <th>Kredit Penunjang</th>

                                <th>Total Kredit</th>

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

<div class="modal fade"
    id="modal"
    tabindex="-1"
    aria-labelledby="exampleModalLabel"
    aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <form id="form">

                <div class="modal-header p-3">

                    <h5 class="modal-title m-2"
                        id="exampleModalLabel">

                        Angka Kredit Form

                    </h5>

                    <button type="button"
                        class="close"
                        data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>


                <div class="modal-body">

                    <div id="respon_error"
                        class="text-danger mb-4">
                    </div>

                    <input type="hidden"
                        name="id"
                        id="id">


                    {{-- USER --}}

                    <div class="form-group">

                        <label>
                            User
                            <sup class="text-danger">*</sup>
                        </label>

                        <select name="user_id"
                            id="user_id"
                            class="form-control form-control-sm"
                            required>

                            <option value="">
                                PILIH USER
                            </option>

                            @php

                                $users = DB::table('users')
                                    ->where('role', '!=', 'Admin');

                                if (Auth::user()->role == 'Admin') {

                                    $users = $users->get();

                                } else {

                                    $users = $users
                                        ->where('id', Auth::id())
                                        ->get();

                                }

                            @endphp


                            @foreach ($users as $item)

                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- NO SK --}}

                    <div class="form-group">

                        <label>
                            No SK
                            <sup class="text-danger">*</sup>
                        </label>

                        <input type="text"
                            name="no_sk"
                            id="no_sk"
                            class="form-control form-control-sm"
                            placeholder="Contoh: KP.407/1/17/POLTEKBANG.MDN/2025"
                            required>

                    </div>


                    {{-- TGL SK --}}

                    <div class="form-group">

                        <label>
                            Tanggal SK
                            <sup class="text-danger">*</sup>
                        </label>

                        <input type="date"
                            name="tgl_sk"
                            id="tgl_sk"
                            class="form-control form-control-sm"
                            required>

                    </div>


                    <div class="form-group">

                        <label>
                            Tanggal Mulai
                            <sup class="text-danger">*</sup>
                        </label>

                        <input type="date"
                            name="tgl_mulai"
                            id="tgl_mulai"
                            class="form-control form-control-sm"
                            required>

                    </div>

                    <div class="form-group">

                        <label>
                            Tanggal Selesai
                            <sup class="text-danger">*</sup>
                        </label>

                        <input type="date"
                            name="tgl_selesai"
                            id="tgl_selesai"
                            class="form-control form-control-sm"
                            required>

                    </div>


                    {{-- KREDIT UTAMA --}}

                    <div class="form-group">

                        <label>
                            Kredit Utama
                        </label>

                        <input type="number"
                            name="kredit_utama"
                            id="kredit_utama"
                            class="form-control form-control-sm"
                            step="0.001"
                            min="0"
                            placeholder="Contoh: 100.000">

                    </div>


                    {{-- KREDIT PENUNJANG --}}

                    <div class="form-group">

                        <label>
                            Kredit Penunjang
                        </label>

                        <input type="number"
                            name="kredit_penunjang"
                            id="kredit_penunjang"
                            class="form-control form-control-sm"
                            step="0.001"
                            min="0"
                            placeholder="Contoh: 10.000">

                    </div>


                    {{-- TOTAL KREDIT --}}

                    <div class="form-group">

                        <label>
                            Total Kredit
                        </label>

                        <input type="number"
                            name="total_kredit"
                            id="total_kredit"
                            class="form-control form-control-sm"
                            step="0.001"
                            min="0"
                            placeholder="Contoh: 110.000">

                    </div>


                </div>


                <div class="modal-footer p-3">

                    <button type="button"
                        class="btn btn-danger btn-sm"
                        data-dismiss="modal">

                        Close

                    </button>

                    <button id="tombol_kirim"
                        class="btn btn-primary btn-sm">

                        Submit

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection


@push('script')

<script src="{{ asset('js/backend/angka_kredit/index.js') }}"></script>

@endpush
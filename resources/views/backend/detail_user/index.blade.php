@extends('backend.app')

@section('content')

    <form action="{{ url('store-detail-user') }}?id=" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row" style="margin-top: -200px;">
            <div class="col-md-12 text-white">
                <h3 class="font-weight-bold">Data Detail User</h3>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                {{-- ALERT SUCCESS --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- ALERT ERROR VALIDASI --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">

                        <strong>Terjadi kesalahan!</strong>

                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="row">

                    <input type="hidden" name="id" value="{{ request('id') }}">

                    {{-- FOTO --}}
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label>Foto</label>

                            
                            <input type="file" name="foto" class="form-control form-control-sm">
                            @if (!empty($data->foto))
                                <div class="mt-2">
                                    <img src="{{ asset('upload/detail-user/' . $data->foto) }}" width="200"
                                        class="img-thumbnail">
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- NIP --}}
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label>NIP</label>
                            <input type="text" name="nip" value="{{ old('nip', $data->nip ?? '') }}"
                                placeholder="Masukkan NIP" class="form-control form-control-sm">
                        </div>
                    </div>

                    {{-- NAMA --}}
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label>Nama</label>
                            <input type="text" name="nama" value="{{ old('nama', $data->nama ?? '') }}"
                                placeholder="Masukkan Nama" class="form-control form-control-sm">
                        </div>
                    </div>

                    {{-- JENIS KELAMIN --}}
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label>Jenis Kelamin</label>

                            <select name="jenis_kelamin" class="form-control form-control-sm">

                                <option value="">-- Pilih Jenis Kelamin --</option>

                                <option value="Laki-laki"
                                    {{ old('jenis_kelamin', $data->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki
                                </option>

                                <option value="Perempuan"
                                    {{ old('jenis_kelamin', $data->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan
                                </option>

                            </select>
                        </div>
                    </div>

                    {{-- TEMPAT LAHIR --}}
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label>Tempat Lahir</label>
                            <input type="text" name="tempat_lahir"
                                value="{{ old('tempat_lahir', $data->tempat_lahir ?? '') }}"
                                placeholder="Masukkan Tempat Lahir" class="form-control form-control-sm">
                        </div>
                    </div>

                    {{-- TANGGAL LAHIR --}}
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label>Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir"
                                value="{{ old('tanggal_lahir', $data->tanggal_lahir ?? '') }}"
                                class="form-control form-control-sm">
                        </div>
                    </div>

                    {{-- EMAIL --}}
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ old('email', $data->email ?? '') }}"
                                placeholder="Masukkan Email" class="form-control form-control-sm">
                        </div>
                    </div>

                    {{-- ALAMAT --}}
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label>Alamat</label>
                            <textarea name="alamat" rows="3" placeholder="Masukkan Alamat" class="form-control form-control-sm">{{ old('alamat', $data->alamat ?? '') }}</textarea>
                        </div>
                    </div>

                    {{-- NIK --}}
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label>NIK</label>
                            <input type="text" name="nik" value="{{ old('nik', $data->nik ?? '') }}"
                                placeholder="Masukkan NIK" class="form-control form-control-sm">
                        </div>
                    </div>

                    {{-- AGAMA --}}
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label>Agama</label>
                            <input type="text" name="agama" value="{{ old('agama', $data->agama ?? '') }}"
                                placeholder="Masukkan Agama" class="form-control form-control-sm">
                        </div>
                    </div>

                    {{-- KEWARGANEGARAAN --}}
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label>Kewarganegaraan</label>
                            <input type="text" name="kewarganegaraan"
                                value="{{ old('kewarganegaraan', $data->kewarganegaraan ?? '') }}"
                                placeholder="Masukkan Kewarganegaraan" class="form-control form-control-sm">
                        </div>
                    </div>

                    {{-- NOMOR SK --}}
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label>Nomor SK</label>
                            <input type="text" name="nomor_sk" value="{{ old('nomor_sk', $data->nomor_sk ?? '') }}"
                                placeholder="Masukkan Nomor SK" class="form-control form-control-sm">
                        </div>
                    </div>

                    {{-- TMT SK --}}
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label>TMT SK</label>
                            <input type="date" name="tmt_sk" value="{{ old('tmt_sk', $data->tmt_sk ?? '') }}"
                                class="form-control form-control-sm">
                        </div>
                    </div>

                    {{-- PANGKAT GOLONGAN --}}
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label>Pangkat Golongan</label>
                            <input type="text" name="pangkat_golongan"
                                value="{{ old('pangkat_golongan', $data->pangkat_golongan ?? '') }}"
                                placeholder="Masukkan Pangkat Golongan" class="form-control form-control-sm">
                        </div>
                    </div>

                    {{-- TANGGAL SK --}}
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label>Tanggal SK</label>
                            <input type="date" name="tanggal_sk"
                                value="{{ old('tanggal_sk', $data->tanggal_sk ?? '') }}"
                                class="form-control form-control-sm">
                        </div>
                    </div>

                    {{-- MASA KERJA TAHUN --}}
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label>Masa Kerja Tahun</label>
                            <input type="number" name="masa_kerja_tahun"
                                value="{{ old('masa_kerja_tahun', $data->masa_kerja_tahun ?? '') }}"
                                placeholder="Masukkan Tahun" class="form-control form-control-sm">
                        </div>
                    </div>

                    {{-- MASA KERJA BULAN --}}
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label>Masa Kerja Bulan</label>
                            <input type="number" name="masa_kerja_bulan"
                                value="{{ old('masa_kerja_bulan', $data->masa_kerja_bulan ?? '') }}"
                                placeholder="Masukkan Bulan" class="form-control form-control-sm">
                        </div>
                    </div>

                    {{-- STATUS KEPEGAWAIAN --}}
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label>Status Kepegawaian</label>
                            
                            <select name="status_kepegawaian" id="status_kepegawaian" class="form-control form-control-sm">
                                <option {{ $data->status_kepegawaian == 'PNS' ?? 'selected' }}>PNS</option>
                                <option {{ $data->status_kepegawaian == 'P3K' ?? 'selected' }}>P3K</option>
                            </select>
                        </div>
                    </div>

                    {{-- BUTTON --}}
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary btn-sm">
                            Simpan Data
                        </button>
                    </div>

                </div>

            </div>
        </div>

    </form>

@endsection

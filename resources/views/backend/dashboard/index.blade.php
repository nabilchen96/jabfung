@extends('backend.app')
@section('content')
    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <div class="row">
                <div class="col-12 col-xl-8 mb-xl-0">
                    <h3 class="font-weight-bold">Dashboard</h3>
                    <a href="{{ route('export.excel', ['page' => 'dashboard']) }}" class="btn btn-success btn-md page-action mt-2" title="Ekspor seluruh data yang dapat Anda akses">
                        Export
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

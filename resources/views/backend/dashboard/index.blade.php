@extends('backend.app')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/backend/dashboard.css') }}">
@endpush

@section('content')
    <div class="dashboard">
        <div class="row dashboard-heading">
            <div class="col-12 text-white">
                <h3 class="font-weight-bold mb-2">Dashboard</h3>
                <p class="mb-0">Ringkasan {{ $admin ? 'seluruh data kepegawaian' : 'data kepegawaian Anda' }}.</p>
            </div>
        </div>

        <div class="row mt-4">
            @foreach ($cards as $card)
                <div class="col-6 col-md-4 col-xl-2 mb-4">
                    <a href="{{ $card['url'] }}" class="card dashboard-card dashboard-stat h-100">
                        <div class="card-body">
                            <p class="dashboard-stat-label">{{ $card['label'] }}</p>
                            <div class="dashboard-stat-value">{{ number_format($card['count'], 0, ',', '.') }}</div>
                            <span class="dashboard-stat-link">Lihat data &rarr;</span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body">
                        <h4 class="dashboard-chart-title">Jenjang Pendidikan</h4>
                        <p class="text-muted">Jumlah riwayat pendidikan per jenjang, bukan pendidikan terakhir setiap pegawai.</p>
                        @if (array_sum($charts['education']['values']->all()) > 0)
                            <div class="dashboard-chart"><canvas id="educationChart" role="img" aria-label="Grafik jumlah riwayat pendidikan per jenjang"></canvas></div>
                            <ul class="dashboard-chart-summary" aria-label="Rincian jenjang pendidikan">
                                @foreach ($charts['education']['labels'] as $index => $label)
                                    <li><span>{{ $label }}</span><strong>{{ $charts['education']['values'][$index] }}</strong></li>
                                @endforeach
                            </ul>
                        @else
                            <div class="dashboard-empty">Belum ada riwayat pendidikan. Tambahkan data di halaman Pendidikan.</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body">
                        <h4 class="dashboard-chart-title">Diklat per Tahun</h4>
                        <p class="text-muted">Jumlah riwayat diklat berdasarkan kolom tahun, {{ $charts['years'][0] }}–{{ $charts['years'][4] }}.</p>
                        @if (array_sum($charts['training']) > 0)
                            <div class="dashboard-chart"><canvas id="trainingChart" role="img" aria-label="Grafik jumlah diklat lima tahun terakhir"></canvas></div>
                        @else
                            <div class="dashboard-empty">Belum ada diklat dengan tahun pada periode ini.</div>
                        @endif
                        <p class="dashboard-chart-note">Diklat tanpa tahun tidak termasuk dalam grafik.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card dashboard-card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-start">
                    <div>
                        <h4 class="dashboard-chart-title">Angka Kredit per Tahun</h4>
                        <p class="text-muted">Jumlah total kredit berdasarkan tahun tanggal mulai, {{ $charts['years'][0] }}–{{ $charts['years'][4] }}.</p>
                    </div>
                    <div class="dashboard-credit-total mb-3">
                        <span>Akumulasi seluruh catatan</span>
                        <strong>{{ number_format($totalCredits, 2, ',', '.') }}</strong>
                    </div>
                </div>
                @if ($hasCreditRecords)
                    <div class="dashboard-chart"><canvas id="creditChart" role="img" aria-label="Grafik angka kredit lima tahun terakhir"></canvas></div>
                @else
                    <div class="dashboard-empty">Belum ada angka kredit pada periode ini.</div>
                @endif
                <div class="table-responsive mt-3">
                    <table class="table dashboard-year-table">
                        <caption class="sr-only">Rincian statistik tahunan</caption>
                        <thead><tr><th>Tahun</th>@foreach ($charts['years'] as $year)<th>{{ $year }}</th>@endforeach</tr></thead>
                        <tbody>
                            <tr><th scope="row">Jumlah Diklat</th>@foreach ($charts['training'] as $count)<td>{{ $count }}</td>@endforeach</tr>
                            <tr><th scope="row">Angka Kredit</th>@foreach ($charts['credits'] as $credit)<td>{{ number_format($credit, 2, ',', '.') }}</td>@endforeach</tr>
                        </tbody>
                    </table>
                </div>
                <p class="dashboard-chart-note">Akumulasi menjumlahkan seluruh catatan dan dapat mencakup periode yang tumpang tindih.</p>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>window.dashboardCharts = {{ Illuminate\Support\Js::from($charts) }};</script>
    <script src="{{ asset('js/backend/dashboard/index.js') }}"></script>
@endpush

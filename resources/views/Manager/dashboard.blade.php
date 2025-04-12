@extends('Manager.templates_manager.header')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <nav class="navbar navbar-expand-lg navbar-light bg-light w-100">
                        <div class="container-fluid">
                            <h1 class="navbar-brand mb-0">Dashboard Manager</h1>
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item">
                                    <a href="{{ route('logout') }}" class="nav-link d-flex align-items-center">
                                        <i class="nav-icon far fa-circle text-danger me-1"></i>
                                        <span class="text-dark">Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Grafik Penjualan</h3>
                </div>
                <div class="card-body">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
            <hr>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Grafik Pembelian</h3>
                </div>
                <div class="card-body">
                    <canvas id="purchasingChart"></canvas>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="card">
                <h4 class="mt-4">Hasil Testing Login</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Status</th>
                            <th>Pesan Error</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loginTest as $test)
                            <tr>
                                <td>{{ $test->nama }}</td>
                                <td>{{ $test->password }}</td>
                                <td>
                                    @if ($test->status)
                                        <span class="badge badge-success">Berhasil</span>
                                    @else
                                        <span class="badge badge-danger">Gagal</span>
                                    @endif
                                </td>
                                <td>{{ $test->error_message ?? '-' }}</td>
                                <td>{{ $test->created_at->format('d-m-Y H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        <div class="d-flex justify-content-center mt-3">
            {{ $loginTest->appends(['transaksi_page' => request('transaksi_page')])->links('vendor/pagination/custom') }}
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // --- Grafik Penjualan ---
            var ctxSales = document.getElementById('salesChart').getContext('2d');
            var salesData = @json($penjualan);

            var labelsSales = salesData.map(data => data.bulan);
            var totalPendapatanSales = salesData.map(data => data.total);

            var salesChart = new Chart(ctxSales, {
                type: 'line',
                data: {
                    labels: labelsSales,
                    datasets: [{
                        label: 'Pendapatan per Bulan',
                        data: totalPendapatanSales,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // --- Grafik Pembelian ---
            var ctxPurchasing = document.getElementById('purchasingChart').getContext('2d');
            var purchasingData = @json($pembelian);

            var labelsPurchasing = purchasingData.map(data => data.bulan);
            var totalPendapatanPurchasing = purchasingData.map(data => data.total);

            var purchasingChart = new Chart(ctxPurchasing, {
                type: 'line',
                data: {
                    labels: labelsPurchasing,
                    datasets: [{
                        label: 'Pengeluaran per Bulan',
                        data: totalPendapatanPurchasing,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
    </script>
@endpush

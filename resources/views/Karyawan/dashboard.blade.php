@extends('Karyawan.templates_karyawan.header')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <nav class="navbar navbar-expand-lg navbar-light bg-light w-100">
                        <div class="container-fluid">
                            <h1 class="navbar-brand mb-0">DASHBOARD</h1>
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
            <div class="container ">
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">Transaksi Menunggu Persetujuan</h5>
                    </div>
                    <div class="card-body">
                        @if ($transaksi->isEmpty())
                            <p class="text-muted">Tidak ada transaksi yang menunggu persetujuan.</p>
                        @else
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. </th>
                                        <th>No Faktur</th>
                                        <th>Total Harga</th>
                                        <th>Metode Pembayaran</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaksi as $key => $t)
                                        <tr>
                                            <td>{{ $transaksi->firstItem() + $key }}</td>
                                            <td>{{ $t->no_faktur }}</td>
                                            <td>Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                                            <td>{{ $t->metode_pembayaran }}</td>
                                            <td>{{ $t->tanggal }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $transaksi->appends(['bahan_page' => request('bahan_page')])->links('vendor/pagination/custom') }}
                </div>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Stok Bahan Baku</h5>
                    </div>
                    <div class="card-body">
                        @if ($bahanBaku->isEmpty())
                            <p class="text-muted">Tidak ada data stok bahan baku.</p>
                        @else
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. </th>
                                        <th>Nama Bahan</th>
                                        <th>Stok</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bahanBaku as $key => $b)
                                        <tr>
                                            <td>{{ $bahanBaku->firstItem() + $key }}</td>
                                            <td>{{ $b->nama }}</td>
                                            <td>{{ $b->stok }}</td>
                                            <td>{{ $b->satuan }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
                <div class="p-4 bg-white shadow-lg rounded-lg">
                    <h2 class="text-lg font-semibold mb-2">Log Aktivitas</h2>
                    <div id="log-container" class="h-60 overflow-auto border p-2 bg-gray-100 rounded-lg"></div>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $bahanBaku->appends(['transaksi_page' => request('transaksi_page')])->links('vendor/pagination/custom') }}
                </div>
            </div>
        </section>
    </div>
@endsection
@push('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function fetchLogs() {
                fetch('/get-logs')
                    .then(response => response.json())
                    .then(data => {
                        let logContainer = document.getElementById("log-container");
                        logContainer.innerHTML = ""; // Bersihkan log lama

                        data.logs.forEach(log => {
                            let logElement = document.createElement("p");
                            logElement.textContent = log;
                            logElement.classList.add("text-sm", "text-gray-700");
                            logContainer.appendChild(logElement);
                        });

                        logContainer.scrollTop = logContainer.scrollHeight; // Auto-scroll ke bawah
                    })
                    .catch(error => console.error('Error fetching logs:', error));
            }

            fetchLogs(); // Panggil saat halaman pertama kali dimuat
            setInterval(fetchLogs, 10000); // Perbarui setiap 10 detik
        });
    </script>
@endpush

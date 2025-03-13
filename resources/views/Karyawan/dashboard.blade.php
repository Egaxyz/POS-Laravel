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
                <div class="d-flex justify-content-center mt-3">
                    {{ $bahanBaku->appends(['transaksi_page' => request('transaksi_page')])->links('vendor/pagination/custom') }}
                </div>
            </div>
        </section>
    </div>
@endsection

@extends('Manager.templates_manager.header')
@push('style')
    <style>
        @media print {

            .pagination,
            .btn-info,
            #btn-info,
            .card-header,
            .modal,
            .modal-backdrop {
                display: none !important;
            }



            /* Menyembunyikan kolom "Detail Pembelian" */
            th:nth-child(2),
            /* Header kolom */
            td:nth-child(2) {
                /* Isi kolom */
                display: none !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <nav class="navbar navbar-expand-lg navbar-light bg-light w-100">
                        <div class="container-fluid">
                            <h1 class="navbar-brand mb-0">Laporan Pembelian</h1>
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
        <div class="card">
            <div class="card-header d-flex justify-content-start align-items-center flex-wrap gap-2">
                <!-- Print Button -->
                <button id="printButton" class="btn btn-primary d-flex align-items-center gap-2" onclick="window.print()">
                    <i class="fas fa-print"></i>
                    <span>Print</span>
                </button>
                <a id="exportButton" href="{{ url('/manager/laporan-pembelian/pdf') }}"
                    class="btn btn-danger d-flex align-items-center gap-2">
                    <i class="fas fa-file-pdf"></i>
                    <span>Export PDF</span>
                </a>
                <a id="exportExcelButton" href="{{ route('manager.pembelian-excel') }}"
                    class="btn btn-success d-flex align-items-center gap-2">
                    <i class="fas fa-file-excel"></i>
                    <span>Export Excel</span>
                </a>
            </div>
            <div class="card-body">
                <h2 id="printTitle" style="text-align: center; display: none;">Data Pembelian</h2>

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Total Harga</th>
                            <th id="btn-info">Detail Pembelian</th>
                            <th>Status Pembelian</th>
                            <th>Tanggal Pembelian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembelian as $data)
                            <tr>
                                <td>Rp. {{ number_format($data->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-toggle="modal"
                                        data-target="#detailModal-{{ $data->id }}">
                                        Lihat Detail
                                    </button>
                                </td>
                                <td>
                                    <span
                                        class=" p-2 rounded rounded-pill {{ $data->status_pembelian == 'Selesai' ? 'bg-success' : 'bg-warning' }}">
                                        {{ $data->status_pembelian == 'Selesai' ? 'Selesai' : 'Proses' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $data->tanggal_pembelian }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @foreach ($pembelian as $data)
        <div class="modal fade" id="detailModal-{{ $data->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Detail Pembelian</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            &times;
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Nama Bahan</th>
                                        <th>Jumlah</th>
                                        <th>Harga Satuan</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->details as $detail)
                                        <tr>
                                            <td>{{ $detail->bahanBaku->nama ?? 'Data tidak tersedia' }}</td>
                                            <td>{{ $detail->jumlah }}</td>
                                            <td>Rp. {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                            <td>Rp.
                                                {{ number_format($detail->jumlah * $detail->harga_satuan, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <div class="d-flex justify-content-center mt-3">
        {{ $pembelian->links('vendor/pagination/custom') }}
    </div>
@endsection
@push('script')
    <script>
        window.onbeforeprint = function() {
            document.getElementById("printButton").style.display = "none";
            document.getElementById("exportButton").style.display = "none";
            document.getElementById("printTitle").style.display = "block"; // Menampilkan judul "Data Barang"
        };

        window.onafterprint = function() {
            document.getElementById("printButton").style.display = "inline-block";
            document.getElementById("exportButton").style.display = "inline-block";
            document.getElementById("printTitle").style.display = "none"; // Menyembunyikan judul setelah print
        };
    </script>
@endpush

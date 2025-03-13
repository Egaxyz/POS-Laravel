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
                            <h1 class="navbar-brand mb-0">Laporan Bahan Baku</h1>
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
            <div class="card-header">
                <button id="printButton" class="btn btn-primary" onclick="window.print()">Print</button>
                <a id="exportButton" href="{{ url('/manager/laporan-bahan/pdf') }}" class="btn btn-danger">Export
                    PDF</a>
            </div>
            <div class="card-body">
                <h2 id="printTitle" style="text-align: center; display: none;">Data Bahan Baku</h2>

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nama Perusahaan</th>
                            <th>Nama</th>
                            <th>Stok</th>
                            <th>Satuan</th>
                            <th>Harga Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bahan as $data)
                            <tr>
                                <td>{{ $data->supplier->nama_perusahaan ?? '-' }}</td>
                                <td>{{ $data->nama }}</td>
                                <td>{{ $data->stok }}</td>
                                <td>{{ $data->satuan }}</td>
                                <td>{{ $data->harga_satuan }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $bahan->links('vendor/pagination/custom') }}
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            let table = $('#example1').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": true,
                "pageLength": 5,
                "language": {
                    "paginate": {
                        "previous": "",
                        "next": "",
                        "number": ""
                    }
                }
            });
        });

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

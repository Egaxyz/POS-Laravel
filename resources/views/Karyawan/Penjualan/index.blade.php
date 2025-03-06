@extends('Karyawan.templates_karyawan.header')
@push('style')
    <style>
        .pagination .page-link {
            padding: 6px 10px;
            font-size: 14px;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }

        .pagination .page-item .page-link:hover {
            background-color: #0056b3;
            color: white;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <nav class="navbar navbar-expand-lg navbar-light bg-light w-100">
                        <div class="container-fluid">
                            <h1 class="navbar-brand mb-0">Daftar Penjualan</h1>
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
                <button class="btn bg-primary" type="button" data-toggle="modal" data-target="#formModal"><i
                        class="fas fa-plus-square"></i> Tambah Data Penjualan</button>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                            &times;</button>
                        <h5><i class="icon fas fa-check"></i>Sukses!</h5>
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-ban"></i>Data Gagal Disimpan!</h5>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th>Jumlah</th>
                            <th>Total Harga</th>
                            <th>Tanggal</th>
                            <th>Metode Pembayaran</th>
                            <th>Status Penjualan</th>
                            <th>Menu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penjualan as $data)
                            <tr>
                                <td>{{ $data->detail->jumlah }}</td>
                                <td>{{ $data->total_harga }}</td>
                                <td>{{ $data->tanggal }}</td>
                                <td>
                                    @if ($data->status_penjualan == 'Proses')
                                        <span class="badge badge-warning py-2 px-3 fs-9 rounded-pill">Proses</span>
                                    @elseif($data->status_penjualan == 'Selesai')
                                        <span class="badge badge-success py-2 px-3 fs-9 rounded-pill">Selesai</span>
                                    @else
                                        <span class="badge badge-danger py-2 px-3 fs-9 rounded-pill">Gagal</span>
                                    @endif
                                </td>
                                <td>{{ $data->metode_pembayaran }}</td>
                                <td>{{ $data->Aksi }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    <nav>
                        <ul class="pagination pagination-sm">
                            {{-- {{ $penjualan->links('pagination::bootstrap-4') }} --}}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    @include('Karyawan/Penjualan/modals')
@endsection

@push('script')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            let table = $('#example1').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": false, // Menghilangkan "Showing entries"
                "autoWidth": false,
                "responsive": true,
                "pageLength": 5,
                "language": {
                    "paginate": {
                        "previous": "",
                        "next": ""
                        "number": ""
                    }
                }
            });
        });

        $('#formModal').on('show.bs.modal', function(e) {
            const btn = $(e.relatedTarget);
            const mode = btn.data('mode');
            const modal = $(this);

            if (mode == 'edit') {
                modal.find('.modal-title').text('Edit Data Penjualan');
            } else {
                modal.find('.modal-title').text('Input Data Penjualan');
            }
        });
    </script>
@endpush

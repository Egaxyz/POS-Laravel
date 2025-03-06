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
                            <h1 class="navbar-brand mb-0">Daftar Pembelian</h1>
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
                        class="fas fa-plus-square"></i> Tambah Data Pembelian</button>
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
                            <th>Supplier</th>
                            <th>Tanggal</th>
                            <th>Total Harga</th>
                            <th>Detail</th>
                            <th>Status Pembelian</th>
                            <th>Menu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembelian as $data)
                            <tr>
                                <td>{{ $data->supplier->nama_perusahaan }}</td>
                                <td>{{ $data->tanggal_pembelian }}</td>
                                <td>Rp. {{ number_format($data->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-toggle="modal"
                                        data-target="#detailModal-{{ $data->id }}">
                                        Lihat Detail
                                    </button>
                                </td>
                                <td>
                                    @if ($data->status_pembelian == 'Pending')
                                        <span class="badge badge-warning py-2 px-3 fs-9 rounded-pill">Pending</span>
                                    @elseif($data->status_pembelian == 'Selesai')
                                        <span class="badge badge-success py-2 px-3 fs-9 rounded-pill">Selesai</span>
                                    @else
                                        <span class="badge badge-danger py-2 px-3 fs-9 rounded-pill">Gagal</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($data->status_pembelian == 'Pending')
                                        <form action="{{ url('/karyawan/pembelian/selesai', $data->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-primary">Selesai</button>
                                        </form>
                                        <form action="{{ url('/karyawan/pembelian/batalkan', $data->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-warning">Batalkan</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>

                            <!-- Modal for Detail Pembelian -->
                            <div class="modal fade" id="detailModal-{{ $data->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="detailModalLabel-{{ $data->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="detailModalLabel-{{ $data->id }}">Detail
                                                Pembelian</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <ul class="list-unstyled">
                                                <li><strong>Nama Bahan:</strong>
                                                    {{ implode(', ', $data->details->map(fn($detail) => $detail->bahanBaku->nama ?? 'Data tidak tersedia')->toArray()) }}
                                                </li>
                                                <li><strong>Jumlah:</strong>
                                                    {{ implode(', ', $data->details->map(fn($detail) => $detail->jumlah ?? 'Data tidak tersedia')->toArray()) }}
                                                </li>
                                                <li><strong>Harga Satuan:</strong>
                                                    {{ implode(', ', $data->details->map(fn($detail) => is_numeric($detail->harga_satuan) ? number_format($detail->harga_satuan, 0, ',', '.') : 'Data tidak tersedia')->toArray()) }}
                                                </li>
                                                <li><strong>Total Harga:</strong>
                                                    {{ implode(', ', $data->details->map(fn($detail) => is_numeric($detail->harga_satuan) && is_numeric($detail->jumlah) ? number_format($detail->harga_satuan * $detail->jumlah, 0, ',', '.') : 'Data tidak tersedia')->toArray()) }}
                                                </li>
                                                <li><strong>Total Keseluruhan:</strong> Rp
                                                    {{ is_numeric($data->total_harga ?? null) ? number_format((float) $data->total_harga, 0, ',', '.') : 'Data tidak tersedia' }}
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-center mt-3">
                    <nav>
                        <ul class="pagination pagination-sm">
                            {{ $pembelian->links('pagination::bootstrap-4') }}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    @include('Karyawan/Pembelian/modals')
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
                "info": false,
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
                modal.find('.modal-title').text('Edit Data Pembelian');
            } else {
                modal.find('.modal-title').text('Input Data Pembelian');
            }
        });
    </script>
@endpush

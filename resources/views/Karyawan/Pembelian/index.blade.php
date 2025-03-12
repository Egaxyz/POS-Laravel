@extends('Karyawan.templates_karyawan.header')
@push('style')
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
                                    @if ($data->status_pembelian == 'Pending')
                                        <form action="{{ url('/karyawan/pembelian/selesai', $data->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ url('/karyawan/pembelian/batalkan', $data->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Modal for Detail Pembelian -->
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
    </div>
    <div class="d-flex justify-content-center mt-3">
        {{ $pembelian->links('vendor/pagination/custom') }}
    </div>
    @include('Karyawan/Pembelian/modals')
@endsection

@push('script')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
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

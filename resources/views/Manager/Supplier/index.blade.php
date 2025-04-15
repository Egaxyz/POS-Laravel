@extends('Manager.templates_manager.header')
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
                            <h1 class="navbar-brand mb-0">Daftar Supplier</h1>
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
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <button class="btn btn-primary d-flex align-items-center gap-2" type="button" data-toggle="modal"
                    data-target="#formModal">
                    <i class="fas fa-plus-square"></i>
                    <span>Tambah Data Supplier</span>
                </button>

                <form action="{{ route('supplier.import') }}" method="POST" enctype="multipart/form-data"
                    class="d-flex align-items-center">
                    @csrf
                    <input type="file" name="file" class="form-control-file">
                    <button type="submit" class="btn btn-info d-flex align-items-center gap-1">
                        <i class="fas fa-file-import"></i>
                        <span>Import</span>
                    </button>
                </form>
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
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                            x</button>
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
                            <th>Nama Perusahaan</th>
                            <th>Kontak</th>
                            <th>Alamat</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Menu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($supplier as $data)
                            <tr>
                                <td>{{ $data->nama_perusahaan }}</td>
                                <td>{{ $data->kontak }}</td>
                                <td>{{ $data->alamat }}</td>
                                <td>{{ $data->email }}</td>
                                <td>{{ $data->status }}</td>
                                <td>
                                    <button class="btn btn-success" type="button" data-toggle="modal"
                                        data-target="#formModal" data-mode="edit" data-id="{{ $data->id }}"
                                        data-nama="{{ $data->nama_perusahaan }}" data-kontak="{{ $data->kontak }}"
                                        data-alamat="{{ $data->alamat }}" data-email="{{ $data->email }}"
                                        data-status="{{ $data->status }}">Edit</button>
                                    <button class="btn btn-danger" type="button" data-toggle="modal"
                                        data-target="#deleteModal" data-id="{{ $data->id }}">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus suppliler ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center mt-3">
        {{ $supplier->links('vendor/pagination/custom') }}
    </div>
    @include('Manager/Supplier/modals')
@endsection

@push('script')
    <script>
        $('#formModal').on('show.bs.modal', function(e) {
            const btn = $(e.relatedTarget);
            console.log(btn.data());
            const mode = btn.data('mode');
            const id = btn.data('id');
            const nama_perusahaan = btn.data('nama');
            const kontak = btn.data('kontak');
            const alamat = btn.data('alamat');
            const email = btn.data('email');
            const status = btn.data('status');
            const modal = $(this);

            if (mode == 'edit') {
                modal.find('.modal-title').text('Edit Data Supplier');
                modal.find('#nama_perusahaan').val(nama_perusahaan);
                modal.find('#kontak').val(kontak);
                modal.find('#alamat').val(alamat)
                modal.find('#email').val(email)
                modal.find('#status').val(status)
                modal.find('.modal-body form').attr('action', '{{ url('/manager/supplier') }}/' +
                    id);
                modal.find('#method').html('@method('PATCH')');
            } else {
                modal.find('.modal-title').text('Input Data Supplier');
                modal.find('#nama_perusahaan').val('');
                modal.find('#alamat').val('');
                modal.find('#email').val('');
                modal.find('#kontak').val('');
                modal.find('#status').val('');
                modal.find('#method').html('');
                modal.find('.modal-body form').attr('action',
                    '{{ url('/manager/supplier') }}');

            }
        });

        $(document).on('click', '[data-toggle="modal"][data-target="#deleteModal"]', function() {
            var userId = $(this).data('id');
            $('#deleteForm').attr('action', '/manager/supplier/' + userId);
        });
    </script>
@endpush

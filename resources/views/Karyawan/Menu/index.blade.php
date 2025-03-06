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
                            <h1 class="navbar-brand">Daftar Menu</h1>
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
                        class="fas fa-plus-square"></i> Tambah Data Menu</button>
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
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Gambar</th>
                            <th>Menu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($menu as $data)
                            <tr>
                                <td>{{ $data->nama_makanan }}</td>
                                <td>{{ $data->harga }}</td>
                                <td>{{ $data->stok }}</td>
                                <td>{{ $data->kategori }}</td>
                                <td>{{ $data->deskripsi }}</td>
                                <td><img src="{{ asset('storage/menu-image/' . $data->gambar) }}"
                                        alt="{{ $data->gambar }}" height="150"></td>
                                <td>
                                    <button class="btn btn-success" type="button" data-toggle="modal"
                                        data-target="#formModal" data-mode="edit" data-id="{{ $data->id }}"
                                        data-nama="{{ $data->nama_makanan }}" data-harga="{{ $data->harga }}"
                                        data-stok="{{ $data->stok }}" data-kategori="{{ $data->kategori }}"
                                        data-deskripsi="{{ $data->deskripsi }}"
                                        data-gambar="{{ $data->gambar }}">Edit</button>
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
                    Apakah Anda yakin ingin menghapus Menu ini?
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
    @include('Karyawan/Menu/modals')
@endsection

@push('script')
    <script>
        $('#formModal').on('show.bs.modal', function(e) {
            const btn = $(e.relatedTarget);
            const mode = btn.data('mode');
            const id = btn.data('id');
            const nama_makanan = btn.data('nama');
            const harga = btn.data('harga');
            const stok = btn.data('stok');
            const kategori = btn.data('kategori');
            const deskripsi = btn.data('deskripsi');
            const gambar = btn.data('gambar');
            const modal = $(this);

            if (mode == 'edit') {
                modal.find('.modal-title').text('Edit Data Menu');
                modal.find('#nama_makanan').val(nama_makanan);
                modal.find('#harga').val(harga);
                modal.find('#stok').val(stok);
                modal.find('#kategori').val(kategori);
                modal.find('#deskripsi').val(deskripsi);
                modal.find('#old_image').val(gambar); // Set the old image name
                const imageUrl = '{{ asset('storage/menu-image') }}/' + gambar;
                modal.find('.img-preview').attr('src', imageUrl).show(); // Show the current image
                modal.find('#gambar').val(''); // Reset the file input value
                modal.find('.modal-body form').attr('action', '{{ url('/karyawan/menu') }}/' + id);
                modal.find('#method').html('@method('PATCH')');
            } else {
                modal.find('.modal-title').text('Input Data Menu');
                modal.find('#nama_makanan').val('');
                modal.find('#stok').val('');
                modal.find('#kategori').val('');
                modal.find('#harga').val('');
                modal.find('#deskripsi').val('');
                modal.find('#gambar').val('');
                modal.find('#old_image').val(''); // Clear the old image name for new entries
                modal.find('.img-preview').attr('src', '').hide(); // Hide the image preview
                modal.find('#method').html('');
                modal.find('.modal-body form').attr('action', '{{ url('/karyawan/menu') }}');
            }
        });

        $(document).on('click', '[data-toggle="modal"][data-target="#deleteModal"]', function() {
            var userId = $(this).data('id');
            $('#deleteForm').attr('action', '/karyawan/menu/' + userId);
        });
    </script>
@endpush

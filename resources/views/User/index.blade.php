@extends('templates.header')
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
                    <div class="col-sm-6">
                        <h1>Daftar Karyawan</h1>
                    </div>
                </div>
            </div>
        </section>

        <div class="card">
            <div class="card-header">
                <button class="btn bg-primary" type="button" data-toggle="modal" data-target="#formModal"><i
                        class="fas fa-plus-square"></i> Tambah Data Karyawan</button>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="remove">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
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
                            <th>Role</th>
                            <th>Status</th>
                            <th>No Hp</th>
                            <th>Menu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($user as $akun)
                            <tr>
                                <td>{{ $akun->nama }}</td>
                                <td>{{ $akun->role }}</td>
                                <td>{{ $akun->status }}</td>
                                <td>{{ $akun->no_hp }}</td>
                                <td>
                                    <button class="btn btn-success" type="button" data-toggle="modal"
                                        data-target="#formModal" data-mode="edit" data-id="{{ $akun->id }}"
                                        data-nama="{{ $akun->nama }}" data-role="{{ $akun->role }}"
                                        data-status="{{ $akun->status }}" data-hp="{{ $akun->no_hp }}">Edit</button>
                                    <button class="btn btn-danger" type="button" data-toggle="modal"
                                        data-target="#deleteModal" data-id="{{ $akun->id }}">Delete</button>
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
                    Apakah Anda yakin ingin menghapus karyawan ini?
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
    @include('User/modals')
@endsection

@push('script')
    <script>
        $('#formModal').on('show.bs.modal', function(e) {
            const btn = $(e.relatedTarget);
            console.log(btn.data());
            const mode = btn.data('mode');
            const id = btn.data('id');
            const nama = btn.data('nama');
            const role = btn.data('role');
            const status = btn.data('status');
            const no_hp = btn.data('hp');
            const modal = $(this);

            if (mode == 'edit') {
                modal.find('.modal-title').text('Edit Data Karyawan');
                modal.find('#nama').val(nama);
                modal.find('#role').val(role);
                modal.find('#status').val(status)
                modal.find('#no_hp').val(no_hp)
                modal.find('.modal-body form').attr('action', '{{ url('/user') }}/' +
                    id);
                modal.find('#method').html('@method('PATCH')');
                modal.find('#passwordField').hide();
                modal.find('#password').removeAttr('required');
            } else {
                modal.find('.modal-title').text('Input Data Karyawan');
                modal.find('#nama').val('');
                modal.find('#password').val('');
                modal.find('#role').val('');
                modal.find('#status').val('');
                modal.find('#no_hp').val('');
                modal.find('#method').html('');
                modal.find('.modal-body form').attr('action',
                    '{{ url('/user') }}');

                modal.find('#passwordField').show();
                modal.find('#password').attr('required', true);
            }
        });

        $(document).on('click', '[data-toggle="modal"][data-target="#deleteModal"]', function() {
            var userId = $(this).data('id');
            $('#deleteForm').attr('action', '/user/' + userId);
        });
    </script>
@endpush

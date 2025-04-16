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
                            <h1 class="navbar-brand mb-0">Test</h1>
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
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <button class="btn btn-primary d-flex align-items-center gap-2" type="button" data-toggle="modal"
                data-target="#formModal">
                <i class="fas fa-plus-square"></i>
                <span>Tambah Data</span>
            </button>


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
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Status</th>
                        <th>Jam Pulang</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($user as $absen)
                        <tr>
                            <td>{{ $absen->user->nama ?? 'Karyawan Tidak Ditemukan' }}</td>
                            <td>{{ $absen->tanggal }}</td>
                            <td>{{ $absen->waktu_masuk }}</td>
                            <td>
                                @if ($absen->status == 'hadir')
                                    <span class="badge badge-success">Hadir</span>
                                @elseif($absen->status == 'sakit')
                                    <span class="badge badge-danger">Sakit</span>
                                @else
                                    <span class="badge badge-info">Cuti</span>
                                @endif
                            </td>
                            <td>{{ $absen->waktu_pulang ?? '-' }}</td>
                            <td>
                                <button class="btn btn-success" type="button" data-toggle="modal" data-target="#formModal"
                                    data-mode="edit" data-id="{{ $absen->id }}" data-nama="{{ $absen->user->nama }}"
                                    data-tanggal="{{ $absen->tanggal }}" data-status="{{ $absen->status }}"
                                    data-masuk="{{ $absen->waktu_masuk }}"
                                    data-pulang="{{ $absen->waktu_pulang }}">Edit</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
                    Apakah Anda yakin ingin menghapus absensi karyawan ini?
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
        {{ $user->links('vendor/pagination/custom') }}
    </div>
    @include('Manager.Absensi.modals')
@endsection

@push('script')
    <script>
        $('#formModal').on('show.bs.modal', function(e) {
            const btn = $(e.relatedTarget);
            console.log(btn.data());
            const mode = btn.data('mode');
            const id = btn.data('id');
            const user_id = btn.data('nama');
            const role = btn.data('role');
            const status = btn.data('status');
            const no_hp = btn.data('hp');
            const modal = $(this);

            if (mode == 'edit') {
                modal.find('.modal-title').text('Edit Data Absen');
                modal.find('#user_id').val(user_id);
                modal.find('#tanggal').val(tanggal);
                modal.find('#status').val(status)
                modal.find('#waktu_masuk').val(waktu_masuk)
                modal.find('#waktu_pulang').val(waktu_pulang)
                modal.find('.modal-body form').attr('action', '{{ url('/manager/user') }}/' +
                    id);
                modal.find('#method').html('@method('PATCH')');
            } else {
                modal.find('.modal-title').text('Input Data Absen');
                modal.find('#user_id').val('');
                modal.find('#tanggal').val('');
                modal.find('#waktu_masuk').val('');
                modal.find('#status').val('');
                modal.find('#waktu_pulang').val('');
                modal.find('#method').html('');
                modal.find('.modal-body form').attr('action',
                    '{{ url('/manager/user') }}');

                modal.find('#passwordField').show();
                modal.find('#password').attr('required', true);
            }
        });

        $(document).on('click', '[data-toggle="modal"][data-target="#deleteModal"]', function() {
            var userId = $(this).data('id');
            $('#deleteForm').attr('action', '/manager/absen/' + userId);
        });
    </script>
@endpush

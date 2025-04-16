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
                            <h1 class="navbar-brand mb-0">Daftar Kehadiran Pegawai</h1>
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

            <form action="{{ route('absen.import') }}" method="POST" enctype="multipart/form-data"
                class="d-flex align-items-center">
                @csrf
                <input type="file" name="file" class="form-control-file">
                <button type="submit" class="btn btn-info d-flex align-items-center gap-1">
                    <i class="fas fa-file-import"></i>
                    <span>Import</span>
                </button>
            </form>
            <form action="{{ route('absen.export') }}" method="POST" enctype="multipart/form-data"
                class="d-flex align-items-center">
                @csrf
                <button type="submit" class="btn btn-info d-flex align-items-center gap-1">
                    <i class="fas fa-file-export"></i>
                    <span>Export Excel</span>
                </button>
            </form>
            <a id="exportButton" href="{{ url('/manager/absensi/pdf') }}" class="btn btn-info">Export
                PDF</a>
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
                        <th>Keterangan</th>
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
                            <td id="waktu-pulang-{{ $absen->id }}">
                                @if ($absen->status == 'hadir' && !$absen->is_selesai)
                                    @php
                                        $waktuPulangDatetime = \Carbon\Carbon::parse(
                                            $absen->tanggal . ' ' . $absen->waktu_pulang,
                                        );
                                    @endphp
                                    @if (now() >= $waktuPulangDatetime)
                                        <button class="btn btn-success btn-selesai" data-id="{{ $absen->id }}">
                                            Selesai
                                        </button>
                                    @else
                                        {{ $absen->waktu_pulang ?? '-' }}
                                    @endif
                                @else
                                    {{ $absen->is_selesai ? 'Selesai' : $absen->waktu_pulang ?? '-' }}
                                @endif
                            </td>
                            <td>{{ $absen->keterangan ?? '-' }}</td>
                            <td>
                                <button class="btn btn-success" type="button" data-toggle="modal" data-mode="edit"
                                    data-target="#formModal" data-id="{{ $absen->id }}"
                                    data-user_id="{{ $absen->user_id }}"
                                    data-nama="{{ $absen->user->nama ?? 'Karyawan Tidak Ditemukan' }}"
                                    data-tanggal="{{ $absen->tanggal }}" data-status="{{ $absen->status }}"
                                    data-masuk="{{ $absen->waktu_masuk }}" data-pulang="{{ $absen->waktu_pulang }}"
                                    data-keterangan="{{ $absen->keterangan }}">Edit</button>
                                <button class="btn btn-danger" type="button" data-toggle="modal" data-target="#deleteModal"
                                    data-id="{{ $absen->id }}">Delete</button>
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
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#example1').DataTable({
                "paging": false,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": true,
            });
        })
        $('#formModal').on('show.bs.modal', function(e) {
            const btn = $(e.relatedTarget);
            console.log(btn.data());
            const mode = btn.data('mode');
            const id = btn.data('id');
            const user_id = btn.data('user_id');
            const tanggal = btn.data('tanggal');
            const waktu_masuk = btn.data('masuk');
            const status = btn.data('status');
            const waktu_pulang = btn.data('pulang');
            const keterangan = btn.data('keterangan');
            const modal = $(this);

            if (mode == 'edit') {
                modal.find('.modal-title').text('Edit Data Absen');
                modal.find('#user_id').val(user_id);
                modal.find('#tanggal').val(tanggal);
                modal.find('#status').val(status)
                modal.find('#waktu_masuk').val(waktu_masuk)
                modal.find('#waktu_pulang').val(waktu_pulang)
                modal.find('#keterangan').val(keterangan)
                modal.find('.modal-body form').attr('action', '{{ url('/manager/absen') }}/' +
                    id);
                modal.find('#method').html('@method('PATCH')');
            } else {
                modal.find('.modal-title').text('Input Data Absen');
                modal.find('#user_id').val('');
                modal.find('#tanggal').val('');
                modal.find('#waktu_masuk').val('');
                modal.find('#status').val('');
                modal.find('#waktu_pulang').val('');
                modal.find('#keterangan').val('');
                modal.find('#method').html('');
                modal.find('.modal-body form').attr('action',
                    '{{ url('/manager/absen') }}');

            }
        });

        $(document).on('click', '[data-toggle="modal"][data-target="#deleteModal"]', function() {
            var userId = $(this).data('id');
            $('#deleteForm').attr('action', '/manager/absen/' + userId);
        });

        function selesaiAbsen(id) {
            fetch(`/manager/absen/${id}/selesai`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update the display
                        const cell = document.querySelector(`#waktu-pulang-${id}`);
                        cell.innerText = 'Selesai';

                        // Remove the button
                        const buttons = document.querySelectorAll(`.btn-selesai[data-id="${id}"]`);
                        buttons.forEach(button => button.remove());

                        // Optional: show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Status absensi telah diupdate',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat mengupdate status',
                    });
                });
        }

        // Add event listeners to all selesai buttons
        document.querySelectorAll('.btn-selesai').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                selesaiAbsen(id);
            });
        });
    </script>
@endpush

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
                            <h1 class="navbar-brand mb-0">Daftar Pengajuan</h1>
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
                <a id="exportButton" href="{{ url('karyawan/pengajuan/pdf') }}" class="btn btn-danger">Export PDF</a>
                <a id="exportExcelButton" href="{{ route('karyawan.pengajuan-excel') }}" class="btn btn-success">Export
                    Excel</a>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
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
                <table id="pengajuanTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Tanggal</th>
                            <th>Status Pengajuan</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $pengajuan)
                            <tr>
                                <td>{{ $pengajuan->user->nama }}</td>
                                <td>{{ $pengajuan->nama_makanan }}</td>
                                <td>{{ $pengajuan->kategori }}</td>
                                <td>{{ $pengajuan->tanggal }}</td>
                                <td>
                                    @if ($pengajuan->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($pengajuan->status == 'disetujui')
                                        <span class="badge badge-success">Disetejui</span>
                                    @else
                                        <span class="badge badge-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>{{ $pengajuan->deskripsi }}</td>
                                <td>
                                    @if ($pengajuan->status == 'pending')
                                        <form id="form-selesai-{{ $pengajuan->id }}"
                                            action="{{ url('/karyawan/pengajuan/selesai', $pengajuan->id) }}"
                                            method="POST" enctype="multipart/form-data" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="file" name="gambar" id="gambar-{{ $pengajuan->id }}"
                                                class="d-none" required>
                                            <button type="button" onclick="confirmSelesai(event, {{ $pengajuan->id }})"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-check"></i> Selesaikan
                                            </button>
                                        </form>
                                        <form action="{{ url('/karyawan/pengajuan/batalkan', $pengajuan->id) }}"
                                            method="POST" style="display:inline;" onsubmit="return confirmDelete(event)">
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
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('assets') }}/plugins/jquery/jquery.min.js"></script>
    <script src="{{ asset('assets') }}/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('assets') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('assets') }}/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('assets') }}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#pengajuanTable').DataTable({
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                "paging": false,
                "ordering": true,
                "info": false,
                "searching": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print"]
            }).buttons().container().appendTo('#pengajuanTable_wrapper .col-md-6:eq(0)');
        });

        function confirmDelete(event) {
            event.preventDefault();
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data yang dibatalkan tidak dapat dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Batalkan!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
        }

        function confirmSelesai(event, id) {
            event.preventDefault();
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Menu akan bertambah sesuai dengan Pengajuan Member.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Selesaikan!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("form-selesai-" + id).submit();
                }
            });
        }
    </script>
@endpush

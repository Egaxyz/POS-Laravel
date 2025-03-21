@extends('Member.templates_member.header')

@section('content')
    <div class="content-wrapper">
        <!-- Navbar -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <nav class="navbar navbar-expand-lg navbar-light bg-light w-100 shadow-sm p-3">
                        <div class="container-fluid">
                            <h1 class="navbar-brand mb-0 fw-bold">DASHBOARD</h1>
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item">
                                    <a href="{{ route('logout') }}" class="nav-link d-flex align-items-center">
                                        <i class="nav-icon far fa-circle text-danger me-2"></i>
                                        <span class="text-dark fw-semibold">Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </section>

        <!-- Konten Dashboard -->
        <section class="content">
            <div class="container">
                <!-- Card Informasi User -->
                @if ($user)
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card shadow-sm mb-3">
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>Nama: </strong>{{ $user->nama ?? '-' }}<br>
                                        <strong>Role:</strong> {{ ucfirst($user->role ?? '-') }} <br>
                                        <strong>No HP:</strong> {{ $user->no_hp ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                <!-- Tabel Data Pengajuan -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover shadow-sm mt-3">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>Nama Menu</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $pengajuan)
                                <tr>
                                    <td>{{ $pengajuan->nama_makanan }}</td>
                                    <td>{{ $pengajuan->kategori }}</td>
                                    <td>{{ $pengajuan->tanggal }}</td>
                                    <td>
                                        @if ($pengajuan->status == 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($pengajuan->status == 'disetujui')
                                            <span class="badge bg-success">Disetujui</span>
                                        @else
                                            <span class="badge bg-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td>{{ $pengajuan->deskripsi }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection

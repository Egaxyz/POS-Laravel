<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Blank Page</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets') }}/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets') }}/dist/css/adminlte.min.css">
    @stack('style')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- Navbar -->

        <aside class="main-sidebar sidebar-dark-primary elevation-4 min-vh-100">
            <a href="{{ asset('assets') }}/index3.html" class="brand-link">
                <i class="fas fa-utensils brand-image elevation-3" style="font-size: 35px; opacity: 1   ;"></i>
                <span class="brand-text fw-bold fs-3">RestoPOS</span>
            </a>


            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="{{ asset('assets') }}/dist/img/20221031_080938_2.png" class="img-circle elevation-3"
                            alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">Ezio Joestar</a>
                    </div>
                </div>
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <li class="nav-item">
                            <a href="menu" class="nav-link">
                                <i class="nav-icon fas fa-book"></i>
                                <p>Menu</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="bahan-baku" class="nav-link">
                                <i class="nav-icon fas fa-box"></i>
                                <p>Bahan Baku</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-exchange-alt"></i>
                                <p>
                                    Transaksi
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="pembelian" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Pembelian</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="penjualan" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Penjualan</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- Main content -->
        <section class="content">
            @yield('content')
        </section>
        @include('Karyawan.templates_karyawan.footer')

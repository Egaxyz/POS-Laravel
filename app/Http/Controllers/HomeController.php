<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        return view('Home.index');
    } 


    public function superuserDashboard() {
    return view('SuperUser.dashboard');
}

public function managerDashboard() {
    return view('Manager.dashboard');
}

public function karyawanDashboard() {
    return view('Karyawan.dashboard');
}
}
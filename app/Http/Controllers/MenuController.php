<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
{
    $user = User::all();
    $menu = Menu::all();
    return view('Karyawan.Menu.index', compact('user', 'menu'));
}

    public function store(Request $request){

        $validated = $request->validate([
            'nama_makanan' => 'required',
            'harga' => 'required',
            'stok' => 'required',
            'kategori' => 'required',
            'deskripsi' =>'required',
            'gambar' =>'required',

        ]);
        $validated['user_id'] = 1;
        $user = User::all();
        $menu = Menu::create($validated);
        
        $user = auth()->user();
        
        if ($user->role == 'superuser') {
        return redirect()->route('superuser.menu')
                ->with('success', 'Menu Berhasil Ditambah');
        } elseif ($user->role == 'karyawan') {
            return redirect()->route('karyawan.menu')
                ->with('success', 'Menu Berhasil Ditambah');
       } else {
            abort(403, 'Unauthorized action.');
        }
    }
    public function update(Request $request, $id)
{
    $validated = $request->validate([
            'nama_makanan' => 'required',
            'harga' => 'required',
            'stok' => 'required',
            'kategori' => 'required',
            'deskripsi' =>'required',
            'gambar' =>'required',
    ]);
    $validated['user_id'] = 1;

    $menu = Menu::find($id);
    $menu->update($validated);

        $user = auth()->user();
    if ($user->role == 'superuser') {
        return redirect()->route('superuser.menu')
                ->with('success', 'Menu Berhasil Diperbarui');
        } elseif ($user->role == 'karyawan') {
            return redirect()->route('karyawan.menu')
                ->with('success', 'Menu Berhasil Diperbarui');
       } else {
            abort(403, 'Unauthorized action.');
        }
}


    public function destroy(Request $request, $id){
        $menu = Menu::find($id);

      $menu -> delete();

        $user = auth()->user();
    if ($user->role == 'superuser') {
        return redirect()->route('superuser.menu')
                ->with('success', 'Menu Berhasil Dihapus');
        } elseif ($user->role == 'karyawan') {
            return redirect()->route('karyawan.menu')
                ->with('success', 'Menu Berhasil Dihapus');
       } else {
            abort(403, 'Unauthorized action.');
        }
    }
}
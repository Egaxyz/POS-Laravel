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
    return view('Menu.index', compact('user', 'menu'));
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
        
        return redirect()->route('Menu', ['user'=>$user, 'menu'=>$menu])->with('success', 'Data Menu Berhasil Ditambahkan');
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

    return redirect()->route('Menu')->with('success', 'Data Menu Berhasil Diperbarui');
}


    public function destroy(Request $request, $id){
        $menu = Menu::find($id);

      $menu -> delete();

      return redirect()->route('Menu', ['menu'=>$menu])->with('success', 'Data Menu Berhasil Dihapus');
    }
}
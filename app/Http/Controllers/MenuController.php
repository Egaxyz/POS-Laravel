<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;
use Storage;

class MenuController extends Controller
{
    public function index()
{
    $menu = Menu::all();
    $user = auth()->user();
        
        if ($user->role == 'superuser') {
            return view('superuser/Menu/index', compact('user', 'menu'));
        } elseif($user->role == 'karyawan') {
            return view('karyawan/Menu/index', compact('user', 'menu'));
        }else {
            abort(403, 'Unauthorized action.');
        }
}

    public function store(MenuRequest $request)
{
     if ($request->hasFile('gambar')) {
        $image = $request->file('gambar');
        $filename = date('Y-m-d') . $image->getClientOriginalName();
        $path = 'menu-image/' . $filename;
        Storage::disk('public')->put($path, file_get_contents($image));
    } else {
        return back()->with('error', 'Gagal mengupload gambar. Pastikan file dipilih.');
    }
    $userId = auth()->id() ?? 1;

    $menu = [
        'nama_makanan' => $request->nama_makanan,
        'user_id' => $userId, // Gunakan user_id yang sudah ditentukan
        'harga' => $request->harga,
        'stok' => $request->stok,
        'kategori' => $request->kategori,
        'gambar' => $filename,
        'deskripsi' => $request->deskripsi,
    ];
    
    $menu = Menu::create($menu);

    $user = auth()->user();

    if ($user && $user->role == 'superuser') {
        return redirect()->route('superuser.menu')->with('success', 'Menu Berhasil Ditambah');
    } elseif ($user && $user->role == 'karyawan') {
        return redirect()->route('karyawan.menu')->with('success', 'Menu Berhasil Ditambah');
    } else {
        return redirect()->route('karyawan.menu')->with('success', 'Menu Berhasil Ditambah');
    }
}


    public function update(MenuRequest $request, $id)
{
    $menu = Menu::findOrFail($id);
    $filename = $menu->gambar; 

    if ($request->hasFile('gambar')) {
        if ($menu->gambar) {
            Storage::disk('public')->delete('menu-image/' . $menu->gambar);
        }
        
        $image = $request->file('gambar');
        $filename = date('Y-m-d') . $image->getClientOriginalName();
        $path = 'menu-image/' . $filename;
        Storage::disk('public')->put($path, file_get_contents($image));
    }

    $userId = auth()->id() ?? 1;

    $menu->update([
        'nama_makanan' => $request->nama_makanan,
        'user_id' => $userId,
        'harga' => $request->harga,
        'stok' => $request->stok,
        'kategori' => $request->kategori,
        'gambar' => $filename, 
        'deskripsi' => $request->deskripsi,
    ]);

    $user = auth()->user();

    if ($user && $user->role == 'superuser') {
        return redirect()->route('superuser.menu')->with('success', 'Menu Berhasil Diperbarui');
    } elseif ($user && $user->role == 'karyawan') {
        return redirect()->route('karyawan.menu')->with('success', 'Menu Berhasil Diperbarui');
    } else {
        return redirect()->route('karyawan.menu')->with('success', 'Menu Berhasil Diperbarui');
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
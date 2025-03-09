<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class MenuBahanBakuController extends Controller
{
public function store(Request $request)
{
    // Decode data dari input tersembunyi
    $data = json_decode($request->bahan, true);

    // Merge data ke dalam request
    $request->merge([
        'bahan_baku_id' => $data['bahan_baku_id'] ?? [],
        'jumlah' => $data['jumlah'] ?? [],
    ]);

    // Validasi data
    $request->validate([
        'menu_id' => 'required|exists:menu,id',
        'bahan_baku_id' => 'required|array',
        'bahan_baku_id.*' => 'exists:bahan_baku,id',
        'jumlah' => 'required|array',
        'jumlah.*' => 'numeric|min:1',
    ]);

    // Simpan data ke database
    $menu_id = $request->menu_id;
    $bahan_baku_ids = $request->bahan_baku_id;
    $jumlahs = $request->jumlah;

    foreach ($bahan_baku_ids as $index => $bahan_id) {
        DB::table('menu_bahan_baku')->insert([
            'menu_id' => $menu_id,
            'bahan_baku_id' => $bahan_id,
            'jumlah' => $jumlahs[$index],
        ]);
    }

    return redirect()->back()->with('success', 'Bahan baku berhasil ditambahkan ke menu.');
}

}
<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\DetailPembelian;
use App\Models\Pembelian;
use App\Models\Supplier;
use DB;
use Illuminate\Http\Request;
use Log;

class PembelianController extends Controller
{
    public function index()
    {
        $bahanBaku = BahanBaku::all();
        $supplier = Supplier::all();
        $pembelian = Pembelian::paginate(5);
        $user = auth()->user();

        if ($user->role == 'superuser') {
            return view('superuser/Pembelian/index', compact('pembelian', 'supplier', 'bahanBaku'));
        } elseif ($user->role == 'karyawan') {
            return view('karyawan/Pembelian/index', compact('pembelian', 'supplier', 'bahanBaku'));
        } else {
            abort(403, 'Unauthorized action.');
        }
    }
public function store(Request $request)
{
    $request->validate([
        'supplier_id' => 'required',
        'total_harga' => 'required',
        'items' => 'required|array',
        'items.*.bahan_baku_id' => 'required|integer',
        'items.*.jumlah' => 'required|integer|min:1',
        'items.*.harga_satuan' => 'required|numeric|min:1',
    ]);

    DB::beginTransaction();
    try {
        // Simpan data pembelian
        $pembelian = new Pembelian();
        $pembelian->user_id = auth()->id();
        $pembelian->supplier_id = $request->supplier_id;
        $pembelian->status_pembelian = 'pending';
        $pembelian->tanggal_pembelian = now();
        $pembelian->total_harga = $request->total_harga;
        $pembelian->save();

        // Simpan detail pembelian dan update stok bahan baku
        foreach ($request->items as $item) {
            $pembelianDetail = new DetailPembelian();
            $pembelianDetail->pembelian_id = $pembelian->id; // Ambil ID dari pembelian yang baru dibuat
            $pembelianDetail->bahan_baku_id = $item['bahan_baku_id'];
            $pembelianDetail->jumlah = $item['jumlah'];
            $pembelianDetail->harga_satuan = $item['harga_satuan'];
            $pembelianDetail->save();
            
            $bahanBaku = BahanBaku::find($item['bahan_baku_id']);
            if ($bahanBaku) {
                $bahanBaku->stok += $item['jumlah'];
                $bahanBaku->harga_satuan = $item['harga_satuan'];
                $bahanBaku->save();
            }

        }
        DB::commit(); // Commit transaction
        
                $user = auth()->user();
                 if ($user->role == 'superuser') {
                     return redirect()->route('superuser.pembelian')->with('success', 'Pembelian Berhasil Ditambahkan.');
                } elseif ($user->role == 'karyawan') {
                     return redirect()->route('karyawan.pembelian')->with('success', 'Pembelian Berhasil Ditambahkan.');
                } else {
                    abort(403, 'Unauthorized action.');
                }
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction on error
            Log::error($e->getMessage()); // Log the error for debugging
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan peminjaman',
                'error' => $e->getMessage(),
            ], 500);
        }  
    }
    
    public function selesai($id)
{
    $pembelian = Pembelian::findOrFail($id);
    $pembelian->status_pembelian = 'selesai';
    $pembelian->save();

    return redirect()->back()->with('success', 'Pembelian Telah Diselesaikan.');
}
    public function batal($id)
{
    $pembelian = Pembelian::findOrFail($id);
    $pembelian->status_pembelian = 'Gagal';
    $pembelian->save();

    return redirect()->back()->with('success', 'Pembelian Telah Digagalkan.');
}

    public function show($id)
    {
        $pembelian = Pembelian::with('details.bahanBaku')->findOrFail($id);
        $user = auth()->user();

        if ($user->role == 'superuser') {
            return view('superuser/Pembelian/show', compact('pembelian'));
        } elseif ($user->role == 'karyawan') {
            return view('karyawan/Pembelian/show', compact('pembelian'));
        } else {
            abort(403, 'Unauthorized action.');
        }
    }
}
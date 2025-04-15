<?php

namespace App\Http\Controllers;

use App\Exports\PembelianExport;
use App\Models\BahanBaku;
use App\Models\DetailPembelian;
use App\Models\Pembelian;
use App\Models\Supplier;
use Carbon\Carbon;
use DB;
use Log;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class PembelianController extends Controller
{
    public function index()
    {

        $bahanBaku = BahanBaku::all();
        $detail = DetailPembelian::all();
        $supplier = Supplier::all();
        $pembelian = Pembelian::with(['details.bahanBaku', 'supplier'])
            ->orderByRaw("CASE WHEN status_pembelian = 'Pending' THEN 0 ELSE 1 END")
            ->orderBy('tanggal_pembelian', 'desc')
            ->paginate(5);

        $user = auth()->user();

        if ($user->role == 'admin') {
            return view('admin/Pembelian/index', compact('pembelian', 'supplier', 'bahanBaku', 'detail'));
        } elseif ($user->role == 'karyawan') {
            return view('karyawan/Pembelian/index', compact('pembelian', 'supplier', 'bahanBaku', 'detail'));
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    public function store(Request $request)
    {

        $request->validate([
            'supplier_id' => 'required',
            'total_harga' => 'required|numeric',
            'items' => 'required|array',
            'items.*.bahan_baku_id' => 'required|integer',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            $pembelian = new Pembelian();
            $pembelian->user_id = auth()->id();
            $pembelian->supplier_id = $request->supplier_id;
            $pembelian->status_pembelian = 'pending';
            $pembelian->tanggal_pembelian = now();
            $pembelian->total_harga = $request->total_harga;
            $pembelian->save();

            foreach ($request->items as $item) {
                $pembelianDetail = new DetailPembelian();
                $pembelianDetail->pembelian_id = $pembelian->id;
                $pembelianDetail->bahan_baku_id = $item['bahan_baku_id'];
                $pembelianDetail->jumlah = $item['jumlah'];
                $pembelianDetail->harga_satuan = $item['harga_satuan'];
                $pembelianDetail->save();
            }

        
            DB::commit();

            Log::info('Pembelian berhasil disimpan', ['pembelian_id' => $pembelian->id]);

            $user = auth()->user();
            if ($user->role == 'admin') {
                return redirect()->route('admin.pembelian')->with('success', 'Pembelian Berhasil Ditambahkan.');
            } elseif ($user->role == 'karyawan') {
                return redirect()->route('karyawan.pembelian')->with('success', 'Pembelian Berhasil Ditambahkan.');
            } else {
                abort(403, 'Unauthorized action.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan pembelian', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan pembelian',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function selesai($id)
    {
        Log::info('Menyelesaikan pembelian', ['pembelian_id' => $id]);

        DB::beginTransaction();
        try {
            $pembelian = Pembelian::findOrFail($id);
            $pembelian->status_pembelian = 'selesai';
            $pembelian->save();

            foreach ($pembelian->details as $detail) {
                $bahanBaku = BahanBaku::find($detail->bahan_baku_id);
                if ($bahanBaku) {
                    $bahanBaku->stok += $detail->jumlah;
                    $bahanBaku->harga_satuan = $detail['harga_satuan'];
                    $bahanBaku->save();
                      // Update stok menu yang menggunakan bahan baku ini
                    $menuIds = DB::table('menu_bahan_baku')
                        ->where('bahan_baku_id', $bahanBaku->id)
                        ->pluck('menu_id');

                    foreach ($menuIds as $menuId) {
                        app(MenuController::class)->perbaruiStokMenu($menuId);
                    }
                }
            }

            DB::commit();

            Log::info('Pembelian selesai', ['pembelian_id' => $id]);
            return redirect()->back()->with('success', 'Pembelian Telah Diselesaikan dan Stok Bertambah.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyelesaikan pembelian', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyelesaikan pembelian.');
        }
    }

    public function batal($id)
    {
        Log::info('Membatalkan pembelian', ['pembelian_id' => $id]);

        $pembelian = Pembelian::findOrFail($id);
        $pembelian->status_pembelian = 'Gagal';
        $pembelian->save();

        return redirect()->back()->with('success', 'Pembelian Telah Digagalkan.');
    }

    public function show($id)
    {

        $pembelian = Pembelian::with('details.bahanBaku')->findOrFail($id);
        $user = auth()->user();

        if ($user->role == 'admin') {
            return view('admin/Pembelian/show', compact('pembelian'));
        } elseif ($user->role == 'karyawan') {
            return view('karyawan/Pembelian/show', compact('pembelian'));
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    public function laporan(Request $request)
    {
        $tahun = $request->input('tahun', Carbon::now()->format('Y'));


        $dataByYear = Pembelian::whereYear('tanggal_pembelian', $tahun)
            ->orderBy('tanggal_pembelian', 'desc')
            ->paginate(5);

        $user = auth()->user();

        if ($user->role == 'admin') {
            return view('admin.Laporan_Pembelian.index', ['pembelian' => $dataByYear, 'tahun' => $tahun]);
        } elseif ($user->role == 'manager') {
            return view('manager.Laporan_Pembelian.index', ['pembelian' => $dataByYear, 'tahun' => $tahun]);
        } else {
            abort(403, 'Anda tidak memiliki akses.');
        }
    }

    public function exportExcel(Excel $excel)
    {
        Log::info('Mengunduh laporan pembelian dalam format Excel');

        return $excel->download(new PembelianExport, 'Laporan_Pembelian.xlsx');
    }
}
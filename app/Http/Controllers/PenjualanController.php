<?php

namespace App\Http\Controllers;

use App\Exports\PenjualanExport;
use App\Models\DetailPenjualan;
use App\Models\Menu;
use App\Models\Penjualan;
use App\Models\BahanBaku;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;
use Maatwebsite\Excel\Excel;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use WindowsPrintConnectorTest;

class PenjualanController extends Controller
{
    public function index() {
    $menu = Menu::all();
    $penjualan = Penjualan::orderByRaw("CASE WHEN status_penjualan = 'Proses' THEN 0 ELSE 1 END")
        ->orderBy('tanggal', 'desc')
        ->paginate(5);

    $user = auth()->user();

    if ($user->role == 'admin') {
        return view('admin/Penjualan/index', compact('penjualan', 'menu'));
    } elseif ($user->role == 'karyawan') {
        return view('karyawan/Penjualan/index', compact('penjualan', 'menu'));
    } else {
        abort(403, 'Unauthorized action.');
    }
}

    public function store(Request $request)
{
    $request->validate([
        'metode_pembayaran' => 'required',
        'total_harga' => 'required|numeric',
        'menus' => 'required|json',
        'uang_diberikan' => 'required_if:metode_pembayaran,Cash|numeric|min:'.$request->total_harga
    ]);

    $menus = json_decode($request->menus, true);

    if (!is_array($menus) || empty($menus)) {
        return back()->withErrors(['menus' => 'Menu tidak boleh kosong']);
    }

    $tahun = Carbon::now()->format('Y');
// Ambil nomor faktur terakhir yang memiliki format yang benar
    $lastItem = Penjualan::where('no_faktur', 'LIKE', "PSN$tahun%")
    ->orderBy('no_faktur', 'desc')
    ->first();

    // Ambil angka terakhir dan tambah 1
    $lastNoUrut = $lastItem ? intval(substr($lastItem->no_faktur, -3)) : 0;
    $newNoUrut = str_pad($lastNoUrut + 1, 3, '0', STR_PAD_LEFT);

    // Buat nomor faktur baru
    $noFaktur = "PSN" . $tahun . $newNoUrut;

    $penjualan = new Penjualan();
    $penjualan->user_id = auth()->id();
    $penjualan->no_faktur = $noFaktur;
    $penjualan->tanggal = now();
    $penjualan->status_penjualan = 'Proses';
    $penjualan->metode_pembayaran = $request->metode_pembayaran;
    $penjualan->total_harga = $request->total_harga;
    $penjualan->save();

        // dd(($request->all()));


    \Log::info("Membuat transaksi baru dengan No Faktur: {$noFaktur}");
    foreach ($menus as $item) {
        // Cari menu berdasarkan nama makanan
        $menu = Menu::where('nama_makanan', $item['nama_makanan'])->first();

        if (!$menu) {
            return back()->withErrors(['menus' => "Menu '{$item['nama_makanan']}' tidak ditemukan."]);
        }

        DetailPenjualan::create([
            'penjualan_id' => $penjualan->id,
            'menu_id' => $menu->id, // Gunakan ID dari database
            'jumlah' => $item['jumlah'],
            'harga_satuan' => $item['harga'], // Use the correct column name
        ]);

    }
 if ($request->metode_pembayaran === 'Cash') {
        session(['uang_diberikan_' . $penjualan->id => $request->uang_diberikan]);
    }
    $user = auth()->user();
    if ($user->role == 'admin') {
        return redirect()->route('admin.penjualan')->with('success', 'Transaksi Berhaisl Dibuat ');
    } elseif ($user->role == 'karyawan') {
        return redirect()->route('karyawan.penjualan')->with('success', 'Transaksi Berhaisl Dibuat');
    } else {
        abort(403, 'Unauthorized action.');
    }
}

public function cetakStruk($id)
{
    $penjualan = Penjualan::findOrFail($id);
    $uangDiberikan = $penjualan->metode_pembayaran === 'Cash' 
                   ? session('uang_diberikan_' . $penjualan->id, 0)
                   : 0;

    $this->printReceipt($penjualan, $uangDiberikan);

    return view('penjualan.struk', compact('penjualan', 'uangDiberikan'));
}


public function selesai(Request $request, $id)
{
    DB::beginTransaction();
    try {
        $penjualan = Penjualan::findOrFail($id);
        if ($penjualan->status_penjualan === 'Selesai') {
            return redirect()->back()->with('error', 'Penjualan sudah selesai sebelumnya.');
        }

        foreach ($penjualan->details as $detail) {
            $menu = Menu::with('bahanBaku')->find($detail->menu_id);
            if (!$menu) {
                throw new \Exception("Menu tidak ditemukan.");
            }

            if ($menu->stok < $detail->jumlah) {
                throw new \Exception("Stok menu '{$menu->nama_makanan}' tidak mencukupi.");
            }
            $menu->decrement('stok', $detail->jumlah);

            foreach ($menu->bahanBaku as $bahan) {
                $stok_terpakai = $bahan->pivot->jumlah * $detail->jumlah;
                if ($bahan->stok < $stok_terpakai) {
                    throw new \Exception("Stok bahan '{$bahan->nama_bahan}' tidak mencukupi.");
                }
                $bahan->decrement('stok', $stok_terpakai);
            }
        }
        $penjualan->status_penjualan = 'Selesai';
        $penjualan->save();
  $uangDiberikan = $penjualan->metode_pembayaran === 'Cash' 
            ? session('uang_diberikan_'.$penjualan->id, 0)
            : 0;
            
        
        // Print receipt first
        $this->printReceipt($penjualan, $uangDiberikan);
        
        \Log::info("Menandai transaksi {$penjualan->no_faktur} sebagai selesai");
        DB::commit();

        // Return JSON response for frontend handling
        $user = auth()->user();
        $redirectRoute = $user->role == 'admin' ? 'admin.penjualan' : 'karyawan.penjualan';
        
        return redirect()->route($redirectRoute)->with('success', 'Transaksi berhasil diselesaikan');
        
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error($e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

private function printReceipt($penjualan, $uangDiberikan = 0)
{
    try {
        $connector = new WindowsPrintConnector("POS-58");
        $printer = new Printer($connector);

        if ($penjualan->metode_pembayaran === 'Cash') {
            $printer->text("Tunai:         Rp " . number_format($uangDiberikan, 0, ',', '.') . "\n");
            $kembalian = $uangDiberikan - ($penjualan->total_harga - ($penjualan->diskon ?? 0) * 1.1);
            $printer->text("Kembalian:     Rp " . number_format($kembalian, 0, ',', '.') . "\n");
        }

        $lokasi = env('RESTAURANT_LOCATION', 'Lokasi belum diatur');
        $diskon = $penjualan->diskon ?? 0;
        $pajak = ($penjualan->total_harga - $diskon) * 0.1;
        $total = $penjualan->total_harga - $diskon + $pajak;
        $kembalian = $uangDiberikan - $total;

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("RestoPos\n");
        $printer->text($lokasi . "\n");
        $printer->text("No Faktur: {$penjualan->no_faktur}\n");
        $printer->text("--------------------------------\n");

        $printer->setJustification(Printer::JUSTIFY_LEFT);
        foreach ($penjualan->details as $detail) {
            $nama = str_pad(substr($detail->menu->nama_makanan, 0, 15), 15);
            $jumlahHarga = $detail->jumlah . " x " . number_format($detail->harga_satuan, 0, ',', '.');
            $printer->text("{$nama} {$jumlahHarga}\n");
        }

        $printer->text("--------------------------------\n");
        $printer->text("Subtotal:      Rp " . number_format($penjualan->total_harga, 0, ',', '.') . "\n");
        $printer->text("Diskon:        Rp " . number_format($diskon, 0, ',', '.') . "\n");
        $printer->text("Pajak (10%):   Rp " . number_format($pajak, 0, ',', '.') . "\n");
        $printer->text("Metode:        {$penjualan->metode_pembayaran}\n");
        $printer->text("TOTAL:         Rp " . number_format($total, 0, ',', '.') . "\n");

        if ($penjualan->metode_pembayaran === 'Cash') {
            $printer->text("Tunai:         Rp " . number_format($uangDiberikan, 0, ',', '.') . "\n");
            $printer->text("Kembalian:     Rp " . number_format($kembalian, 0, ',', '.') . "\n");
        }

        $printer->text("--------------------------------\n");
        $printer->text(now()->format('d M Y') . "\n");
        $printer->text("Terima Kasih \n");

        $printer->cut();
        $printer->close();

    } catch (\Exception $e) {
        Log::error('Gagal mencetak struk: ' . $e->getMessage());
        throw new \Exception('Gagal mencetak struk: ' . $e->getMessage());
    }


return view('karyawan.Penjualan.struk', [
            'penjualan' => $penjualan,
            'uangDiberikan' => $uangDiberikan,
            'kembalian' => isset($kembalian) ? $kembalian : 0,
            'lokasi'=>$lokasi
        ]);
}


private function kurangiStokBahanBaku($menu_id, $jumlah_pesanan) {
$menu = Menu::find($menu_id);

if (!$menu || !$menu->bahanBaku()->exists()) return;

foreach ($menu->bahanBaku as $bahan) {
    $stok_terpakai = $bahan->pivot->jumlah * $jumlah_pesanan;

    if ($bahan->stok < $stok_terpakai) {
        throw new \Exception("Stok {$bahan->nama_bahan} tidak mencukupi untuk pesanan ini.");
    }

    // Kurangi stok bahan baku
    $bahan->decrement('stok', $stok_terpakai);
}
}
    public function batal($id)
{
    $penjualan = Penjualan::findOrFail($id);
    $penjualan->status_penjualan = 'Gagal';
    \Log::info("Transaksi {$penjualan->no_faktur} dibatalkan oleh user ID: " . auth()->id());

    $penjualan->save();

    return redirect()->back()->with('success', 'Pembelian Telah Digagalkan.');
}

public function laporan(Request $request)
{
    // Tahun default adalah tahun sekarang
    $tahun = $request->input('tahun', Carbon::now()->format('Y'));

    // Ambil data berdasarkan tahun dari kolom tanggal_pembelian
    $dataByYear = Penjualan::whereYear('tanggal', $tahun)
        ->orderBy('tanggal', 'desc')
        ->paginate(5);

    $user = auth()->user();

    if ($user->role == 'admin') {
        return view('admin.Laporan_Penjualan.index', ['penjualan' => $dataByYear, 'tahun' => $tahun]);
    } elseif ($user->role == 'manager') {
        return view('manager.Laporan_Penjualan.index', ['penjualan' => $dataByYear, 'tahun' => $tahun]);
    } else {
        abort(403, 'Anda tidak memiliki akses.');
    }
}
    

public function exportExcel(Excel $excel){
        return $excel->download(new PenjualanExport, 'Laporan_Penjualan.xlsx');
    }
}
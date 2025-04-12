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
        'menus' => 'required|json'
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

    $user = auth()->user();
    if ($user->role == 'admin') {
        return redirect()->route('penjualan.struk', ['no_faktur' => $penjualan->no_faktur]);
    } elseif ($user->role == 'karyawan') {
        return redirect()->route('penjualan.struk', ['no_faktur' => $penjualan->no_faktur]);
    } else {
        abort(403, 'Unauthorized action.');
    }
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

public function selesai($id)
{
    DB::beginTransaction();
    try {
        $penjualan = Penjualan::findOrFail($id);
        if ($penjualan->status_penjualan === 'Selesai') {
            return redirect()->back()->with('error', 'Penjualan sudah selesai sebelumnya.');
        }

        // Looping setiap menu yang dipesan
        foreach ($penjualan->details as $detail) {
            $menu = Menu::with('bahanBaku')->find($detail->menu_id);
            if (!$menu) {
                throw new \Exception("Menu tidak ditemukan.");
            }

            // Kurangi stok menu
            if ($menu->stok < $detail->jumlah) {
                throw new \Exception("Stok menu '{$menu->nama_makanan}' tidak mencukupi.");
            }
            $menu->decrement('stok', $detail->jumlah);

            // Log the menu and its ingredients


            // Kurangi stok bahan baku sesuai menu
            foreach ($menu->bahanBaku as $bahan) {
                $stok_terpakai = $bahan->pivot->jumlah * $detail->jumlah; // jumlah bahan dikali jumlah pesanan

                if ($bahan->stok < $stok_terpakai) {
                    throw new \Exception("Stok bahan '{$bahan->nama_bahan}' tidak mencukupi.");
                }

                // Log the ingredient and its stock reduction

                // Kurangi stok bahan baku
                $bahan->decrement('stok', $stok_terpakai);
            }
        }

        // Update status penjualan menjadi selesai
        $penjualan->status_penjualan = 'Selesai';
        $penjualan->save();
        \Log::info("Menandai transaksi {$penjualan->no_faktur} sebagai selesai");

        DB::commit();
        return redirect()->back()->with('success', 'Penjualan Telah Diselesaikan.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error($e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
public function cetakStruk(Request $request, $no_faktur)
{
    $penjualan = Penjualan::where('no_faktur', $no_faktur)
        ->with(['detailPenjualan.menu'])
        ->firstOrFail();

    // Lokasi dari .env
    $lokasi = env('RESTAURANT_LOCATION', 'Lokasi belum diatur');

    // Data tunai dan kembalian
    $uangDiberikan = $request->input('uang_diberikan', 0);
    $diskon = $penjualan->diskon ?? 0;
    $pajak = ($penjualan->total_harga - $diskon) * 0.1;
    $total = $penjualan->total_harga - $diskon + $pajak;
    $kembalian = $uangDiberikan - $total;

    // === CETAK STRUK PRINTER ===
    try {
        $connector = new WindowsPrintConnectorTest("POS-58"); // Ganti sesuai printer kamu
        $printer = new Printer($connector);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("🍽️ RestoPos 🍽️\n");
        $printer->text($lokasi . "\n");
        $printer->text("No Faktur: {$penjualan->no_faktur}\n");
        $printer->text("--------------------------------\n");

        $printer->setJustification(Printer::JUSTIFY_LEFT);
        foreach ($penjualan->detailPenjualan as $detail) {
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
        $printer->text(now()->format('d M Y H:i') . "\n");
        $printer->text("😊 Terima Kasih 😊\n");

        $printer->cut();
        $printer->close();
    } catch (\Exception $e) {
        return back()->with('error', 'Gagal mencetak struk: ' . $e->getMessage());
    }

    return view('karyawan.Penjualan.struk', compact('penjualan', 'lokasi', 'uangDiberikan', 'kembalian'));
}


public function exportExcel(Excel $excel){
        return $excel->download(new PenjualanExport, 'Laporan_Penjualan.xlsx');
    }
}
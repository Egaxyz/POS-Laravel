<?php

namespace App\Http\Controllers;

use App\Models\DetailPenjualan;
use App\Models\Menu;
use App\Models\Penjualan;
use App\Models\BahanBaku;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class PenjualanController extends Controller
{
    public function index() {
    $menu = Menu::all();
    $penjualan = Penjualan::orderByRaw("CASE WHEN status_penjualan = 'Proses' THEN 0 ELSE 1 END")
        ->orderBy('tanggal', 'desc')
        ->paginate(5);

    $user = auth()->user();

    if ($user->role == 'superuser') {
        return view('superuser/Penjualan/index', compact('penjualan', 'menu'));
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
    if ($user->role == 'superuser') {
        return redirect()->route('superuser.penjualann')->with('success', 'Pembelian Berhasil Ditambahkan.');
    } elseif ($user->role == 'karyawan') {
        return redirect()->route('karyawan.penjualan')->with('success', 'Pembelian Berhasil Ditambahkan.');
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
            \Log::info("Processing menu: {$menu->nama_makanan}");
            \Log::info("Ingredients for menu:", $menu->bahanBaku->toArray());

            // Kurangi stok bahan baku sesuai menu
            foreach ($menu->bahanBaku as $bahan) {
                $stok_terpakai = $bahan->pivot->jumlah * $detail->jumlah; // jumlah bahan dikali jumlah pesanan

                if ($bahan->stok < $stok_terpakai) {
                    throw new \Exception("Stok bahan '{$bahan->nama_bahan}' tidak mencukupi.");
                }

                // Log the ingredient and its stock reduction
                \Log::info("Reducing stock for ingredient: {$bahan->nama_bahan} by {$stok_terpakai}");

                // Kurangi stok bahan baku
                $bahan->decrement('stok', $stok_terpakai);
            }
        }

        // Update status penjualan menjadi selesai
        $penjualan->status_penjualan = 'Selesai';
        $penjualan->save();

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

    if ($user->role == 'superuser') {
        return view('superuser.Laporan_Penjualan.index', ['penjualan' => $dataByYear, 'tahun' => $tahun]);
    } elseif ($user->role == 'manager') {
        return view('manager.Laporan_Penjualan.index', ['penjualan' => $dataByYear, 'tahun' => $tahun]);
    } else {
        abort(403, 'Anda tidak memiliki akses.');
    }
}
public function cetakStruk(Request $request)
{
    // Ambil query parameters
    $totalHarga = $request->query('total_harga');
    $uangDiberikan = $request->query('uang_diberikan');
    $kembalian = $request->query('kembalian');
    $metodePembayaran = $request->query('metode_pembayaran'); // Get the payment method
    $selectedMenus = json_decode(urldecode($request->query('menus')), true);

    // Get the current year
    $currentYear = date('Y');

    // Get the last invoice number for the current year from the penjualan table
    $lastInvoice = DB::table('penjualan')
        ->where('no_faktur', 'like', 'psn' . $currentYear . '%')
        ->orderBy('no_faktur', 'desc')
        ->first();

    // Determine the new sequential number
    if ($lastInvoice) {
        // Extract the last number from the no_faktur
        $lastNoFaktur = $lastInvoice->no_faktur;
        $lastSequentialNumber = (int)substr($lastNoFaktur, -3); // Get the last three digits
        $newSequentialNumber = str_pad($lastSequentialNumber + 1, 3, '0', STR_PAD_LEFT); // Increment and pad with zeros
    } else {
        // If no previous invoice exists for the current year, start from 001
        $newSequentialNumber = '001';
    }

    // Create the new invoice number
    $newInvoiceNumber = 'PSN' . $currentYear . $newSequentialNumber;

    // Buat data penjualan sementara untuk struk
    $penjualan = (object) [
        'no_faktur' => $newInvoiceNumber, // Use the new invoice number
        'total_harga' => $totalHarga,
        'diskon' => 0, // Diskon bisa disesuaikan
        'metode_pembayaran' => $metodePembayaran, // Use the payment method from query
        'tanggal' => now(),
        'details' => array_map(function ($menu) {
            return (object) [
                'menu' => (object) [
                    'nama_makanan' => $menu['nama_makanan'],
                    'harga_satuan' => $menu['harga'],
                ],
                'jumlah' => $menu['jumlah'],
                'harga_satuan' => $menu['harga'],
            ];
        }, $selectedMenus),
    ];

    // Format tanggal
    $penjualan->tanggal_formatted = Carbon::parse($penjualan->tanggal)
        ->locale('id')
        ->translatedFormat('l Y/m/d H:i');

    $lokasi = env('RESTAURANT_LOCATION');

    // Tampilkan view struk
    $user = auth()->user();
    if ($user->role == 'superuser') {
        return view('superuser.Penjualan.struk', compact('penjualan', 'lokasi', 'totalHarga', 'uangDiberikan', 'kembalian'));
    } elseif ($user->role == 'karyawan') {
        return view('karyawan.Penjualan.struk', compact('penjualan', 'lokasi', 'totalHarga', 'uangDiberikan', 'kembalian'));
    } else {
        abort(403, 'Unauthorized action.');
    }
}

}
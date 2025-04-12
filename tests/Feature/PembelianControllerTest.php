<?php

namespace Tests\Feature;

use App\Models\BahanBaku;
use App\Models\Pembelian;
use App\Models\Supplier;
use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembelianControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::create([
            'nama' => 'nigga',
            'password' => Hash::make('admin123'),
            'no_hp' => '1222',
            'status' => 'aktif',
            'role' => 'karyawan'
        ]);

        $this->actingAs($user);
    }

    public function test_can_create_pembelian()
    {
        $supplier = Supplier::create([
            'nama_perusahaan' => 'Supplier Test',
            'alamat' => 'Jalan Test',
            'kontak' => '08123456789',
            'email' => 'supplier@test.com',
            'status' => 'aktif',
        ]);

        $bahanBaku = BahanBaku::create([
            'nama' => 'Tepung',
            'stok' => 0,
            'harga_satuan' => 10000,
             'supplier_id' => $supplier->id,
        ]);

        $data = [
            'supplier_id' => $supplier->id,
            'total_harga' => 30000,
            'items' => [
                [
                    'bahan_baku_id' => $bahanBaku->id,
                    'jumlah' => 3,
                    'harga_satuan' => 10000,
                ],
            ]
        ];

        $response = $this->post('/karyawan/pembelian', $data);

        $response->assertRedirect(); // Redirect ke halaman setelah pembelian
        $this->assertDatabaseHas('pembelian', [
            'supplier_id' => $supplier->id,
            'total_harga' => 30000,
        ]);
    }

    public function test_can_view_pembelian_list()
    {
        // Tambahkan jika ingin test index
        $response = $this->get('/karyawan/pembelian');
        $response->assertStatus(200);
    }

    public function test_can_finish_pembelian()
    {
        $supplier = Supplier::create([
            'nama_perusahaan' => 'Supplier Test',
            'alamat' => 'Alamat',
            'kontak' => '08888',
            'email' => 'test@supplier.com',
            'status' => 'aktif',
        ]);

        $bahan = BahanBaku::create([
            'nama' => 'Gula',
            'stok' => 0,
            'harga_satuan' => 5000,
            'supplier_id' => $supplier->id,
        ]);

        $pembelian = Pembelian::create([
            'user_id' => auth()->id(),
            'supplier_id' => $supplier->id,
            'status_pembelian' => 'pending',
            'tanggal_pembelian' => now(),
            'total_harga' => 10000,
        ]);

        $pembelian->details()->create([
            'bahan_baku_id' => $bahan->id,
            'jumlah' => 2,
            'harga_satuan' => 5000,
        ]);

        $response = $this->patch("/karyawan/pembelian/selesai/{$pembelian->id}");

        $response->assertRedirect();
        $this->assertEquals('Selesai', $pembelian->fresh()->status_pembelian);
    }

    public function test_can_cancel_pembelian()
    {
        $supplier = Supplier::create([
            'nama_perusahaan' => 'Supplier Test',
            'alamat' => 'Alamat',
            'kontak' => '08888',    
            'email' => 'test@supplier.com',
            'status' => 'aktif',
        ]);

        $pembelian = Pembelian::create([
            'user_id' => auth()->id(),
            'supplier_id' => $supplier->id,
            'status_pembelian' => 'pending',
            'tanggal_pembelian' => now(),
            'total_harga' => 15000,
        ]);

        $response = $this->patch("/karyawan/pembelian/batalkan/{$pembelian->id}");

        $response->assertRedirect();
        $this->assertEquals('Gagal', $pembelian->fresh()->status_pembelian);
    }
}
<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::create([
        'nama'=>'nigga',
        'password'=>Hash::make('admin123'),
        'no_hp'=>'1222',
        'status'=>'aktif',
        'role'=>'manager'
        ]);
        $credentials = [
            'nama' => 'nigga',
            'password' => 'admin123'
        ];
        $this->actingAs($user);
    }

    public function test_can_create_supplier()
    {
        $data = [
            'nama_perusahaan' => 'Supplier Baru',
            'alamat' => 'Jl. Mawar No. 12',
            'kontak' => '08123456789',
            'email' => 'contoh@gmail.com',
            'status' => 'aktif',
        ];

        $response = $this->post('/manager/supplier', $data);

        $response->assertRedirect('/manager/supplier'); // redirect ke index
        $this->assertDatabaseHas('supplier', $data);
    }

    public function test_can_view_supplier_list()
    {
        // buat 3 supplier secara manual
        Supplier::create([
            'nama_perusahaan' => 'Supplier 1',
            'alamat' => 'Alamat 1',
            'kontak' => '0811111111',
            'email' => 'contoh@gmail.com',
            'status' => 'aktif',
        ]);
        Supplier::create([
            'nama_perusahaan' => 'Supplier 2',
            'alamat' => 'Alamat 2',
            'kontak' => '0822222222',
            'email' => 'contoddh@gmail.com',
            'status' => 'aktif',
        ]);
        Supplier::create([
            'nama_perusahaan' => 'Supplier 3',
            'alamat' => 'Alamat 3',
            'kontak' => '0833333333',
            'email' => 'conto333h@gmail.com',
            'status' => 'aktif',
        ]);

        $response = $this->get('/manager/supplier');

        $response->assertStatus(200);
        $response->assertViewHas('supplier');
    }

    public function test_can_update_supplier()
    {
        $supplier = Supplier::create([
            'nama_perusahaan' => 'Old Supplier',
            'alamat' => 'Old Address',
            'kontak' => '0800000000',
            'email' => 'contoddfh@gmail.com',
            'status' => 'aktif',
        ]);

        $updateData = [
            'nama_perusahaan' => 'Supplier Updated',
            'alamat' => 'Jl. Melati No. 99',
            'kontak' => '08991234567',
            'email' => 'contohee@gmail.com',
            'status' => 'aktif',
        ];

        $response = $this->patch("/manager/supplier/{$supplier->id}", $updateData);

        $response->assertRedirect('/manager/supplier');
        $this->assertDatabaseHas('supplier', $updateData);
    }

    public function test_can_delete_supplier()
    {
        $supplier = Supplier::create([
            'nama_perusahaan' => 'Supplier Hapus',
            'alamat' => 'Alamat Hapus',
            'kontak' => '0877777777',
            'email' => 'conddtoh@gmail.com',
            'status' => 'aktif',
        ]);

        $response = $this->delete("/manager/supplier/{$supplier->id}");

        $response->assertRedirect('/manager/supplier');
        $this->assertDatabaseMissing('supplier', ['id' => $supplier->id]);
    }
}
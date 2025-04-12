<?php

namespace Tests\Feature;

use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function testLogin(){
        $user = User::create([
        'nama'=>'nigga',
        'password'=>Hash::make('admin123'),
        'no_hp'=>'1222',
        'status'=>'aktif',
        'role'=>'karyawan'
    ]);
    $credentials = [
        'nama' => 'nigga',
        'password' => 'admin123'
    ];

        $response = $this->post('/login', $credentials);
        $response->assertStatus(302);
        $response->assertRedirect('karyawan/dashboard');
        // $this->assertAuthenticatedAs($user);
    }
}
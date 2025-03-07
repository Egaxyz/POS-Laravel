<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    public $table = 'penjualan';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'no_faktur',
        'total_harga',
        'tanggal',
        'metode_pembayaran',
        'status_penjualan',

    ];
       public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function details()
{
    return $this->hasMany(DetailPenjualan::class, 'penjualan_id', 'id');
}
public function menu()
{
    return $this->hasOneThrough(Menu::class, DetailPenjualan::class, 'penjualan_id', 'id', 'id', 'menu_id');
}

    
}
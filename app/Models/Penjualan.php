<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    public $table = 'pembelian';
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

    public function detail()
    {
        return $this->hasManyThrough(Menu::class, DetailPenjualan::class, 'penjualan_id', 'id', 'id', 'menu_id');
    }
}
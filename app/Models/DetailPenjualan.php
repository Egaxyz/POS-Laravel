<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    public $timestamps = false;
    protected $table = 'detail_penjualan';
    public $fillable = [
        'menu_id',
        'penjualan_id',
        'jumlah',
        'harga_satuan',
    ];
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
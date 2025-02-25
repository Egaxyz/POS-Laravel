<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'detail_pembelian';
    protected $fillable = ['pembelian_id', 'bahan_baku_id', 'jumlah', 'harga_satuan'];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }
}
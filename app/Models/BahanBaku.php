<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    public $table = 'bahan_baku';
    public $timestamps = false;
    protected $fillable = [
        'nama',
        'stok',
        'satuan',
        'harga_satuan'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }
}
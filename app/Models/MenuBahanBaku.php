<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuBahanBaku extends Model
{
    protected $table = 'menu_bahan_baku';
    public $timestamps = false;
    protected $fillable = [
        'menu_id',
        'bahan_baku_id',
        'jumlah'
    ];
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }
    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id', 'id');
    }
    
}
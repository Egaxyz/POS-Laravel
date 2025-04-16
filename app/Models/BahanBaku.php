<?php

namespace App\Models;

use App\Http\Controllers\MenuController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BahanBaku extends Model
{
    use SoftDeletes;
    public $table = 'bahan_baku';
    public $timestamps = false;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'supplier_id',
        'nama',
        'stok',
        'satuan',
        'harga_satuan'
    ];
protected static function boot()
{
    parent::boot();

    static::updated(function ($bahan) {
        $menuIds = \DB::table('menu_bahan_baku')
            ->where('bahan_baku_id', $bahan->id)
            ->pluck('menu_id');

        foreach ($menuIds as $menuId) {
            (new MenuController)->updateMenuPrice($menuId);
        }
    });
}

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }
}
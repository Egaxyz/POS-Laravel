<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'user_id',
        'nama_member',
        'kontak',
        'email',
        'alamat',
        'tanggal_bergabung',
        'status'
    ];
    public $timestamps = false;

    protected $table = 'member';
}
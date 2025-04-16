<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    public $table = 'user';
    public $timestamps = false;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'nama',
        'password',
        'no_hp',
        'status',
        'role'
    ];
    public function setUserPassAttribute($value)
    {
        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    /**
     * Mengecek apakah pengguna aktif
     */
    public function isActive()
    {
        return $this->status == 'aktif';
    }
}
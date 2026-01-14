<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

protected $fillable = [
    'name', 'job_title', 'role', 'password', 'status', 'user_code'
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // protected $casts = [
    //     'password' => 'hashed',
    // ];

 public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isProvider()
    {
        return $this->role === 'provider';
    }

    public function isCashier()
    {
        return $this->role === 'cashier';
    }

}



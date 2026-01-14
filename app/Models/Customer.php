<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = [
        'name',
        'phone',
        'region',
        'street',
        'building_number',
        'floor',
        'apartment',
        'landmark',
        'notes',
    ];

    // علاقة: عميل لديه طلبات
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

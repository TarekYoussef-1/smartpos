<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    // نسمح بالتعبئة
    protected $fillable = [
    'user_id','shift_id', 'shift_type_id', 'customer_id', 'type', 'total', 
    'discount', 'tax', 'status', 'customer_name', 'customer_phone', 
    'customer_address', 'daily_serial',
    'business_date',
];

    // علاقة: الطلب ينتمي إلى عميل
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // علاقة: الطلب لديه أصناف
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // علاقة: الطلب ينتمي إلى مستخدم (الكاشير)
    public function cashier()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}

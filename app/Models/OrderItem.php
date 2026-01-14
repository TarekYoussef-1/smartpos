<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'note'
    ];

    // علاقة: الصنف ينتمي لطلب
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // علاقة: الصنف ينتمي لمنتج
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

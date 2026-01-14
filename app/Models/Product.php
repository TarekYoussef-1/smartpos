<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'price',
        'image',
        'active',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}

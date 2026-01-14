<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Printer extends Model // <-- تأكد أن الاسم هنا 'Printer'
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'printer_name',
        'printer_ip',
        'active',
        'type',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
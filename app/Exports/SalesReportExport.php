<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping
{
    private $index = 0;

    public function collection()
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                'products.name as product_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_sales')
            )
            ->groupBy('products.name')
            ->orderBy('products.name')
            ->get();
    }

    public function map($row): array
    {
        $this->index++;

        return [
            $this->index,
            $row->product_name,
            $row->total_quantity,
            number_format($row->total_sales, 2)
        ];
    }

    public function headings(): array
    {
        return [
            'م',
            'اسم الصنف',
            'الكمية',
            'إجمالي البيع من الصنف'
        ];
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * عرض تقرير مبيعات الأصناف على الشاشة.
     */
    public function sales(Request $request)
    {
        $from = $request->input('from');
        $to   = $request->input('to');
        $report = collect();

        if ($from && $to) {
            $report = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereDate('orders.created_at', '>=', $from)
                ->whereDate('orders.created_at', '<=', $to)
                ->groupBy('products.id', 'products.name')
                ->select(
                    'products.name as product_name',
                    DB::raw('COUNT(DISTINCT orders.id) as orders_count'),
                    DB::raw('SUM(order_items.quantity) as total_qty'),
                    DB::raw('SUM(order_items.quantity * order_items.price) as total_amount')
                )
                ->orderBy('total_amount', 'desc')
                ->get();
        }

        return view('reports.sales', compact('from', 'to', 'report'));
    }

    /**
     * عرض صفحة الطباعة المخصصة لتقرير المبيعات.
     */
    public function salesPrint(Request $request)
    {
        $from = $request->input('from');
        $to   = $request->input('to');
        $report = collect();

        if ($from && $to) {
            $report = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereDate('orders.created_at', '>=', $from)
                ->whereDate('orders.created_at', '<=', $to)
                ->groupBy('products.id', 'products.name')
                ->select(
                    'products.name as product_name',
                    DB::raw('SUM(order_items.quantity) as total_qty'),
                    DB::raw('SUM(order_items.quantity * order_items.price) as total_amount')
                )
                ->orderBy('total_amount', 'desc')
                ->get();
        }

        // حساب الإجمالي الكلي لعرضه في نهاية التقرير
        $grandTotal = $report->sum('total_amount');

        // إرسال البيانات إلى فيو الطباعة
        return view('reports.sales-print', compact('from', 'to', 'report', 'grandTotal'));
    }
}
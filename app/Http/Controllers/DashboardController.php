<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Department;
use App\Models\Order;;

use DatePeriod;
use DateInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Printer;



class DashboardController extends Controller
{
    // Middleware لضمان تسجيل الدخول
    // public function __construct()
    // {
    //    $this->middleware(['auth','checkRole:admin']);
    // }


    public function index(Request $request)
    {
        // --- إحصائيات اليوم (لا تتأثر بالفلتر) ---
        $today = now()->format('Y-m-d');
        $todayOrdersQuery = Order::whereDate('created_at', $today)->where('status', 'paid');

        $todaySales = $todayOrdersQuery->sum('total');
        $todayOrdersCount = $todayOrdersQuery->count();
        $averageOrderValue = $todayOrdersCount > 0 ? $todaySales / $todayOrdersCount : 0;
        $monthStart = now()->startOfMonth();
        $monthEnd   = now()->endOfMonth();

        $monthlySales = Order::whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 'paid')
            ->sum('total');
        $dineInCount = Order::whereDate('created_at', $today)->where('type', 'dine_in')->where('status', 'paid')->count();
        $takeAwayCount = Order::whereDate('created_at', $today)->where('type', 'take_away')->where('status', 'paid')->count();
        $deliveryCount = Order::whereDate('created_at', $today)->where('type', 'delivery')->where('status', 'paid')->count();

        // --- البيانات الأساسية (لا تتأثر بالفلتر) ---
        $departmentsCount = Department::count();
        $productsCount = Product::count();
        $usersCount = User::count();

        // --- بيانات المخططات (لا تتأثر بالفلتر) ---
        $orderTypeChartData = $this->getOrderTypeChartData();
        $salesChartData = $this->getSalesChartData(7);

        // --- بناء استعلام الطلبات مع الفلاتر الجديدة ---
        $ordersQuery = Order::with('customer')->orderBy('created_at', 'desc');
        // عدد البرنترات    
        $printersCount = Printer::count();

        // فلتر برقم الطلب
        if ($request->filled('order_id')) {
            $ordersQuery->where('id', $request->order_id);
        }

        // فلتر بنطاق زمني
        if ($request->filled('start_date')) {
            $ordersQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $ordersQuery->whereDate('created_at', '<=', $request->end_date);
        }

        // عرض الطلبات باستخدام paginate (10 طلبات في كل صفحة)
        $orders = $ordersQuery->paginate(10);

        // ✅ التحقق من طلب AJAX
        if ($request->ajax() || $request->wantsJson()) {
            // إرجاع استجابة JSON تحتوي على الجدول والتقسيم الصفحي
            $tableHtml = view('dashboard._orders_table', compact('orders'))->render();
            $paginationHtml = $orders->appends(request()->query())->links('pagination::bootstrap-5')->toHtml();

            return response()->json([
                'table' => $tableHtml,
                'pagination' => $paginationHtml
            ]);
        }
        $currentShift = null;
        if (Session::has('shift_id')) {
            $currentShift = DB::table('shifts')->where('id', Session::get('shift_id'))->first();
        }
        // =====================
        // الشيفتات (للمدير فقط)
        // =====================
        $shifts = [];
        $totalShiftsSales = 0;

        if (Session::has('user') && Session::get('user')->role === 'admin') {

            $shifts = DB::table('shifts')
                ->leftJoin('users', 'users.id', '=', 'shifts.user_id')
                ->leftJoin('orders', 'orders.shift_id', '=', 'shifts.id')
                ->select(
                    'shifts.id',
                    'shifts.opened_at',
                    'shifts.closed_at',
                    'users.name as user_name',
                    DB::raw('IFNULL(SUM(orders.total), 0) as total_sales')
                )
                ->groupBy(
                    'shifts.id',
                    'shifts.opened_at',
                    'shifts.closed_at',
                    'users.name'
                )
                ->orderBy('shifts.opened_at', 'desc')
                ->get();

            $totalShiftsSales = $shifts->sum('total_sales');
        }


        return view('dashboard.index', compact(
            'todaySales',
            'monthlySales',
            'todayOrdersCount',
            'averageOrderValue',
            'dineInCount',
            'takeAwayCount',
            'deliveryCount',
            'orders',
            'departmentsCount',
            'productsCount',
            'usersCount',
            'orderTypeChartData',
            'salesChartData',
            'currentShift',
            'shifts',
            'totalShiftsSales',
            'printersCount'
        ));
    }

    public function cancelOrder(Order $order)
    {
        // يمكنك إضافة تحقق هنا، مثلاً لا يمكن إلغاء الطلبات القديمة
        if ($order->status === 'paid') {
            $order->status = 'cancelled';
            $order->save();

            // يمكنك إعادة المنتجات للمخزون هنا إذا كان لديك نظام مخزون
        }

        return redirect()->route('dashboard.index')->with('success', 'تم إلغاء الطلب بنجاح.');
    }


    // دالة مساعدة للحصول على بيانات المخطط الدائري
    private function getOrderTypeChartData()
    {
        $data = Order::where('status', 'paid')
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return [
            'labels' => [
                'Dine In',
                'Take Away',
                'Delivery'
            ],
            'data' => [
                $data['dine_in'] ?? 0,
                $data['take_away'] ?? 0,
                $data['delivery'] ?? 0,
            ]
        ];
    }

    // دالة مساعدة للحصول على بيانات مخطط المبيعات (آخر 7 أيام)
    private function getSalesChartData($days = 7)
    {
        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d M'); // مثال: "5 Dec"

            $sales = Order::whereDate('created_at', $date)
                ->where('status', 'paid')
                ->sum('total');
            $data[] = $sales;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    //  جلب بيانات المبيعات بناءً على فترة زمنية (لـ AJAX)
    public function getSalesChartDataByPeriod($period)
    {
        $labels = [];
        $data = [];

        switch ($period) {
            case 'today':
                $date = now()->format('Y-m-d');
                $sales = Order::whereDate('created_at', $date)->where('status', 'paid')->sum('total');
                $labels = ['اليوم'];
                $data = [$sales];
                break;

            case 'last7days':
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i)->format('Y-m-d');
                    $labels[] = now()->subDays($i)->format('d M'); // مثال: "5 Dec"
                    $sales = Order::whereDate('created_at', $date)->where('status', 'paid')->sum('total');
                    $data[] = $sales;
                }
                break;

            case 'thismonth':
                $daysInMonth = now()->daysInMonth;
                for ($i = 1; $i <= $daysInMonth; $i++) {
                    $date = now()->format('Y-m') . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    $labels[] = $i;
                    $sales = Order::whereDate('created_at', $date)->where('status', 'paid')->sum('total');
                    $data[] = $sales;
                }
                break;

            case 'lastmonth':
                $startOfLastMonth = now()->subMonth()->startOfMonth();
                $endOfLastMonth = now()->subMonth()->endOfMonth();

                $orders = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
                    ->where('status', 'paid')
                    ->selectRaw('DATE(created_at) as date, SUM(total) as total')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();

                // ملء الأيام التي ليس بها مبيعات بقيمة 0
                $period = new DatePeriod(
                    $startOfLastMonth,
                    new DateInterval('P1D'),
                    $endOfLastMonth
                );
                foreach ($period as $day) {
                    $dateStr = $day->format('Y-m-d');
                    $labels[] = $day->format('d');
                    $salesForDay = $orders->where('date', $dateStr)->first();
                    $data[] = $salesForDay ? $salesForDay->total : 0;
                }
                break;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    // جلب بيانات المخطط الدائري بناءً على فترة زمنية (لـ AJAX)
    public function getOrderTypeChartDataByPeriod($period)
    {
        $query = Order::where('status', 'paid');

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', now());
                break;
            case 'last7days':
                $query->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()]);
                break;
            case 'thismonth':
                $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                break;
            case 'lastmonth':
                $query->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year);
                break;
        }

        $data = $query->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return response()->json([
            'labels' => [
                'Dine In',
                'Take Away',
                'Delivery'
            ],
            'data' => [
                $data['dine_in'] ?? 0,
                $data['take_away'] ?? 0,
                $data['delivery'] ?? 0,
            ]
        ]);
    }
    // إغلاق الشيفت
    public function closeShift($id)
    {
        // 1) تأكد أن الشيفت موجود
        $shift = DB::table('shifts')->where('id', $id)->first();

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'الشيفت غير موجود'
            ], 404);
        }

        // 2) متقفلش وهو مقفول بالفعل
        if ($shift->closed_at != null) {
            return response()->json([
                'success' => false,
                'message' => 'الشيفت مقفول بالفعل'
            ], 400);
        }

        // 3) اجمع مبيعات الشيفت
        $totalSales = DB::table('orders')
            ->where('shift_id', $id)
            ->where('status', 'paid')
            ->sum('total');

        // 4) اقفل الشيفت
        DB::table('shifts')
            ->where('id', $id)
            ->update([
                'closed_at' => now(),
                'closing_balance' => $totalSales
            ]);

        Session::forget('shift_id');
        Session::forget('shift_type_id');

        return response()->json([
            'success' => true,
            'message' => 'تم إغلاق الشيفت بنجاح',
            'redirect' => route('shift.print', $id)
        ]);
    }
    // طباعة تقرير الشيفت   
    public function printShift($id)
    {
        // 🔍 1) التأكد من وجود الشيفت
        $shift = DB::table('shifts')
            ->join('users', 'users.id', '=', 'shifts.user_id')
            ->select('shifts.*', 'users.name as cashier_name')
            ->where('shifts.id', $id)
            ->first();

        if (!$shift) {
            abort(404, 'Shift not found');
        }

        // 🔍 2) جلب الطلبات الخاصة بالشيفت
        $orders = DB::table('orders')
            ->where('shift_id', $id)
            ->orderBy('id')
            ->get();

        // 🔍 3) إجمالي مبيعات Paid
        $paidTotal = DB::table('orders')
            ->where('shift_id', $id)
            ->where('status', 'paid')
            ->sum('total');

        // 🔍 4) إجمالي Cancelled
        $cancelledTotal = DB::table('orders')
            ->where('shift_id', $id)
            ->where('status', 'cancelled')
            ->sum('total');

        // 🔍 5) إجمالي عدد الطلبات
        $totalOrders = $orders->count();

        // 🔍 6) تجهيز الريسيت
        return view('dashboard.shift_report', compact(
            'shift',
            'orders',
            'paidTotal',
            'cancelledTotal',
            'totalOrders'
        ));
    }

    public function shiftCashForm($id)
    {
        
        // الشيفت
        $shift = DB::table('shifts')
            ->leftJoin('users', 'users.id', '=', 'shifts.user_id')
            ->where('shifts.id', $id)
            ->select(
                'shifts.*',
                'users.name as cashier_name'
            )
            ->first();

        if (!$shift) {
            abort(404, "Shift not found");
        }

        // إجمالي الإيرادات المدفوعة
        $paidTotal = DB::table('orders')
            ->where('shift_id', $id)
            ->where('status', 'paid')
            ->sum('total');

        // إجمالي الملغي
        $cancelledTotal = DB::table('orders')
            ->where('shift_id', $id)
            ->where('status', 'cancelled')
            ->sum('total');

        // عدد الطلبات
        $orderCount = DB::table('orders')
            ->where('shift_id', $id)
            ->count();

        // ========= NEW =========

        $shift_total = $paidTotal;  // قيمة الشيفت من النظام
        $cash_total  = 0;           // لحد ما الفورم يتبني
        $diff        = $cash_total - $shift_total;

        return view('dashboard.shift_cash', [
            'shift'          => $shift,
            'paidTotal'      => $paidTotal,
            'cancelledTotal' => $cancelledTotal,
            'orderCount'     => $orderCount,
            'shift_total'    => $shift_total,
            'cash_total'     => $cash_total,
            'diff'           => $diff,
            'title'          => "مراجعة شيفت رقم: $id",
        ]);
    }


    public function shiftCashCount($id)
    {
        $shift = DB::table('shifts')
            ->leftJoin('users', 'users.id', '=', 'shifts.user_id')
            ->select(
                'shifts.*',
                'users.name as user_name'
            )
            ->where('shifts.id', $id)
            ->first();

        if (!$shift) {
            abort(404);
        }

        $orders = DB::table('orders')
            ->where('shift_id', $id)
            ->where('status', 'paid')
            ->select('id', 'total', 'created_at')
            ->get();

        $totalSales = $orders->sum('total');

        return view('dashboard.shift_cash', compact('shift', 'orders', 'totalSales'));
    }

    public function shiftCashPage($id)
    {
        $shift = DB::table('shifts')->where('id', $id)->first();

        if (!$shift) {
            abort(404, 'Shift Not Found');
        }

        return view('dashboard.shift_cash', compact('id', 'shift'));
    }

    public function shiftCashSave(Request $request, $id)
    {
        $denoms = $request->input('denom', []);

        $cashTotal = 0;
        foreach ($denoms as $value => $qty) {
            $cashTotal += ($value * $qty);
        }

        DB::table('shifts')
            ->where('id', $id)
            ->update([
                'closing_balance' => $cashTotal,
                'closing_denoms' => json_encode($denoms),
                'closed_at' => now(),
            ]);

        return redirect()->route('shift.cash.print', $id);
    }



    public function shiftCashPrint($id)
    {
        $shift = DB::table('shifts')
            ->leftJoin('users', 'users.id', '=', 'shifts.user_id')
            ->where('shifts.id', $id)
            ->select('shifts.*', 'users.name as cashier_name')
            ->first();

        if (!$shift) {
            abort(404, "Shift not found");
        }

        $orderCount = DB::table('orders')
            ->where('shift_id', $id)
            ->count();

        $paidTotal = DB::table('orders')
            ->where('shift_id', $id)
            ->where('status', 'paid')
            ->sum('total');

        $defaultDenoms = [
            200 => 0,
            100 => 0,
            50  => 0,
            20  => 0,
            10  => 0,
            5   => 0,
            1   => 0,

        ];

        $savedDenoms = json_decode($shift->closing_denoms ?? '{}', true);
        $denoms = $savedDenoms + $defaultDenoms;

        $cash_total = $shift->closing_balance ?? 0;
        $diff = $cash_total - $paidTotal;

        return view('dashboard.shift_cash_print', compact(
            'shift',
            'paidTotal',
            'denoms',
            'cash_total',
            'diff',
            'orderCount'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Department;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Printer as PrinterModel;

class POSController extends Controller
{
    public function index()
    {
        $categories = Department::orderBy('name')->get();
        return view('pos.index', compact('categories'));
    }

    public function getItems($department)
    {
        return Product::where('department_id', $department)
            ->where('active', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'image']);
    }

    public function storeDelivery(Request $request)
    {
        session(['delivery_customer' => $request->all()]);
        return response()->json(['success' => true]);
    }

    public function storeCustomer(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $customer = Customer::updateOrCreate(
            ['phone' => $request->phone],
            $request->all()
        );

        return response()->json([
            'success' => true,
            'customer_id' => $customer->id
        ]);
    }

    public function searchCustomer($phone)
    {
        $customer = Customer::where('phone', $phone)->first();

        return response()->json([
            'success' => (bool)$customer,
            'customer' => $customer
        ]);
    }

    /**
     * حفظ الطلب + توليد PDF + طباعة أوتوماتيك
     */
 public function printInvoice(Request $request)
{
    // ================== Validation ==================
    $data = $request->validate([
        'type' => 'required|in:dine_in,take_away,delivery',
        'customer_id' => 'nullable|exists:customers,id',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'nullable|numeric',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.price' => 'required|numeric',
        'items.*.total' => 'required|numeric',
        'total' => 'required|numeric',
    ]);

    // ================== Shift Check ==================
    $shift = DB::table('shifts')
        ->where('id', Session::get('shift_id'))
        ->whereNull('closed_at')
        ->first();

    if (!$shift) {
        return response()->json([
            'success' => false,
            'logout'  => true
        ]);
    }

    // ================== Create Order ==================
    $order = DB::transaction(function () use ($data) {

        $businessDate = now()->format('Y-m-d');

        $lastSerial = Order::where('business_date', $businessDate)
            ->lockForUpdate()
            ->max('daily_serial');

        $serial = ($lastSerial ?? 0) + 1;

        $order = Order::create([
             'user_id'        => Session::get('user')->id,
            'shift_id'       => Session::get('shift_id'),
            'shift_type_id'  => Session::get('shift_type_id'),
            'type'           => $data['type'],
            'customer_id'    => $data['type'] === 'delivery' ? ($data['customer_id'] ?? null) : null,
            'total'          => $data['total'],
            'status'         => 'paid',
            'daily_serial'   => $serial,
            'business_date'  => $businessDate,
        ]);

        foreach ($data['items'] as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product_id'] ?? null,
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
                'subtotal'   => $item['total'],
            ]);
        }

        return $order;
    });

    // ================== Load Relations ==================
    $order->load(['items.product.department', 'customer', 'cashier']);

    // ================== Tools ==================
    $wkhtmltopdf = '"C://Program Files/wkhtmltopdf/bin/wkhtmltopdf.exe"';
    $pdfPrinter  = '"C:/xampp/htdocs/smartpos/PDFtoPrinter.exe"';
    $options     = "--page-width 74mm --page-height 300mm -T 0 -L 0 -R 0";

    // ================== 1️⃣ المطبخ الرئيسي ==================
    $pdfKitchenAll = public_path("printing/KITCH_ALL.pdf");
    $htmlKitchenAll = view('pos.print_kitchen', [
        'order' => $order,
        'items' => $order->items
    ])->render();

    $tmpKitchenAll = public_path("printing/tmp_kitchen_all.html");
    file_put_contents($tmpKitchenAll, $htmlKitchenAll);

    shell_exec("$wkhtmltopdf $options \"$tmpKitchenAll\" \"$pdfKitchenAll\" 2>&1");
    shell_exec("$pdfPrinter \"$pdfKitchenAll\" \"XP-80C\" 2>&1");
    unlink($tmpKitchenAll);

    // ================== 2️⃣ مطبخ جانبي (بدون قسم 1) ==================
    $secondaryItems = $order->items->filter(fn($i) =>
        $i->product && $i->product->department_id != 1
    );

    if ($secondaryItems->count() > 0) {
        $pdfKitchenSec = public_path("printing/KITCH_SEC.pdf");
        $htmlKitchenSec = view('pos.print_kitchen', [
            'order' => $order,
            'items' => $secondaryItems
        ])->render();

        $tmpKitchenSec = public_path("printing/tmp_kitchen_sec.html");
        file_put_contents($tmpKitchenSec, $htmlKitchenSec);

        shell_exec("$wkhtmltopdf $options \"$tmpKitchenSec\" \"$pdfKitchenSec\" 2>&1");
        shell_exec("$pdfPrinter \"$pdfKitchenSec\" \"KitchenPrinter\" 2>&1");
        unlink($tmpKitchenSec);
    }

    // ================== 3️⃣ فاتورة العميل ==================
    $pdfReceipt = public_path("printing/RECEIPT.pdf");
    $htmlReceipt = view('pos.print_receipt', compact('order'))->render();
    $tmpReceipt = public_path("printing/tmp_receipt.html");
    file_put_contents($tmpReceipt, $htmlReceipt);

    shell_exec("$wkhtmltopdf $options \"$tmpReceipt\" \"$pdfReceipt\" 2>&1");
    shell_exec("$pdfPrinter \"$pdfReceipt\" \"XP-80C\" 2>&1");
    unlink($tmpReceipt);

    // ================== Response ==================
    return response()->json([
        'success'   => true,
        'order_id'  => $order->id
    ]);
}
    /**
     * صفحة HTML للمطبخ (PDF فقط)
     */
    public function printKitchenHTML(Order $order, Request $request)
    {
        $order->load('items.product.department');

        $items = $order->items;

        if ($request->has('exclude_department')) {
            $excludeId = (int) $request->exclude_department;
            $items = $items->filter(fn($item) => $item->product && $item->product->department_id != $excludeId);
        }

        return view('pos.print_kitchen', [
            'order' => $order,
            'items' => $items
        ]);
    }

    public function reprint(Order $order)
{
    // تحميل العلاقات
    $order->load(['items.product.department', 'customer', 'cashier']);

    // Tools
    $wkhtmltopdf = '"C://Program Files/wkhtmltopdf/bin/wkhtmltopdf.exe"';
    $pdfPrinter  = '"C:/xampp/htdocs/smartpos/PDFtoPrinter.exe"';
    $options     = "--page-width 74mm --page-height 300mm -T 0 -L 0 -R 0";

    // اسم مختلف عشان ما يدهسش على الطباعة الأصلية
    $pdfReceipt = public_path("printing/RECEIPT_REPRINT.pdf");

    // 👈 أهم سطر: isReprint = true
    $htmlReceipt = view('pos.print_receipt', [
        'order'     => $order,
        'isReprint' => true
    ])->render();

    $tmpReceipt = public_path("printing/tmp_receipt_reprint.html");
    file_put_contents($tmpReceipt, $htmlReceipt);

    shell_exec("$wkhtmltopdf $options \"$tmpReceipt\" \"$pdfReceipt\" 2>&1");
    shell_exec("$pdfPrinter \"$pdfReceipt\" \"XP-80C\" 2>&1");

    unlink($tmpReceipt);

    return back()->with('success', 'تمت إعادة طباعة الفاتورة');
}

}

<?php

namespace App\Http\Controllers;

use App\Models\Printer;
use App\Models\Department;
use Illuminate\Http\Request;

class PrinterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            return Printer::with('department')->orderBy('id')->get();
        }
        return view('printers.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ✅ الخطوة الأولى: معالجة قيمة الـ checkbox قبل التحقق
        $request->merge([
            'active' => $request->has('active') // سيكون true أو false
        ]);

        // ✅ الخطوة الثانية: التحقق من البيانات
        $request->validate([
            'printer_name' => 'required|string|max:255',
            'printer_ip'   => 'required|ip',
            'type'         => 'required|in:kitchen,cashier',
            'department_id' => 'required_if:type,kitchen|exists:departments,id|nullable',
            'active'       => 'required|boolean',
        ]);

        $printer = Printer::create([
            'printer_name' => $request->printer_name,
            'printer_ip'   => $request->printer_ip,
            'type'         => $request->type,
            'department_id' => $request->type === 'kitchen' ? $request->department_id : null,
            'active'       => $request->active,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => 'تمت إضافة الطابعة بنجاح.',
                'printer' => $printer
            ], 200);
        }

        return redirect()->route('printers.index')->with('success', 'تمت إضافة الطابعة بنجاح.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $printer = Printer::findOrFail($id);
        return response()->json($printer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $printer = Printer::findOrFail($id);

        // ✅ الخطوة الأولى: معالجة قيمة الـ checkbox قبل التحقق
        $request->merge([
            'active' => $request->has('active') // سيكون true أو false
        ]);

        // ✅ الخطوة الثانية: التحقق من البيانات
        $request->validate([
            'printer_name' => 'required|string|max:255',
            'printer_ip'   => 'required|ip',
            'type'         => 'required|in:kitchen,cashier',
            'department_id' => 'required_if:type,kitchen|exists:departments,id|nullable',
            'active'       => 'required|boolean',
        ]);

        $printer->update([
            'printer_name' => $request->printer_name,
            'printer_ip'   => $request->printer_ip,
            'type'         => $request->type,
            'department_id' => $request->type === 'kitchen' ? $request->department_id : null,
            'active'       => $request->active,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => 'تم تعديل الطابعة بنجاح.',
                'printer' => $printer
            ], 200);
        }

        return redirect()->route('printers.index')->with('success', 'تم تعديل الطابعة بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Printer::findOrFail($id)->delete();
        return response()->json(['success' => 'تم حذف الطابعة بنجاح.']);
    }

    /**
     * دالة لاختبار الاتصال بالطابعة عبر API
     */
    public function testConnection($ip)
    {
        try {
            // استخدام fsockopen كطريقة بسيطة وفعالة لاختبار المنفذ
            $connection = @fsockopen($ip, 9100, $errno, $errstr, 2); // مهلة 2 ثانية

            if ($connection) {
                fclose($connection);
                return response()->json(['success' => true, 'message' => 'الاتصال بالطابعة ناجح']);
            }

            return response()->json(['success' => false, 'message' => 'فشل الاتصال بالطابعة']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }
}
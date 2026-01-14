<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Support\Facades\Log;


class DepartmentController extends Controller
{
     public function index(Request $request)
    {
        // --- بداية كود التشخيص ---
        Log::info('=== DEPARTMENTS INDEX REQUEST START ===');
        Log::info('Full URL: ' . $request->fullUrl());
        Log::info('Request Method: ' . $request->method());
        Log::info('All Request Data (Query & Post): ' . print_r($request->all(), true));
        Log::info('All Headers: ' . print_r($request->headers->all(), true));
        
        Log::info('-> $request->ajax() result: ' . ($request->ajax() ? 'TRUE' : 'FALSE'));
        Log::info('-> $request->wantsJson() result: ' . ($request->wantsJson() ? 'TRUE' : 'FALSE'));
        Log::info('-> $request->query("json") result: ' . ($request->query('json') ? 'TRUE' : 'FALSE'));
        // --- نهاية كود التشخيص ---

        $departments = Department::select('id', 'name', 'created_at')
            ->orderBy('id')
            ->get()
            ->map(function ($dept) {
                $dept->created_at = $dept->created_at?->format('Y-m-d H:i:s');
                return $dept;
            });

        if ($request->wantsJson() || $request->ajax() || $request->query('json')) {
            Log::info('CONDITION MET: Returning JSON response.');
            return response()->json($departments);
        }

        Log::info('CONDITION FAILED: Returning VIEW response.');
        Log::info('=== DEPARTMENTS INDEX REQUEST END ===');
        return view('departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|unique:departments,name|max:255',
            ]);

            $dept = Department::create([
                'name' => $request->name
            ]);

            return response()->json([
                'success' => 'تم إضافة القسم بنجاح',
                'department' => $dept
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'حدث خطأ أثناء الإضافة',
                'message' => $e->getMessage() // لتصحيح الخطأ إذا ظهر
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:departments,name,' . $id . '|max:255',
        ]);

        try {
            $dept = Department::findOrFail($id);
            $dept->name = $request->name;
            $dept->save();

            return response()->json(['success' => 'تم تعديل القسم بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء التعديل']);
        }
    }

    public function destroy($id)
    {
        try {
            $dept = Department::findOrFail($id);
            $dept->delete();

            return response()->json(['success' => 'تم حذف القسم بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء الحذف']);
        }
    }
}

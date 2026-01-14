<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::select('id', 'user_code', 'name', 'job_title', 'role', 'status', 'created_at', 'updated_at')
                     ->orderBy('id')
                     ->get()
                     ->map(function ($user) {
                         $user->created_at = $user->created_at?->format('Y-m-d') ?? '';
                         $user->updated_at = $user->updated_at?->format('Y-m-d') ?? '';
                         return $user;
                     });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($users);
        }

        return view('users.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_code' => 'required|string|max:10|unique:users,user_code',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:4',
            'role' => 'required|in:admin,cashier,kitchen,waiter',
            'status' => 'required|in:active,inactive',
            'job_title' => 'nullable|string|max:255',
        ]);

        // تحويل اسم الدور إلى معرف رقمي
        $roleMapping = [
            'admin' => 1,
            'cashier' => 2,
            'kitchen' => 3,
            'waiter' => 4
        ];
        
        // التحقق من وجود الدور في المصفوفة
        if (!isset($roleMapping[$request->role])) {
            return response()->json(['error' => 'الدور المحدد غير صالح'], 400);
        }
        
        // إضافة المستخدم مع جميع الحقول المطلوبة
        $user = new User();
        $user->user_code = $request->user_code;
        $user->name = $request->name;
        $user->job_title = $request->job_title;
        $user->role = $request->role; // استخدام قيمة enum مباشرة
        $user->password = $request->password; // حفظ كلمة المرور كما هي بدون تشفير
        $user->status = $request->status;
        
  
        $user->save();

        return response()->json(['success' => 'تمت إضافة المستخدم بنجاح']);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,cashier,kitchen,waiter',
            'status' => 'required|in:active,inactive',
            'job_title' => 'nullable|string|max:255',
        ];

        if ($request->filled('user_code') && $request->user_code !== $user->user_code) {
            $rules['user_code'] = 'string|max:10|unique:users,user_code';
        }

        $request->validate($rules);

        // تحويل اسم الدور إلى معرف رقمي
        $roleMapping = [
            'admin' => 1,
            'cashier' => 2,
            'kitchen' => 3,
            'waiter' => 4
        ];
        
        $data = [
            'name' => $request->name,
            'job_title' => $request->job_title,
            'role' => $request->role, // استخدام قيمة enum مباشرة
            'status' => $request->status,
        ];
        
        if ($request->filled('user_code')) {
            $data['user_code'] = $request->user_code;
        }
        
        // حفظ كلمة المرور كما هي بدون تشفير
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }
        
     
        $user->update($data);

        return response()->json(['success' => 'تم تعديل المستخدم بنجاح']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => 'تم حذف المستخدم بنجاح']);
    }
}
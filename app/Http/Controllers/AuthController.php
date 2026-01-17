<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class AuthController extends Controller
{
 public function loginPage()
{
    $shiftTypes = DB::table('shift_types')->get();
    return view('auth.login', compact('shiftTypes'));
}


public function login(Request $request)
{
    // 1️⃣ Validation
    $request->validate([
        'user_code'      => 'required',
        'password'       => 'required',
        'shift_type_id'  => 'required',
    ]);

    // 2️⃣ التحقق من المستخدم
    $user = DB::table('users')
        ->where('user_code', $request->user_code)
        ->where('password', $request->password) // بدون hash (حسب نظامك الحالي)
        ->where('status', 'active')
        ->first();

    if (!$user) {
        return back()->with('error', 'User Code أو Password غير صحيح');
    }

    // 3️⃣ جلب نوع الشيفت
    $shiftType = DB::table('shift_types')
        ->where('id', $request->shift_type_id)
        ->first();

    if (!$shiftType) {
        return back()->with('error', 'نوع الشيفت غير صحيح');
    }

    // 4️⃣ تخزين المستخدم في السيشن
    Session::put('user', $user);
    Session::put('shift_type', $shiftType);
    Session::put('shift_type_id', $shiftType->id);

    // 5️⃣ التحقق من وجود شيفت مفتوح لنفس اليوم
    $existingShift = DB::table('shifts')
        ->where('user_id', $user->id)
        ->where('shift_type_id', $shiftType->id)
        ->whereNull('closed_at')
        ->whereDate('opened_at', now()->toDateString()) // ✅ بدل created_at
        ->first();

    if ($existingShift) {
        $shiftId = $existingShift->id;
    } else {
        // 6️⃣ فتح شيفت جديد
        $shiftId = DB::table('shifts')->insertGetId([
            'user_id'          => $user->id,
            'shift_type_id'    => $shiftType->id,
            'opened_at'        => now(),
            'opening_balance'  => 0,
            'notes'            => 'Shift opened automatically at login',
        ]);
    }

    // 7️⃣ تخزين الشيفت في السيشن
    Session::put('shift_id', $shiftId);

    // 8️⃣ التوجيه حسب الدور
    if ($user->role === 'admin') {
        return redirect()->route('dashboard.index');
    }

    if ($user->role === 'manager') {
        return redirect()->route('dashboard.manager');
    }

    // cashier
    return redirect()->route('pos.index');
}




public function logout(Request $request)
{
    Session::forget('user');
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login.page');
}
}

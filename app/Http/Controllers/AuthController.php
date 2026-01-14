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
    // 1) Validation
    $request->validate([
        'user_code' => 'required',
        'password' => 'required',
        'shift_type_id' => 'required',
    ]);

    // 2) التأكد من اليوزر
    $user = DB::table('users')
        ->where('user_code', $request->user_code)
        ->where('password', $request->password) // بدون Hash
        ->where('status', 'active')
        ->first();

    if (!$user) {
        return back()->with('error', 'User ID أو Password غير صحيح ');
    }

    // 3) نحضر بيانات الشيفت المختار
    $shiftType = DB::table('shift_types')
                    ->where('id', $request->shift_type_id)
                    ->first();

    // 4) تخزين المستخدم ونوع الشيفت في السيشن
    Session::put('user', $user);
    Session::put('shift_type', $shiftType);
    Session::put('shift_type_id', $shiftType->id);  

    // 5) التحقق من وجود شيفت مفتوح مسبقًا لنفس اليوزر ونوع الشيفت
    $existingShift = DB::table('shifts')
        ->where('user_id', $user->id)
        ->where('shift_type_id', $shiftType->id)
        ->whereNull('closed_at') // الشيفت مفتوح
        ->first();

    if ($existingShift) {
        $shiftId = $existingShift->id;
    } else {
        // فتح شيفت جديد
        $shiftId = DB::table('shifts')->insertGetId([
            'user_id' => $user->id,
            'shift_type_id' => $shiftType->id,
            'opened_at' => now(),
            'opening_balance' => 0,
            'notes' => 'Shift opened automatically at login - type: ' . $shiftType->name,
        ]);
    }

    // 6) تخزين shift_id في السيشن
    Session::put('shift_id', $shiftId);

    // 7) تحويل حسب نوع اليوزر
    if ($user->role == 'admin') {
        return redirect()->route('dashboard.index');
    } else {
        return redirect()->route('pos.index');
    }
}


public function logout(Request $request)
{
    Session::forget('user');
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login.page');
}
}

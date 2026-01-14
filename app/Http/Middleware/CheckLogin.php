<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role = null)
    {
        // إذا لم يتم تحديد دور، اسمح بالمرور
        if (!$role) {
            return $next($request);
        }

        // التحقق مما إذا كان المستخدم مسجل دخوله
        if (!Auth::check()) {
            return redirect()->route('login.page');

        }

        // التحقق مما إذا كان دور المستخدم يطابق الدور المطلوب
        if (Auth::user()->role !== $role) {
            // يمكنك تخصيص الرسالة أو الصفحة
            abort(403, 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }

        return $next($request);
    }
}
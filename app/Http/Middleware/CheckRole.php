<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // جلب المستخدم من السيشن
        $user = $request->session()->get('user');

        // لو مفيش سيشن → رجّع للوجين
        if (!$user) {
            return redirect()->route('login.page');
        }

        // لو دور المستخدم مش من الأدوار المسموح بها → رجّع للوجين
        if (!in_array(strtolower(trim($user->role)), array_map('strtolower', $roles))) {
                return redirect()->route('login.page');
}

        return $next($request);
    }
}

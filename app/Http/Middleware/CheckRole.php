<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->session()->get('user');

        if (!$user) {
            return redirect()->route('login.page');
        }

        if (isset($user->status) && $user->status !== 'active') {
            $request->session()->flush();
            return redirect()->route('login.page');
        }

        $allowedRoles = array_map('strtolower', $roles);
        $userRole = strtolower(trim($user->role ?? ''));

        if ($userRole === '' || !in_array($userRole, $allowedRoles)) {
            abort(403, 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }

        return $next($request);
    }
}

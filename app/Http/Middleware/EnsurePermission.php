<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    /**
     * فرض صلاحية محددة على المسار حسب دور المستخدم الديناميكي
     * الاستخدام: ->middleware('permission:sessions.create')
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(404);
        }

        if (!$user->hasPermission($permission)) {
            if ($request->expectsJson()) {
                abort(403, 'ليس لديك صلاحية لهذا الإجراء.');
            }

            return redirect()->route('dashboard')
                ->withErrors(['error' => 'ليس لديك صلاحية للوصول إلى هذا القسم. تواصل مع مدير النظام لتعديل صلاحيات دورك.']);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            abort(404);
        }

        if (auth()->user()->role !== 'admin') {
            if ($request->expectsJson()) {
                abort(403, 'غير مصرح لك بالوصول إلى لوحة التحكم الإدارية.');
            }

            return redirect()->route('portal.dashboard')
                ->withErrors(['error' => 'غير مصرح لك بالوصول إلى لوحة التحكم الإدارية. هذه الواجهة خاصة بمدير النظام فقط.']);
        }

        return $next($request);
    }
}

<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsurePermission;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            SecurityHeaders::class,
        ]);

        $middleware->alias([
            'admin' => EnsureAdmin::class,
            'permission' => EnsurePermission::class,
        ]);

        // توجيه الزوار غير المسجلين: حظر مسارات الإدارة التجسسية بـ 404 وتوجيه الميسرين للدخول
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->is('admin*') || $request->is('api*')) {
                abort(404);
            }
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

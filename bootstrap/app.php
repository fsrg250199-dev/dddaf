<?php

use App\Http\Middleware\EnsureIsActive;
use App\Http\Middleware\EnsureIsAdmin;
use App\Http\Middleware\EnsureIsEmployee;
use App\Http\Middleware\EnsureIsManager;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Registrar middleware para rutas
        $middleware->alias([
            'ensure.is.admin' => EnsureIsAdmin::class,
            'ensure.is.manager' => EnsureIsManager::class,
            'ensure.is.employee' => EnsureIsEmployee::class,
            'ensure.is.active' => EnsureIsActive::class,
            'role' => RoleMiddleware::class,
        ]);

        // Si quieres que sea global (opcional):
        // $middleware->append([
        //     App\Http\Middleware\RoleMiddleware::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

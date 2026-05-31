<?php

use App\Http\Middleware\ApiKeyMiddleware;
use App\Http\Middleware\EnsureEmployeeRole;
use App\Http\Middleware\EnsureOwnerRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'employee' => EnsureEmployeeRole::class,
            'owner' => EnsureOwnerRole::class,
            'api.key' => ApiKeyMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

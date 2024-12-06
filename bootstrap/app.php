<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware pour le groupe 'web'
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\RoleMiddleware::class, // Ajout ici pour le groupe 'web'
        ]);

        // Middleware pour le groupe 'api'
        $middleware->api(append: [
            \App\Http\Middleware\RoleMiddleware::class, // Ajout ici pour le groupe 'api' si nécessaire
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

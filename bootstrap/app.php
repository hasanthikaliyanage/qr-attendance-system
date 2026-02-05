<?php

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
        $middleware->alias([
            // Custom middleware aliases
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'lecturer' => \App\Http\Middleware\LecturerMiddleware::class,
            'student' => \App\Http\Middleware\StudentMiddleware::class,
            'must.change.password' => \App\Http\Middleware\MustChangePassword::class,
            'admin.or.lecturer' => \App\Http\Middleware\AdminOrLecturerMiddleware::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
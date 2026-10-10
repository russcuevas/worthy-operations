<?php

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
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'hq' => \App\Http\Middleware\HqMiddleware::class,
            'cluster_head' => \App\Http\Middleware\ClusterHeadMiddleware::class,
            'top_coor' => \App\Http\Middleware\TopCoorMiddleware::class,
            'registry_officer' => \App\Http\Middleware\RegistryOfficerMiddleware::class,
            'supervisor' => \App\Http\Middleware\SupervisorMiddleware::class,
            'staff' => \App\Http\Middleware\StaffMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

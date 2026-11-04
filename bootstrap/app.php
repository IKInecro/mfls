<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // Track page views for public pages and apply security headers
        $middleware->web(append: [
            \App\Http\Middleware\TrackPageView::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
        
        // TAMBAHKAN INI - Handle CORS di API
        $middleware->api(prepend: [
            HandleCors::class,
        ]);
        
        // Proxy trust
        $middleware->trustProxies(at: '*');

        // Alias middleware lain
        $middleware->alias([
            'survey.check' => \App\Http\Middleware\EnsureSurveyIsFilled::class,
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
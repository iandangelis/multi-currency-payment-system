<?php

use App\Exceptions\ExchangeRateNotFoundException;
use App\Exceptions\ExchangeRateUnavailableException;
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
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (
            ExchangeRateUnavailableException $e
        ) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 503);
        });

        $exceptions->render(function (
            ExchangeRateNotFoundException $e
        ) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        });
    })->create();

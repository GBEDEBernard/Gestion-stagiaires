<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Ngrok et les proxys HTTPS envoient X-Forwarded-Proto=https.
        // Sans cette confiance, Laravel croit être en HTTP et génère des
        // formulaires HTTP, ce qui déclenche l'alerte navigateur.
        $middleware->trustProxies(at: '*');

        // Enregistrer les middlewares Spatie
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'attendance' => \App\Http\Middleware\EnsureDailyAttendance::class,
            'account_active' => \App\Http\Middleware\EnsureAccountActive::class,
        ]);

        // Impersonation admin : bascule "en tant que" employé / stagiaire
        $middleware->web(append: [
            \App\Http\Middleware\Impersonate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            return response()->view('errors.404', [], 404);
        });

        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e) {
            return response()->view('errors.403', [], 403);
        });

        $exceptions->renderable(function (\Illuminate\Session\TokenMismatchException $e) {
            return response()->view('errors.419', [], 419);
        });

        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            if ($e->getStatusCode() === 403) {
                return response()->view('errors.403', [], 403);
            }
            if ($e->getStatusCode() === 404) {
                return response()->view('errors.404', [], 404);
            }
            if ($e->getStatusCode() === 401) {
                return response()->view('errors.401', [], 401);
            }
        });

        $exceptions->renderable(function (\Throwable $e) {
            if (config('app.debug')) {
                return;
            }
            return response()->view('errors.500', [], 500);
        });
    })
    ->create();

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        // health: '/up',

        using: function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
     
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->group(base_path('routes/pos.php'));

            Route::middleware('api')
                ->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,

            'Excel' => Maatwebsite\Excel\Facades\Excel::class,

            'auth' => \App\Http\Middleware\Authenticate::class,
            'admin' => \App\Http\Middleware\Admin::class,
            'admin.role' => \App\Http\Middleware\EnsureAdminRole::class,
            'pos' => \App\Http\Middleware\PosAuthenticate::class,
            'pos.api' => \App\Http\Middleware\PosApiAuth::class,
            // 'OAuth2'    => \App\Http\Middleware\OAuth2::class,
            'Image' => Intervention\Image\Facades\Image::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'Socialite' => Laravel\Socialite\Facades\Socialite::class,
        ]);
        $middleware->web(append: [
        ]);

        // Inbound PlutoPay webhooks carry no session/CSRF token; they are
        // authenticated by their HMAC signature instead.
        $middleware->validateCsrfTokens(except: [
            'webhooks/plutopay',
            'webhooks/nexo-pos-plutopay',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

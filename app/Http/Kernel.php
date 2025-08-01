<?php

namespace App\Http;

use App\Http\Middleware\DebugbarAPIProfiling;
use App\Http\Middleware\DemoMiddleware;
use App\Http\Middleware\LanguageManager;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\SetUploadTempDir::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            DemoMiddleware::class,
            LanguageManager::class,
            // \App\Http\Middleware\WizardSettings::class,
            // \App\Http\Middleware\CustomAuth::class,
        ],

        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            DemoMiddleware::class,
            // \App\Http\Middleware\CheckChild::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth'               => \App\Http\Middleware\Authenticate::class,
        'auth.basic'         => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers'      => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'                => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'              => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm'   => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed'             => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle'           => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'           => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role'               => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'permission'         => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
        'Role'               => \App\Http\Middleware\CheckRole::class,
        'checkChild'         => \App\Http\Middleware\CheckChild::class,
        'language'           => \App\Http\Middleware\LanguageManager::class,
        'checkStudent'       => \App\Http\Middleware\CheckStudent::class,
        'checkSchoolStatus'  => \App\Http\Middleware\CheckSchoolStatus::class,
        'status'             => \App\Http\Middleware\Status::class,
        'SwitchDatabase'             => \App\Http\Middleware\SwitchDatabase::class,
        'APISwitchDatabase'             => \App\Http\Middleware\APISwitchDatabase::class,
        'verifiedEmail'           => \App\Http\Middleware\MustVerifyEmail::class,
        'CheckForMaintenanceMode'           => \App\Http\Middleware\CheckForMaintenanceMode::class,
        '2fa' => \App\Http\Middleware\CheckTwoFactorAuthenticated::class,
        'wizardSettings' => \App\Http\Middleware\WizardSettings::class,
    ];
}

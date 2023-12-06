<?php

declare(strict_types=1);

namespace Fwstats\Providers;

use Fwstats\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

final class RouteServiceProvider extends ServiceProvider
{
    public const string HOME = '/home';

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $user = $request->user();

            return Limit::perMinute(60)->by($user instanceof User ? $user->id : $request->ip());
        });

        $this->routes(function () {
            Route::middleware('web')->group(base_path('routes/web.php'));
            Route::middleware('api')->prefix('api')->group(base_path('routes/api.php'));
        });
    }
}

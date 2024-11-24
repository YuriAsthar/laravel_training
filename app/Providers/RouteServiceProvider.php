<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;


class RouteServiceProvider extends ServiceProvider
{
    private const DEFAULT_WEB_SUBDOMAIN = 'app.%s';

    private const DEFAULT_API_SUBDOMAIN = 'api.%s';

    public function boot(): void
    {
        RateLimiter::for(
            'api',
            fn (Request $request) => Limit::perMinute(config('app.api.rate-limit-per-minute'))
                ->by($request->user()?->id ?: $request->ip()),
        );

        $this->routes(function (): void {
            $this->mapApiRoutes();
            $this->mapWebRoutes();
        });
    }

    protected function mapWebRoutes(): void
    {
        $domain = sprintf(self::DEFAULT_WEB_SUBDOMAIN, config('app.url'));

        Route::middleware('web')
            ->domain($domain)
            ->group(base_path('routes/web.php'));
    }

    protected function mapApiRoutes(): void
    {
        $domain = sprintf(self::DEFAULT_API_SUBDOMAIN, config('app.url'));

        Route::middleware('api')
            ->domain($domain)
            ->group(base_path('routes/api.php'));
    }
}

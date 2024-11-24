<?php

namespace App\Providers;

use Carbon\Carbon;
use Carbon\Translator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Translator::get('pt_BR')->addDirectory(base_path('lang/Carbon'));
        Carbon::setLocale('pt_BR.php');
    }
}

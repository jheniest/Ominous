<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\View\Composers\CategoryMenuComposer;
use App\View\Composers\HeadlinesComposer;

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
        View::composer(['layouts.app', 'components.app-layout'], CategoryMenuComposer::class);
        View::composer(['layouts.auth'], HeadlinesComposer::class);
    }
}

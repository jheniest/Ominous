<?php

namespace App\Providers;

use Carbon\Carbon;
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
        // Configura Carbon para português brasileiro
        Carbon::setLocale('pt_BR');
        setlocale(LC_TIME, 'pt_BR.UTF-8', 'pt_BR', 'Portuguese_Brazil.1252');
        
        View::composer(['layouts.app', 'components.app-layout'], CategoryMenuComposer::class);
        View::composer(['layouts.auth'], HeadlinesComposer::class);
    }
}

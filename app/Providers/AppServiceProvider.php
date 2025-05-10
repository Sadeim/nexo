<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Service;
use App\Models\Setting;
use App\Services\CheckoutService;
use App\Services\Payment\PaymentGateways\PaymentGatewayManager;
use App\Services\Payment\PaymentGateways\TelrGateway;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if(env('APP_SECURE')){
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            $settings = new Setting();
            $shared_categories = Category::active()->get()->take(5);
            $shared_services = Service::get()->take(5);    
 
            $view->with([
                'settings' => $settings,
                'shared_categories' => $shared_categories,
                'shared_services' => $shared_services,
            ]);
        });
    }
}

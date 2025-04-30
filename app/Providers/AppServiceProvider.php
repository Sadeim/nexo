<?php

namespace App\Providers;

use App\Models\Category;
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
        $this->app->bind(PaymentGatewayManager::class, function ($app) {
            return new PaymentGatewayManager();
        });

        $this->app->bind(CheckoutService::class, function ($app) {
            return new CheckoutService();
        });

        $this->app->bind(TelrGateway::class, function ($app) {
            return new TelrGateway();
        });
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

            // if(auth()->check()){
            //     $user = auth()->user();
            // }

            $cart_count = getCartCountFromCookies();
            // if (getCartCountFromCookies() > 0) {
            //     $cart_count = session('cart_count', getCartCountFromCookies());
            // } else
            if(auth()->check() && auth()->user()->cart){ 
                $cart_count = auth()->user()->cart->products->count();
            }
 
            $view->with([
                'settings' => $settings,
                'shared_categories' => $shared_categories,
                'shared_cart_count' => $cart_count,
            ]);
        });
    }
}

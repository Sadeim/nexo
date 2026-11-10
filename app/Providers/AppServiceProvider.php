<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Employee;
use App\Models\Service;
use App\Models\Setting;
use App\Services\CheckoutService;
use App\Services\Payment\PaymentGateways\PaymentGatewayManager;
use App\Services\Payment\PaymentGateways\TelrGateway;
use App\Support\Portal;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
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

            // The booking modal lives in the footer, so it renders on every
            // page and needs the barber list everywhere — not only on the
            // pages whose controller happens to pass it.
            //
            // Which also means this query runs on every page, so it is cached
            // and cannot throw: see Employee::publicBarbersCached().
            $shared_barbers = Employee::publicBarbersCached();

            $view->with([
                'settings' => $settings,
                'shared_categories' => $shared_categories,
                'shared_services' => $shared_services,
                'shared_barbers' => $shared_barbers,
            ]);
        });

        $this->bootPortal();
    }

    /**
     * The employee portal's shared wiring.
     *
     * Kept apart from the composer above because none of it touches the
     * database, and the portal should not acquire a dependency on the CMS
     * tables by accident.
     */
    private function bootPortal(): void
    {
        /*
         * One login bucket per IP for the whole portal.
         *
         * `throttle:5,1` would key on the request fingerprint, which includes
         * the URI — so /me/login and /work/login would get five attempts each
         * and the real limit would be ten. Keying on the IP alone makes the
         * two mounts share one allowance, which is what "5 a minute" meant.
         */
        /*
         * The booking modal's phone lookup. Ten a minute per IP: enough for a
         * customer who mistypes their number a few times, little enough that
         * walking a range of numbers to see which ones the shop knows is not
         * worth anyone's afternoon.
         */
        RateLimiter::for('booking-identify', function ($request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('portal-login', function ($request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        /*
         * Everything a portal view needs to know about which version it is in.
         *
         * Scoped to portal.* views, and deliberately free of queries: the one
         * model read here is the already-authenticated employee, which the
         * guard has resolved before any view renders.
         */
        View::composer('portal.*', function ($view) {
            $variant = Portal::variant();
            $employee = Auth::guard('employee')->user();

            $view->with([
                'portalVariant' => $variant,
                'portalIsDesktop' => $variant === Portal::DESKTOP,
                'portalLayout' => Portal::layout(),

                // Builds links that stay in the version the barber is using.
                'portalRoute' => fn (string $screen, array $parameters = []) => Portal::route($screen, $parameters),

                // The same screen in the other version — the handover target
                // when a /work window is too narrow for it.
                'portalCounterpart' => Portal::counterpartUrl(),
                'portalMinWidth' => Portal::MIN_DESKTOP_WIDTH,

                // 'today', 'bookings', ... — the screen without either
                // version's prefix, so nav highlighting is written once.
                'portalScreen' => Portal::screenFrom(request()->route()?->getName() ?? ''),

                'portalUser' => $employee,

                // Whether this employee reads the whole shop or their own
                // chair. Here rather than in each view so a screen cannot
                // accidentally ask a different question than the controller
                // that filled it did.
                'portalIsManager' => (bool) $employee?->isManager(),
            ]);
        });
    }
}

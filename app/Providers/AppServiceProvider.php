<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\URL;
use App\Services\AttendanceService;
use App\Services\LeaveService;
use Illuminate\Pagination\Paginator;

use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Force register files if not already registered
        if (!$this->app->bound('files')) {
            $this->app->singleton('files', function ($app) {
                return new Filesystem();
            });
        }

        // ================= REGISTER SERVICES =================
        $this->app->singleton(AttendanceService::class, function ($app) {
            return new AttendanceService();
        });

        $this->app->singleton(LeaveService::class, function ($app) {
            return new LeaveService($app->make(AttendanceService::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Override Eloquent User Provider for Offline POS Support
        Auth::provider('eloquent', function ($app, array $config) {
            return new \App\Auth\OfflineUserProvider($app['hash'], $config['model']);
        });

        // Force HTTPS in production (Render / Cloud hosting)
        //  if (config('app.env') === 'production' || env('APP_FORCE_HTTPS', true)) {
        if (config('app.env') === 'production' || (env('APP_FORCE_HTTPS', false) && !app()->isLocal())) {
            URL::forceScheme('https');
        }

        // Force Bootstrap 5 for Pagination
        Paginator::useBootstrapFive();

        // Pass pending counts to sidebar
        view()->composer('layouts.app', function ($view) {
            try {
                if (auth()->check()) {
                    $pendingStaffCount = \App\Models\User::where('role', 'staff')
                        ->whereNotIn('status', ['approved', 'rejected'])
                        ->count();
                    $pendingAgentCount = \App\Models\DeliveryAgent::whereNotIn('approval_status', ['approved', 'rejected'])
                        ->count();
                    $pendingHrCount = \App\Models\User::where('role', 'hr')
                        ->whereNotIn('status', ['approved', 'rejected'])
                        ->count();

                    $view->with('sidebarPendingStaff', $pendingStaffCount);
                    $view->with('sidebarPendingAgent', $pendingAgentCount);
                    $view->with('sidebarPendingHr', $pendingHrCount);
                }
            } catch (\Throwable $e) {
                $view->with('sidebarPendingStaff', 0);
                $view->with('sidebarPendingAgent', 0);
                $view->with('sidebarPendingHr', 0);
            }
        });
        // Register Observers for Notifications
        \App\Models\Sale::observe(\App\Observers\SaleObserver::class);
        \App\Models\Customer::observe(\App\Observers\CustomerObserver::class);
        \App\Models\Purchase::observe(\App\Observers\PurchaseObserver::class);
        \App\Models\Leave::observe(\App\Observers\LeaveObserver::class);
        \App\Models\Employee::observe(\App\Observers\EmployeeObserver::class);
        \App\Models\Shipment::observe(\App\Observers\ShipmentObserver::class);
        \App\Models\Attendance::observe(\App\Observers\AttendanceObserver::class);
        \App\Models\Product::observe(\App\Observers\ProductObserver::class);
    }
}

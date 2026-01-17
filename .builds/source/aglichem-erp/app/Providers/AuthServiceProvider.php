<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Dealer;
use App\Models\Dispatch;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Policies\DealerPolicy;
use App\Policies\DispatchPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Order::class => OrderPolicy::class,
        Product::class => ProductPolicy::class,
        Dealer::class => DealerPolicy::class,
        Dispatch::class => DispatchPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register gates for additional permissions
        $this->registerGates();
    }

    /**
     * Register authorization gates.
     */
    protected function registerGates(): void
    {
        \Gate::define('manage-orders', function ($user) {
            return $user->hasAnyRole(['admin', 'sales_officer']);
        });

        \Gate::define('manage-dispatches', function ($user) {
            return $user->hasAnyRole(['admin', 'dispatch_officer']);
        });

        \Gate::define('view-reports', function ($user) {
            return $user->hasAnyRole(['admin', 'accountant']);
        });
    }
}


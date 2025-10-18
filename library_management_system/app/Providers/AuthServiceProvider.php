<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        'App\Models\User' => 'App\Policies\UserPolicy',
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('view-admin-sidebar', function ($user) {
            return $user->role === 'librarian';
        });

        Gate::define('view-header-links', function ($user = null) {

            if (is_null($user)) return true; //Allow guests to view header links

            return in_array($user->role, ['student', 'teacher']);
        });
    }
}

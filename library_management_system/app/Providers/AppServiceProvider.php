<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;
use App\Models\Semester;

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
        // Share categories with the user sidebar component
        View::composer('components.user-sidebar', function ($view) {
            $view->with('categories', Category::with('genres')->orderBy('name')->get());
        });

        // Share active semester with all views
        View::composer('*', function ($view) {
            $view->with('activeSemester', Semester::where('status', 'active')->first());
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Settings;

class SettingsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Fetch all settings as key => value
        $settings = Settings::all()->pluck('value', 'key')->toArray();

        // Transform keys into nested arrays (e.g., 'borrowing.max_books_per_student')
        $nested = [];
        foreach ($settings as $key => $value) {
            \Illuminate\Support\Arr::set($nested, $key, $value);
        }

        // Add to Laravel config
        config(['settings' => $nested]);
    }

    public function register()
    {
        //
    }
}

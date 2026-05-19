<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        view()->composer('layouts.app', function ($view) {
            $notificationCount = 0;
            if (auth()->check()) {
                $notificationCount = \App\Models\VaccinationReminder::where('user_id', auth()->id())
                    ->whereIn('status', ['sent', 'completed'])
                    ->count();
            }
            $view->with('notificationCount', $notificationCount);
        });
    }
}

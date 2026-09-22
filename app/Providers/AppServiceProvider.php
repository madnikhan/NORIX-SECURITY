<?php

namespace App\Providers;

use App\Models\Site;
use App\Models\StaffNotification;
use App\Observers\SiteObserver;
use Illuminate\Support\Facades\Auth;
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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Site::observe(SiteObserver::class);

        View::composer('layouts.staff', function ($view): void {
            $unread = 0;
            $user = Auth::guard('staff')->user();
            if ($user) {
                $unread = StaffNotification::query()
                    ->where('guard_id', $user->id)
                    ->whereNull('read_at')
                    ->count();
            }
            $view->with('unreadAlerts', $unread);
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');

            $root = config('app.url');
            if (is_string($root) && $root !== '') {
                URL::forceRootUrl(rtrim($root, '/'));
            }
        }
    }
}

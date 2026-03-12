<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
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
        // This logic runs every time 'components.sidebar' is rendered
        View::composer('components.sidebar', function ($view) {
            if (auth()->check()) {
                // Fetch departments with counts once for the entire sidebar
                $sidebarDepartments = auth()->user()->departments()
                    ->withCount(['tickets as pending_tickets_count' => function ($query) {
                        $query->whereNull('tck_date_action')
                              ->where('tck_active', 1);
                    }])->get();

                // Share the variable with the view
                $view->with('sidebarDepartments', $sidebarDepartments);
            }
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View; 
use Illuminate\Support\Facades\Auth;
use App\Models\Department;
use App\Models\Feedback;
use App\Models\Ticket;
use App\Models\Action;
use App\Observers\FeedbackObserver;

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
        Feedback::observe(FeedbackObserver::class);
        
        Paginator::useBootstrapFive();

        View::composer('components.sidebar', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                // Feedbacks that haven't been turned into tickets yet
                $validationCount = Feedback::where('fbk_status', 0)->count();

                // Tickets assigned to departments that are still open
                // If Dept Head, only count their department's tickets
                $actionCount = 0;
                if ($user->hasAnyRole( 'Department Head')) {
                    $actionQuery = Ticket::where('tck_active', 1);

                        $deptIds = $user->departments->pluck('dep_id');
                        $actionQuery->whereIn('dep_id', $deptIds);
                    
                    
                    $actionCount = $actionQuery->count();
                }

                // Actions that needs verification        
                $verificationCount = Action::where('act_status', 0)
                                        ->count();      

                // Keep your existing sidebarDepartments logic
                $sidebarDepartments = Department::where('dep_active', 1)
                    ->withCount(['tickets as pending_tickets_count' => function ($query) {
                        $query->where('tck_active', 1);
                    }])->get();

                $view->with([
                    'validationCount' => $validationCount,
                    'actionCount' => $actionCount,
                    'verificationCount' => $verificationCount,
                    'sidebarDepartments' => $sidebarDepartments
                ]);
            }
        });
    }
}

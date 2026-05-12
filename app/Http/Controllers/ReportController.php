<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Department;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    // ==========================================
    // 1. FEEDBACK TRANSACTIONS
    // ==========================================
    public function transactions(Request $request)
    {
        // Simple paginated log of all tickets with their relationships loaded
        $tickets = Ticket::with(['feedback.type', 'feedback.theme', 'department'])
            ->latest('tck_date_created')
            ->paginate(15)
            ->withQueryString();

        return view('reports.transactions', compact('tickets'));
    }

    // ==========================================
    // 2. TRANSACTION ANALYSIS
    // ==========================================
    public function analysis()
    {
        // Total Volume Trends (Last 30 Days)
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $volumeTrends = Ticket::select(DB::raw('DATE(tck_date_created) as date'), DB::raw('count(*) as total'))
            ->where('tck_date_created', '>=', $thirtyDaysAgo)
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        // Status Distribution (Open vs Resolved)
        $statusDistribution = [
            'Resolved' => Ticket::whereNotNull('tck_date_verified')->count(),
            'Open/In Progress' => Ticket::whereNull('tck_date_verified')->count(),
        ];

        // Department Workload
        $departmentWorkload = Department::withCount('tickets')
            ->orderByDesc('tickets_count')
            ->get();

        // Branch Volume
        $branchVolume = DB::table('feedbacks')
            ->select('branch_id', DB::raw('count(*) as total'))
            ->groupBy('branch_id')
            ->orderByDesc('total')
            ->get();

        // Average Resolution Time (in Hours)
        // Calculates the difference between creation and verification for resolved tickets
        $avgResolutionHours = Ticket::whereNotNull('tck_date_verified')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, tck_date_created, tck_date_verified)) as avg_hours'))
            ->value('avg_hours');

        return view('reports.analysis', compact(
            'volumeTrends', 
            'statusDistribution', 
            'departmentWorkload', 
            'branchVolume', 
            'avgResolutionHours'
        ));
    }

    // ==========================================
    // 3. CUSTOMER SATISFACTION (CSAT)
    // ==========================================
    public function satisfaction()
    {
        // Base query for tickets that actually have a rating
        $ratedTickets = Ticket::whereNotNull('tck_rate');

        // Overall Average Rating
        $overallAverage = round($ratedTickets->avg('tck_rate'), 2);

        // Rating Distribution (How many 5s, 4s, 3s, etc.)
        // Pluck formats it nicely as [5 => 120, 4 => 85, ...]
        $ratingDistribution = Ticket::select('tck_rate', DB::raw('count(*) as total'))
            ->whereNotNull('tck_rate')
            ->groupBy('tck_rate')
            ->orderByDesc('tck_rate')
            ->pluck('total', 'tck_rate');

        // Performance by Department (Average rating per department)
        $departmentPerformance = Department::select('departments.dep_id', 'departments.dep_name')
            ->withAvg(['tickets as average_rating' => function ($query) {
                $query->whereNotNull('tck_rate');
            }], 'tck_rate')
            ->orderByDesc('average_rating')
            ->get();

        // Resolution Rate (%)
        $totalTickets = Ticket::count();
        $closedTickets = $ratedTickets->count();
        $resolutionRate = $totalTickets > 0 ? round(($closedTickets / $totalTickets) * 100, 1) : 0;

        // Recent Reviews Feed (Last 10 rated tickets)
        $recentReviews = Ticket::with(['feedback', 'department'])
            ->whereNotNull('tck_rate')
            ->latest('tck_date_verified')
            ->take(10)
            ->get();

        return view('reports.satisfaction', compact(
            'overallAverage',
            'ratingDistribution',
            'departmentPerformance',
            'resolutionRate',
            'recentReviews'
        ));
    }
}
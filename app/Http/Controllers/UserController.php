<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Charts\FeedbackChart;
use App\Charts\TopDepartmentsChart;
use App\Charts\VolumeChart;
use App\Charts\ClassificationChart;
use App\Charts\DepartmentChart;
use App\Charts\ConfidenceChart;

class UserController extends Controller
{

    public function dashboard(FeedbackChart $feedbackChart, TopDepartmentsChart $topDepartmentsChart)
    {
        return view('dashboard', [
            'feedbackChart' => $feedbackChart->build(),
            'topDepartmentsChart' => $topDepartmentsChart->build(),
        ]);
    }

    public function feedbacks()
    {
        return view('feedbacks');
    }

    public function search()
    {
        return view('search');
    }

    public function analytics()
    {
        // Load JSON data
        $json = file_get_contents('sample_feedback.json');
        $feedbacks = json_decode($json, true);

        // Stats calculation
        $total = count($feedbacks);
        $reviewed = count(array_filter($feedbacks, fn($f) => !empty($f['status'])));
        $routed = count(array_filter($feedbacks, fn($f) => !empty($f['department'])));
        $avg_confidence = array_sum(array_column($feedbacks, 'confidence')) / max($total, 1);

        $stats = [
            'total' => $total,
            'reviewed' => $reviewed,
            'routed' => $routed,
            'avg_confidence' => $avg_confidence,
        ];

        // Sample chart data from JSON
        $volumeChart = (new LarapexChart)->lineChart()
            ->addData('Feedback Count', [3, 5, 2, 4, 6, 3, 5])
            ->setHeight(250)
            ->setXAxis(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);

        $classificationChart = (new LarapexChart)->pieChart()
            ->addData([
                count(array_filter($feedbacks, fn($f) => $f['classification'] === 'Complaint')),
                count(array_filter($feedbacks, fn($f) => $f['classification'] === 'Suggestion')),
                count(array_filter($feedbacks, fn($f) => $f['classification'] === 'Commendation')),
                count(array_filter($feedbacks, fn($f) => $f['classification'] === 'Inquiry')),
                count(array_filter($feedbacks, fn($f) => $f['classification'] === 'Concern')),
            ])
            ->setHeight(250)
            ->setLabels(['Complaint', 'Suggestion', 'Commendation', 'Inquiry', 'Concern']);

        $departmentChart = (new LarapexChart)->barChart()
            ->addData('Feedbacks', [
                count(array_filter($feedbacks, fn($f) => $f['department'] === 'Registrar')),
                count(array_filter($feedbacks, fn($f) => $f['department'] === 'Library')),
                count(array_filter($feedbacks, fn($f) => $f['department'] === 'Facilities')),
            ])
            ->setHeight(250)
            ->setXAxis(['Registrar', 'Library', 'Facilities']);

        $confidenceChart = (new LarapexChart)->areaChart()
            ->addData('Low Confidence Count', [1, 0, 1, 0, 1]) // Just sample values
            ->setHeight(250)
            ->setXAxis(['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5']);

        return view('analytics', compact(
            'stats',
            'volumeChart',
            'classificationChart',
            'departmentChart',
            'confidenceChart'
        ));
        
    }

    public function departments()
    {
        return view('departments');
    }

    public function auditLog()
    {
        return view('admin.audit-log');
    }

    public function settings()
    {
        return view('settings');
    }
}

<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class TopDepartmentsChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\BarChart
    {
        return $this->chart->barChart()
            ->setSubtitle('Number of Feedbacks')
            ->addData('Feedback Count', [45, 31, 18, 12, 9])
            ->setHeight(250)
            ->setXAxis(['Registrar', 'IT Services', 'Library', 'Finance', 'Admissions']);
    }
}

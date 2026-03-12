<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class DepartmentChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\BarChart
    {
        return $this->chart->barChart()
        ->addData('Feedbacks', [12, 9, 7])
        ->setXAxis(['Registrar', 'Finance', 'IT']);
    }
}

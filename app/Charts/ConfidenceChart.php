<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class ConfidenceChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\AreaChart
    {
        return $this->chart->areaChart()
        ->addData('Low Confidence Count', [3, 2, 5, 4, 6])
        ->setXAxis(['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5']);
    }
}

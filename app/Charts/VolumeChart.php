<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class VolumeChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\LineChart
    {
        return $this->chart->lineChart()
        ->setTitle('Daily Feedback')
        ->addData('Feedback Count', [5, 8, 12, 4, 6, 10, 14])
        ->setXAxis(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);
    }
}

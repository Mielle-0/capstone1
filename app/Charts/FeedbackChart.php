<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class FeedbackChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\LineChart
    {
        return $this->chart->lineChart()
            ->setSubtitle('Automatically generated')
            ->addData('Feedback Received', [12, 18, 15, 22, 30, 25, 19])
            ->setHeight(250)
            ->setXAxis(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);
    }
}

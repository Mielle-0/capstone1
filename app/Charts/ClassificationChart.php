<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class ClassificationChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\PieChart
    {
        return $this->chart->pieChart()
        ->setTitle('Feedback by Classification')
        ->addData([40, 20, 20, 10, 10])
        ->setLabels(['Complaint', 'Suggestion', 'Commendation', 'Inquiry', 'Concern']);
    }
}

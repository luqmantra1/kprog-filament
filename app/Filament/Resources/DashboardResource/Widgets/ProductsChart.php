<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\Product;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ProductsChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected static ?int $sort =3;

    protected function getData(): array
    {
        $data = $this->getProductsPerMonth();

        return [
            'datasets' => [
                [
                    'label' => 'Products Created', // ✅ Fixed label
                    'data' => $data['productsPerMonth']
                ]
            ],
            'labels' => $data['months']
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getProductsPerMonth(): array
    {
        $now = Carbon::now();

        $productsPerMonth = collect(range(1, 12))->map(function ($month) use ($now) {
            return Product::whereYear('created_at', $now->year) // ✅ Ensures current year
                ->whereMonth('created_at', $month)
                ->count();
        })->toArray();

        $months = collect(range(1, 12))->map(function ($month) use ($now) {
            return $now->month($month)->format('M');
        })->toArray();

        return [
            'productsPerMonth' => $productsPerMonth,
            'months' => $months
        ];
    }
}

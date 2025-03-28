<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\Order;
use App\Enums\OrderStatusEnum; // Ensure you have this Enum
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OrdersChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Orders Status Chart';

    protected function getData(): array
    {
        // Get all possible statuses
        $statuses = collect(OrderStatusEnum::cases())->map(fn($status) => $status->value)->toArray();

        // Get order counts grouped by status
        $data = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Ensure all statuses are in the dataset, even if count is 0
        $formattedData = [];
        foreach ($statuses as $status) {
            $formattedData[$status] = $data[$status] ?? 0; // Set 0 if status not found
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => array_values($formattedData),
                ],
            ],
            'labels' => array_keys($formattedData),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

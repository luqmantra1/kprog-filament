<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\Order; // ✅ Use the Order model, not OrderResource
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn; // ✅ Import TextColumn
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected static ?int $sort = 4; // ✅ Fixed missing semicolon

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query() // ✅ Correct query
                    ->orderBy('created_at', 'desc')
            )
            ->paginated([5]) // ✅ Correct pagination method
            ->columns([
                TextColumn::make('number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Order Date')
                    ->date(),
            ]);
    }
}

<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Flowframe\Trend\Trend;
use App\Models\Transaction;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class WidgetExpenseChart extends ChartWidget
{
    use InteractsWithPageFilters;
    
    protected static ?string $heading = 'Credit';
    protected static string $color = 'danger';

    protected function getData(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null)
            ? Carbon::parse($this->filters['startDate'])
            : now()->startOfYear();

        $endDate = ! is_null($this->filters['endDate'] ?? null)
            ? Carbon::parse($this->filters['endDate'])
            : now();
        
        $data = Trend::query(Transaction::pengeluaran())
        ->between(
            start: $startDate,
            end: $endDate,
        )
        ->perWeek()
        ->sum('amount');
 
        return [
            'datasets' => [
                [
                    'label' => 'pengeluaran',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

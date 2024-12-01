<?php

namespace App\Filament\Santri\Widgets;

use Carbon\Carbon;
use Flowframe\Trend\Trend;
use App\Models\Transaction;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class WidgateIncomeChart extends ChartWidget
{
    protected static ?string $heading = 'pemasukan';
    protected static string $color = 'success';

    protected function getData(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null)
            ? Carbon::parse($this->filters['startDate'])
            : now()->startOfYear();

        $endDate = ! is_null($this->filters['endDate'] ?? null)
            ? Carbon::parse($this->filters['endDate'])
            : now();

        // Mendapatkan ID user yang sedang login
        $userId = auth()->user()->id;

        // Query untuk mengambil data transaksi hanya untuk user yang sedang login
        $transactionQuery = Transaction::pemasukan()
            ->where('user_id', $userId) // Menambahkan filter untuk user yang sedang login
            ->whereBetween('date_transaction', [$startDate, $endDate]);

        // Pastikan untuk menyetel rentang tanggal dengan benar
        $data = Trend::query($transactionQuery)
            ->between($startDate, $endDate) // Menyatakan rentang tanggal secara eksplisit
            ->perDay()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Pengeluaran',
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

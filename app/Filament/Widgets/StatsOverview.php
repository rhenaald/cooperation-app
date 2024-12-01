<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;
    
    protected function getStats(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null)
            ? Carbon::parse($this->filters['startDate'])
            : now()->startOfYear();

        $endDate = ! is_null($this->filters['endDate'] ?? null)
            ? Carbon::parse($this->filters['endDate'])
            : now();
        
        // Sesuaikan nama kolom dengan yang ada di tabel
        $pemasukan = Transaction::pemasukan()
            ->whereBetween('date_transaction', [$startDate, $endDate]) // Gunakan 'date_transaction' atau nama yang benar
            ->sum('amount');

        $pengeluaran = Transaction::pengeluaran()
            ->whereBetween('date_transaction', [$startDate, $endDate]) // Sesuaikan juga di sini
            ->sum('amount');

        $santriCount = User::role('santri')->count();
        return [
            Stat::make('Jumlah Santri', (string) $santriCount),
            Stat::make('Uang Tersedia','Rp. '.' '. $pemasukan - $pengeluaran),
            Stat::make('Total Pemasukan', 'Rp. '.' '.$pemasukan),
            Stat::make('Total Pengeluaran', 'Rp. '.' '.$pengeluaran)
        ];
    }
}

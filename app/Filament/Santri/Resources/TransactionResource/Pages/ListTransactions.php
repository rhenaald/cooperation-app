<?php

namespace App\Filament\Santri\Resources\TransactionResource\Pages;

use App\Filament\Santri\Resources\TransactionResource;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    protected function getTableQuery(): ?Builder
    {
        return Transaction::where('user_id', Auth::user()->id);
    }
}

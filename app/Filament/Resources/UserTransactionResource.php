<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\UserTransaction;
use Filament\Resources\Resource;
// use Illuminate\Foundation\Auth\User;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserTransactionResource\Pages;
use App\Filament\Resources\UserTransactionResource\RelationManagers;
use App\Models\Transaction;

class UserTransactionResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Tabungan Santri';

    protected static ?string $navigationGroup = 'Tabungan';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('saldo')
                ->getStateUsing(function ($record) {
                    $pemasukan =  $record->transactions
                        ->filter(function ($transaction) {
                            return $transaction->category_id === 1;
                        })
                        ->sum('amount');
                    
                    $pengeluaran =  $record->transactions
                        ->filter(function ($transaction) {
                            return $transaction->category_id === 2;
                        })
                        ->sum('amount');

                    return $pemasukan - $pengeluaran;
                })
                -> money("IDR"),
                Tables\Columns\TextColumn::make('Debit')
                ->getStateUsing(function ($record) {
                    return $record->transactions
                        ->filter(function ($transaction) {
                            return $transaction->category_id === 1;
                        })
                        ->sum('amount');
                })
                -> money("IDR"),
                Tables\Columns\TextColumn::make('Credit')
                ->getStateUsing(function ($record) {
                    return $record->transactions
                        ->filter(function ($transaction) {
                            return $transaction->category_id === 2;
                        })
                        ->sum('amount');
                })
                -> money("IDR"),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('debit')
                    ->label('Debit')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Nominal Debit')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10000000),
                        Forms\Components\TextInput::make('note')
                            ->label('Catatan')
                            ->nullable() 
                            ->maxLength(255), 
                    ])
                    ->action(function ($record, array $data) {
                        $amount = $data['amount'];
                        $note = $data['note'] ?? 'Tidak ada catatan';

                        Transaction::create([
                            'user_id' => $record->id,
                            'amount' => $amount,
                            'date_transaction' => now(),
                            'category_id' => 1, 
                            'note' => $note,
                        ]);
                    }),
                
                Tables\Actions\Action::make('credit')
                    ->label('Credit')
                    ->icon('heroicon-o-minus')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Nominal Credit')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10000000),
                    ])
                    ->action(function ($record, array $data) {
                        $amount = $data['amount'];
                        Transaction::create([
                            'user_id' => $record->id,
                            'amount' => $amount,
                            'date_transaction' => now(),
                            'category_id' => 2,
                            'note' => 'Tambah credit',
                        ]);
                    }),
                
                Tables\Actions\Action::make('show')
                    ->label('Lihat Transaksi')
                    ->icon('heroicon-o-eye')
                    ->action(function ($record) {
                        return redirect()->route('filament.dashboard.resources.transactions.index', [
                            'user_id' => $record->id,
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserTransactions::route('/'),
        ];
    }
}

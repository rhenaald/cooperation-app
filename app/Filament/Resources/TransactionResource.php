<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Tabungan';

    public static function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        // Cek apakah terdapat parameter `user_id` di URL
        $userId = request()->get('user_id');
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                Forms\Components\DatePicker::make('date_transaction')
                    ->required()
                    ->default(now()) 
                    ->maxDate(now()),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('note')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('user.name')->label('User')->sortable(),
            Tables\Columns\TextColumn::make('date_transaction')->date()->sortable(),
            Tables\Columns\TextColumn::make('category.name')->label('Kategori')->sortable(),
            Tables\Columns\TextColumn::make('amount')->numeric()->prefix("Rp. ")->sortable(),
            Tables\Columns\TextColumn::make('note')->searchable(),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('user_id')
            ->label('Filter User')
            ->relationship('user', 'name')
            ->preload()
            ->default(request()->get('user_id')),
        ])->hiddenFilterIndicators()
        ->actions([
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTransactions::route('/'),
            // 'create' => Pages\CreateTransaction::route('/create'),
            // 'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}

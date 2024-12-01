<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Manage Pengguna';

    protected static ?string $navigationGroup = 'Manage';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(('User Detail'))->schema([
                    Forms\Components\TextInput::make('name')
                    ->required(fn ($context) => $context === 'create')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(fn ($context) => $context === 'create')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->default(fn ($context) => $context === 'create' ? '12345' : null)
                    ->required(fn ($context) => $context === 'create')
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                Forms\Components\Select::make('role')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->default(function ($record) {
                        return Role::where('name', 'santri')->first()?->id;
                    }),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Jenis Pengguna') 
                    ->getStateUsing(fn ($record) => $record->roles->pluck('name')->join(', '))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function create(array $data): User
    {
        // Setelah form disubmit, Anda bisa menetapkan role secara eksplisit
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'santri',  // Set role secara eksplisit
        ]);

        return $user;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            // 'create' => Pages\CreateUser::route('/create'),
            // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

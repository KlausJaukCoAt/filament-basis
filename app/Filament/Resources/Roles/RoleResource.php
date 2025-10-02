<?php 

namespace App\Filament\Resources\Roles;

use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\Role;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Support\Icons\Heroicon;
use UnitEnum;


class RoleResource extends Resource
{



    protected static ?string $model = Role::class;
        
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Key;
    protected static string|UnitEnum|null $navigationGroup = 'User and Roles';
   
    
    protected static ?int $navigationSort = 2;


    # Permission
    public static function canAccess(): bool
    {
        $user = auth()->guard()->user();
        return $user instanceof User && $user->can('manage permissions');
    }

      public static function form(Schema $schema): Schema
        {
            return $schema->schema([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('permissions')
                    ->multiple()
                    ->relationship('permissions', 'name')
                    ->preload()
                    ->label('Berechtigungen'),

            ]);
        }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('permissions.name')
                ->label('Berechtigungen')
                ->badge()
                ->limit(3),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
    
}
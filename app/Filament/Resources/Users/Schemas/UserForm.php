<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')                    
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),
                #DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->label('Passwort')
                    ->password()
                    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                    ->dehydrated(fn ($state) => filled($state))
                    ->rule(Password::defaults()->mixedCase()->numbers()->symbols())
                    ->confirmed(),

                    TextInput::make('password_confirmation')
                        ->label('Passwort bestätigen')
                        ->password()
                        ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                        ->dehydrated(false),

                        Select::make('roles')
                            ->label('Rollen')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                        ->searchable(),
                    ]);
    }
}

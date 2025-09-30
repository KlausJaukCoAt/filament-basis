<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    # Notificaton after creating a user
    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
    protected function afterCreate(): void
    {
        Notification::make()
            ->title('User created: ' . $this->record->name)
            ->success()
            ->send();
    }
}

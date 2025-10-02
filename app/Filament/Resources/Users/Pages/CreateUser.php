<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

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
        // Neue Permissions aus dem Formular
        $permissions = Permission::whereIn('id', $this->data['roles'])->pluck('name')->toArray();

        // Logging
        activity()
            ->performedOn($this->record)
            ->useLog('role')
            ->event('updated')
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => $permissions,
            ])
            ->log("Permissions added");  
    }
}

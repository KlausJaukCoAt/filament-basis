<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected array $oldData = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    # Notificaton after edit a user
    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function beforeSave(): void
    {
        // Alte Permissions sichern, bevor sie überschrieben werden
        $this->oldData = $this->record->toArray();
    }
    protected function afterSave(): void
    {

        // Logging
        activity()
            ->performedOn($this->record)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $this->oldData,
                'new' => $this->record->toArray(),
            ])
            ->log("User '{$this->record->name}' wurde geändert");

        Notification::make()
            ->title('User ID: ' . $this->record->id .' modified')
            ->success()
            ->send();
    }
}

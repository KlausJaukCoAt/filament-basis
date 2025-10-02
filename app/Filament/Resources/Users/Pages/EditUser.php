<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Traits\HasRecordLocking;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Actions\Action as FormAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\Role as Roles;

class EditUser extends EditRecord
{
    use HasRecordLocking;
    protected static string $resource = UserResource::class;

    protected array $oldData = [];
    protected array $oldRoles = [];

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
  
        // Alte Daten sichern, bevor sie überschrieben werden
        $this->oldData = $this->record->toArray();
        
        // Alte Rollen sichern, bevor sie überschrieben werden
        $this->oldRoles = $this->record->roles->pluck('name')->toArray();
        
    }
    protected function afterSave(): void
    {        
            // Locking
        $this->unlockRecord($this->record);

        // Neue Roles aus dem Formular
        $rolesIds = $this->data['roles'] ?? [];
        $newRoles= Roles::whereIn('id', $rolesIds)->get();
        
        // Permissions synchronisieren
        $this->record->syncPermissions($newRoles);                
        

        // Logging
        activity()
            ->performedOn($this->record)
            ->useLog('user')
            ->event('updated')
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $this->oldRoles,
                'new' => $newRoles->pluck('name')->toArray(),
            ])
            ->log("User modified");


    }
    // Record Locking Trait
    // Sperrung bei Edit
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->lockRecord($this->record); // Muss aktiv sein
        return $data;
    }
    // bei Save entsperren
    protected function afterEdit(): void
    {
        $this->unlockRecord($this->record);
    }
        protected function getFormActions(): array
    {
        return [
        FormAction::make('save')
                ->label('save')
                ->color('primary')
                ->icon('heroicon-o-x-circle')
                ->action(function () {
                    $this->save(); // Eintrag speichern
                    $this->unlockRecord($this->record);
                    return redirect()->route('filament.admin.resources.users.index');
                }),
            FormAction::make('cancel')
                ->label('cancel')
                ->color('gray')
                ->icon('heroicon-o-x-circle')
                ->action(function () {
                    $this->unlockRecord($this->record);
                    return redirect()->route('filament.admin.resources.users.index');
                }),
        ];
    }
}

<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Traits\HasRecordLocking;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\Action as FormAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;



class EditRole extends EditRecord
{
    use HasRecordLocking;

    protected static string $resource = RoleResource::class;

    protected array $oldPermissions = [];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    # Activity Log & Notification

    protected function beforeSave(): void
    {
        // Alte Permissions sichern, bevor sie überschrieben werden
        $this->oldPermissions = $this->record->permissions->pluck('name')->toArray();
    }
    protected function afterSave(): void
    {
        // Locking
        $this->unlockRecord($this->record);

        // Neue Permissions aus dem Formular
        $permissionIds = $this->data['permissions'] ?? [];
        $newPermissions = Permission::whereIn('id', $permissionIds)->get();

        // Permissions synchronisieren
        $this->record->syncPermissions($newPermissions);

        // Logging
        activity()
            ->performedOn($this->record)
            ->useLog('role')
            ->event('updated')
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $this->oldPermissions,
                'new' => $newPermissions->pluck('name')->toArray(),
            ])
            ->log("Role modified");  
    }


    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
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
                ->label('save changes')
                ->color('primary')
                ->icon('heroicon-o-x-circle')
                ->action(function () {
                    $this->unlockRecord($this->record);
                    return redirect()->route('filament.admin.resources.roles.index');
                }),
            FormAction::make('cancel')
                ->label('cancel')
                ->color('gray')
                ->icon('heroicon-o-x-circle')
                ->action(function () {
                    $this->unlockRecord($this->record);
                    return redirect()->route('filament.admin.resources.roles.index');
                }),
        ];
    }


}

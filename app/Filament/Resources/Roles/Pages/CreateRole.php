<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Models\Permission;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function afterCreate(): void
    {
        // Neue Permissions aus dem Formular
        $permissions = Permission::whereIn('id', $this->data['permissions'])->pluck('name')->toArray();

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

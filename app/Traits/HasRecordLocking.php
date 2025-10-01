<?php

namespace App\Traits;
use App\Models\RecordLock;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

trait HasRecordLocking
{
    public function lockRecord($record)
    {
        if (! $record) return;

        $existingLock = RecordLock::where('lockable_type', get_class($record))
            ->where('lockable_id', $record->id)
            ->with('user')
            ->first();

        if ($existingLock && $existingLock->user_id !== Auth::id()) {
            $lockedBy = $existingLock->user->name ?? 'ein anderer Benutzer';

            Notification::make()
                ->title('Datensatz gesperrt')
                ->body("Dieser Datensatz wird gerade vom User <strong><span style='color:red'>{$lockedBy}</span></strong> bearbeitet. Bitte versuchen Sie es später erneut.")
                ->danger()
                ->send();
            
        return redirect()->route('filament.admin.resources.roles.index');
            
            
        }

        RecordLock::updateOrCreate(
            [
                'lockable_type' => get_class($record),
                'lockable_id' => $record->id,
            ],
            [
                'user_id' => Auth::id(),
                'locked_at' => now(),
            ]
        );
    }

    public function unlockRecord($record): void
    {
        if (! $record) return;

        RecordLock::where('lockable_type', get_class($record))
            ->where('lockable_id', $record->id)
            ->where('user_id', Auth::id())
            ->delete();
    }
}

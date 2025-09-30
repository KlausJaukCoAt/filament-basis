<?php 
namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Role extends SpatieRole
{
    use LogsActivity;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'guard_name',
    ];
    protected static function booted(): void
    {
        static::creating(function ($role) {
            if (empty($role->guard_name)) {
                $role->guard_name = 'web';
            }
        });
    }

    # Log Activity
    public function getActivitylogOptions(): LogOptions
    {
        
        return LogOptions::defaults()        
            ->useLogName('permission')            
            ->logAll()           // Loggt alle Felder
            //->logOnlyDirty()         // Loggt nur geänderte Felder
            ->dontSubmitEmptyLogs()  // Keine leeren Logs
            ->setDescriptionForEvent(function(string $eventName) {
                $changes = $this->getDirty();
                $roleName = $this->name ?? $this->id;
                
                switch ($eventName) {
                    case 'created':
                        return "Neuer Rolle '{$roleName}' wurde erstellt";
                    
                    case 'updated':
                        return "Bei Rolle '{$roleName}' wurde " . implode(', ', $changes) . " geändert";

                    case 'deleted':
                        return "Rolle '{$roleName}' wurde gelöscht";
                        
                    default:
                        return "Rolle '{$roleName}' wurde {$eventName}";
                }
            });
    }
    

}
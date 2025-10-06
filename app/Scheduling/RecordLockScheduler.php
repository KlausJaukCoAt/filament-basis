<?php

namespace App\Scheduling;

use Illuminate\Console\Scheduling\Schedule;

class RecordLockScheduler
{
    public static function register(Schedule $schedule): void
    {
        $schedule->command('unlock:stale-records')
            ->everyFiveMinutes()
            ->description('Entsperrt alte Datensätze in record_locks');
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RecordLock;

class UnlockRecordLocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unlock:record-locks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove Locks of RecordLocks table odler than 30 mintues';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $timeout = now()->subSeconds(env('RECORD_LOCK_TIMEOUT_SECONDS', 1800));
        $count = RecordLock::where('locked_at', '<', $timeout)->delete();
        $this->info("{$count} RecordLocks removed.");
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RecordLock;

class UnlockAllRecordLocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:unlock-all-record-locks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all Locks of RecordLocks table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $count = RecordLock::all()->count();
       RecordLock::truncate();
       $this->info("{$count} RecordLocks removed.");
    }
}

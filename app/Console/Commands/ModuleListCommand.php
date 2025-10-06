<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ModuleListCommand extends Command
{
    protected $signature = 'module:list';
    protected $description = 'Listet alle vorhandenen Module im modules/-Verzeichnis';

    public function handle(): void
    {
        $modulesPath = base_path('modules');

        if (!File::exists($modulesPath)) {
            $this->error('Kein modules/-Verzeichnis gefunden.');
            return;
        }

        $modules = collect(File::directories($modulesPath))
            ->map(fn($path) => basename($path))
            ->sort();

        if ($modules->isEmpty()) {
            $this->info('Keine Module gefunden.');
            return;
        }

        $this->info('📦 Vorhandene Module:');
        foreach ($modules as $module) {
            $this->line("- {$module}");
        }
    }
}
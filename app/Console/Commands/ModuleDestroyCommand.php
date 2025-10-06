<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleDestroyCommand extends Command
{
    protected $signature = 'module:destroy {name}';
    protected $description = 'Löscht ein Modul vollständig';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $path = base_path("modules/{$name}");

        if (!File::exists($path)) {
            $this->error("Modul {$name} existiert nicht.");
            return;
        }

        File::deleteDirectory($path);
        $this->info("🗑️ Modul {$name} wurde gelöscht.");
    }
}
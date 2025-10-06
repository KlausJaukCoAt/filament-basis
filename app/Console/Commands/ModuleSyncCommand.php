<?php 

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleSyncCommand extends Command
{
    protected $signature = 'module:sync {--module=} {--migrate} {--seed}';
    protected $description = 'Synchronisiert Composer-Autoload und Modul-Migrationen/Seeder';

    public function handle(): void
    {
        $module = $this->option('module');
        $modulesPath = base_path('modules');

        $modules = $module
            ? [Str::studly($module)]
            : collect(File::directories($modulesPath))->map(fn($path) => basename($path))->sort()->toArray();

        $this->info('🔄 Composer-Autoload aktualisieren...');
        exec('composer dump-autoload');

        foreach ($modules as $mod) {
            $this->line("📦 Modul: {$mod}");

            if ($this->option('migrate')) {
                $migrationPath = "modules/{$mod}/Database/Migrations";
                if (File::exists(base_path($migrationPath))) {
                    Artisan::call('migrate', [
                        '--path' => $migrationPath,
                        '--force' => true,
                    ]);
                    $this->line("✅ Migrationen ausgeführt für {$mod}");
                }
            }

            if ($this->option('seed')) {
                $seederClass = "Modules\\{$mod}\\Database\\Seeders\\{$mod}Seeder";
                if (class_exists($seederClass)) {
                    Artisan::call('db:seed', [
                        '--class' => $seederClass,
                    ]);
                    $this->line("🌱 Seeder ausgeführt für {$mod}");
                }
            }
        }

        $this->info('✅ Synchronisation abgeschlossen.');
    }
}
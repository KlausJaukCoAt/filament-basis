<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Support\ModuleBuilder;

class ModuleMakeCommand extends Command
{
    protected $signature = 'module:make 
        {module : Name des bestehenden Moduls} 
        {--model= : Name des Modells} 
        {--controller : Erzeuge Controller} 
        {--seeder : Erzeuge Seeder} 
        {--factory : Erzeuge Factory} 
        {--migration : Erzeuge Migration} 
        {--test : Erzeuge Pest-Test}';

    protected $description = 'Erzeuge einzelne Komponenten für ein bestehendes Modul';

    public function handle(): void
    {
        $module = Str::studly($this->argument('module'));
        $model = Str::studly($this->option('model'));

        if (!$model) {
            $this->error('Bitte gib ein Model mit --model=ModelName an.');
            return;
        }

        $modulePath = base_path("modules/{$module}");
        if (!File::exists($modulePath)) {
            $this->error("Modul {$module} existiert nicht.");
            return;
        }

        $this->info("🔧 Erzeuge Komponenten für Modul {$module} mit Model {$model}");

        ModuleBuilder::createModel($module, $model);

        if ($this->option('controller')) {
            ModuleBuilder::createController($module, $model);
        }

        if ($this->option('seeder')) {
            ModuleBuilder::createSeeder($module, $model);
        }

        if ($this->option('factory')) {
            ModuleBuilder::createFactory($module, $model);
        }

        if ($this->option('migration')) {
            ModuleBuilder::createMigration($module, $model);
        }

        if ($this->option('test')) {
            ModuleBuilder::createPestTest($module, $model);
        }

        $this->info("✅ Komponenten erfolgreich erstellt.");
    }
}
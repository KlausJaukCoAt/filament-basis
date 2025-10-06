<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Support\ModuleBuilder;

class ModuleInitCommand extends Command
{
    protected $signature = 'module:init {name : Name des neuen Moduls} {--all= : Name des Modells für alle Komponenten}';
    protected $description = 'Initialisiert ein neues Modul mit vollständiger Struktur und Komponenten';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $model = Str::studly($this->option('all'));

        if (!$model) {
            $this->error('Bitte gib ein Model mit --all=ModelName an.');
            return;
        }

        $this->info("⚙️ Initialisiere Modul {$name} mit Model {$model}");

        ModuleBuilder::createFolders($this, $name);
        ModuleBuilder::createServiceProvider($this, $name);
        ModuleBuilder::createComposerJson($this, $name);
        ModuleBuilder::createRoutes($this, $name);
        ModuleBuilder::createModel($this, $name, $model);
        ModuleBuilder::createController($this, $name, $model);
        ModuleBuilder::createSeeder($this, $name, $model);
        ModuleBuilder::createFactory($this, $name, $model);
        ModuleBuilder::createMigration($this, $name, $model);
        ModuleBuilder::createPestTest($this, $name, $model);

        $this->info("🎉 Modul {$name} mit {$model} vollständig erstellt.");
    }
}
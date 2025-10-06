<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleInitCommand extends Command
{
    protected $signature = 'module:init {name} {--all=}';
    protected $description = 'Initialisiert ein neues Modul mit vollständiger Struktur und Komponenten';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $model = $this->option('all');

        if (!$model) {
            $this->error('Bitte gib ein Model mit --all=ModelName an.');
            return;
        }

        $this->info("⚙️ Initialisiere Modul {$name} mit Model {$model}");

        $this->createFolders($name);
        $this->createServiceProvider($name);
        $this->createComposerJson($name);
        $this->createRoutes($name);
        $this->createModel($name, $model);
        $this->createController($name, $model);
        $this->createSeeder($name, $model);
        $this->createFactory($name, $model);
        $this->createMigration($name, $model);
        $this->createPestTest($name, $model);

        $this->info("🎉 Modul {$name} mit {$model} vollständig erstellt.");
    }

    // Alle createX() Methoden hier einfügen – siehe vorherige Antwort
}
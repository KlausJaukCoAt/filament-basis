<?php

namespace App\Support;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleBuilder
{
    protected static function stub(string $name): string
    {
        $path = resource_path("stubs/module/{$name}.stub");

        if (!File::exists($path)) {
            throw new \RuntimeException("Stub {$name}.stub nicht gefunden.");
        }

        return File::get($path);
    }

    protected static function render(string $stub, array $replace): string
    {
        foreach ($replace as $key => $value) {
            $stub = str_replace("{{{$key}}}", $value, $stub);
        }

        return $stub;
    }

    public static function createFolders(Command $console, string $module): void
    {
        $basePath = base_path("modules/{$module}");
        $folders = [
            'Models', 'Http/Controllers', 'Routes', 'Providers',
            'Database/Migrations', 'Database/Seeders', 'Database/Factories',
            'Tests/Feature', 'Tests/Unit',
        ];

        foreach ($folders as $folder) {
            File::makeDirectory("{$basePath}/{$folder}", 0755, true);
        }

        $console->info("📁 Ordnerstruktur erstellt.");
    }

    public static function createModel(Command $console, string $module, string $model): void
    {
        $stub = self::stub('model');
        $code = self::render($stub, compact('module', 'model'));

        File::put(base_path("modules/{$module}/Models/{$model}.php"), $code);
        $console->info("✅ Model {$model} erstellt.");
    }

    public static function createController(Command $console, string $module, string $model): void
    {
        $stub = self::stub('controller');
        $code = self::render($stub, compact('module', 'model'));

        File::put(base_path("modules/{$module}/Http/Controllers/{$model}Controller.php"), $code);
        $console->info("✅ Controller {$model}Controller erstellt.");
    }

    public static function createSeeder(Command $console, string $module, string $model): void
    {
        $stub = self::stub('seeder');
        $code = self::render($stub, compact('module', 'model'));

        File::put(base_path("modules/{$module}/Database/Seeders/{$model}Seeder.php"), $code);
        $console->info("✅ Seeder {$model}Seeder erstellt.");
    }

    public static function createFactory(Command $console, string $module, string $model): void
    {
        $stub = self::stub('factory');
        $code = self::render($stub, compact('module', 'model'));

        File::put(base_path("modules/{$module}/Database/Factories/{$model}Factory.php"), $code);
        $console->info("✅ Factory {$model}Factory erstellt.");
    }

    public static function createMigration(Command $console, string $module, string $model): void
    {
        $table = Str::plural(Str::snake($model));
        $timestamp = now()->format('Y_m_d_His');
        $filename = "{$timestamp}_create_{$table}_table.php";

        $stub = self::stub('migration');
        $code = self::render($stub, compact('module', 'model', 'table'));

        File::put(base_path("modules/{$module}/Database/Migrations/{$filename}"), $code);
        $console->info("✅ Migration für {$table} erstellt.");
    }

    public static function createRoutes(Command $console, string $module): void
    {
        $prefix = Str::kebab($module);

        $apiStub = self::stub('routes_api');
        $webStub = self::stub('routes_web');

        $apiCode = self::render($apiStub, compact('prefix'));
        $webCode = self::render($webStub, compact('prefix'));

        File::put(base_path("modules/{$module}/Routes/api.php"), $apiCode);
        File::put(base_path("modules/{$module}/Routes/web.php"), $webCode);

        $console->info("✅ Routen api.php und web.php erstellt.");
    }

    public static function createServiceProvider(Command $console, string $module): void
    {
        $stub = self::stub('serviceprovider');
        $code = self::render($stub, compact('module'));

        File::put(base_path("modules/{$module}/Providers/{$module}ServiceProvider.php"), $code);
        $console->info("✅ ServiceProvider erstellt.");
    }

    public static function createComposerJson(Command $console, string $module): void
    {
        $namespace = "Modules\\\\{$module}\\\\";
        $stub = self::stub('composer');
        $code = self::render($stub, compact('module', 'namespace'));

        File::put(base_path("modules/{$module}/composer.json"), $code);
        $console->info("✅ composer.json erstellt.");
    }

    public static function createPestTest(Command $console, string $module, string $model): void
    {
        $stub = self::stub('pest');
        $code = self::render($stub, compact('module', 'model'));

        File::put(base_path("modules/{$module}/Tests/Feature/{$model}ApiTest.php"), $code);
        $console->info("✅ Pest-Test {$model}ApiTest.php erstellt.");
    }

    public static function createAll(Command $console, string $module, string $model): void
    {
        $console->info("⚙️ Initialisiere Modul {$module} mit Model {$model}");

        self::createFolders($console, $module);
        self::createServiceProvider($console, $module);
        self::createComposerJson($console, $module);
        self::createRoutes($console, $module);
        self::createModel($console, $module, $model);
        self::createController($console, $module, $model);
        self::createSeeder($console, $module, $model);
        self::createFactory($console, $module, $model);
        self::createMigration($console, $module, $model);
        self::createPestTest($console, $module, $model);

        $console->info("🎉 Modul {$module} mit {$model} vollständig erstellt.");
    }

    public static function createComponent(Command $console, string $module, string $model, array $components): void
    {
        $console->info("🔧 Erzeuge ausgewählte Komponenten für Modul {$module} mit Model {$model}");

        foreach ($components as $component) {
            match ($component) {
                'model'      => self::createModel($console, $module, $model),
                'controller' => self::createController($console, $module, $model),
                'seeder'     => self::createSeeder($console, $module, $model),
                'factory'    => self::createFactory($console, $module, $model),
                'migration'  => self::createMigration($console, $module, $model),
                'test'       => self::createPestTest($console, $module, $model),
                default      => $console->warn("⚠️ Unbekannte Komponente: {$component}"),
            };
        }

        $console->info("✅ Komponenten erfolgreich erstellt.");
    }
}
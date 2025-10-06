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

    # Create Folders
    protected function createFolders(string $name): void
{
    $basePath = base_path("modules/{$name}");
    $folders = [
        'Models',
        'Http/Controllers',
        'Routes',
        'Providers',
        'Database/Migrations',
        'Database/Seeders',
        'Database/Factories',
        'Tests/Feature',
        'Tests/Unit',
    ];

    foreach ($folders as $folder) {
        File::makeDirectory("{$basePath}/{$folder}", 0755, true);
    }

    $this->info("📁 Ordnerstruktur erstellt.");
}
# Create Model
protected function createModel(string $name, string $model): void
{
    $path = base_path("modules/{$name}/Models/{$model}.php");

    $stub = <<<PHP
<?php

namespace Modules\\{$name}\\Models;

use Illuminate\\Database\\Eloquent\\Model;

class {$model} extends Model
{
    protected \$fillable = ['name', 'email'];
}
PHP;

    File::put($path, $stub);
    $this->info("✅ Model {$model} erstellt.");
}
# Create Controller
protected function createController(string $name, string $model): void
{
    $path = base_path("modules/{$name}/Http/Controllers/{$model}Controller.php");

    $stub = <<<PHP
<?php

namespace Modules\\{$name}\\Http\\Controllers;

use Illuminate\\Routing\\Controller;

class {$model}Controller extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Hallo aus {$model}Controller']);
    }
}
PHP;

    File::put($path, $stub);
    $this->info("✅ Controller {$model}Controller erstellt.");
}
# Create Seeder
protected function createSeeder(string $name, string $model): void
{
    $path = base_path("modules/{$name}/Database/Seeders/{$model}Seeder.php");

    $stub = <<<PHP
<?php

namespace Modules\\{$name}\\Database\\Seeders;

use Illuminate\\Database\\Seeder;
use Modules\\{$name}\\Models\\{$model};

class {$model}Seeder extends Seeder
{
    public function run(): void
    {
        {$model}::factory()->count(10)->create();
    }
}
PHP;

    File::put($path, $stub);
    $this->info("✅ Seeder {$model}Seeder erstellt.");
}
# Create Factory
protected function createFactory(string $name, string $model): void
{
    $path = base_path("modules/{$name}/Database/Factories/{$model}Factory.php");

    $stub = <<<PHP
<?php

namespace Modules\\{$name}\\Database\\Factories;

use Modules\\{$name}\\Models\\{$model};
use Illuminate\\Database\\Eloquent\\Factories\\Factory;

class {$model}Factory extends Factory
{
    protected \$model = {$model}::class;

    public function definition(): array
    {
        return [
            'name' => \$this->faker->name(),
            'email' => \$this->faker->safeEmail(),
        ];
    }
}
PHP;

    File::put($path, $stub);
    $this->info("✅ Factory {$model}Factory erstellt.");
}
# Create Migration
protected function createMigration(string $name, string $model): void
{
    $table = Str::plural(Str::snake($model));
    $timestamp = now()->format('Y_m_d_His');
    $filename = "{$timestamp}_create_{$table}_table.php";
    $path = base_path("modules/{$name}/Database/Migrations/{$filename}");

    $stub = <<<PHP
<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('{$table}', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->string('email')->unique();
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$table}');
    }
};
PHP;

    File::put($path, $stub);
    $this->info("✅ Migration für {$table} erstellt.");
}
# Create Routes
protected function createRoutes(string $name): void
{
    $routePrefix = Str::kebab($name);
    $apiPath = base_path("modules/{$name}/Routes/api.php");
    $webPath = base_path("modules/{$name}/Routes/web.php");

    $apiStub = <<<PHP
<?php

use Illuminate\\Support\\Facades\\Route;

Route::prefix('{$routePrefix}')->group(function () {
    // API-Routen hier
});
PHP;

    $webStub = <<<PHP
<?php

use Illuminate\\Support\\Facades\\Route;

Route::prefix('{$routePrefix}')->group(function () {
    // Web-Routen hier
});
PHP;

    File::put($apiPath, $apiStub);
    File::put($webPath, $webStub);
    $this->info("✅ Routen api.php und web.php erstellt.");
}
# Create Service Provider
protected function createServiceProvider(string $name): void
{
    $path = base_path("modules/{$name}/Providers/{$name}ServiceProvider.php");

    $stub = <<<PHP
<?php

namespace Modules\\{$name}\\Providers;

use Illuminate\\Support\\ServiceProvider;

class {$name}ServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        \$this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
        \$this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        \$this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }
}
PHP;

    File::put($path, $stub);
    $this->info("✅ ServiceProvider erstellt.");
}
# Create Composer.json
protected function createComposerJson(string $name): void
{
    $namespace = "Modules\\\\{$name}\\\\";
    $path = base_path("modules/{$name}/composer.json");

    $stub = <<<JSON
{
    "name": "your-org/{$name}-module",
    "type": "library",
    "autoload": {
        "psr-4": {
            "{$namespace}": "src/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "{$namespace}Providers\\\\{$name}ServiceProvider"
            ]
        }
    }
}
JSON;

    File::put($path, $stub);
    $this->info("✅ composer.json erstellt.");
}
# Create Pest Test
protected function createPestTest(string $name, string $model): void
{
    $path = base_path("modules/{$name}/Tests/Feature/{$model}ApiTest.php");

    $stub = <<<PHP
<?php

use Modules\\{$name}\\Models\\{$model};
use function Pest\\Laravel\\getJson;

beforeEach(function () {
    {$model}::factory()->create([
        'name' => 'Max Mustermann',
        'email' => 'max@example.com',
    ]);
});

it('liefert {$model}-Daten via API', function () {
    \$response = getJson('/api/{$name}');

    \$response->assertOk()
             ->assertJsonFragment([
                 'name' => 'Max Mustermann',
                 'email' => 'max@example.com',
             ]);
});
PHP;

    File::put($path, $stub);
    $this->info("✅ Pest-Test {$model}ApiTest.php erstellt.");
}
}
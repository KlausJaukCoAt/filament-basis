<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleLoaderServiceProvider extends ServiceProvider
{
    public function register()
    {
        foreach (glob(base_path('Modules/*/Providers/*ServiceProvider.php')) as $providerPath) {
            $providerClass = $this->getClassFromPath($providerPath);
            $this->app->register($providerClass);
        }
    }

    protected function getClassFromPath(string $path): string
    {
        $relative = str_replace(base_path() . '/', '', $path);
        $class = str_replace(['/', '.php'], ['\\', ''], $relative);
        return $class;
    }
}
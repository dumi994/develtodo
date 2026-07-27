<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Native\Desktop\Facades\Window;
use Native\Desktop\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        // Assicura che le cartelle storage necessarie esistano
        $dirs = ['app', 'framework/cache', 'framework/sessions', 'framework/views', 'logs'];
        foreach ($dirs as $dir) {
            $path = storage_path($dir);
            if (!is_dir($path)) {
                @mkdir($path, 0777, true);
            }
        }

        // Esegue le migration all'avvio per evitare errori 500
        Artisan::call('migrate', ['--force' => true]);

        // Verifica se il setup è già stato completato
        $seedFile = storage_path('app/.seeded');
        if (!file_exists($seedFile)) {
            // Reindirizza alla pagina di setup
            Window::open(route('setup'));
        } else {
            Window::open();
        }
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [];
    }
}

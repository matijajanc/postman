<?php

declare(strict_types=1);

namespace Matijajanc\Postman\Providers;

use Illuminate\Support\ServiceProvider;
use Matijajanc\Postman\Commands\PostmanGenerateCommand;

class PostmanServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/postman.php' => config_path('postman.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../../config/postman.php', 'postman');
    }

    public function register()
    {
        $this->app->bind('command.postman:generate', PostmanGenerateCommand::class);

        $this->commands([
            'command.postman:generate',
        ]);
    }
}

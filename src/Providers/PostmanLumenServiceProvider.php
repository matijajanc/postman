<?php

declare(strict_types=1);

namespace Matijajanc\Postman\Providers;

use Illuminate\Support\ServiceProvider;
use Matijajanc\Postman\Commands\PostmanGenerateCommand;

class PostmanLumenServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->configure('postman');
        $path = realpath(__DIR__.'/../../config/postman.php');
        $this->mergeConfigFrom($path, 'postman');
    }

    public function register()
    {
        $this->app->bind('command.postman:generate', PostmanGenerateCommand::class);

        $this->commands([
            'command.postman:generate',
        ]);
    }
}

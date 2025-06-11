<?php

namespace SobhanAali\LaravelRepoControllerGenerator;

use Illuminate\Support\ServiceProvider;
use YourName\RepoControllerGenerator\Console\MakeControllerRepoCommand;

class RepoControllerGeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // Register the command
            $this->commands([
                MakeControllerRepoCommand::class,
            ]);

            // Publish stubs
            $this->publishes([
                __DIR__.'/stubs' => base_path('stubs'),
            ], 'repo-controller-stubs');
        }
    }

    public function register()
    {
        //
    }
}

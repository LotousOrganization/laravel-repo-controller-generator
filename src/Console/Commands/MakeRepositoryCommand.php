<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeRepositoryCommand extends GeneratorCommand
{
    protected $name = 'make:repository';
    protected $description = 'Create a new repository class';
    protected $type = 'Repository';

    protected function getStub()
    {
        return base_path('stubs/repository.stub');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Repositories\Eloquent';
    }

    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that the repository should use'],
        ];
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $model = $this->option('model');
        
        if (!$model) {
            $model = str_replace('Repository', '', class_basename($name));
        }

        if (!str_contains($model, '\\')) {
            $model = "App\\Models\\{$model}";
        }

        $model = trim($model, '\\');
        $modelBasename = class_basename($model);

        $replacements = [
            '{{ model }}' => $model,
            '{{ modelBasename }}' => $modelBasename,
            '{{ modelVariable }}' => lcfirst($modelBasename),
            '{{namespacedModel}}' => $model,
            '{{model}}' => $modelBasename,
            '{{modelVariable}}' => lcfirst($modelBasename),
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );
    }
}
<?php

namespace SobhanAali\LaravelRepoControllerGenerator\Helper;

use SobhanAali\LaravelRepoControllerGenerator\Console\ConsoleMessager;

class Repository extends File
{
    /**
     * $path = '/Admin/CRUD/'
     * $model = 'Post'
     */
    public static function create($model)
    {
        $namespace = 'App\Repositories\Eloquent';

        $path = app_path('Repositories/' . str_replace('\\', '/', 'Eloquent/'.$model.'Repository') . '.php');

        $stubPath = base_path('stubs/repository.stub');

        $stub = file_get_contents($stubPath);

        $replacements = [
            '{{ namespace }}'     => $namespace,
            '{{ modelBasename }}' => $model,
            '{{ modelVariable }}' => lcfirst($model),
            '{{ class }}'         => $model.'Repository'
        ];

        $directory = dirname($path);

        $output = str_replace(array_keys($replacements), array_values($replacements), $stub);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $pathWithoutBase = self::pathWithoutBase($path);
        if (file_exists($path)) {
            if (! ConsoleMessager::confirm("⚠️ \u{200A}File already exists at: {$pathWithoutBase}. Do you want to overwrite it?", false)) {
                ConsoleMessager::warn("⚠️ \u{200A}Skipped repository creation: {$pathWithoutBase}");
                ConsoleMessager::line('');
                return;
            }
        }

        file_put_contents($path, $output);

        ConsoleMessager::info("✔️ \u{200A}Repository successfully created: {$pathWithoutBase}");
        ConsoleMessager::line('');
    }
}
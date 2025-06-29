<?php

namespace SobhanAali\LaravelRepoControllerGenerator\Helper;

use SobhanAali\LaravelRepoControllerGenerator\Console\ConsoleMessager;

class Controller extends File
{
    /**
     * $path = '/Admin/CRUD/'
     * $model = 'Post'
     */
    public static function create($controller , $model , $controllerPath , $path)
    {
        $namespace = 'App\\Http\\Controllers\\' . rtrim(str_replace('/' , '\\' , $controllerPath) , '\\');
        $controllerPath = app_path('Http/Controllers/' . $controllerPath . $controller . '.php');

        $stub = file_get_contents(base_path('stubs/controller.repo.stub'));

        $replacements = [
            '{{ namespace }}'     => $namespace,
            '{{ controller }}'    => $controller,
            '{{ model }}'         => $model,
            '{{ modelVariable }}' => lcfirst($model),
            '{{ path }}'          => str_replace('/' , '\\' , $path)
        ];
        
        $directory = dirname($controllerPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $pathWithoutBase = self::pathWithoutBase($controllerPath);
        if (file_exists($controllerPath)) {
            if (! ConsoleMessager::confirm("⚠️\u{200A}File already exists at: {$pathWithoutBase}. Do you want to overwrite it?", false)) {
                ConsoleMessager::warn("⚠️ \u{200A}Skipped: {$pathWithoutBase}");
                ConsoleMessager::line('');
                return;
            }
        }

        $output = str_replace(array_keys($replacements), array_values($replacements), $stub);

        file_put_contents($controllerPath, $output);

        ConsoleMessager::info("✔️ \u{200A}Controller successfully created: {$pathWithoutBase}");
        ConsoleMessager::line('');
    }
}
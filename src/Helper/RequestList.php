<?php

namespace SobhanAali\LaravelRepoControllerGenerator\Helper;

use Illuminate\Support\Str;
use SobhanAali\LaravelRepoControllerGenerator\Console\ConsoleMessager;

class RequestList extends File
{
    /**
     * $path = '/Admin/CRUD/'
     * $model = 'Post'
     */
    public static function create($model)
    {
        if (! ConsoleMessager::confirm("📦 Do you want to generate requests for the model '{$model}'?", false)) {
            ConsoleMessager::warn("⛔ Request generation for model '{$model}' was cancelled.");
            ConsoleMessager::line('');
            return;
        }
        ConsoleMessager::line('');

        $stub = file_get_contents(base_path('stubs/request.list.repo.stub'));

        $path = app_path("Http/Requests/{$model}ListRequest.php");

        $namespace = "App\\Http\\Requests";

        $replacements = [
            '{{ namespace }}'     => $namespace,
            '{{ class }}'         => $model.'ListRequest',
        ];

        $output = str_replace(array_keys($replacements), array_values($replacements), $stub);
        $directory = dirname($path);
        
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $pathWithoutBase = self::pathWithoutBase($path);
        if(file_exists($path)){

            if(! ConsoleMessager::confirm("⚠️ \u{200A}File already exists at: {$pathWithoutBase}. Do you want to overwrite it?", false)) {
                ConsoleMessager::warn("⚠️ \u{200A}Skipped request creation: {$pathWithoutBase}");
                ConsoleMessager::line('');

                return;
            }

        }

        file_put_contents($path, $output);

        ConsoleMessager::info("✔️ \u{200A}Request successfully created: {$pathWithoutBase}");
        ConsoleMessager::line('');


        
    }
}
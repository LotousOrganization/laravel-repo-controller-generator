<?php

namespace SobhanAali\LaravelRepoControllerGenerator\Helper;

use Illuminate\Support\Str;
use SobhanAali\LaravelRepoControllerGenerator\Console\ConsoleMessager;

class Resource extends File
{
    /**
     * $path = '/Admin/CRUD/'
     * $model = 'Post'
     */
    public static function create($model)
    {
        if (! ConsoleMessager::confirm("📦 Do you want to generate resources for the model '{$model}'?", false)) {
            ConsoleMessager::warn("⛔ Resource generation for model '{$model}' was cancelled.");
            ConsoleMessager::line('');
            return;
        }
        ConsoleMessager::line('');

        self::createResourceTrait($model);

        $stub = file_get_contents(base_path('stubs/resource.repo.stub'));

        $responsePaths = [
            'Admin'   => app_path("Http/Resources/{$model}/Admin{$model}Resource.php"),
            'Public'  => app_path("Http/Resources/{$model}/{$model}Resource.php"),
        ];

        $namespace = "App\\Http\\Resources\\{$model}";

        foreach($responsePaths as $key => $path){

            $replacements = [
                '{{ namespace }}'     => $namespace,
                '{{ class }}'         => $key === 'Admin' ? 'Admin'.$model.'Resource' : $model.'Resource',
                '{{ model }}'         => $model,
                '{{ trait }}'         => "{$model}ResourceTrait"
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
                    continue;
                }

            }

            file_put_contents($path, $output);

            ConsoleMessager::info("✔️ \u{200A}Resource successfully created: {$pathWithoutBase}");
            ConsoleMessager::line('');
        }
    }

    public static function getResourceOutputString($model)
    {
        $modelInstance = app("App\Models\\$model");
        $fillables = $modelInstance->getFillable();

        $outputs = [];

        $outputs['id'] = '$this->id';

        foreach ($fillables as $fillable) {

            $outputs[$fillable] = '$this->'.$fillable;
            
        }

        $outputs['created_at'] = '$this->created_at';
        $outputs['updated_at'] = '$this->updated_at';
        
        $outputLines = [];
        foreach ($outputs as $key => $value) {
            
            $outputLines[] = "    '$key' => $value,";
        }

        $outputString = "[\n" . implode("\n", $outputLines) . "\n];";

        return $outputString;
    }

    public static function createResourceTrait($model)
    {
        $namespace = "App\\Http\\Resources\\{$model}";
        $traitStub = file_get_contents(dirname(__DIR__).'/internal-stubs/resource-trait.stub');

        $replacements = [
            '{{ namespace }}'     => $namespace,
            '{{ trait-name }}'    => "{$model}ResourceTrait",
            '{{ output }}'        => self::getResourceOutputString($model),
            '{{ model }}'         => $model
        ];

        $output = str_replace(array_keys($replacements), array_values($replacements), $traitStub);

        $path = app_path("Http/Resources/{$model}/{$model}ResourceTrait.php");
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $pathWithoutBase = self::pathWithoutBase($path);

        if (file_exists($path)) {
            if (! ConsoleMessager::confirm("⚠️ File already exists at: {$pathWithoutBase}. Do you want to overwrite it?", false)) {
                ConsoleMessager::warn("⚠️ Skipped trait creation: {$pathWithoutBase}");
                ConsoleMessager::line('');
                return;
            }
        }

        file_put_contents($path, $output);

        ConsoleMessager::info("✔️ Resource Trait successfully created: {$pathWithoutBase}");
        ConsoleMessager::line('');
    }

}
<?php

namespace SobhanAali\LaravelRepoControllerGenerator\Helper;

use Illuminate\Support\Str;
use SobhanAali\LaravelRepoControllerGenerator\Console\ConsoleMessager;

class Request extends File
{
    /**
     * $path = '/Admin/CRUD/'
     * $model = 'Post'
     */
    public static function create($path , $model)
    {
        $stub = file_get_contents(base_path('stubs/request.stub'));

        $requestPaths = [
            'Store'   => app_path("Http/Requests/{$path}{$model}/Store{$model}Request.php"),
            'Update'  => app_path("Http/Requests/{$path}{$model}/Update{$model}Request.php"),
        ];

        $namespacePath = trim(str_replace('/', '\\', $path), '\\');
        $namespace = "App\\Http\\Requests\\{$namespacePath}\\{$model}";

        foreach($requestPaths as $key => $path){

            $replacements = [
                '{{ namespace }}'     => $namespace,
                '{{ class }}'         => $key.$model.'Request',
                '{{ rules }}'         => self::getRequestRulesString($key , $model)
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

            ConsoleMessager::info("✔️ \u{200A}Request successfully created: {$pathWithoutBase}");
            ConsoleMessager::line('');
        }
        
    }

    public static function getRequestRulesString($requestKey, $model)
    {
        $modelInstance = app("App\Models\\$model");
        $fillables = $modelInstance->getFillable();

        $rules = [];

        foreach ($fillables as $fillable) {

            $isStore = $requestKey === 'Store';
            $requiredRule = $isStore ? 'required' : 'nullable';
        
            if (Str::endsWith($fillable, '_id')) {
                $baseName = Str::beforeLast($fillable, '_id');
                $table = $baseName === 'parent' 
                    ? Str::snake($model).'s'
                    : Str::plural($baseName);
        
                $rules[$fillable] = [
                    $requiredRule,
                    'integer',
                    "exists:$table,id",
                ];
            } else {
                $rules[$fillable] = [
                    $requiredRule,
                    'string',
                ];
            }
        }
        

        $ruleLines = [];
        foreach ($rules as $field => $ruleSet) {
            $rulesArray = empty($ruleSet)
                ? '[]'
                : '[\'' . implode("', '", $ruleSet) . '\']';

            $ruleLines[] = "    '$field' => $rulesArray,";
        }

        $rulesString = "[\n" . implode("\n", $ruleLines) . "\n];";

        return $rulesString;
    }
}
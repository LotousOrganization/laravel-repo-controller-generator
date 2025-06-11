<?php

namespace SobhanAali\LaravelRepoControllerGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeControllerRepoCommand extends Command
{
    protected $signature = 'make:repo-controller {name}';

    protected $description = 'Create a new controller class with optional repository and requests (StoreRequest , UpdateRequest)';

    public function handle()
    {
        $name = $this->argument('name');

        $controllerPath = app_path('Http/Controllers/' . str_replace('\\', '/', $name) . '.php');
        
        $pathParts = explode('/', $name);
        $className = array_pop($pathParts);
        $namespace = 'App\\Http\\Controllers' . (count($pathParts) ? '\\' . implode('\\', $pathParts) : '');

        $model = Str::replaceLast('Controller', '', $className);
        
        $path = '';

        for($i = 0 ; $i < count($pathParts); $i++){
            if($pathParts[$i] != 'Api' && $pathParts[$i] != 'V1'){
                $path = $path.'/'.$pathParts[$i];
            }
        }

        $path = $path.'/';

        $this->addRepository($model);
        $this->addRequestFiles($model , $path);

        $stubPath = base_path('stubs/controller.repo.stub');
        $stub = file_get_contents($stubPath);

        $replacements = [
            '{{ namespace }}'     => $namespace,
            '{{ controller }}'    => $className,
            '{{ model }}'         => $model,
            '{{ modelVariable }}' => lcfirst($model),
            '{{ path }}'          => $path
        ];

        $output = str_replace(array_keys($replacements), array_values($replacements), $stub);

        $directory = dirname($controllerPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($controllerPath, $output);

        $this->info("Controller created at: {$controllerPath}");
    }

    public function addRequestFiles($model , $path)
    {
        $modelClass = "App\\Models\\$model";

        if (!class_exists($modelClass)) {
            $this->error("Model '$model' not found at $modelClass.");
            return;
        }

        $path = str_replace('/' , '\\' , $path);

        $requestPaths = [
            'create'  => app_path('Http/Requests'.$path.$model.'\\' . str_replace('\\', '/', 'Store'.$model.'Request') . '.php'),
            'update'  => app_path('Http/Requests'.$path.$model.'\\' . str_replace('\\', '/', 'Update'.$model.'Request') . '.php'),
        ];

        $stubPath = base_path('stubs/request.stub');
        $namespace = 'App\Http\Requests\Admin\CRUD\\'.$model;
        $modelInstance = app("App\Models\\$model");
        $fillables = $modelInstance->getFillable();

        $rules = [];
        foreach($fillables as $key => $fillable){
            $rules[$fillable] = [];
        }
        $rulesString = var_export($rules, true);
        $rulesString = preg_replace('/\s+/', ' ', $rulesString);
        $rulesString = str_replace(['array (', ')'], ["[", ']'], $rulesString);
        $rulesString = str_replace(',', ",\n", $rulesString);
        $rulesString = preg_replace('/\[\s+\]/', '[]', $rulesString);

        foreach($requestPaths as $key => $path){
            $stub = file_get_contents($stubPath);

            $replacements = [
                '{{ namespace }}'    => $namespace,
                '{{ class }}'        => $model.$key.'Request',
                '{{ rules }}'         => $rulesString
            ];

            $output = str_replace(array_keys($replacements), array_values($replacements), $stub);
            $directory = dirname($path);

            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            file_put_contents($path, $output);

            $this->info("Request created at: {$path}");
        }
    }

    public function addRepository($model)
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

        file_put_contents($path, $output);

        $this->info("Repository created at: {$path}");
    }
}

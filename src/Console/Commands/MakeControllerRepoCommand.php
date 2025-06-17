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
        $this->checkStubs();

        $name = $this->argument('name');

        $controllerPath = app_path('Http/Controllers/' . str_replace('\\', '/', $name) . '.php');
        
        $pathParts = explode('/', $name);
        $className = array_pop($pathParts);
        $namespace = 'App\\Http\\Controllers' . (count($pathParts) ? '\\' . implode('\\', $pathParts) : '');

        $model = Str::replaceLast('Controller', '', $className);
        
        $filtered = array_filter($pathParts, fn($p) => !in_array($p, ['Api', 'V1']));
        $path = implode('/', $filtered) . '/';


        $this->addRepository($model);
        $this->addRequestFiles($model , $path);

        $stubPath = base_path('stubs/controller.repo.stub');
        $stub = file_get_contents($stubPath);

        $path = str_replace('/' , '\\' , $path);

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

        if (file_exists($controllerPath)) {
            if (! $this->confirm("⚠️\u{200A}File already exists at: {$this->pathWithoutBase($controllerPath)}. Do you want to overwrite it?", false)) {
                $this->warn("⚠️ \u{200A}Skipped: {$this->pathWithoutBase($controllerPath)}");
                $this->line('');
                return;
            }
        }

        file_put_contents($controllerPath, $output);

        $this->info("✔️ \u{200A}Controller successfully created: {$this->pathWithoutBase($controllerPath)}");
    }

    public function addRequestFiles($model , $path)
    {
        $modelClass = "App\\Models\\$model";

        if (!class_exists($modelClass)) {
            $this->error("❌\u{200A}Model '{$model}' not found at {$modelClass}. Request classes cannot be generated without the model.");
            return;
        }        

        $requestPaths = [
            'Store'   => app_path("Http/Requests/{$path}{$model}/Store{$model}Request.php"),
            'Update'  => app_path("Http/Requests/{$path}{$model}/Update{$model}Request.php"),
        ];        

        $stubPath = base_path('stubs/request.stub');

        $namespacePath = trim(str_replace('/', '\\', $path), '\\');
        $namespace = "App\\Http\\Requests\\{$namespacePath}\\{$model}";

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
                '{{ namespace }}'     => $namespace,
                '{{ class }}'         => $key.$model.'Request',
                '{{ rules }}'         => $rulesString
            ];

            $output = str_replace(array_keys($replacements), array_values($replacements), $stub);
            $directory = dirname($path);

            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            if (file_exists($path)) {
                if (! $this->confirm("⚠️ \u{200A}File already exists at: {$this->pathWithoutBase($path)}. Do you want to overwrite it?", false)) {
                    $this->warn("⚠️ \u{200A}Skipped request creation: {$this->pathWithoutBase($path)}");
                    $this->line('');
                    continue;
                }
            }

            file_put_contents($path, $output);

            $this->info("✔️ \u{200A}Request successfully created: {$this->pathWithoutBase($path)}");
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

        if (file_exists($path)) {
            if (! $this->confirm("⚠️ \u{200A}File already exists at: {$this->pathWithoutBase($path)}. Do you want to overwrite it?", false)) {
                $this->warn("⚠️ \u{200A}Skipped repository creation: {$this->pathWithoutBase($path)}");
                $this->line('');
                return;
            }
        }

        file_put_contents($path, $output);

        $this->info("✔️ \u{200A}Repository successfully created: {$this->pathWithoutBase($path)}");
    }

    public function pathWithoutBase($path)
    {
        return Str::after($path, base_path() . DIRECTORY_SEPARATOR);
    }

    public function checkStubs()
    {
        $missing = [];

        if (! file_exists(base_path('stubs/request.stub'))) {
            $missing[] = 'stubs/request.stub';
        }

        if (! file_exists(base_path('stubs/controller.repo.stub'))) {
            $missing[] = 'stubs/controller.repo.stub';
        }

        if (! file_exists(base_path('stubs/repository.stub'))) {
            $missing[] = 'stubs/repository.stub';
        }

        if (! empty($missing)) {
            $this->error("❌  Required stub files are missing:\n");

            foreach ($missing as $stub) {
                $this->line("   - {$stub}");
            }

            $this->line("\n📦  Before you start, please publish the stub files using the command below:\n");
            $this->line("   php artisan vendor:publish --tag=repo-controller-stubs");
            $this->line('');

            exit(1);
        }
    }

}

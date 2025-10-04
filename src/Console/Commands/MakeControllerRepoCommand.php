<?php

namespace SobhanAali\LaravelRepoControllerGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use SobhanAali\LaravelRepoControllerGenerator\Console\ConsoleMessager;
use SobhanAali\LaravelRepoControllerGenerator\Helper\Controller;
use SobhanAali\LaravelRepoControllerGenerator\Helper\Repository;
use SobhanAali\LaravelRepoControllerGenerator\Helper\RepositoryInterface;
use SobhanAali\LaravelRepoControllerGenerator\Helper\Request;
use SobhanAali\LaravelRepoControllerGenerator\Helper\RequestList;
use SobhanAali\LaravelRepoControllerGenerator\Helper\Resource;

class MakeControllerRepoCommand extends Command
{
    protected $signature = 'make:repo-controller {name}';

    protected $description = 'Create a new controller class with optional repository and requests (StoreRequest , UpdateRequest)';

    protected $basePath;
    protected $path = '';
    protected $name;
    protected $model;
    

    public function handle()
    {
        ConsoleMessager::setIO($this->input, $this->output);

        $this->checkStubs();

        $this->line('');

        $name = $this->argument('name');

        $pathParts = explode('/', $name);

        $basePath = $pathParts[0].'/'.$pathParts[1].'/';
        
        $path = '';
        for($i = 2; $i < count($pathParts) - 1 ; $i++){
            $path .= $pathParts[$i].'/';
        }

        $className = array_pop($pathParts);
        $model = Str::replaceLast('Controller', '', $className);

        $modelClass = "App\\Models\\$model";

        if (!class_exists($modelClass)) {
            $this->error("❌\u{200A}Model '{$model}' not found at {$modelClass}. Request classes cannot be generated without the model.");
        } else {
            Request::create($path , $model);
            RequestList::create($model);
            Resource::create($model);
        }

        RepositoryInterface::create($model);
        Repository::create($model);
        
        Controller::create($className , $model , $basePath.$path , $path);

    }

    public function checkStubs()
    {
        $missing = [];

        if (! file_exists(base_path('stubs/request.repo.stub'))) {
            $missing[] = 'stubs/request.repo.stub';
        }

        if (! file_exists(base_path('stubs/controller.repo.stub'))) {
            $missing[] = 'stubs/controller.repo.stub';
        }

        if (! file_exists(base_path('stubs/repository.stub'))) {
            $missing[] = 'stubs/repository.stub';
        }

        if (! file_exists(base_path('stubs/resource.repo.stub'))) {
            $missing[] = 'stubs/resource.repo.stub';
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

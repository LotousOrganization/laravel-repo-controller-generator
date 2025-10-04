<?php

namespace SobhanAali\LaravelRepoControllerGenerator\BaseClass;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function all(FormRequest $request);

    public function create(FormRequest $request);

    public function update(FormRequest $request ,  Model $object);

    public function delete(Model $object);
}

<?php

namespace SobhanAali\LaravelRepoControllerGenerator\BaseClass;

interface BaseRepositoryInterface
{
    public function all($request);

    public function create($request);

    public function update($request ,  $object);

    public function delete($object);
}

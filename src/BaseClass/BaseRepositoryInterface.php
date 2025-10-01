<?php

namespace SobhanAali\LaravelRepoControllerGenerator\BaseClass;

interface BaseRepositoryInterface
{
    public function all();

    public function create($data);

    public function update($object,$data);

    public function delete($object);
}

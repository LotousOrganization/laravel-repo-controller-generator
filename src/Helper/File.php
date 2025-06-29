<?php

namespace SobhanAali\LaravelRepoControllerGenerator\Helper;

use Illuminate\Support\Str;

abstract class File
{
    public static function pathWithoutBase($path)
    {
        return Str::after($path, base_path() . DIRECTORY_SEPARATOR);
    }
}
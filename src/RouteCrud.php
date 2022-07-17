<?php
namespace RA\Core;

class RouteCrud
{
    public static function run($namespace) {
        $prefix = \Str::kebab($namespace);

        Route::get($prefix, $namespace.'\ListAction');
        Route::get($prefix.'/list', $namespace.'\ListAction');
        Route::get($prefix.'/single/{id}', $namespace.'\SingleAction');
        Route::get($prefix.'/new', $namespace.'\NewAction');
        Route::post($prefix.'/create', $namespace.'\CreateAction');
        Route::get($prefix.'/edit/{id}', $namespace.'\EditAction');
        Route::post($prefix.'/update/{id}', $namespace.'\UpdateAction');
        Route::post($prefix.'/patch/{id}', $namespace.'\PatchAction');
        Route::get($prefix.'/delete/{id}', $namespace.'\DeleteAction');
        Route::get($prefix.'/search', $namespace.'\SearchAction');
        Route::get($prefix.'/duplicate/{id}', $namespace.'\DeleteAction');
        Route::get($prefix.'/sort', $namespace.'\SortAction');
        Route::get($prefix.'/sort-paginated', $namespace.'\SortPaginatedAction');
    }
}

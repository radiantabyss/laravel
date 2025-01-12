<?php
namespace RA\Support\Facades;

use Illuminate\Support\Facades\Facade;

class TableFacade extends Facade
{
    protected static function getFacadeAccessor() { return '\RA\Support\Helpers\TableHelper'; }
}

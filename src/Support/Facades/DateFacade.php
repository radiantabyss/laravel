<?php
namespace RA\Support\Facades;

use Illuminate\Support\Facades\Facade;

class DateFacade extends Facade
{
    protected static function getFacadeAccessor() { return '\RA\Support\Helpers\DateHelper'; }
}

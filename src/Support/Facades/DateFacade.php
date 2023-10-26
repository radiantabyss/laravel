<?php
namespace Lumi\Core\Support\Facades;

use Illuminate\Support\Facades\Facade;

class DateFacade extends Facade
{
    protected static function getFacadeAccessor() { return '\Lumi\Core\Support\Helpers\DateHelper'; }
}

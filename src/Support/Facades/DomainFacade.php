<?php
namespace RA\Core\Support\Facades;

use Illuminate\Support\Facades\Facade;

class DomainFacade extends Facade
{
    protected static function getFacadeAccessor() { return '\RA\Core\Support\Helpers\DomainHelper'; }
}

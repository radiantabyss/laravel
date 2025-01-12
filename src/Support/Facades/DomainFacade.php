<?php
namespace RA\Support\Facades;

use Illuminate\Support\Facades\Facade;

class DomainFacade extends Facade
{
    protected static function getFacadeAccessor() { return '\RA\Support\Helpers\DomainHelper'; }
}

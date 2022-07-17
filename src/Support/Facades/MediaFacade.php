<?php
namespace RA\Core\Support\Facades;

use Illuminate\Support\Facades\Facade;

class MediaFacade extends Facade
{
    protected static function getFacadeAccessor() { return '\RA\Core\Support\Helpers\MediaHelper'; }
}

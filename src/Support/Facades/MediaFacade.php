<?php
namespace RA\Support\Facades;

use Illuminate\Support\Facades\Facade;

class MediaFacade extends Facade
{
    protected static function getFacadeAccessor() { return '\RA\Support\Helpers\MediaHelper'; }
}

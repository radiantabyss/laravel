<?php
namespace RA\Core;

use Illuminate\Support\Facades\Route as LaravelRoute;

class Route extends LaravelRoute
{
    public static function get($url, $action) {
        return parent::get($url, self::formatAction($action));
    }

    public static function post($url, $action) {
        return parent::post($url, self::formatAction($action));
    }

    private static function formatAction($action) {
        if ( !preg_match('/\\\\Actions/', $action) ) {
            preg_match('/(\w+)\\\\/', $action, $match);
            $action = str_replace($match[0], $match[0].'Actions\\', $action);
        }

        if ( !preg_match('/\@run/', $action) ) {
            $action .= '@run';
        }

        return $action;
    }
}

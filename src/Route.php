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

    public static function any($url, $action) {
        return parent::any($url, self::formatAction($action));
    }

    private static function formatAction($action) {
        if ( !preg_match('/\\\\Actions/', $action) ) {
            preg_match('/(\w+\\\\)\w+Action$/', $action, $match);
            $action = str_replace($match[1], $match[1].'Actions\\', $action);
        }

        if ( !preg_match('/\@run/', $action) ) {
            $action .= '@run';
        }

        return $action;
    }
}

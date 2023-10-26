<?php
namespace Lumi\Core;

use Illuminate\Support\Facades\Route as LaravelRoute;

class Route extends LaravelRoute
{
    public static function get($url, $action) {
        return parent::get($url, self::formatAction($action));
    }

    public static function post($url, $action) {
        return parent::post($url, self::formatAction($action));
    }

    public static function options($url, $action) {
        return parent::options($url, self::formatAction($action));
    }

    public static function any($url, $action) {
        return parent::any($url, self::formatAction($action));
    }

    private static function formatAction($action) {
        if ( !preg_match('/\\\\Actions/', $action) ) {
            preg_match('/(\w+\\\\|)\w+Action$/', $action, $match);
            $last_match_position = strrpos($action, $match[1]);

            if ( $last_match_position !== false ) {
                $action = substr_replace($action, $match[1].'Actions\\', $last_match_position, strlen($match[1]));
            }
        }

        if ( !preg_match('/\@run/', $action) ) {
            $action .= '@run';
        }

        return $action;
    }
}

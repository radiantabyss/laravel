<?php
namespace RA\Core\Support\Helpers;

class SeoHelper
{
    public function title($title = null, $suffix = null) {
        $page_title = '';

        //set default title
        if ( !$title ) {
            $page_title .= \Domain::name();

            $action = \Domain::actionName();
            if ( $action != 'list' ) {
                $page_title .= ' / '.$action;
            }
        }
        //else has custom title
        else {
            $page_title .= $title;
        }

        //append suffix
        if ( $suffix !== false ) {
            //default suffix
            if ( !$suffix ) {
                $page_title .= ' - '.config('app.name');
            }
            //custom suffix
            else {
                $page_title .= $suffix;
            }
        }

        return '<title>'.$page_title.'</title>';
    }
}

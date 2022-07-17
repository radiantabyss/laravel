<?php
namespace RA\Core\Support\Helpers;

class TableHelper
{
    public function getFilters($filters = false) {
        $string = '';

        if ( $filters === false ) {
            $filters = session(\Domain::get().'_filters') ?? [];
        }

        if ( $filters ) {
            foreach ( $filters as $key => $value ) {
                if ( $value === '' || $value === null ) {
                    continue;
                }

                $string .= '&'.$key.'='.$value;
            }
        }

        return '?'.trim($string, '&');
    }

    public function getSortingUrl($filters, $order_by, $default = 'asc') {
        $url = \URL::current().'?';

        foreach ( $filters as $key => $value ) {
            if ( $value === '' ) continue;
            if ( in_array($key, ['order_by', 'order', 'page', '_url']) ) continue;

            $url .= $key.'='.$value.'&';
        }

        $url .= '&order_by='.$order_by;

        if ( isset($filters['order']) && $filters['order_by'] == $order_by ) {
            $url .= '&order='.($filters['order'] == 'asc' ? 'desc' : 'asc');
        }
        else {
            $url .= '&order='.$default;
        }

        return $url;
    }

    public function getSortingHtml($filters, $order_by) {
        if ( isset($filters['order_by']) &&  $filters['order_by'] == $order_by ) {
            if ( isset($filters['order']) && $filters['order'] == 'desc' ) {
                return '<i class="ion-chevron-down"></i>';
            }

            return '<i class="ion-chevron-up"></i>';
        }

        return '<span><i class="ion-chevron-up"></i><i class="ion-chevron-down"></i></span>';
    }
}

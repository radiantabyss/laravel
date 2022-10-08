<?php
if ( !function_exists('findBy') ) {
    function findBy($items, $value, $key = 'id') {
        foreach ( $items as $item ) {
            if ( is_object($item) && $item->$key == $value ) {
                return $item;
            }
            else if ( !is_object($item) && $item[$key] == $value ) {
                return $item;
            }
        }

        return false;
    }
}

if ( !function_exists('groupBy') ) {
    function groupBy($items, $key = 'id') {
        $newItems = [];
        foreach ($items as $item) {
            if (is_object($item)) {
                $newItems[$item->{$key}][] = $item;
            } else {
                $newItems[$item[$key]][] = $item;
            }
        }
        return $newItems;
    }
}

if ( !function_exists('keyBy') ) {
    function keyBy($items, $key = 'id') {
        $newItems = [];
        foreach ($items as $item) {
            if (is_object($item)) {
                $newItems[$item->{$key}] = $item;
            } else {
                $newItems[$item[$key]] = $item;
            }
        }
        return $newItems;
    }
}

if ( !function_exists('pluck') ) {
    function pluck($items, $key = 'id') {
        $plucked = [];
        foreach ($items as $item) {
            if (is_object($item)) {
                $plucked[] = $item->{$key};
            } else {
                $plucked[] = $item[$key];
            }
        }

        return $plucked;
    }
}

if ( !function_exists('spread') ) {
    function spread($arr, $spread) {
        foreach ( $spread as $key => $value ) {
            if ( is_object($arr) ) {
                $arr->$key = $value;
            }
            else {
                $arr[$key] = $value;
            }
        }

        return $arr;
    }
}

if ( !function_exists('toArray') ) {
    function toArray($var) {
        return json_decode(json_encode($var), true);
    }
}

<?php
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

function toArray($var) {
    return json_decode(json_encode($var), true);
}

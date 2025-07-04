<?php
function dmp($text, $text2 = null) {
    $pre = true;
    if ( php_sapi_name() == 'cli' || request()->ajax() ) {
        $pre = false;
    }

    if ( $pre ) {
        echo '<pre>';
    }

    if ( $text2 !== null ) {
        echo $text.': ';
        var_dump($text2);
    }
    else {
        if ( gettype($text) == 'object' && get_class($text) == 'Illuminate\Database\Eloquent\Collection' ) {
            var_dump(toArray($text));
        }
        else {
            var_dump($text);
        }
    }

    if ( $pre ) {
        echo '</pre>';
    }
    else {
        echo "\n";
    }
}

function ddmp($text, $text2 = null) {
    dmp($text, $text2);
    die();
}

function is_assoc($arr) {
    return array_keys($arr) !== range(0, count($arr) - 1);
}

function is_me() {
    return file_get_contents(base_path()."/ip.txt") == $_SERVER["REMOTE_ADDR"] || $_SERVER["REMOTE_ADDR"] == '127.0.0.1';
}

function yes_no($var, $value = null, $red = false) {
    $yes = '<span class="color-green">Yes</span>';
    $no = $red ? '<span class="color-red">No</span>' : 'No';

    if ( !$value ) {
        return $var ? $yes : $no;
    }

    return $var === $value ? $yes : $no;
}

function checked($var, $val) {
    if ( is_array($val) ) {
        return in_array($var, $val) ? 'checked' : '';
    }
    return ($var == $val ? 'checked' : '');
}

function selected($var, $val) {
    return ($var == $val ? 'selected="selected"' : '');
}

function ints_to_strings($arr) {
    foreach( $arr as &$var ) {
        $var = (string) $var;
    }

    return $arr;
}

function get_first_last_name($name) {
    $name = ucwords($name);
    $exp = explode(' ', $name);
    $last_name = array_pop($exp);
    $first_name = implode(' ', $exp);

    if ( !$first_name ) {
        $first_name = $last_name;
        $last_name = '';
    }

    return compact('first_name', 'last_name');
}

function log_db($message, $type = '') {
    \RA\Models\Log::create(compact('message', 'type'));
}

function start_query_log() {
    \DB::enableQueryLog();
}

function get_query_log($full_log = false) {
    $query_logs = \DB::getQueryLog();

    foreach ( $query_logs as &$query_log ) {
        $query = $query_log['query'];
        foreach ( $query_log['bindings'] as $binding ) {
            if ( is_bool($binding) ) {
                $binding = $binding ? 1 : 0;
            }
            else if ( !is_numeric($binding) ) {
                $binding = '"'.$binding.'"';
            }

            $query = preg_replace('/\?/', $binding, $query, 1);
        }

        if ( $full_log ) {
            $query_log['full_query'] = $query;
        }
        else {
            $query_log['full_query'] = [
                'query' => $query,
                'time' => $query_log['time'],
            ];
        }
    }

    if ( $full_log ) {
        dmp($query_logs);
    }
    else {
        dmp(pluck($query_logs, 'full_query'));
    }
}

function get_blocks_data_html($folder, $blocks_data) {
    \RA\Core\BuilderAdmin::initialize(str_replace('.', '/', $folder).'/partials/blocks');
    return \RA\Core\BuilderAdmin::writeBlocks($blocks_data, true);
}

function to_words($str) {
    return ucwords(str_replace(['-', '_'], ' ', $str));
}

function decode_json($string) {
    if (gettype($string) == 'string') {
        return json_decode($string, true);
    }

    return $string;
}

function encode_json($array, $null_if_empty = true) {
    if ( gettype($array) == 'string' ) {
        return $array;
    }

    if ( $array === null || !count($array) ) {
        return $null_if_empty ? null : json_encode([]);
    }

    return json_encode($array);
}

function parse_window_value($window) {
    if (preg_match('/\:/', $window)) {
        $exp = explode(':', $window);

        return date('d M', strtotime($exp[0])) . ' - ' . date('d M', strtotime($exp[1]));
    }

    return ucwords(str_replace('_', ' ', $window));
}

function random_string($length = 8) {
    $string = "";
    $possible = "12346789ABCDEFGHJKLMNPQRTVWXYZ";
    $maxlength = strlen($possible);
    if ($length > $maxlength) {
        $length = $maxlength;
    }
    $i = 0;
    while ($i < $length) {
        $char = substr($possible, mt_rand(0, $maxlength - 1), 1);
        if (!strstr($string, $char)) {
            $string .= $char;
            $i++;
        }
    }
    return $string;
}

function delete_recursive($directory) {
    foreach(glob("{$directory}/*") as $file) {
        if ( is_link($file) ) {
            @rmdir($file);
            @unlink($file);
        }
        else if ( is_dir($file) ) {
            delete_recursive($file);
        }
        else {
            @unlink($file);
        }
    }

    if ( !glob("{$directory}/*") ) {
        foreach( glob("{$directory}/.*") as $file ) {
            if ( $file == $directory.'/.' || $file == $directory.'/..' ) continue;

            @unlink($file);
        }
    }

    @rmdir($directory);
}

function copy_recursive($source, $dest) {
    if ( !file_exists($source) ) {
        return;
    }

    // Check for symlinks
    if ( is_link($source) ) {
        return symlink(readlink($source), $dest);
    }

    // Simple copy for a file
    if ( is_file($source) ) {
        return copy($source, $dest);
    }

    // Make destination directory
    if ( !is_dir($dest) ) {
        mkdir($dest);
    }

    // Loop through the folder
    $dir = dir($source);
    while ( false !== $entry = $dir->read() ) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep copy directories
        copy_recursive("$source/$entry", "$dest/$entry");
    }

    // Clean up
    $dir->close();
    return true;
}

function get_files_recursive(string $directory, array $allFiles = []) {
    $files = array_diff(scandir($directory), ['.', '..']);

    foreach ($files as $file) {
        $fullPath = $directory. DIRECTORY_SEPARATOR .$file;

        if( is_dir($fullPath) ) {
            $allFiles += get_files_recursive($fullPath, $allFiles);
        }
        else {
            $allFiles[] = $fullPath;
        }
    }

    return $allFiles;
}

function get_block_css_classes($data) {
    extract($data);

    if ( !isset($classes) ) {
        $classes = '';
    }

    $classes .= isset($width) ? ' w-'.$width : '';
    $classes .= isset($width_mobile) ? ' wm-'.$width_mobile : '';

    foreach ( config('settings.directions') as $direction ) {
        $classes .= isset(${'margin_'.$direction}) ? ' m'.$direction[0].'-'.${'margin_'.$direction} : '';
        $classes .= isset(${'padding_'.$direction}) ? ' p'.$direction[0].'-'.${'padding_'.$direction} : '';
        $classes .= isset(${'margin_mobile_'.$direction}) ? ' mm'.$direction[0].'-'.${'margin_mobile_'.$direction} : '';
        $classes .= isset(${'padding_mobile_'.$direction}) ? ' pm'.$direction[0].'-'.${'padding_mobile_'.$direction} : '';
    }

    return trim(preg_replace('/\s+/', ' ', $classes));

}

function start_load_time() {
    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $GLOBALS['load_time_start'] = $time;
    $GLOBALS['load_times'] = [];
}

function get_load_time($total = false, $return = false) {
    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $time = round(($time - $GLOBALS['load_time_start']), 4);

    foreach ( $GLOBALS['load_times'] as $load_time ) {
        $time -= $load_time;
    }

    $GLOBALS['load_times'][] = $time;

    $total_time = ($total ? array_sum($GLOBALS['load_times']) : $time);

    if ( $return ) {
        return $total_time;
    }

    echo $total_time."\n";
}

function add_ordinal_suffix($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ( (($number % 100) >= 11) && (($number%100) <= 13) ) {
        return $number. 'th';
    }
    else {
        return $number. $ends[$number % 10];
    }
}

function command_exists($command) {
    $is_windows = strpos(PHP_OS, 'WIN') === 0;
    $response = shell_exec(($is_windows ? 'where ' : 'which ').$command);

    if ( $response === null ) {
        return false;
    }

    if ( $is_windows && preg_match('/Could not find files for the given pattern/', $response) ) {
        return false;
    }

    if ( !$is_windows && !$response ) {
        return false;
    }

    return true;
}

function sort_array_by_array(array $array_to_sort, array $order_array) {
    // Create a copy of the order_array with values as keys
    $order = array_flip($order_array);

    // Sort the array_to_sort according to the order_array
    usort($array_to_sort, function($a, $b) use ($order) {
        $pos_a = isset($order[$a]) ? $order[$a] : null;
        $pos_b = isset($order[$b]) ? $order[$b] : null;

        if ($pos_a === null && $pos_b === null) {
            // both items are not in the order array, keep their relative position
            return 0;
        }
        if ($pos_a === null) {
            // $a is not in the order array, move it down
            return 1;
        }
        if ($pos_b === null) {
            // $b is not in the order array, move it up
            return -1;
        }

        // Both items are in the order array, sort normally
        return $pos_a - $pos_b;
    });

    return $array_to_sort;
}

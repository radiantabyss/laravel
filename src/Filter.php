<?php
namespace Lumi\Core;

class Filter
{
    protected static $table;
    protected static $query;
    protected static $is_count;

    public static function apply($query, $filters = null, $is_count = false) {
        //set table
        if ( !static::$table ) {
            static::$table = \Str::snake(\Domain::get());
        }

        //set query
        static::$query = $query;

        //set is count
        static::$is_count = $is_count;

        //get filters from request
        if ( $filters === null ) {
            $filters = \Request::all();
        }

        $operators = [];
        foreach ( $filters as $key => $value ) {
            if ( !preg_match('/__operator/', $key) ) {
                continue;
            }

            $operators[str_replace('__operator', '', $key)] = $value;
        }

        //set order
        static::order_by($filters['order_by'] ?? '', $filters['order'] ?? 'asc');

        foreach ($filters as $key => $value) {
            //ignore empty values
            if ( ($value === '' || $value === null) ) {
                continue;
            }

            //ignore reserved keys
            if ( in_array($key, ['order', 'order_by', 'page', 'per_page']) ) {
                continue;
            }

            //ignore operators
            if ( preg_match('/__operator/', $key) ) {
                continue;
            }

            $operator = $operators[$key] ?? '';

            //check if a custom method is defined
            try {
                static::$key($value, $operator);
                continue;
            }
            catch(\Error $e) {}

            $column = static::$table.'.'.$key;

            //apply default where if no operator is specified
            if ( !isset($operators[$key]) ) {
                static::$query->where($column, $value);
                continue;
            }

            //apply where based on the operator
            if ( $operator == 'is_empty' ) {
                static::$query->where(function($query) use($column) {
                    $query->where($column, '=', '')
                        ->orWhereNull($column);
                });
            }
            else if ( $operator == 'is_not_empty' ) {
                static::$query->where(function($query) use($column) {
                    $query->whereNotNull($column)
                        ->where($column, '<>', '');
                });
            }
            else if ( $operator == '!=' ) {
                static::$query->where($column, '<>', $value);
            }
            else if ( in_array($operator, ['>', '<']) ) {
                static::$query->where($column, $operator, $value);
            }
            else if ( $operator == 'in' ) {
                $value = explode('|', $value);
                static::$query->whereIn($column, $value);
            }
            else if ( $operator == 'not_in' ) {
                $value = explode('|', $value);
                static::$query->whereNotIn($column, $value);
            }
            else if ( $operator == 'contains' ) {
                static::$query->where($column, 'LIKE', '%' . $value . '%');
            }
            else if ( $operator == 'not_contains' ) {
                static::$query->where($column, 'NOT LIKE', '%' . $value . '%');
            }
        }
    }

    public static function setTable($table) {
        static::$table = $table;
    }

    protected static function order_by($order_by, $order) {
        if ( static::$is_count ) {
            return;
        }

        if ( $order_by ) {
            $order_bys = explode(',', $order_by);
            $orders = explode(',', $order);

            foreach ( $order_bys as $i => $order_by ) {
                //check if a custom method is defined
                try {
                    static::{'order_by_'.$order_by}(trim($orders[$i] ?? $orders[0]));
                    continue;
                }
                catch(\Error $e) {}

                static::$query->orderBy(trim($order_by), trim($orders[$i] ?? $orders[0]));
            }
        }
        else {
            static::$query->orderBy('id', 'desc');
        }
    }

    protected static function name($value) {
        static::$query->where(static::$table.'.name', 'LIKE', '%' . $value . '%');
    }

    protected static function start_date($value) {
        static::$query->where(static::$table.'.date', '>=', $value);
    }

    protected static function end_date($value) {
        static::$query->where(static::$table.'.date', '<=', $value);
    }

    protected static function start_created_at($value) {
        static::$query->where(static::$table.'.created_at', '>=', apply_reverse_timezone($value, auth()->user()->timezone));
    }

    protected static function end_created_at($value) {
        static::$query->where(static::$table.'.created_at', '<=', apply_reverse_timezone($value . ' 23:59:59', auth()->user()->timezone));
    }

    protected static function _($value) {}
    protected static function limit($value) {}
}

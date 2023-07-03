<?php
namespace RA\Core;

trait Filter
{
    protected static $table;
    protected static $query;

    public static function apply($query, $filters = null) {
        //set table
        if ( !self::$table ) {
            self::$table = \Str::snake(\Domain::get());
        }

        //set query
        self::$query = $query;

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
        self::order_by($filters['order_by'] ?? '', $filters['order'] ?? 'asc');

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

            //check if a custom method is defined
            if ( method_exists(__CLASS__, $key) ) {
                self::$key($value, $operator);
                continue;
            }

            $column = self::$table.'.'.$key;

            //apply default where if no operator is specified
            if ( !isset($operators[$key]) ) {
                self::$query->where($column, $value);
                continue;
            }

            //apply where based on the operator
            $operator = $operators[$key] ?? '';

            if ( $operator == 'is_empty' ) {
                self::$query->where(function($query) use($column) {
                    $query->where($column, '=', '')
                        ->orWhereNull($column);
                });
            }
            else if ( $operator == 'is_not_empty' ) {
                self::$query->where(function($query) use($column) {
                    $query->whereNotNull($column)
                        ->where($column, '<>', '');
                });
            }
            else if ( $operator == '!=' ) {
                self::$query->where($column, '<>', $value);
            }
            else if ( in_array($operator, ['>', '<']) ) {
                self::$query->where($column, $operator, $value);
            }
            else if ( $operator == 'in' ) {
                $value = explode('|', $value);
                self::$query->whereIn($column, $value);
            }
            else if ( $operator == 'not_in' ) {
                $value = explode('|', $value);
                self::$query->whereNotIn($column, $value);
            }
            else if ( $operator == 'contains' ) {
                self::$query->where($column, 'LIKE', '%' . $value . '%');
            }
            else if ( $operator == 'not_contains' ) {
                self::$query->where($column, 'NOT LIKE', '%' . $value . '%');
            }
        }
    }

    protected static function order_by($order_by, $order) {
        if ( $order_by ) {
            $order_bys = explode(',', $order_by);
            $orders = explode(',', $order);

            foreach ( $order_bys as $i => $order_by ) {
                self::$query->orderBy(trim($order_by), trim($orders[$i] ?? $orders[0]));
            }
        }
        else {
            self::$query->orderBy('id', 'desc');
        }
    }

    protected static function name($value) {
        self::$query->where(self::$table.'.name', 'LIKE', '%' . $value . '%');
    }

    protected static function start_date($value) {
        self::$query->where(self::$table.'.date', '>=', $value);
    }

    protected static function end_date($value) {
        self::$query->where(self::$table.'.date', '<=', $value);
    }

    protected static function start_created_at($value) {
        self::$query->where(self::$table.'.created_at', '>=', apply_reverse_timezone($value, auth()->user()->timezone));
    }

    protected static function end_created_at($value) {
        self::$query->where(self::$table.'.created_at', '<=', apply_reverse_timezone($value . ' 23:59:59', auth()->user()->timezone));
    }
}

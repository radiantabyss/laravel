<?php
namespace RA\Core;

trait Filter
{
    protected static $query;
    protected static $has_default_order;

    public static function apply($query, $filters = null, $has_default_order = true) {
        //set query
        self::$query = $query;

        //set default order
        self::$has_default_order = $has_default_order;

        //get filters from request
        if ( $filters === null ) {
            $filters = \Request::all();
        }

        $operators = [];
        foreach ($filters as $key => $value) {
            if ( ($value === '' || $value === null) && !in_array($operators[$key] ?? '', ['is_empty', 'is_not_empty']) ) {
                continue;
            }

            if (in_array($key, ['order', 'order_by', 'page', 'per_page', 'export_csv'])) {
                continue;
            }

            $operatorsCheck = explode('__operator', $key);
            if ( count($operatorsCheck) == 2 ) {
                $operators[$operatorsCheck[0]] = $value;
                continue;
            }

            $operator = $operators[$key] ?? '=';

            if (method_exists(__CLASS__, $key)) {
                self::$key($value, $operator);
            }
            else {
                if ( in_array($operator, ['in', 'not_in']) ) {
                    preg_match_all('/(".*?",)|(".*?"$)/', $value, $matches);
                    foreach ($matches[0] as &$field) {
                        $field = ltrim(rtrim(str_replace('",', '', trim($field)),'"'), '"');
                    }
                    $value = $matches[0];
                }

                if ( $operator == 'is_empty' ) {
                    self::$query->where(function ($q) use ($key) {
                        $q->where($key, '=', '')
                            ->orWhereNull($key);
                    });
                }
                else if ( $operator == 'is_not_empty' ) {
                    self::$query->where(function ($q) use ($key) {
                        $q->whereNotNull($key)
                            ->where($key, '<>', '');
                    });
                }
                else if ( $operator == '!=' ) {
                    self::$query->where($key, '<>', $value);
                }
                else if ( in_array($operator, ['=', '>', '<']) ) {
                    self::$query->where($key, $operator, $value);
                }
                else if ( $operator == 'in' ) {
                    self::$query->whereIn($key, $value);
                }
                else if ( $operator == 'not_in' ) {
                    self::$query->whereNotIn($key, $value);
                }
                else if ( $operator == 'contains' ) {
                    self::$query->where($key, 'LIKE', '%' . $value . '%');
                }
                else if ( $operator == 'not_contains' ) {
                    self::$query->where($key, 'NOT LIKE', '%' . $value . '%');
                }
                else {
                    self::$query->where($key, $value);
                }
            }
        }

        self::order_by($filters['order_by'] ?? '', $filters['order'] ?? 'asc');
    }

    protected static function order_by($order_by, $order) {
        if ( $order_by ) {
            $order_bys = explode(',', $order_by);
            $orders = explode(',', $order);

            foreach ( $order_bys as $i => $order_by ) {
                self::$query->orderBy($order_by, $orders[$i] ?? $orders[0]);
            }
        }
        else if ( self::$has_default_order ) {
            self::$query->orderBy('id', 'desc');
        }
    }

    protected static function name($value) {
        self::$query->where('name', 'LIKE', '%' . $value . '%');
    }

    protected static function start_date($value) {
        self::$query->where('date', '>=', $value);
    }

    protected static function end_date($value) {
        self::$query->where('date', '<=', $value);
    }

    protected static function start_created_at($value) {
        self::$query->where('created_at', '>=', apply_reverse_timezone($value, auth()->user()->timezone));
    }

    protected static function end_created_at($value) {
        self::$query->where('created_at', '<=', apply_reverse_timezone($value . ' 23:59:59', auth()->user()->timezone));
    }
}

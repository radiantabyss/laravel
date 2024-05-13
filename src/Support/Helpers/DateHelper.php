<?php
namespace Lumi\Core\Support\Helpers;

class DateHelper
{
    public function getTimezoneOffset($remote_tz, $origin_tz = null) {
        if($origin_tz === null) {
            if(!is_string($origin_tz = date_default_timezone_get())) {
                return false; // A UTC timestamp was returned -- bail out!
            }
        }
        $origin_dtz = new DateTimeZone($origin_tz);
        $remote_dtz = new DateTimeZone($remote_tz);
        $origin_dt = new DateTime("now", $origin_dtz);
        $remote_dt = new DateTime("now", $remote_dtz);
        $offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
        return $offset / -1;
    }

    public function getTimezoneOffsetHourFormat($timezone) {
        if ( !$timezone ) {
            return '+00:00';
        }

        $offset = $this->getTimezoneOffset($timezone);
        $is_positive = true;
        if ( $offset < 0 ) {
            $offset *= -1;
            $is_positive = false;
        }

        return ($is_positive ? '+' : '-') . date('H:i', $offset);
    }

    public function getTimezoneOffsetForSql($timezone, $field = 'created_at') {
        return 'CONVERT_TZ(' . $field . ', "+00:00", "' . $this->getTimezoneOffsetHourFormat($timezone) . '")';
    }

    public function applyTimezone($timestamp, $timezone = false, $format = 'Y-m-d H:i:s') {
        if (!$timezone) {
            return $timestamp;
        }

        return date($format, strtotime($timestamp) + $this->getTimezoneOffset($timezone));
    }

    public function applyReverseTimezone($timestamp, $timezone = false, $format = 'Y-m-d H:i:s') {
        if (!$timezone) {
            return $timestamp;
        }

        return date($format, strtotime($timestamp) - $this->getTimezoneOffset($timezone));
    }

    public function prettyTimezone($timezone) {
        $offset = $this->getTimezoneOffset($timezone);
        $hour = floor($offset / 3600);
        $hour = abs($hour) < 10 ? ( $hour < 0 ? '-0'.abs($hour) : '+0'.$hour) : ($hour > 0 ? '+'.$hour : $hour);
        $minutes = ($offset % 3600) / 60;
        $minutes = $minutes < 10 ? '0'.$minutes : $minutes;
        $pretty = $timezone.' (GMT'.$hour.':'.$minutes.')';

        return $pretty;
    }

    public function prettyHour($hour) {
        $pretty_hour = $hour.' AM';

        if ( $hour > 11 ) {
            $pretty_hour = $hour == 12 ? '12 PM' : ($hour - 12).' PM';
        }
        else if ( $hour == 0 ) {
            $pretty_hour = '12 AM';
        }

        return $pretty_hour;
    }

    public function prettyDate($date, $with_hour = false, $with_day = true, $with_month = true) {
        $timestamp = $date;
        if (gettype($date) == 'string' || preg_match('/\d+/', $timestamp)) {
            $timestamp = strtotime($date);
        }
        else if (gettype($date) == 'object') {
            $timestamp = strtotime($date->toString());
        }

        $format = 'Y';

        if ( $with_month ) {
            $format = date('Y', $timestamp) != date('Y') ? 'M Y' : 'M';
        }

        if ( $with_day ) {
            $format = ($with_day ? 'd ' : '').$format;
        }

        $format .= $with_hour ? ' @ H:i' : '';

        return date($format, $timestamp);
    }

    public function getTimezones() {
        $timezones = [];
        foreach ( DateTimeZone::listIdentifiers(DateTimeZone::ALL) as $timezone ) {
            $timezones[] = [
                'text' => pretty_timezone($timezone),
                'value' => $timezone,
            ];
        }

        return $timezones;
    }

    public function range($start, $end) {
        $dates = [];
        $start = new \DateTime($start);
        $end = new \DateTime($end);
        $end = $end->modify('+1 day'); // Include end date

        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($start, $interval, $end);

        foreach ( $dateRange as $date ) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }

    public function isInRange($date, $start, $end = false) {
        if ( is_string($start) ) {
            if ( !$end ) {
                throw 'End date is required.';
            }

            return $date >= $start && $date <= $end;
        }

        return $date >= $start[0] && $date <= $start[count($start) - 1];
    }

    public function parseWindow($window) {
        $timezone = '';
        $months_short = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        $months_long = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'];

        //today
        if ( $window == 'today' ) {
            $start = $end = date('Y-m-d');
        }
        //yesterday
        else if ( $window == 'yesterday' ) {
            $start = $end = date('Y-m-d', strtotime('yesterday'));
        }
        //x_days_ago
        else if ( preg_match('/_days_ago/', $window) ) {
            $days = str_replace('_days_ago', '', $window);
            $start = date('Y-m-d', strtotime('-'.$days.' days'));
            $end = date('Y-m-d', strtotime('-'.$days.' days'));
        }
        //last_x_hours
        else if ( preg_match('/last_/', $window) && preg_match('/_hours/', $window) ) {
            $amount = str_replace('last_', '', str_replace('_hours', '', $window));
            $start = date('Y-m-d H:i:s', strtotime('-'.$amount.' hours'));
            $end = date('Y-m-d H:i:s');
        }
        //last_x_days
        else if ( preg_match('/last_/', $window) && preg_match('/_days/', $window) ) {
            $amount = str_replace('last_', '', str_replace('_days', '', $window));
            $start = date('Y-m-d', strtotime('-'.$amount.' days'));
            $end = date('Y-m-d');
        }
        //current_month
        else if ( $window == 'current_month' ) {
            $start = date('Y-m-01');
            $end = date('Y-m-d');
        }
        //last_month
        else if ( $window == 'last_month' ) {
            $start = date('Y-m-01', strtotime('-1 month'));
            $end = date('Y-m-t', strtotime('-1 month'));
        }
        //x_months_ago
        else if ( preg_match('/_months_ago/', $window) ) {
            $months = str_replace('_months_ago', '', $window);
            $start = date('Y-m-01', strtotime('-'.$months.' months'));
            $end = date('Y-m-t', strtotime('-'.$months.' months'));
        }
        //month or month long or month + year
        else if ( in_array(strtolower($window), $months_short)
            || in_array(strtolower($window), $months_long)
            || preg_match('/\w+ \d{4}/', $window)
        ) {
            $start = date('Y-m-d', strtotime('first day of '.$window));
            $end = date('Y-m-d', strtotime('last day of '.$window));
        }
        //year
        else if ( preg_match('/^\d{4}$/', $window) ) {
            $start = $window.'-01-01';
            $end = $window.'-12-31';
        }
        //month
        else if ( preg_match('/^\d{4}-\d{2}$/', $window) ) {
            $exp = explode('-', $window);
            $start = $window.'-01';
            $end = $window.'-'.cal_days_in_month(CAL_GREGORIAN, $exp[1], $exp[0]);
        }
        //date
        else if ( preg_match('/^\d{4}-\d{2}-\d{2}$/', $window) ) {
            $start = date('Y-m-d', strtotime($window));
            $end = date('Y-m-d', strtotime($window));
        }
        //date:date (date interval)
        else if ( preg_match('/\:/', $window) ) {
            $exp = explode(':', $window);
            $start = date('Y-m-d', strtotime($exp[0]));
            $end = date('Y-m-d', strtotime($exp[1]));
        }
        //lifetime
        else if ( $window == 'lifetime' || $window == 'none' ) {
            return null;
        }

        return compact('start', 'end');
    }

    public function fromYearMonth($year, $month) {
        return $year.'-'.$month.'-'.cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }
}

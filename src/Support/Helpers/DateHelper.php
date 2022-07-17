<?php
namespace RA\Core\Support\Helpers;

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
}

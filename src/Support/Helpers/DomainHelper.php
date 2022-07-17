<?php
namespace RA\Core\Support\Helpers;

class DomainHelper
{
    public function get($package = '') {
        if ( !app('request')->route() ) {
            return '';
        }

        $route = app('request')->route()->getAction();
        $action = trim(str_replace($route['namespace'], '', str_replace('@run', '', $route['controller'])), '\\');
        $domain = explode('\\', $action);
        array_pop($domain);
        array_splice($domain, 1, 2);

        foreach ( $domain as &$val ) {
            $val = \Str::kebab($val);
        }

        return ($package ? $package.'::' : '').implode('/', $domain);
    }

    public function name($plural = false) {
        $name = $this->get();

        if ( $plural ) {
            $name = preg_replace('/ys$/', 'ies', $name.'s');
        }

        return to_words($name);
    }

    public function action() {
        $route = app('request')->route()->getAction();
        $action = trim(str_replace($route['namespace'], '', str_replace('@run', '', $route['controller'])), '\\');
        $exp = explode('\\', $action);
        $action = \Str::kebab(str_replace('Action', '', end($exp)));

        return $action;
    }

    public function actionName() {
        $action = ucwords(str_replace('-', ' ', $this->action()));
        return $action;
    }

    public function view($package = '', $is_revision = false) {
        if ( !is_string($package) ) {
            $is_revision = $package;
            $package = '';
        }

        $view = $is_revision ? 'revision' : $this->action();
        $domain = \Str::studly($this->get());

        if ( $package ) {
            $view = $package.'::'.$domain.'.'.$view;
        }
        else {
            $view = 'AppDomains::'.$domain.'.views.'.$view;
        }

        return $view;
    }

    public function viewsPath($package = '', $is_revision = false) {
        if ( !is_string($package) ) {
            $is_revision = $package;
            $package = '';
        }

        $domain = \Str::studly($this->get());

        if ( $package ) {
            $path = $package.'::'.$domain;
        }
        else {
            $path = 'AppDomains::'.$domain.'.views';
        }

        return $path;
    }
}

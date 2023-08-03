<?php

namespace ishop;

use Exception;
use ishop\helpers\data\constants\Properties;

class Router
{

    protected static array $routes = [];
    protected static array $route = [];

    public static function add($regexp, $route = []): void
    {
        self::$routes[$regexp] = $route;
    }

    public static function getRoutes(): array
    {
        return self::$routes;
    }

    public static function getRoute(): array
    {
        return self::$route;
    }

    public static function removeQueryString(string $url): string
    {
        if ($url) {
            $params = explode('&', $url, 2);

            if (!str_contains($params[0], '=')) {
                return rtrim($params[0], '/');
            }
        }

        return '';
    }

    /**
     * @throws Exception
     */
    public static function dispatch(string $url): void
    {
        $clearUrl = self::removeQueryString($url);

        if (!self::matchRoute($clearUrl)) {
            throw new Exception("Страница не найдена", 404);
        }

        if (!empty(self::$route['lang'])) {
            App::$app->setProperty(Properties::LANG, self::$route['lang']);
        }

        $controller =
            'app\controllers\\' .
            self::$route['admin_prefix'] .
            self::$route['controller'] .
            'Controller';

        if (!class_exists($controller)) {
            throw new Exception("Контроллер {$controller} не найден", 404);
        }

        /** @var Controller $controllerObject */
        $controllerObject = new $controller(self::$route);
        $controllerObject->getModel();

        $action = self::toLowerCamelCase(self::$route['action'] . 'Action');

        if (!method_exists($controllerObject, $action)) {
            throw new Exception("Метод {$controller}::{$action} не найден", 404);
        }

        $controllerObject->$action();
        $controllerObject->getView();
    }

    private static function updateRoute(array $matches, array $route): void
    {
        foreach ($matches as $k => $v) {
            if (is_string($k)) {
                $route[$k] = $v;
            }
        }

        if (empty($route['action'])) {
            $route['action'] = 'index';
        }

        if (!isset($route['admin_prefix'])) {
            $route['admin_prefix'] = '';
        } else {
            $route['admin_prefix'] .= '\\';
        }

        $route['controller'] = self::toUpperCamelCase($route['controller']);

        self::$route = $route;
    }

    public static function matchRoute(string $url): bool
    {
        foreach (self::$routes as $pattern => $route) {
            if (preg_match("#{$pattern}#", $url, $matches)) {
                self::updateRoute($matches, $route);
                return true;
            }
        }
        return false;
    }

    protected static function toUpperCamelCase($name): string
    {
        $ucName = ucwords(str_replace('-', ' ', $name));
        return str_replace(' ', '', $ucName);
    }

    protected static function toLowerCamelCase($name): string
    {
        return lcfirst(self::toUpperCamelCase($name));
    }

}
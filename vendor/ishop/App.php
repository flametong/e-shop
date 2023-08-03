<?php

namespace ishop;

class App
{
    public static Registry $app;

    public function __construct()
    {
        new ErrorHandler();

        session_start();

        self::$app = Registry::getInstance();
        $this->getParams();

        $query = trim(urldecode($_SERVER['QUERY_STRING']),'/');
        Router::dispatch($query);
    }

    protected function getParams(): void
    {
        $params = require_once CONFIG . '/params.php';

        if (!empty($params)) {
            foreach ($params as $name => $value) {
                self::$app->setProperty($name, $value);
            }
        }
    }
}
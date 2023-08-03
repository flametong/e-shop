<?php

namespace ishop;

class Language
{
    // Массив со всеми переводными фразами страницы
    public static array $langData = [];
    // Массив со всеми переводными фразами шаблона
    public static array $langLayout = [];
    // Массив со всеми переводными фразами вида
    public static array $langView = [];

    public static function load(string $code, array $route): void
    {
        $langLayout = APP . "/languages/{$code}.php";
        $langView = APP . "/languages/{$code}/{$route['controller']}/{$route['action']}.php";

        if (file_exists($langLayout)) {
            self::$langLayout = require_once $langLayout;
        }

        if (file_exists($langView)) {
            self::$langView = require_once $langView;
        }

        self::$langData = array_merge(self::$langLayout, self::$langView);
    }

    public static function get(string $key): string
    {
        return self::$langData[$key] ?? $key;
    }

}
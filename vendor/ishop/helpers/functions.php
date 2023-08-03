<?php

use \ishop\App;
use \ishop\Language;
use \ishop\helpers\data\constants\get\GetTypes;
use \ishop\helpers\data\constants\post\PostTypes;

function debug($data, $die = false): void
{
    echo '<pre>' . print_r($data, 1) . '</pre>';
    if ($die) die;
}

function h(string $str): string
{
    return htmlspecialchars($str);
}

function redirect($http = false): never
{
    if ($http) {
        $redirect = $http;
    } else {
        $redirect =
            isset($_SERVER['HTTP_REFERER'])
                ? $_SERVER['HTTP_REFERER']
                : PATH;
    }

    header("Location: $redirect");
    exit;
}

function getLangCorrectBaseUrl()
{
    return
        PATH
        . '/'
        . (
        App::$app->getProperty('lang')
            ? App::$app->getProperty('lang') . '/'
            : ''
        );
}

/**
 * @param string $key Key of GET array
 * @param $type Values 'i' - int, 'f' - float, 's' - string
 * @return int|float|string
 */
function get(string $key, $type = GetTypes::TYPE_INT): int|float|string
{
    $param = $key;
    $$param = $_GET[$param] ?? '';

    if ($type === GetTypes::TYPE_INT) {
        return (int)$$param;
    } elseif ($type === GetTypes::TYPE_FLOAT) {
        return (float)$$param;
    } else {
        return trim($$param);
    }
}

/**
 * @param string $key Key of POST array
 * @param $type Values 'i' - int, 'f' - float, 's' - string
 * @return int|float|string
 */
function post(string $key, $type = PostTypes::TYPE_STRING): int|float|string
{
    $param = $key;
    $$param = $_POST[$param] ?? '';

    if ($type === PostTypes::TYPE_INT) {
        return (int)$$param;
    } elseif ($type === PostTypes::TYPE_FLOAT) {
        return (float)$$param;
    } else {
        return trim($$param);
    }
}

function getPhrase(string $key): string
{
    return Language::get($key);
}

function getCartIcon(int $id): string
{
    if (
        !empty($_SESSION['cart'])
        && array_key_exists($id, $_SESSION['cart'])
    ) {
        $icon = '<i class="fas fa-luggage-cart"></i>';
    } else {
        $icon = '<i class="fas fa-shopping-cart"></i>';
    }

    return $icon;
}

function getFieldValue(string $key): string
{
    return
        isset($_SESSION['form_data'][$key])
            ? h($_SESSION['form_data'][$key])
            : '';
}
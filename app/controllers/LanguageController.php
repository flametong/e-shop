<?php

namespace app\controllers;

use app\models\Cart;
use ishop\App;
use ishop\helpers\data\constants\get\GetKeys;
use ishop\helpers\data\constants\get\GetTypes;
use ishop\helpers\data\constants\Properties;

class LanguageController extends AppController
{

    private function getLangCorrectUrl(string $newLang): string
    {
        // Отрезаем базовый URL
        /*
        ***  TODO: убрать $_SERVER['HTTP_REFERER'],
        ***   получать предыдущий язык через COOKIE
        */
        $url = trim(
            str_replace(
                PATH,
                '',
                $_SERVER['HTTP_REFERER']
            ),
            '/'
        );

        // Explode URL into 2 parts where:
        // $url_parts[0] - current language (if it's not based lang)
        // $url_parts[1] - the rest part of URL
        $urlParts = explode('/', $url, 2);
        $currLang = App::$app->getProperty(Properties::LANGUAGE);
        $languages = App::$app->getProperty(Properties::LANGUAGES);

        // Finding the 1st part in Registry
        if (array_key_exists($urlParts[0], $languages)) {
            // Assign the 1st part a new language
            // if it's not based lang
            if ($newLang !== $currLang['code']) {
                $urlParts[0] = $newLang;
            } else {
                // If new language is based
                // then delete old lang from array
                array_shift($urlParts);
            }

        } else {
            // Assign the 1st part a new language
            // if it's not based lang
            if ($newLang !== $currLang['code']) {
                array_unshift($urlParts, $newLang);
            }
        }

        return PATH . '/' . implode('/', $urlParts);
    }

    public function changeAction(): void
    {
        $lang = get(GetKeys::LANG, GetTypes::TYPE_STRING);
        $languages = App::$app->getProperty(Properties::LANGUAGES);

        if ($lang) {
            if (array_key_exists($lang, $languages)) {
                Cart::translateCart($languages[$lang]);
                redirect($this->getLangCorrectUrl($lang));
            }
        }
        redirect();
    }

}
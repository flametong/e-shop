<?php

namespace app\widgets\language;

use ishop\App;
use ishop\helpers\data\constants\Properties;
use RedBeanPHP\R;

class Language
{

    protected string $tpl;
    protected array $languages;
    protected array $language;

    public function __construct()
    {
        $this->tpl = __DIR__ . '/lang_tpl.php';
        $this->run();
    }

    protected function run(): void
    {
        $this->languages = App::$app->getProperty(Properties::LANGUAGES);
        $this->language = App::$app->getProperty(Properties::LANGUAGE);
        echo $this->getHtml();
    }

    public static function getLanguages(): array
    {
        return R::getAssoc(
            "SELECT code, title, base, id
                 FROM language
                 ORDER BY base DESC"
        );
    }

    public static function getLanguage($languages)
    {
        $lang = App::$app->getProperty(Properties::LANG);

        if ($lang && array_key_exists($lang, $languages)) {
            $key = $lang;
        } elseif (!$lang) {
            $key = key($languages);
        } else {
            $lang = h($lang);
            throw new \Exception("Not found language {$lang}", 404);
        }

        $lang_info = $languages[$key];
        $lang_info['code'] = $key;

        return $lang_info;
    }

    protected function getHtml(): string
    {
        ob_start();
        require_once $this->tpl;
        return ob_get_clean();
    }

}
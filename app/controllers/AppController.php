<?php

namespace app\controllers;

use app\models\AppModel;
use app\models\Wishlist;
use app\widgets\language\Language;
use ishop\App;
use ishop\Controller;
use ishop\helpers\data\constants\Properties;
use RedBeanPHP\R;

class AppController extends Controller
{

    public function __construct($route)
    {
        parent::__construct($route);
        new AppModel();

        App::$app->setProperty(Properties::LANGUAGES, Language::getLanguages());
        App::$app->setProperty(
            Properties::LANGUAGE,
            Language::getLanguage(App::$app->getProperty(Properties::LANGUAGES))
        );

        $currLang = App::$app->getProperty(Properties::LANGUAGE);
        \ishop\Language::load($currLang['code'], $this->route);

        $categories = R::getAssoc(
            "SELECT c.*, cd.* 
                     FROM category c 
                     JOIN category_description cd
                     ON c.id = cd.category_id
                     WHERE cd.language_id = ?",
            [$currLang['id']]
        );

        App::$app->setProperty(
            "categories_{$currLang['code']}",
            $categories
        );

        App::$app->setProperty(
            Properties::WISHLIST,
            Wishlist::getWishListIds()
        );
    }

}
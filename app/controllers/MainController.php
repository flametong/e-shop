<?php

namespace app\controllers;

use app\models\Main;
use ishop\App;
use ishop\helpers\data\constants\Properties;
use RedBeanPHP\R;

/** @property Main $model */
class MainController extends AppController
{

    public function indexAction(): void
    {
        $lang = App::$app->getProperty(Properties::LANGUAGE);

        $slides = R::findAll('slider');

        $products = $this->model->getHits($lang['id'], 6);

        $this->set(compact('slides', 'products'));
        $this->setMeta(
            getPhrase('main_index_meta_title'),
            getPhrase('main_index_meta_description'),
            getPhrase('main_index_meta_keywords'),
        );
    }

}
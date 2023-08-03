<?php

namespace app\controllers;

use app\models\Page;
use ishop\App;
use ishop\helpers\data\constants\Properties;
use ishop\helpers\data\enums\Meta;

/** @property  Page $model */
class PageController extends AppController
{

    public function viewAction(): void
    {
        $lang = App::$app->getProperty(Properties::LANGUAGE);
        $page = $this->model->getPage(
            $this->route['slug'],
            $lang['id']
        );

        if (!$page) {
            $this->error404();
            return;
        }

        $this->setMeta(
            $page[Meta::Title->value],
            $page[Meta::Description->value],
            $page[Meta::Keywords->value]
        );
        $this->set(compact('page'));
    }

}
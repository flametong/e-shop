<?php

namespace app\controllers;

use app\models\Breadcrumbs;
use app\models\Product;
use ishop\App;
use ishop\helpers\data\constants\Properties;
use ishop\helpers\data\enums\Meta;

/** @property Product $model */
class ProductController extends AppController
{

    public function viewAction(): void
    {
        $lang = App::$app->getProperty(Properties::LANGUAGE);
        $product = $this->model->getProduct(
            $this->route['slug'], $lang['id']
        );

        if (!$product) {
            $this->error404();
            return;
        }

        $breadcrumbs = Breadcrumbs::getBreadcrumbs($product['category_id'], $product['title']);

        $gallery = $this->model->getGallery($product['id']);

        $this->setMeta(
            $product[Meta::Title->value],
            $product[Meta::Description->value],
            $product[Meta::Keywords->value],
        );

        $this->set(
            compact(
                'product',
                'gallery',
                'breadcrumbs'
            )
        );
    }

}
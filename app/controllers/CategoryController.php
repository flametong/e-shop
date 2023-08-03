<?php

namespace app\controllers;

use app\models\Breadcrumbs;
use app\models\Category;
use ishop\App;
use ishop\helpers\data\constants\get\GetKeys;
use ishop\helpers\data\constants\Properties;
use ishop\helpers\data\enums\Meta;
use ishop\Pagination;

/** @property Category $model */
class CategoryController extends AppController
{

    public function viewAction(): void
    {
        $lang = App::$app->getProperty(Properties::LANGUAGE);
        $langId = $lang['id'];

        $category = $this->model->getCategory(
            $this->route['slug'],
            $langId
        );

        if (!$category) {
            $this->error404();
            return;
        }

        $categoryId = $category['id'];

        $breadcrumbs = Breadcrumbs::getBreadcrumbs($categoryId);

        $ids = $this->model->getChildrenIds($categoryId);
        $ids = !$ids ? $categoryId : $ids . $categoryId;

        $page = get(GetKeys::PAGE);
        // TODO: сделать $perPage динамическим
        //  в зависимости от выбора пользователя
        $perPage = App::$app->getProperty(Properties::PAGINATION);
        $total = $this->model->getCountProducts($ids);

        $pagination = new Pagination($page, $perPage, $total);
        $start = $pagination->getStart();

        $products = $this->model->getProducts(
            $ids, $langId, $start, $perPage
        );

        $this->setMeta(
            $category[Meta::Title->value],
            $category[Meta::Description->value],
            $category[Meta::Keywords->value]
        );

        $this->set(
            compact(
                'category',
                'breadcrumbs',
                'products',
                'total',
                'pagination'
            )
        );
    }

}
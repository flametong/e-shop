<?php

namespace app\controllers;

use app\models\Search;
use ishop\App;
use ishop\helpers\data\constants\get\GetKeys;
use ishop\helpers\data\constants\get\GetTypes;
use ishop\helpers\data\constants\Properties;
use ishop\Pagination;

/** @property Search $model */
class SearchController extends AppController
{

    public function indexAction(): void
    {
        $searchQuery = get(GetKeys::SEARCH_QUERY, GetTypes::TYPE_STRING);
        $lang = App::$app->getProperty(Properties::LANGUAGE);
        $langId = $lang['id'];

        $page = get(GetKeys::PAGE);
        $perPage = App::$app->getProperty(Properties::PAGINATION);
        $total = $this->model->getCountSearchingProducts($searchQuery, $langId);

        $pagination = new Pagination($page, $perPage, $total);
        $start = $pagination->getStart();

        $products = $this->model->getSearchingProducts(
            $searchQuery, $langId, $start, $perPage
        );

        $this->setMeta(getPhrase('tpl_search_title'));
        $this->set(
            compact(
                'searchQuery',
                'products',
                'pagination',
                'total'
            )
        );
    }

}
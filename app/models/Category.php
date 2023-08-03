<?php

namespace app\models;

use ishop\App;
use ishop\helpers\data\constants\get\GetKeys;
use ishop\helpers\data\constants\get\GetTypes;
use ishop\helpers\data\constants\Properties;
use RedBeanPHP\R;

class Category extends AppModel
{

    public function getCategory(string $slug, int $langId): array
    {
        return R::getRow(
            "SELECT c.*, cd.*
                 FROM category c
                 JOIN category_description cd 
                 ON c.id = cd.category_id
                 WHERE c.slug = ? AND cd.language_id = ?",
            [$slug, $langId]
        );
    }

    public function getChildrenIds(string $id): string
    {
        $langCode = App::$app->getProperty(Properties::LANGUAGE)['code'];
        $categories =
            App::$app->getProperty("categories_{$langCode}");

        $ids = '';

        foreach ($categories as $catId => $property) {
            if ($property['parent_id'] === $id) {
                $ids .= $catId . ',';
                $ids .= $this->getChildrenIds($catId);
            }
        }

        return $ids;
    }

    public function getProducts(
        string $ids,
        string $langId,
        int    $start,
        int    $perPage
    ): array
    {
        $sortValues = [
            'by_default' => '',
            'title_asc' => 'ORDER BY title ASC',
            'title_desc' => 'ORDER BY title DESC',
            'price_asc' => 'ORDER BY price ASC',
            'price_desc' => 'ORDER BY price DESC',
        ];

        $orderBy = '';
        $sortType = get(GetKeys::SORT, GetTypes::TYPE_STRING) ?? '';

        if (
            $sortType !== ''
            && array_key_exists($sortType, $sortValues)
        ) {
            $orderBy = $sortValues[$sortType];
        }

        return R::getAll(
            "SELECT p.*, pd.*
                 FROM product p
                 JOIN product_description pd
                 ON p.id = pd.product_id
                 WHERE 
                     p.status = 1 
                     AND p.category_id IN ($ids)
                     AND pd.language_id = ?
                 $orderBy
                 LIMIT ?, ?",
            [$langId, $start, $perPage]
        );
    }

    public function getCountProducts(string $ids): int
    {
        return R::count(
            'product',
            "category_id IN ($ids) AND status = 1"
        );
    }

}
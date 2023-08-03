<?php

namespace app\models;

use RedBeanPHP\R;

class Search extends AppModel
{

    public function getCountSearchingProducts(string $search, int $langId): int
    {
        return R::getCell(
            "SELECT COUNT(*)
                 FROM product p 
                 JOIN product_description pd 
                     ON p.id = pd.product_id
                 WHERE 
                     p.status = 1 
                     AND pd.language_id = ?
                     AND pd.title LIKE ?",
            [$langId, "%{$search}%"]
        );
    }

    public function getSearchingProducts(
        string $search,
        int    $langId,
        int    $start,
        int    $perPage
    ): array
    {
        return R::getAll(
            "SELECT p.*, pd.*
                 FROM product p
                 JOIN product_description pd 
                     ON p.id = pd.product_id
                 WHERE 
                     p.status = 1
                     AND pd.language_id = ?
                     AND pd.title LIKE ?
                 LIMIT ?, ?",
            [$langId, "%{$search}%", $start, $perPage]
        );
    }

}
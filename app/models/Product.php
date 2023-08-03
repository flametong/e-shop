<?php

namespace app\models;

use RedBeanPHP\R;

class Product extends AppModel
{

    public function getProduct(string $slug, int $langId): array
    {
        return R::getRow(
            "SELECT p.*, pd.*
                 FROM product p 
                 JOIN product_description pd ON p.id = pd.product_id
                 WHERE p.status = 1 AND p.slug = ? AND pd.language_id = ?",
            [$slug, $langId]
        );
    }

    public function getGallery($productId): array
    {
        return R::getAll(
            "SELECT * 
                 FROM product_gallery 
                 WHERE product_id = ?",
            [$productId]
        );
    }

}
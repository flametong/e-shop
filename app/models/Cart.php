<?php

namespace app\models;

use ishop\helpers\data\constants\SessionKeys;
use RedBeanPHP\R;

class Cart extends AppModel
{

    public function getProduct(int $id, int $langId): array
    {
        return R::getRow(
            "SELECT p.*, pd.*
                 FROM product p
                 JOIN product_description pd ON p.id = pd.product_id
                 WHERE p.status = 1 AND p.id = ? AND pd.language_id = ?",
            [$id, $langId]
        );
    }

    public function addToCart(array $product, int $qty = 1): bool
    {
        $qty = abs($qty);

        if (
            $product['is_download']
            && isset($_SESSION[SessionKeys::CART][$product['id']])
        ) {
            return false;
        }

        if (isset($_SESSION[SessionKeys::CART][$product['id']])) {
            $_SESSION[SessionKeys::CART][$product['id']]['qty'] += $qty;
        } else {
            if ($product['is_download']) {
                $qty = 1;
            }

            $_SESSION[SessionKeys::CART][$product['id']] = [
                'title' => $product['title'],
                'slug' => $product['slug'],
                'price' => $product['price'],
                'qty' => $qty,
                'img' => $product['img'],
                'is_download' => $product['is_download'],
            ];
        }

        $_SESSION[SessionKeys::CART_QUANTITY] =
            !empty($_SESSION[SessionKeys::CART_QUANTITY])
                ? $_SESSION[SessionKeys::CART_QUANTITY] + $qty
                : $qty;

        $_SESSION[SessionKeys::CART_SUM] =
            !empty($_SESSION[SessionKeys::CART_SUM])
                ? $_SESSION[SessionKeys::CART_SUM] + $qty * $product['price']
                : $qty * $product['price'];

        return true;
    }

    public function deleteItem(int $id): void
    {
        $qtyMinus = $_SESSION[SessionKeys::CART][$id]['qty'];
        $sumMinus =
            $_SESSION[SessionKeys::CART][$id]['qty'] * $_SESSION[SessionKeys::CART][$id]['price'];

        $_SESSION[SessionKeys::CART_QUANTITY] -= $qtyMinus;
        $_SESSION[SessionKeys::CART_SUM] -= $sumMinus;

        unset($_SESSION[SessionKeys::CART][$id]);
    }

    public function clearItems(): void
    {
        unset($_SESSION[SessionKeys::CART]);
        unset($_SESSION[SessionKeys::CART_QUANTITY]);
        unset($_SESSION[SessionKeys::CART_SUM]);
    }

    public static function translateCart(array $lang): void
    {
        if (empty($_SESSION[SessionKeys::CART])) {
            return;
        }

        $ids = implode(',', array_keys($_SESSION[SessionKeys::CART]));
        $products = R::getAll(
            "SELECT p.id, pd.title
                 FROM product p
                 JOIN product_description pd on p.id = pd.product_id
                 WHERE p.id IN ($ids) AND pd.language_id = ?",
            [$lang['id']]
        );

        foreach ($products as $product) {
            $_SESSION[SessionKeys::CART][$product['id']]['title'] = $product['title'];
        }
    }

}
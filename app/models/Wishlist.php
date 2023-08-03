<?php

namespace app\models;

use ishop\helpers\data\constants\CookieKeys;
use RedBeanPHP\R;
use ishop\helpers\data\enums\Timestamp;

class Wishlist extends AppModel
{

    public function getProduct(int $id): array|null|string
    {
        return R::getCell(
            "SELECT id
                 FROM product
                 WHERE status = 1 AND id = ?",
            [$id]
        );
    }

    public function addToWishlist(int $id): void
    {
        $wishlistIds = self::getWishListIds();
        $month = Timestamp::Month->value;

        if (!$wishlistIds) {
            setcookie(CookieKeys::WISHLIST, $id, time() + $month, '/');
        } else {
            if (in_array($id, $wishlistIds)) {
                return;
            }

            if (count($wishlistIds) > 5) {
                array_shift($wishlistIds);
            }

            $wishlistIds[] = $id;
            $wishlistIds = implode(',', $wishlistIds);
            setcookie(CookieKeys::WISHLIST, $wishlistIds, time() + $month, '/');
        }
    }

    public static function getWishListIds(): array
    {
        $wishlist = $_COOKIE[CookieKeys::WISHLIST] ?? '';

        if ($wishlist) {
            $wishlist = explode(',', $wishlist);
        }

        if (!is_array($wishlist)) {
            return [];
        }

        $wishlist = array_slice($wishlist, 0, 6);

        return array_map('intval', $wishlist);
    }

    public function getWishlistProducts(int $langId): array
    {
        $wishlistIds = self::getWishListIds();

        if (!$wishlistIds) {
            return [];
        }

        // TODO: сделать сортировку так,
        //  чтобы товары были в порядке добавления пользователем
        $wishlistIds = implode(',', $wishlistIds);

        return R::getAll(
            "SELECT p.*, pd.*
                     FROM product p
                     JOIN product_description pd 
                         ON p.id = pd.product_id
                     WHERE 
                         p.status = 1 
                         AND p.id IN ($wishlistIds)
                         AND pd.language_id = ?
                     LIMIT 6",
            [$langId]
        );
    }

    public function deleteFromWishlist(int $id): bool
    {
        $wishlistIds = self::getWishListIds();
        $key = array_search($id, $wishlistIds);

        if ($key === false) {
            return false;
        }

        unset($wishlistIds[$key]);

        $month = Timestamp::Month->value;
        $hour = Timestamp::Hour->value;

        if ($wishlistIds) {
            $wishlistIds = implode(',', $wishlistIds);
            setcookie(CookieKeys::WISHLIST, $wishlistIds, time() + $month, '/');
        } else {
            setcookie(CookieKeys::WISHLIST, '', time() - $hour, '/');
        }

        return true;
    }

}
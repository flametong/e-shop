<?php

namespace app\controllers;


use app\models\Wishlist;
use ishop\App;
use ishop\helpers\data\constants\Properties;
use ishop\helpers\data\constants\Results;

/** @property Wishlist $model */
class WishlistController extends AppController
{

    public function indexAction(): void
    {
        $lang = App::$app->getProperty(Properties::LANGUAGE);
        $products = $this->model->getWishlistProducts($lang['id']);

        $this->setMeta(getPhrase('wishlist_index_title'));
        $this->set(compact('products'));
    }

    public function addAction(): never
    {
        $id = get('id');

        if (!$id) {
            $answer = [
                'result' => Results::ERROR,
                'text' => getPhrase('tpl_wishlist_add_error'),
            ];
            exit(json_encode($answer));
        }

        $product = $this->model->getProduct($id);

        if ($product) {
            $this->model->addToWishlist($id);
            $answer = [
                'result' => Results::SUCCESS,
                'text' => getPhrase('tpl_wishlist_add_success'),
            ];
        } else {
            $answer = [
                'result' => Results::ERROR,
                'text' => getPhrase('tpl_wishlist_add_error'),
            ];
        }

        exit(json_encode($answer));
    }

    public function deleteAction(): never
    {
        $id = get('id');

        if ($this->model->deleteFromWishlist($id)) {
            $answer = [
                'result' => Results::SUCCESS,
                'text' => getPhrase('tpl_wishlist_delete_success'),
            ];
        } else {
            $answer = [
                'result' => Results::ERROR,
                'text' => getPhrase('tpl_wishlist_delete_error'),
            ];
        }

        exit(json_encode($answer));
    }

}
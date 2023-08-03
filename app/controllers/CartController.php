<?php

namespace app\controllers;

use app\models\Cart;
use app\models\Order;
use app\models\User;
use ishop\App;
use ishop\helpers\data\constants\Attributes;
use ishop\helpers\data\constants\get\GetKeys;
use ishop\helpers\data\constants\post\PostKeys;
use ishop\helpers\data\constants\Properties;
use ishop\helpers\data\constants\SessionKeys;
use ishop\helpers\data\constants\Utilities;

/** @property Cart $model */
class CartController extends AppController
{

    public function addAction()
    {
        $lang = App::$app->getProperty(Properties::LANGUAGE);
        $id = get(GetKeys::ID);
        $qty = get(GetKeys::QUANTITY);

        if (!$id) {
            return false;
        }

        $product = $this->model->getProduct($id, $lang['id']);

        if (!$product) {
            return false;
        }

        $this->model->addToCart($product, $qty);

        if ($this->isAjax()) {
            $this->loadView('cart_modal');
        }

        redirect();
    }

    public function showAction(): void
    {
        $this->loadView('cart_modal');
    }

    public function deleteAction(): void
    {
        $id = get(GetKeys::ID);

        if (isset($_SESSION[SessionKeys::CART])) {
            $this->model->deleteItem($id);
        }

        if ($this->isAjax()) {
            $this->loadView('cart_modal');
        }

        redirect();
    }

    public function clearAction()
    {
        if (empty($_SESSION[SessionKeys::CART])) {
            return false;
        }

        $this->model->clearItems();
        $this->loadView('cart_modal');
    }

    public function viewAction(): void
    {
        $this->setMeta(getPhrase('tpl_cart_title'));
    }

    private function registerUser(): int
    {
        $user = new User();
        $user->load();
        $attributes = $user->attributes;

        if (
            !$user->validate($attributes)
            || !$user->checkUnique()
        ) {
            $user->setErrors();
            $_SESSION[SessionKeys::FORM_DATA] = $attributes;
            redirect();
        } else {
            $user->hashAttribute(Attributes::PASSWORD);

            if (!$userId = $user->save(Utilities::USER)) {
                $_SESSION[SessionKeys::ERRORS] = getPhrase('cart_checkout_error_register');
                redirect();
            }

            return $userId;
        }
    }

    public function checkoutAction(): void
    {
        if (empty($_POST)) {
            return;
        }

        // Registering a user
        // if he is not authorized
        if (!User::checkAuth()) {
            $userId = $this->registerUser();
        }

        // Order saving
        $data['user_id'] = $userId ?? $_SESSION[SessionKeys::USER]['id'];
        $data['note'] = post(PostKeys::NOTE);
        $userEmail =
            $_SESSION[SessionKeys::USER][Attributes::EMAIL] ?? post(PostKeys::EMAIL);

        if (!$orderId = Order::saveOrder($data)) {
            $_SESSION[SessionKeys::ERRORS] = getPhrase('cart_checkout_error_save_order');
        } else {
            Order::mailOrder(
                $orderId,
                $userEmail,
                'mail_order_user'
            );
            Order::mailOrder(
                $orderId,
                App::$app->getProperty(Properties::ADMIN_EMAIL),
                'mail_order_admin'
            );

            unset($_SESSION[SessionKeys::CART]);
            unset($_SESSION[SessionKeys::CART_SUM]);
            unset($_SESSION[SessionKeys::CART_QUANTITY]);

            $_SESSION[SessionKeys::SUCCESS] = getPhrase('cart_checkout_order_success');
        }

        redirect();
    }

}
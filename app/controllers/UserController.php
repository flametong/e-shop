<?php

namespace app\controllers;

use app\models\User;
use ishop\App;
use ishop\helpers\data\constants\Attributes;
use ishop\helpers\data\constants\Properties;
use ishop\helpers\data\constants\SessionKeys;
use ishop\helpers\data\constants\Utilities;
use ishop\Pagination;

/** @property User $model */
class UserController extends AppController
{

    private function fillAttributes(): void
    {
        foreach ($this->model->attributes as $key => $value) {
            if (!empty($value) && $key !== Attributes::PASSWORD) {
                $_SESSION[SessionKeys::USER][$key] = $value;
            }
        }
    }

    public function credentialsAction(): void
    {
        if (!User::checkAuth()) {
            redirect(getLangCorrectBaseUrl());
        }

        $this->setMeta(getPhrase('user_credentials_title'));

        if (empty($_POST)) {
            return;
        }

        $this->model->load();
        $attributes = &$this->model->attributes;

        if (empty($attributes[Attributes::PASSWORD])) {
            unset($attributes[Attributes::PASSWORD]);
        }

        unset($attributes[Attributes::EMAIL]);

        if (!$this->model->validate($attributes)) {
            $this->model->setErrors();
            $_SESSION[SessionKeys::FORM_DATA] = $attributes;
        } else {
            if (!empty($this->model->attributes[Attributes::PASSWORD])) {
                $this->model->hashAttribute(Attributes::PASSWORD);
            }

            $userId = $_SESSION[SessionKeys::USER]['id'];

            if ($this->model->update(Utilities::USER, $userId)) {
                $_SESSION[SessionKeys::SUCCESS] = getPhrase('user_credentials_success');
                $this->fillAttributes();
            } else {
                $_SESSION[SessionKeys::ERRORS] = getPhrase('user_credentials_error');
            }
        }

        redirect();
    }

    public function signupAction(): void
    {
        if (User::checkAuth()) {
            redirect(getLangCorrectBaseUrl());
        }

        $this->setMeta(getPhrase('tpl_signup'));

        if (empty($_POST)) {
            return;
        }

        $this->model->load();
        $attributes = $this->model->attributes;

        if (
            !$this->model->validate($attributes)
            || !$this->model->checkUnique()
        ) {
            $this->model->setErrors();
            $_SESSION[SessionKeys::FORM_DATA] = $attributes;
        } else {
            $this->model->hashAttribute(Attributes::PASSWORD);

            if ($this->model->save(Utilities::USER)) {
                $_SESSION[SessionKeys::SUCCESS] = getPhrase('user_signup_success_register');
            } else {
                $_SESSION[SessionKeys::ERRORS] = getPhrase('user_signup_error_register');
            }
        }

        redirect();
    }

    public function loginAction(): void
    {
        if (User::checkAuth()) {
            redirect(getLangCorrectBaseUrl());
        }

        $this->setMeta(getPhrase('tpl_login'));

        if (empty($_POST)) {
            return;
        }

        if ($this->model->login()) {
            $_SESSION[SessionKeys::SUCCESS] = getPhrase('user_login_success_login');
            redirect(getLangCorrectBaseUrl());
        } else {
            $_SESSION[SessionKeys::ERRORS] = getPhrase('user_login_error_login');
            redirect();
        }
    }

    public function logoutAction(): void
    {
        if (User::checkAuth()) {
            unset($_SESSION[SessionKeys::USER]);
        }

        redirect(getLangCorrectBaseUrl() . 'user/login');
    }

    public function accountAction(): void
    {
        if (!User::checkAuth()) {
            redirect(getLangCorrectBaseUrl() . 'user/login');
        }

        $this->setMeta(getPhrase('tpl_account'));
    }

    public function ordersAction(): void
    {
        if (!User::checkAuth()) {
            redirect(getLangCorrectBaseUrl() . 'user/login');
        }

        $page = get('page');
        $perPage = App::$app->getProperty(Properties::PAGINATION);
        $userId = $_SESSION[SessionKeys::USER]['id'];
        $total = $this->model->getCountOrders($userId);

        $pagination = new Pagination($page, $perPage, $total);
        $start = $pagination->getStart();

        $orders = $this->model->getUserOrders(
            $userId, $start, $perPage
        );

        $this->setMeta(getPhrase('user_orders_title'));
        $this->set(
            compact(
                'orders',
                'pagination',
                'total'
            )
        );
    }

    public function orderAction(): void
    {
        if (!User::checkAuth()) {
            redirect(getLangCorrectBaseUrl() . 'user/login');
        }

        $id = get('id');
        $order = $this->model->getUserOrder($id);

        if (!$order) {
            throw new \Exception('Not found order', 404);
        }

        $this->setMeta(getPhrase('user_order_title'));
        $this->set(compact('order'));
    }

    public function filesAction(): void
    {
        if (!User::checkAuth()) {
            redirect(getLangCorrectBaseUrl() . 'user/login');
        }

        $lang = App::$app->getProperty(Properties::LANGUAGE);

        $page = get('page');
        $perPage = App::$app->getProperty(Properties::PAGINATION);
        $userId = $_SESSION[SessionKeys::USER]['id'];
        $total = $this->model->getCountFiles($userId);

        $pagination = new Pagination($page, $perPage, $total);
        $start = $pagination->getStart();

        $files = $this->model->getUserFiles(
            $userId, $lang['id'], $start, $perPage
        );

        $this->setMeta(getPhrase('user_files_title'));
        $this->set(
            compact(
                'files',
                'pagination',
                'total'
            )
        );
    }

    public function downloadAction(): void
    {
        if (!User::checkAuth()) {
            redirect(getLangCorrectBaseUrl() . 'user/login');
        }

        $id = get('id');
        $lang = App::$app->getProperty(Properties::LANGUAGE);
        $userId = $_SESSION[SessionKeys::USER]['id'];

        $file = $this->model->getUserFile($userId, $lang['id'], $id);

        if (!$file) {
            redirect();
        }

        $path = WWW . "/downloads/{$file['filename']}";

        if (file_exists($path)) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file['original_name']) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit();
        } else {
            $_SESSION[SessionKeys::ERRORS] = getPhrase('user_download_error');
        }

        redirect();
    }

}
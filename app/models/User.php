<?php

namespace app\models;

use ishop\helpers\data\constants\SessionKeys;
use RedBeanPHP\OODBBean;
use RedBeanPHP\R;
use ishop\helpers\data\constants\Attributes;

class User extends AppModel
{

    public array $attributes = [
        Attributes::EMAIL => '',
        Attributes::PASSWORD => '',
        Attributes::NAME => '',
        Attributes::ADDRESS => '',
    ];

    public array $rules = [
        'required' => [
            Attributes::EMAIL,
            Attributes::PASSWORD,
            Attributes::NAME,
            Attributes::ADDRESS
        ],
        Attributes::EMAIL => [Attributes::EMAIL],
        'lengthMin' => [
            [Attributes::PASSWORD, 6],
        ],
        'optional' => [Attributes::EMAIL, Attributes::PASSWORD],
    ];

    public array $labels = [
        Attributes::EMAIL => 'tpl_signup_email_input',
        Attributes::PASSWORD => 'tpl_signup_password_input',
        Attributes::NAME => 'tpl_signup_name_input',
        Attributes::ADDRESS => 'tpl_signup_address_input',
    ];

    public static function checkAuth(): bool
    {
        return isset($_SESSION[SessionKeys::USER]);
    }

    public function checkUnique(string $textError = ''): bool
    {
        $user = R::findOne(
            'user',
            'email = ?',
            [$this->attributes[Attributes::EMAIL]]
        );

        if (!$user) {
            return true;
        }

        $this->errors['unique'][] =
            $textError
                ?: getPhrase('user_signup_error_email_unique');

        return false;
    }

    public function login(bool $isAdmin = false): bool
    {
        $email = post(Attributes::EMAIL);
        $password = post(Attributes::PASSWORD);

        if (!$email || !$password) {
            return false;
        }

        $user = $this->checkAdmin($isAdmin, $email);

        if ($user) {
            if (password_verify($password, $user->password)) {
                $this->writeToSession($user);
                return true;
            }
        }

        return false;
    }

    public function checkAdmin(
        bool   $isAdmin,
        string $email
    ): OODBBean|null
    {
        if ($isAdmin) {
            $user = R::findOne(
                'user',
                "email = ? AND role = 'admin'",
                [$email]
            );
        } else {
            $user = R::findOne(
                'user',
                "email = ?",
                [$email]
            );
        }

        return $user;
    }

    public function writeToSession(OODBBean|null $user): void
    {
        if (!$user) {
            return;
        }

        foreach ($user as $k => $v) {
            if (!$k != Attributes::PASSWORD) {
                $_SESSION[SessionKeys::USER][$k] = $v;
            }
        }
    }

    public function getCountOrders(int $userId): int
    {
        return R::count(
            'orders',
            'user_id = ?',
            [$userId]
        );
    }

    public function getUserOrders(
        int $userId,
        int $start,
        int $perPage
    ): array
    {
        return R::getAll(
            "SELECT *
                 FROM orders
                 WHERE user_id = ? 
                 ORDER BY id DESC
                 LIMIT ?, ?",
            [$userId, $start, $perPage]
        );
    }

    public function getUserOrder(int $id): array
    {
        return R::getAll(
            "SELECT o.*, op.*
                 FROM orders o
                 JOIN order_product op 
                     ON o.id = op.order_id
                 WHERE o.id = ?",
            [$id]
        );
    }

    public function getCountFiles(int $userId): int
    {
        return R::count(
            'order_download',
            'user_id = ? AND status = 1',
            [$userId]
        );
    }

    public function getUserFiles(
        int $userId,
        int $langId,
        int $start,
        int $perPage
    ): array
    {
        return R::getAll(
            "SELECT od.*, d.*, dd.*
                 FROM order_download od
                 JOIN download d 
                     ON od.download_id = d.id
                 JOIN download_description dd 
                     ON d.id = dd.download_id
                 WHERE 
                     od.user_id = ?   
                     AND dd.language_id = ?
                     AND od.status = 1
                 LIMIT ?, ?",
            [$userId, $langId, $start, $perPage]
        );
    }

    public function getUserFile(
        int $userId,
        int $langId,
        int $downloadId
    ): array
    {
        return R::getRow(
            "SELECT od.*, d.*, dd.*
                 FROM order_download od
                 JOIN download d 
                     ON od.download_id = d.id
                 JOIN download_description dd 
                     ON d.id = dd.download_id
                 WHERE 
                     od.user_id = ? 
                     AND dd.language_id = ?
                     AND od.download_id = ?
                     AND od.status = 1",
            [$userId, $langId, $downloadId]
        );
    }

}
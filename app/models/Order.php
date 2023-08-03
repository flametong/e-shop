<?php

namespace app\models;

use ishop\App;
use ishop\helpers\data\constants\Properties;
use ishop\helpers\data\constants\SessionKeys;
use ishop\helpers\data\constants\Utilities;
use PHPMailer\PHPMailer\PHPMailer;
use RedBeanPHP\R;

class Order extends AppModel
{

    public static function saveOrder(array $data): int|false
    {
        R::begin();

        try {
            $order = R::dispense('orders');

            $order->user_id = $data['user_id'];
            $order->note = $data['note'];
            $order->total = $_SESSION[SessionKeys::CART_SUM];
            $order->quantity = $_SESSION[SessionKeys::CART_QUANTITY];

            $orderId = R::store($order);
            self::saveOrderProduct($orderId, $data['user_id']);

            R::commit();

            return $orderId;
        } catch (\Exception $e) {
            R::rollback();
            return false;
        }
    }

    private static function saveDigital(
        int        $productId,
        int|string $orderId,
        int        $userId
    ): void
    {
        $downloadId = R::getCell(
            "SELECT download_id
                 FROM product_download
                 WHERE product_id = ?",
            [$productId]
        );
        $orderDownload = R::xdispense('order_download');

        $orderDownload->order_id = $orderId;
        $orderDownload->user_id = $userId;
        $orderDownload->product_id = $productId;
        $orderDownload->download_id = $downloadId;
        $orderDownload->status = 1;

        R::store($orderDownload);
    }

    public static function saveOrderProduct(
        int|string $orderId,
        int        $userId
    ): void
    {
        $sqlPart = '';
        $binds = [];

        foreach ($_SESSION[SessionKeys::CART] as $productId => $product) {
            // If a digital product
            if ($product['is_download']) {
                self::saveDigital($productId, $orderId, $userId);
            }

            $sum = $product['qty'] * $product['price'];
            $sqlPart .= "(?, ?, ?, ?, ?, ?, ?),";
            $binds = array_merge(
                $binds,
                [
                    $orderId, $productId, $product['title'],
                    $product['slug'], $product['qty'], $product['price'], $sum
                ]
            );
        }

        $sqlPart = rtrim($sqlPart, ',');

        R::exec(
            "INSERT INTO order_product
                     (order_id, product_id, title, slug, quantity, price, sum) 
                 VALUES $sqlPart",
            $binds
        );
    }

    public static function mailOrder(
        int|string $orderId,
        string     $userEmail,
        string     $tpl
    ): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->SMTPDebug = 3;
            $mail->CharSet = Utilities::DEFAULT_CHARSET;
            $mail->Host = App::$app->getProperty(Properties::SMTP_HOST);
            $mail->SMTPAuth = App::$app->getProperty(Properties::SMTP_AUTH);
            $mail->Username = App::$app->getProperty(Properties::SMTP_USERNAME);
            $mail->Password = App::$app->getProperty(Properties::SMTP_PASSWORD);
            $mail->SMTPSecure = App::$app->getProperty(Properties::SMTP_SECURE);
            $mail->Port = App::$app->getProperty(Properties::SMTP_PORT);
            $mail->isHTML();

            $mail->setFrom(
                App::$app->getProperty(Properties::SMTP_FROM_EMAIL),
                App::$app->getProperty(Properties::SMTP_NAME)
            );
            $mail->addAddress($userEmail);
            $mail->Subject = sprintf(
                getPhrase('cart_checkout_mail_subject'),
                $orderId
            );

            ob_start();
            require \APP . "/views/mail/{$tpl}.php";
            $body = ob_get_clean();

            $mail->Body = $body;

            return $mail->send();
        } catch (\Exception $e) {
//            debug($e, 1);
            return false;
        }
    }

}
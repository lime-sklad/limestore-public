<?php

use Core\Classes\Utils\Utils;

header('Content-type: Application/json');

$Payment = new Core\Classes\Cart\Payment;

if(empty($_POST['payment_method_id'])) {
    return Utils::abort([
        'type' => 'error',
        'texy' => 'Empty'
    ]);
    exit;
}

$Payment->deletePaymentMethod([
    'payment_method_id' => $_POST['payment_method_id']
]);

echo $utils::successAbort('Ok');
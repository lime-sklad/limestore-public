<?php

use Core\Classes\Privates\User;

header('Content-type: application/json');


$seller_id = $_POST['seller_id'];

$getSessionRole = User::getCurrentUser();

if($seller_id == 1 || $getSessionRole['user_role'] != 'admin' || $getSessionRole['user_id'] == $seller_id) {
    return $utils::abort([
        'type' => 'error',
        'text' => 'İstifadəçini silmək mümkün deyil'
    ]);

    exit;
}

$user->deleteUser($seller_id);

return $utils::abort([
    'type' => 'success',
    'text' => 'OK'
]);
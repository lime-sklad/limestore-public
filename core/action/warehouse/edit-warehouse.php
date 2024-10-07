<?php
header('Content-type: Applcation/json');

use Core\Classes\Services\Warehouse\Warehouse;
use Core\Classes\Utils\Utils;

$warehouse = new Warehouse;

if(!empty($_POST['prepare'])) {
    
    $data = $_POST['prepare'];

    $warehouse->editWarehouse($data);
    echo $utils::successAbort('Ok');
} else {
    echo Utils::abort([
        'type' => 'success',
        'text' => 'Ok'
    ]);   
}
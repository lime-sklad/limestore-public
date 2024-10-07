<?php 

header('Content-type: Application/json');

$warehouse = new Core\Classes\Services\Warehouse\Warehouse;

if(empty($_POST['list'])) {
    echo $utils::successAbort('Səbət boşdur');
    exit;
}


$list = $_POST['list'];

$warehouse->handleProductWriteOFf($list);

return $utils::abort([
    'text' => 'Əlavə edildi',
    'type' => 'success'
]);
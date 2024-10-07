<?php

use Core\Classes\Utils\Utils;

 header('Content-type', 'Application/json');

$report = new \Core\Classes\Report;

if(!empty($_POST['prepare'])) {
    $data = $_POST['prepare'];

    $report->editReport($data);

    $getLastEditedOrder = $report::getOrderById($_POST['oderId']);

    echo Utils::abort([
        'type' => 'success',
        'text' => 'Ok',
        'res' => [
            'edit-report-order-note' => $getLastEditedOrder['order_who_buy'],
            'product_second_price' => $getLastEditedOrder['order_stock_sprice'],
            'change_product_count' => $getLastEditedOrder['order_stock_count'],
            'edit-report-order-total' => $getLastEditedOrder['order_stock_total_price'],
            'res-report-order-profit' => $getLastEditedOrder['order_total_profit']
        ]
    ]);

} else {
    $utils::errorAbort('Emprty');
}




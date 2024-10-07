<?php

use Core\Classes\Utils\Utils;

header('Content-type: Application/json');

$stock_id = $_POST['data'];

$data_page = $main->initController('productHistory');

$sql = $data_page['sql'];


$sql['bindList'] = [
    ':id1' => $stock_id,
    ':id2' => $stock_id,
    ':id3' => $stock_id,
];



$table_result = $main->prepareData($sql, $data_page['page_data_list']);

$total = $Render->view('/component/include_component.twig', [
	'renderComponent' => [
		'/component/modal/custom_modal/u_modal.twig' => [
            'title' => 'Hereketler',
            'path' => '/component/table/table_wrapper.twig',
            'class_list' => '',
            'table' => $table_result['result'], 
             
		]  
	]
]);


$utils::abort([
	'res' => $total,
]);

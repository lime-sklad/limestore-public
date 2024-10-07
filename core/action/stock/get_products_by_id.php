<?php
header('Content-type: Application/json');
use core\classes\dbWrapper\db;
use Core\Classes\Privates\accessManager;
use Core\Classes\Utils\Utils;

$data = $_POST['data'];

$id = $data['id'];


$row = db::select([
	'table_name' => 'stock_list',
	'col_list' => '*',
	'query' => [
		'base_query' => ' WHERE stock_id = :id ',
	],
	'bindList' => [
		'id' => $id
	]

])->first()->get();


$stock_id 				= $row['stock_id'];
$stock_name 			= $row['stock_name'];
$stock_second_price 	= $row['stock_second_price'];
$stock_count 			= $row['stock_count'];
$first_price			= $row['stock_first_price'];
	
$complete = array(
	'stock_id'  		  => $stock_id,	
	'stock_name' 		  => $stock_name,
	'stock_first_price'	  => accessManager::checkDataPremission('th_buy_price') ? $first_price : '',
	'description'		  => $row['stock_phone_imei'],
	'stock_second_price'  => $stock_second_price, 
	'stock_count'         => $stock_count, 	 
	// 'manat_image' 		  => $manat_image 	 	 
);


$json_show = array(
	'param' => $complete
); 

echo json_encode($json_show);




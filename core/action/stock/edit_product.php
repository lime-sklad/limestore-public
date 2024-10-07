<?php

use Core\Classes\Utils\Utils;
use Core\Classes\Services\Warehouse\Traits\Arrival;
use Core\Classes\Services\Warehouse\Traits\WriteOff;

header('Content-type: Application/json');

$postData = $_POST['data'];

$stock_id = $postData['product_id'];

$advanced = $postData['advanced'] ?? null;

// добавляем новую категорию для товара (не создаем категория, а добавляем уже существующую для товра )
if (!empty($advanced['new_added_category']) && !empty($postData['product_id'])) {
	$products->setProductCategory($stock_id, $advanced['new_added_category']);
}

// если нужно изменить категория товара
if (!empty($advanced['edited_category'])) {
	$products->editProductCategory($advanced['edited_category']);
}

// если нужно удалить категория товара
if (!empty($advanced['deleted_category'])) {
	$products->deleteProductCategory($advanced['deleted_category']);
}


if (!empty($advanced['new_added_provider'])) {
	$products->setProductProvider($stock_id, $advanced['new_added_provider']);
}

if (!empty($advanced['edited_provider'])) {
	$products->editProductProvider($advanced['edited_provider']);
}

if (!empty($advanced['deleted_provider'])) {
	$products->deleteProductProvider($advanced['deleted_provider']);
}


/**
 * исправить массив что бы выдывал только 1 результат
 * в js дописать js функцию update_table_row 
 * добавить в function.php id к каждой строке в табице для обновления резальутатата
 */

$prepare_data = $postData['prepare_data'];

if (empty($advanced) && count($prepare_data) < 2) {
	return $utils::abort([
		'type' => 'error',
		'text' => 'Вы ничего не изменили'
	]);
}

if (!empty($prepare_data) && count($prepare_data) > 1) {
	echo $products->editProduct($prepare_data);
	$productsFilter->editProductFilter($prepare_data, $prepare_data['upd_product_id']);
}


// updFooter: {
// 	newItemCount: prepare_data.plus_minus_product_count,
// 	oldItemCount: Utils.getItemOldCount,

// 	oldFooterCount: Utils.getTableFooterCount
// }


// $ControllerData = $init->getControllerData(Utils::getPostPage())->allData;

// $a = (int)str_replace(' ', '', $_POST['updFooter']['oldFooterCount']);
// $b =  (int)str_replace(' ', '', $_POST['updFooter']['oldItemCount']);
// $c =  (int)str_replace(' ', '', $_POST['updFooter']['newItemCount']);


$total = $Render->view('/component/include_component.twig', [
	'renderComponent' => [
		'/component/table/table_footer_row.twig' => [	
			'table_total' => $utils->updateTableFooterData($_POST['updFooter'])		
		]  
	]
]);


return $utils::abort([
	'type' => 'success',
	'text' => 'Updated',
	'total' => $total
]);

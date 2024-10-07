<?php
header('Content-type: Application/json');

use Core\Classes\Services\Warehouse\Warehouse;

$postData = $_POST['data'];

$prepare_data = $postData['prepare_data'];

$data = [];

if(!empty($prepare_data) && count($prepare_data) > 0) {
	try {			
		if(!empty($prepare_data['stock_barcode_list'])) { 
			$barcode_list = $prepare_data['stock_barcode_list'];
	
			$products->hasProductBarcode($barcode_list) ?? die;
		}
		
		$products->addProduct($prepare_data);

		// получаем последний добавленный товар, тоесть тот который мы только что добавили
		$last_stock_id = $products->getLastAddedProduct('stock_id');

		
		Warehouse::logProductArrival([
			'id' => $last_stock_id,
			'count' => $products->getProductById($last_stock_id)['stock_count'],
			'description' => ''
		]);


		//добавляем фильтры для товара
		$productsFilter->setProductFilter($prepare_data, $last_stock_id);

		
		if(!empty($prepare_data['stock_barcode_list'])) {
			$products->setProductBarcode($barcode_list, $last_stock_id);
		}
		
		// тут добавляем категорию товара
		$products->setProductCategory($last_stock_id, $prepare_data['category_list']);

		// тут добавляем поставщика товра
		$products->setProductProvider($last_stock_id, $prepare_data['provider_list']);

		//выводим сообщение о добавленом товаре
		$utils::abort([
			'type' => 'success',
			'text' => 'Ok'
		]);
	} catch (Exception $e) {
		$utils::abort([
			'type' => 'error',
			'text' => "error [$e]"
		]);
	}

}
<?php 
header('Content-type: Application/json');

$postData = $_POST['data'];

//удалить товар
if(!empty($postData['stock_id'])) {
	$products->deleteProduct($postData['stock_id']);


	$total = $Render->view('/component/include_component.twig', [
		'renderComponent' => [
			'/component/table/table_footer_row.twig' => [	
				'table_total' => $utils->updateTableFooterData($_POST['updFooter'])		
			]  
		]
	]);

	return $utils::abort([
		'type'	=> 'success',
		'text'	=> 'Ok',
		'table_footer' => $total
	]);
} else {
	return $utils::abort([
		'type'	=> 'error',
		'text' 	=> 'Cant find id'
	]);
}

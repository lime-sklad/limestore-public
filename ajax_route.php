<?php 

// if(!isset($_SESSION['user'])) {
//     $login_dir = '/login.php';
// 	header("Location: $login_dir");
// 	exit();      
// }

use Core\Classes\Privates\accessManager;
use Core\Classes\Utils\Utils;

require_once 'start.php';

session_write_close();

$routeLis = [
	//general
	'checkNewVersion'			=> ROOT.'/core/action/update/check_new_version.php',
	'downloadUpdate'			=> ROOT.'/core/action/update/download_update.php',
	'check_date'				=> ROOT.'/core/main/check_date.php',
	'activeLicense'				=> ROOT.'/core/main/license/active-license.php',
	'createBackup'			=> ROOT.'/core/action/backup/telegramBotDatabaseBackup.php',
	
	// products
	'addProduct'			=> ROOT.'/core/action/stock/add_stock.php',
	'editProduct'			=> ROOT.'/core/action/stock/edit_product.php',
	'deleteProducts'		=> ROOT.'/core/action/stock/delete_products.php',
	'getProductsById' 		=> ROOT.'/core/action/stock/get_products_by_id.php',
	'filtredProducts'		=> ROOT.'/core/action/stock/get_filter_stock.php',
	'editProductBarcodeModal' => ROOT.'/core/action/barcode/edit_product_barcode_modal.php',
	'setProductBarcode'		=> ROOT.'/core/action/barcode/set_product_barcode.php',
	'getProductHistory'		=> ROOT.'/core/action/stock/get_products_history.php',

	// checkout
	'addToCart' 			=> ROOT.'/core/action/cart/cart_item_row.php',
	'checkout'				=> ROOT.'/core/action/cart/checkout.php',
	

	'search'				=> ROOT.'/core/action/search/search.php',
	'autocomplete' 			=> ROOT.'/core/action/search/autocomplete.php',
	'advancedSearch'		=> ROOT.'/core/action/search/advanced_search.php',
	
	'modal'					=> ROOT.'/core/action/modal/modal.php',
	'appendMoreFields'		=> ROOT.'/core/action/add_more_fields.php',

	'appendStoriesModal'		=> ROOT.'/core/action/stories/appendStoriesModal.php',
	'getStories'				=> ROOT.'/core/action/stories/getStories.php',

	// report
	'editReport'			=> ROOT.'/core/action/report/edit.php',
	'deleteOrder'			=> ROOT.'/core/action/report/refaund.php',
	
	'includeStats'			=> ROOT.'/core/pulgin/stats_card/stats_report.php',
	'reportChart'			=> ROOT.'/core/pulgin/charts/report_month_chart_stats.php',
	'reportChartCategory'	=> ROOT.'/core/pulgin/charts/report_category_charts.php',
	'reportChartProvider'	=> ROOT.'/core/pulgin/charts/report_provider_charts.php',
	'reportTopProducts'		=> ROOT.'//core/pulgin/charts/report_top_products.php',
	
	'scanBarcode' 			=> ROOT.'/core/action/barcode/scanBarcode.php',		

	// expense
	'editExpense'			=> ROOT.'/core/action/expense/edit_expense.php',
	'deleteExpense'			=> ROOT.'/core/action/expense/delete_expense.php',
	'addExpense'			=> ROOT.'/core/action/expense/add_expense.php',

	// transfer
	'addTransfer'			=> ROOT.'/core/action/warehouse-transfer/add-transfer.php',

	// arrival
	'addArrivalProducts'	=> ROOT.'/core/action/arrival-products/add-arrival-products.php',

	// writeoff
	'addWriteOff'			=> ROOT.'/core/action/write-off-products/add-write-off-products.php',


	//admin
	'addUser'				=> ROOT.'/core/action/admin/user/add-user.php',
	'deleteUser'			=> ROOT.'/core/action/admin/user/delete-user.php',
	'editUser'				=> ROOT.'/core/action/admin/user/edit-user.php',

	//category
	'addCategory' 			=> ROOT.'/core/action/category/add-category.php',
	'editCategory' 			=> ROOT.'/core/action/category/edit-category.php',
	'deleteCategory' 			=> ROOT.'/core/action/category/delete-category.php',


	//warehouse
	'addWarehouse'			=> ROOT.'/core/action/warehouse/add-warehouse.php',
	'editWarehouseInfo'		=> ROOT.'/core/action/warehouse/edit-warehouse.php',
	'deleteWarehouse'		=> ROOT.'/core/action/warehouse/delete-warehouse.php',

	//payment method
	'addPaymentMethod'		=> ROOT.'/core/action/payment-method/add-payment-method.php',
	'editPaymentMethod'		=> ROOT.'/core/action/payment-method/edit-payment-method.php',
	'deletePaymentMethod'	=> ROOT.'/core/action/payment-method/delete-payment-method.php',

	// provider
	'addProvider'			=> ROOT.'/core/action/provider/add-provider.php',
	'editProvider'			=> ROOT.'/core/action/provider/edit-provider.php',
	'deleteProvider'		=> ROOT.'/core/action/provider/delete-provider.php',
	
	// filter
	'addFilter'				=> ROOT.'/core/action/filter/add-filter.php',
	'deleteFilter'			=> ROOT.'/core/action/filter/delete-filter.php',
	'editFilter'			=> ROOT.'/core/action/filter/edit-filter.php',

];

$route = $_POST['route'];

accessManager::accessActionRoute($route, function($hasAccess) {
	if($hasAccess) {
		echo Utils::abort([
			'type' => 'error',
			'text' => 'access denied!'
		]);

		die;
	}
});

require $routeLis[$route]; 


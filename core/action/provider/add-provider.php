<?php
header('Content-type: Application/json');

if (!empty($_POST) && count($_POST['post_data']) > 0) {
	$provider->addProvider($_POST['post_data']);

	$this_data = $init->getControllerData($utils::getPostPage())->allData;

	$table_result = $main->prepareCustomData($provider->getLastAddedProvider(), $this_data['page_data_list']);

	$table = $Render->view('/component/include_component.twig', [
		'renderComponent' => [
			'/component/table/table_row.twig' => [
				'table' => $table_result['result'],
				'table_tab' => $utils::getPostPage(),
				'table_type' => $utils::getPostType()
			]
		]
	]);

	echo $utils::abort([
		'success' => 'ok',
		'table' => $table,
		'provider_id' => $table_result['base_result'][0]['provider_id'],
		'provider_name' => $table_result['base_result'][0]['provider_name'],
	]);
}

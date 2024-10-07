<?php

use Core\Classes\Utils\Utils;

header('Content-type: Application/json');

if(empty($_POST) || empty($_POST['filter_title']) || empty($_POST['filter_option'])) {
    return $utils::abort([
        'type' => 'error',
        'text' => 'Заполните все поля'
    ]);
}


$productsFilter->addFilter(['title' => $_POST['filter_title'] ], $_POST['filter_option']);

$this_data = $init->getControllerData($utils::getPostPage())->allData;

$table_result = $main->prepareCustomData($productsFilter->getLastAddedFilter(), $this_data['page_data_list']);

$table = $Render->view('/component/include_component.twig', [
    'renderComponent' => [
        '/component/table/table_row.twig' => [		
            'table' => $table_result['result'],
            'table_tab' => $utils::getPostPage(),
        ]  
    ]
]);	


echo $utils::abort([
    'type' => 'success',
    'text' => 'OK',
    'table' => $table
]);

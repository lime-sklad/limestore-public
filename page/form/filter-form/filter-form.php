<?php

$data_page = $init->initController($page);
$page_config = $data_page['page_data_list'];

$table_result = $main->prepareData($data_page['sql'], $data_page['page_data_list']);

echo $Render->view('/component/inner_container.twig', [
    'renderComponent' => [
        '/component/filter/create_new_filter.twig' => [		
        ],

        '/component/table/table_wrapper.twig' => [
            'table' => $table_result['result'],
            'table_tab' => $page,
            'table_type' => $type,				
        ],        
    ]
]);
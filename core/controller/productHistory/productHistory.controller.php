<?php 

$accessManager = new \core\classes\privates\accessManager;
$user = new \core\classes\privates\user;

return [
    'tab' => [
        'is_main' => false  ,
        'title'		 		=> 'Anbar',
        'icon'				=> [
            'img_big'		 	=> 'assets/img/svg/070-file hosting.svg',
            'img_small'			=> '',
            'modify_class' 		=> 'las la-boxes'
        ],
        'link'  			=> '/page/base.php',		
        'template_src'      => '/page/base_tpl.twig',
        'background_color'  => 'rgba(72, 61, 139, 0.1)',
        'tab' => array(
            'list' => [
                'tab_stock_phone',
                'tab_stock_form',
            ],
            'active' => 'tab_stock_phone'
        )
    ],
    'sql' => [
        'table_name' => 'stock_list',
        'col_list'	=> "stock_list.stock_id,
                        stock_list.stock_name,
                        stock_list.stock_count,
                       CONCAT(IF(arrival_products.count >= 0, '+', ''), arrival_products.count) AS count,
                        arrival_products.full_date,
                        'Alış fakturası' AS operation_type ",
        'query' => array(
            "base_query" => ' ',
            "body" => " JOIN
                            arrival_products ON stock_list.stock_id = arrival_products.id_from_stock
                        WHERE
                            stock_list.stock_id = :id1
    
                        UNION ALL
    
                        SELECT
                            stock_list.stock_id,
                            stock_list.stock_name,
                            stock_list.stock_count,
                            -write_off_products.count AS count,
                            write_off_products.full_date,
                            'Silinmə fakturası' AS operation_type
                        FROM
                            stock_list
                        JOIN
                            write_off_products ON stock_list.stock_id = write_off_products.id_from_stock
                        WHERE
                            stock_list.stock_id = :id2

                        UNION ALL
    
                        SELECT
                            stock_list.stock_id,
                            stock_list.stock_name,
                            stock_list.stock_count,
                            -stock_order_report.order_stock_count AS count,
                            stock_order_report.order_date as full_date,
                            'Satış' AS operation_type
                        FROM
                            stock_list
                        JOIN
                            stock_order_report ON stock_list.stock_id = stock_order_report.stock_id
                        WHERE
                            stock_list.stock_id = :id3                            
    
                ",		
            'sort_by' => " ORDER BY full_date DESC ",
            'limit' => ' LIMIT 50 ', 
        ),	
        'bindList' => array(	
            ':id1' => null,
            ':id2' => null,					
            ':id3' => null,					
        )       
    ],
    'page_data_list' => [
        'sort_key' => 'stock_id',
        'get_data' => [
            'id' 				=> 'stock_id',
            'stock_add_date'	=> 'full_date',
            'name'			 	=> 'stock_name',
            'description' 		=> 'operation_type',
            'count'				=> 'count',
        ],
        'table_total_list'	=> [
            'stock_count',
            'sum_first_price'
        ],
        'modal' => [
            'template_block' => 'edit_product',
            'modal_fields' => array(			
            )					
        ],
        'filter_fields' => [
            // 'color',
            // 'storage',
            // 'ram',
            // 'brand'
        ],
        'form_fields_list' => array(
        )

    ]
];
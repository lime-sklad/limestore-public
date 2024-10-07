<?php 
namespace Core\Classes\Services;

use \Core\classes\dbWrapper\db;

class Provider 
{
    /**
     * получаем список поставщиков
     * @return array|null
     * old function name get_provider_list
     **/ 
    public function getProviderList() 
    {
        return db::select([
            'table_name' => ' stock_provider ',
            'col_list' => ' * ',
            'query' => [
                'base_query' => ' WHERE visible = "visible" ',                
                'body' => '',
                'joins' => '',
                'sort_by' => 'ORDER BY provider_id DESC'
            ],
            'bindList' => array(
            )
        ])->get();
    }

    /**
     * получаем список поставщиков
     * @return array|null
     * old function name get_category_list
     **/  
    function getCategoryList() 
    {
        return db::select([
            'table_name' => ' stock_category ',
            'col_list' => ' * ',
            'query' => [
                'base_query' => ' WHERE visible = "visible" ',
                'body' => '',
                'joins' => '',
                'sort_by' => 'ORDER BY category_id DESC'
            ],
            'bindList' => array(
            )
            
        ])->get();
    }
    

    /**
     * Выводит сумму продаж по категориям
     * 
     * @param date m.y $date дата
     * @return array|null
     */
    public function sumSalesByProvider($date)
    {
        return db::select([
            'table_name' => ' stock_order_report ',
            'col_list'	=> ' SUM(order_stock_total_price) AS total, provider_name ',
        
            'query' => array(
                'base_query' => "",
                'body' =>  "
                            INNER JOIN 
                                products_provider_list ON products_provider_list.id_from_stock = stock_order_report.stock_id 
                                    AND stock_order_report.stock_order_visible = 0
                                    AND stock_order_report.order_stock_count > 0 
                                    
                            LEFT JOIN 
                                stock_provider ON stock_provider.provider_id = products_provider_list.id_from_provider
        
                ",
                "joins" => " WHERE order_my_date = :mydateyear   ",		
                'sort_by' => " GROUP BY stock_provider.provider_id ",
            ),
            'bindList' => array(
                'mydateyear'    => $date
            )  
        ])->get();        
    }


    /**
     * 
     * old function name add_new_provider
     */
    public function addProvider($post_data)
    {
        $data = [];

        $col_post_list = [
            'add_provider_name' => [
                'col_name' => 'provider_name',
                'required' => true
            ],
        ];


        foreach ($col_post_list as $key => $value) {
            if (array_key_exists($key, $post_data)) {
                $data = array_merge($data, [
                    $value['col_name'] => $post_data[$key]
                ]);
            }
        }

        $default_data = [
            'visible' => 'visible',
        ];

        $data = array_merge($data, $default_data);

        return db::insert('stock_provider', [$data]);
    }    


    /**
     * 
     * 
     */
    public function editProvider($data)
    {
        // в первом массиве мы должны описать и связвать данные $_POST с таблицей
        $option = [
            'before' => " UPDATE stock_provider, user_control SET ",
            'after' => " WHERE stock_provider.provider_id  = :provider_id ",
            'post_list' => [
                //id
                'provider_id' => [
                    'query' => false,
                    'bind' => 'provider_id',
                    'require' => true
                ],
                //изменить название категории
                'upd_provider_name' => [
                    'query' => "stock_provider.provider_name = :cat_name",
                    'bind' => 'cat_name',
                ],
            ]
        ];

        return db::update($option, $data);
    }


    /**
     * 
     */
    public function deleteProvider($id)
    {
        return db::delete([
            [
                'table_name' => 'stock_provider',
                'where' => ' provider_id = :id',
                'bindList' => [
                    ':id' => $id
                ],
            ],
            [
                'table_name' => 'products_provider_list',
                'where' => ' id_from_provider = :id',
                'bindList' => [
                    ':id' => $id
                ],
            ]		
        ]);          
    }


    /**
     * 
     */
    public function getLastAddedProvider()
    {
        return db::select([
            'table_name' => 'stock_provider as tb',
            'col_list' => '*',
            'query' => [
                'base_query' => ' INNER JOIN stock_provider ON stock_provider.visible = "visible" ',
                'sort_by' => 'GROUP BY stock_provider.provider_id DESC ORDER BY stock_provider.provider_id DESC LIMIT 1'
            ]
        ])->get();
    }
}
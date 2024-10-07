<?php 

namespace Core\Classes\Services\Warehouse\Traits;

use Core\Classes\Utils\Utils;
use core\classes\dbWrapper\db;
use Core\Classes\Products;

trait WriteOff 
{

    /**
     * 
     */
    public function handleProductWriteOFf($list)
    {

        foreach($list as $key => $row) {
            $id = $row['id'];
            $count = $row['count'];
        
            Products::decreaseProductCount([
                'stock_id' => $id,
                'product_count' => $count
            ]);


            self::logProductWriteOff([
                'description' => $row['description'],
                'count'       => $count,
                'id'          => $id,
            ]);

        }        
    }

    /**
     * 
     */
    public static function logProductWriteOff($data)
    {
        $transaction_id = Utils::generateTransactionId();

        /**
         * @param array = [
         * 	'stock_id' => $id,
         * 	'description' => $desc,
         *  'count' => $count,
         *  'transaction_id' => $transaction_id
         * ];
         */            
        return db::insert('write_off_products', [
            [
                'description' 				=> $data['description'],
                'count' 					=> $data['count'],
                'day_date' 					=> Utils::getDateMY(),
                'full_date'					=> Utils::getDateDMY(),
                'id_from_stock' 			=>  $data['id'],
                'transaction_id' 			=> $transaction_id
            ]
        ]); 
    }


    /**
     * 
     */
    public function getWriteOffByDate($date, $controllerIndex)
    {
        $data_page = $this->main->initController($controllerIndex);
        
        $data_page['sql']['query']['body'] = $data_page['sql']['query']['body'] . "  AND write_off_products.day_date = :mydateyear";
        $data_page['sql']['bindList']['mydateyear'] = $date ?? date("m.Y");
        
        return $this->main->prepareData($data_page['sql'], $data_page['page_data_list']);        
    }
}
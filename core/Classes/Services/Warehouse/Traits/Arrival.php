<?php 

namespace Core\Classes\Services\Warehouse\Traits;

use Core\Classes\Utils\Utils;
use core\classes\dbWrapper\db;
use Core\Classes\Products;

trait Arrival
{

    /**
     * 
     */
    public function handleProductArrival($productList)
    {

        foreach($productList as $key => $row) {
            $id = $row['id'];
            $count = $row['count'];
        
            // stock_arrivals_count($id, $count);

            Products::increaseProductCount([
                'stock_id' => $id,
                'product_count' => $count
            ]);

            
            self::logProductArrival([
                'description' => $row['description'],
                'count'       => $count,
                'id'          => $id,
            ]);
        }
    }


    /**
     * 
     */
    public static function logProductArrival($data)
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
        return db::insert('arrival_products', [
            [
                'description' 				=> $data['description'],
                'count' 					=> $data['count'],
                'day_date' 					=> Utils::getDateMY(),
                'full_date'					=> Utils::getDateDMY(),
                'id_from_stock' 			=> $data['id'],
                'transaction_id' 			=> $transaction_id
            ]
        ]);        
    }

    /**
     * 
     */
    public function getArrivalByDate($date, $controllerIndex)
    {
        $data_page = $this->main->initController($controllerIndex);
        
        $data_page['sql']['query']['body'] = $data_page['sql']['query']['body'] . "  AND arrival_products.day_date = :mydateyear";
        $data_page['sql']['bindList']['mydateyear'] = $date ?? date("m.Y");
        
        return $this->main->prepareData($data_page['sql'], $data_page['page_data_list']);
    }
}
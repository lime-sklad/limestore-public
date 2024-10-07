<?php

namespace core\classes\dbWrapper\traits;
use Core\Classes\Utils\Utils;

trait select
{

    public static $result;
    
    /**
     * Делает запрос в базу данных
     * 
     * @param array $query - массив с данными таблицы
     *  $pdo_fetch_type = \PDO::FETCH_ASSOC, string $placeholders = 'named'
     */
    public static function select(array $query, $settings = array()) {

        /**
        *    $res = $db::select([
        *        'table_name' => 'stock_list',
        *        'col_list' => '*',
        *        'base_query' => '',
        *        'param' => [
        *            'query' => [
        *                'param' => '',
        *                'joins' => '',
        *                'bindList' => [
        *                    'param' => $param
        *                ]
        *            ],
        *            'sort_by' => '',
        *            'limit' => ''
        *        ],
        *    ])->get(); 
        **/
        $defaultSettings = [
            'fetch_type' => \PDO::FETCH_ASSOC,
            'placeholders' => 'named',
            'bindType' => 'bindParam'
        ];

        $defaultSettings = array_merge($defaultSettings, $settings);
    
        // Utils::log($defaultSettings);   

        $placeholders = $defaultSettings['placeholders'];
        $pdo_fetch_type = $defaultSettings['fetch_type'];
        $bindType = $defaultSettings['bindType'];

        $query_row = $query['query'];

        $conditions 		= [];
        $table_name 		= $query['table_name'] 				?? '';
        $col_list 			= $query['col_list'] 				?? '';
        $base_query 		= $query_row['base_query'] 				?? '';
        $body				= $query_row['body'] 		?? '';
        $joins				= $query_row['joins'] 		?? '';
        $sort_by			= $query_row['sort_by'] 			?? '';
        $limit				= $query_row['limit'] 				?? '';
        $bind_list			= $query['bindList'] 	?? array();
     
    
        $query  = "SELECT $col_list FROM $table_name ";
        $query .= $base_query;
        $query .= $body;
        $query .= $joins;
        $query .= $sort_by;
        $query .= $limit;
    

        $conditions = array_merge($conditions, $bind_list);
    
        $stock_view = parent::$dbpdo->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));
    
        if($placeholders == 'named') {
            foreach($conditions as $bind_key => $bindValue) {
                if($bindType == 'bindParam') {
                    $stock_view->bindParam($bind_key, $bindValue);
                } 
                
                if($bindType == 'bindValue') {
                    $stock_view->bindValue($bind_key, $bindValue);
                }

            }
        
            $stock_view->execute();
        }
    
        if($placeholders == 'positional') {
            $stock_view->execute($conditions);
        }
    
        
        self::$result = $stock_view->fetchAll($pdo_fetch_type);
    
        $stock_view->closeCursor();
        

        return new static;
    }

    public static function first()
    {
        if(count(self::$result) > 0) {
            self::$result = self::$result[0];
        }
        
        return new static;
    }

    public static function columnName(string $columnName)
    {
        self::$result = self::$result[$columnName];

        return new static;
    }

    /**
     * @return array
     */
    public static function get(): array
    {  
        return (array) self::$result;
    }
}

<?php

namespace Core\Classes\Utils;

use Core\Classes\Privates\AccessManager;
use Core\Classes\System\Init;
use GuzzleHttp\Client;


class Utils
{


    /**
     * 
     * @param int $price
     * @return int
     * old function name decorate_num
     */
    public static function prettyPrintNumber($price)
    {
        // Проверяем, является ли число дробным
        if (strpos($price, '.') !== false) {
            // Преобразуем число в дробное с двумя знаками после запятой
            $formatted_number = number_format((float)$price, 2, '.', ' ');
        } else {
            // Преобразуем число в целое
            $formatted_number = number_format((float)$price, 0, '.', ' ');
        }

        return $formatted_number;
    }
    

    /**
     * выводит месяц и год
     * @return date|year month and year
     * 
     * old function name get_my_dateyear
     */
    public static function getDateMY() 
    {
        return date("m.Y");
    }

    /**
     * выводит день месяц и год
     * @return day|month|year
     * 
     * old function get_my_datetoday()
     */
    public static function getDateDMY() 
    {
        return  date("d.m.Y");
    }
    

    /**
     * 
     */
    public static function log($var) 
    {
        echo "<pre>";
            print_r($var) ;
        echo "</pre>";
    }

    /**
     * Выводить json
     * @param array $arr 
     * @return json|null
     */
    public static function abort(array $arr)
    {
        echo json_encode($arr);
    }


    /**
     * 
     */
    public static function errorAbort(string $errText) 
    {
        return self::abort([
            'type' => 'error',
            'text' => $errText
        ]);
    }

    /**
     * 
     */
    public static function successAbort(string $text) 
    {
        return self::abort([
            'type' => 'success',
            'text' => $text
        ]);
    }    


    /**
     * 
     */
    public static function getPostPage()
    {
        return $_POST['page'] ?? false;
    }


    /**
     * 
     */
    public static function getPostType()
    {
        return $_POST['type'] ?? false;
    }    


    /**
     * 
     */
    public static function stringToInt($val)
    {
        return (int) str_replace(' ', '', $val);
    }

    /**
     * 
     */
    public static function stringToFloat($val)
    {
        return (float) str_replace(' ', '', $val);
    }


    /**
     * 
     */
    public static function generateTransactionId()
    {
        $new_sault = rand(000000,999999);
        $new_sault2 = rand(000000,555555);
    
        $transaction_id = $new_sault * $new_sault2 / 2;
    
        return (int) $transaction_id;        
    }


    
    /**
     * проверяем есть интернет у пользоваетля
     * 
     * @return boolean true/false
     */
    public static function hasConnetion() 
    {
        $client = new Client();

        $response = $client->request('GET', 'https://www.google.com/', [
            'headers' => [
                'Accept' => 'application/json', // Пример добавления заголовков, если это необходимо
            ]
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode == 200) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * получаем сегодняшний день недели 
     */
    public static function getCurrentWeek() 
    {
        $today_list = getdate();
        return $today_list['wday'];
    }    


    /**
     * Проверяем наличие папки если нет, то создаем папку
     * @param string $dir path
     */
    public static function checkAndCreateDir($dir) 
    {
        if(!file_exists($dir)) {
            mkdir($dir, 0700);
        }
        
        return true;
    }



    /**
     * time.windows.com - 180-200ms
     * time.google.com - 80-90ms
     * time.cloudflare.com - 8-25ms
     */
    public static function ntp($server = 'time.cloudflare.com', $port = 123) {
        $socket = @fsockopen("udp://$server", $port, $err_no, $err_str, 1);
        if (!$socket) return;
    
        fwrite($socket, chr(0x1b).str_repeat("\0",47));
    
        $packetReceived = fread($socket, 48);
    
        $unixTimestamp = unpack('N',$packetReceived, 40)[1] - 2208988800;
    
        $utcDate = date("d.m.Y",$unixTimestamp);

        return $utcDate;
    }




    /**
     * извлекаем архив и устанавливем обновление 
     * 
     * old function name unpack_update
    */
    public static function unpackZip($pathToZip, $pathToExtract, callable $callback) 
    {	
        $zip = new \ZipArchive;

        if($zip->open($pathToZip) === TRUE) {
            $zip->extractTo($pathToExtract);
            $zip->close();    

            return $callback(true);
        } 

        return $callback(false);
    }


    /**
    * @return boolean
    */
    public static function createZip($option) 
    {
        $zip = new \ZipArchive();
        
        if (!$zip->open($option['path_to_file'].$option['file_name'].'.zip', \ZIPARCHIVE::CREATE)) {
            return false;
        }

        $zip->addFile($option['path_to_file'].$option['file_name'], $option['file_name']);
        $zip->close();
    }    


    /**
     * 
     * 
     * 
     */
    public static function getTagsList() 
    {
        $default_data = [
            [
                'tags_id' => 'success',
                'class_list' => 'mark mark-tags mark-success-fill width-100 height-100',
            ],
            [
                'tags_id' => 'success-light',
                'class_list' => 'mark mark-tags mark-success width-100 height-100',
            ],
            [
                'tags_id' => 'danger',
                'class_list' => 'mark mark-tags mark-danger-fill width-100 height-100'
            ],
            [
                'tags_id' => 'danger-light',
                'class_list' => 'mark mark-tags mark-danger width-100 height-100'
            ],		
            [
                'tags_id' => 'rose',
                'class_list' => 'mark mark mark-tags mark-rose width-100 height-100'
            ],		
            [
                'tags_id' => 'warning', 
                'class_list' => 'mark mark-tags mark-warning width-100 height-100'
            ],
            [
                'tags_id' => 'primary',                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      
                'class_list' => 'mark mark-tags mark-primary width-100 height-100'
            ],
            [
                'tags_id' => 'black',
                'class_list' => 'mark mark-tags mark-black-fill width-100 height-100'
            ],
            [
                'tags_id' => 'blue',
                'class_list' => 'mark mark-tags mark-blue-fill width-100 height-100'
            ],
            [
                'tags_id' => 'gray',
                'class_list' => 'mark mark-tags mark-gray width-100 height-100'
            ]
        ];
            
        // если нужно вывести только теги 
        return $default_data;
    }    


    /**
     * 
     * old function name table_footer_result
     */
    public function compareTableFooterData($type_list, $data) 
    {
        $res = [];

        $rowData = [
            'stock_total_count' => [],
            'search_result' => count($data),
            'sum_stock_first_price' => [],
            'sum_profit' => 0,
            'sum_order_count' => 0,
            'sum_rasxod' => 0,
            'sum_sales' => 0,
            'sum_margin' => 0,
            'sum_card_money' => 0,

        ];


        foreach($data as $stock) {
            if(array_key_exists('stock_count', $stock)) {
    
                if($stock['stock_count'] > 0) {
                    $rowData['stock_total_count'][] = $stock['stock_count'];
                }
    
                if(array_key_exists('stock_first_price', $stock)) {
                    if($stock['stock_count'] > 0) {
                        $rowData['sum_stock_first_price'][] = $stock['stock_first_price'] * $stock['stock_count'];
                    }
                }
            }
    
            if(array_key_exists('order_total_profit', $stock)) {
                $rowData['sum_profit'] += $stock['order_total_profit'];
            }
    
            if(array_key_exists('order_stock_count', $stock)) {
                if($stock['order_stock_count'] > 0 ) {
                    $rowData['sum_order_count'] += $stock['order_stock_count'];
                } 
            }
    
            if(array_key_exists('rasxod_money', $stock)) {
                $rowData['sum_rasxod'] += $stock['rasxod_money'];
            }
    
            if(array_key_exists('order_stock_total_price', $stock)) {
                $rowData['sum_sales'] += $stock['order_stock_total_price'];
            }
    
            if(array_key_exists('payment_method', $stock)) {
                if($stock['payment_method'] == 2) {
                    $rowData['sum_card_money'] += $stock['order_stock_total_price'];
                }
            }
        }

        $rowData['stock_total_count'] = array_sum($rowData['stock_total_count']);
        $rowData['sum_stock_first_price'] = array_sum($rowData['sum_stock_first_price']);

        return $this->prepareTableFooterData($type_list, $rowData);
    }   
    
    /**
     * 
     */
    public function prepareTableFooterData($type_list, $rowData)
    {
        $res = [];
        
        foreach($type_list as $type) {
            //количество товара
            switch ($type) {
                case 'stock_count':
                     array_push($res, [
                        'title' => 'Ümumi say',
                        'value' => $this->prettyPrintNumber($rowData['stock_total_count']),
                        'class_list' => 'tf-res-stock-count',
                        'mark' 	=> [
                            'mark_text' => 'ədəd',
                            'mark_modify_class' => ''
                        ]
                    ]);
                    break;
                    case 'card_cash':
                        array_push($res, [
                           'title' => 'Kart',
                           'value' => $this->prettyPrintNumber($rowData['sum_card_money']),
                           'mark' 	=> [
                                'mark_modify_class' => 'mark-icon-manat button-icon-right manat-icon--black'	
                           ]
                       ]);
                       break;				
                case 'sum_total_sales':
                    array_push($res, [
                       'title' => 'Satış',
                       'value' => $this->prettyPrintNumber($rowData['sum_sales']),
                       'class_list' => 'tf-total-report',
                       'mark' 	=> [
                            'mark_modify_class' => 'mark-icon-manat button-icon-right manat-icon--black'			
                        ]
                   ]);
                   break;				
                case 'search_count': 
                    array_push($res, [
                        'title' => 'Tapıldı',
                        'value' => $this->prettyPrintNumber($rowData['search_result']),
                        'mark' 	=> [
                            'mark_text' => 'ədəd',
                            'mark_modify_class' => ''
                        ]
                    ]);
                    break;
                case 'sum_first_price': 
                    if(AccessManager::checkDataPremission('th_buy_price')) {
                        array_push($res, [
                            'title' => 'Malların ümumi dəyəri',
                            'class_list' => 'tf-res-stock-sum',
                            'value' => $this->prettyPrintNumber($rowData['sum_stock_first_price']),
                            'mark' 	=> [
                                'mark_modify_class' => 'mark-icon-manat button-icon-right manat-icon--black'			
                            ]
                        ]);
                    }
                    break;
                case 'sum_profit':
                    array_push($res, [
                        'title' => 'ümumi Mənfəət',
                        'value' => $this->prettyPrintNumber($rowData['sum_profit']),
                        'mark' 	=> [
                            'mark_modify_class' => 'mark-icon-manat button-icon-right manat-icon--black'			
                        ]
                    ]);
                    break;
                case 'stock_order_count':
                    array_push($res, [
                       'title' => 'Qaimə',
                       'value' => $this->prettyPrintNumber($rowData['sum_order_count']),
                       'mark' 	=> [
                           'mark_text' => 'ədəd',
                           'mark_modify_class' => ''
                       ]
                   ]);
                   break;
                   
                case 'sum_total_rasxod': 
                    array_push($res, [
                        'title' => 'ümumi xərc',
                        'value' => $this->prettyPrintNumber($rowData['sum_rasxod']),
                        'mark' => [
                            'mark_modify_class' => 'mark-icon-manat button-icon-right manat-icon--black'
                        ]
                    ]); 
                    break;
                case 'sum_margin': 
                    array_push($res, [
                        'title' => 'Xalis mənfəət',
                        'value' => $this->prettyPrintNumber( $rowData['sum_profit'] - $rowData['sum_rasxod']),
                        'mark' => [
                            'mark_modify_class' => 'mark-icon-manat button-icon-right manat-icon--black'
                        ]
                    ]); 
                    break;
                    case 'sum_total_sales_margin': 
                        array_push($res, [
                            'title' => 'Qaliq (kassa)',
                            'value' => $this->prettyPrintNumber( ($rowData['sum_sales'] - $rowData['sum_rasxod']) - $rowData['sum_card_money'] ),
                            'mark' => [
                                'mark_modify_class' => 'mark-icon-manat button-icon-right manat-icon--black'
                            ]
                        ]); 
                        break;				 
                    
            }
        }
    
        return $res;
    }


    /**
     * 
     */
    public function updateTableFooterData($data)
    {
        $init = new Init;
        
        $ControllerData = $init->getControllerData(Utils::getPostPage())->allData;

        $table_footer_list = $ControllerData['page_data_list']['table_total_list'];

        $resData = [];

        foreach($data as $index => $row) {
            //количество товара
            switch ($index) {
                case 'stock_count':
                        $oldCount           =  self::stringToInt($row['oldFooterCount'] ?? 0);
                        $oldProductCount    =  self::stringToInt($row['oldItemCount'] ?? 0);
                        $newProductCount    =  self::stringToInt($row['newItemCount'] ?? $oldProductCount);
    
                        $oldCount = $oldCount - $oldProductCount;
    
                        $resData['stock_total_count'] = $oldCount + $newProductCount;
                    break;
                case 'sum_first_price':
                    $oldFooterPrice     =  self::stringToFloat($row['oldFooterPrice']  ?? 0);
                    $oldProductPrice    =  self::stringToFloat($row['oldProductPrice'] ?? 0);
                    $newProductPrice    =  self::stringToFloat($row['newProductPrice'] ?? $oldProductPrice);   
                    $oldProductCount    =  self::stringToInt($row['oldProductCount'] ?? 0);   
                    $newProductCount    =  self::stringToInt($row['newProductCount'] ?? $oldProductCount); 

                    $footerPrice = $oldFooterPrice - ($oldProductPrice * $oldProductCount);

                    $resData['sum_stock_first_price'] = $footerPrice + ($newProductPrice * $newProductCount);
                    break;
                
                case 'sum_total_sales';
                break;
               
            }
        }

        return $this->prepareTableFooterData($table_footer_list, $resData);		
    }


    /**
     * 
     */
    public static function isCorrectLocalDate() 
    {
        $d = new \core\classes\dbWrapper\db;

        $data = $d::select([
            'table_name' => 'stock_order_report',
            'col_list' => '*',
            'query' => [
                'base_query' => '',
            ],
            'sort_by' => ' GROUP BY order_stock_id DESC ORDER BY order_stock_id DESC ',
            'limit' => 'limit 1'
        ]);
    
        $last_sale_date = $data[0]['order_date'];
        
        $local_date = (string) self::getDateDMY();
    
        if(strtotime($last_sale_date) > strtotime($local_date)) {
            return true;
        } else {
            return false;
        }
    }
    
}

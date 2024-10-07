<?php 
namespace Core\Classes\Privates;

use Core\Classes\Utils\Utils;
use core\classes\dbWrapper\db;
use Core\Classes\Privates\User;
use GuzzleHttp\Psr7\Uri;


/**
*	Роли: 
*		У каждого пользователя есть своя роль
*		Первая и основаная роль это - Администаротор (admin)
*		Втораая роль Старший продавец - admin_seller
*		Третья роль младший продавец - seller
*	
*	
*	Привилегии:
*		admin - по дефолту имеет доступ ко всем функциям
*			и страницам, ко всем данным, запретить что ли бо админу НЕ ВОЗМОЖНО.
*			- Админ может назначать роли пользователям
*			- Удалять и добавлять новых пользователей
*			- Редактировать их данные
*			- Ограничть просморт страниц для определенного юзера
*			- Ограничить просмотрт данных для опрделенного юзера
*			- Продавать товар
*			- Добавлять товар в базу/удалять товары из базы
*			- Смотреть отчет о продажах
*			- Делать возварты товаров
*			- Приоритетная роль (роль юзера чии привилегии выше) - нет  
*
*		admin_seller - по дефолту имеет доступ ко всем функциям
*			и страницам, ко всем данным. Admin может настроить права для этой категории пользователя
*			- Может смотерть данные юзеров, пароли и другую информацию
*			- не может редактировать данные пользователей
*			- не может добавлять новых юзеров и удалять юзеров
*			- Не может назначать роли пользователям
*			- может добавлять товар в базу
*			- может редактировать и удалять товары с базы
*			- может делать продажи товара
*			- может смотреть отчет продажи
*			- не может удалять или делать возврат продажи
*			- Приоритетная роль (роль юзера чии привилегии выше) - admin
*
*		seller - по дефолту имеет доступ ко всем страницам и данным таблицы
*			- Не может смотреть данные юзеров, редактировать информацию 
*			  добавлять новых юзеров и удалять
*			- Не может добавлять товар в склад
*			- Не может редактировать и удалять товар
*			- Не может удалить отчет о продажах  и сделать возврат
*			- Может сделать продажу товара
*			- Приоритетная роль (роль юзера чии привилегии выше) - admin, admin_seller
*
*						
*
*/
class accessManager extends user
{    

    /**
     * @return boolean 
     * true данных нет в базе и они НЕ запрещены
     * false данные есть в базе и они запрещены
     */
    public function isDataAvailable($dataName) 
    {
        //id пользователя из сессии
        $user_id = User::getCurrentUser('get_id');	

        $res = db::select([
            'table_name' => 'user_control',
            'col_list' => '*',
            'query' => [
                'base_query' => ' 
                    INNER JOIN access_data ON access_data.data_name = :dataName AND access_data.user_id = :user_id                 
                ',

                // 'sort_by' => 'GROUP BY data_td_accsess.td_id DESC '                            
            ],
            'bindList' => [
                ':dataName' => $dataName,
                ':user_id' => $user_id  
            ]
            ], ['bindType' => 'bindValue'])->get();

        return count($res) > 0 ? false : true;
    }

     
    /**
     * проверяем и выводим название таблицы
     * 
     * old function name  check_th_return_name
     */
    public static function checkDataPremission($dataName) 
    {
        $checkedData = self::isDataAvailable($dataName);

        if ($checkedData == false) {
            return false;
        }
        
        $restrictedData = self::getRestrictedDataList();

        foreach ($restrictedData as $key => $row) {
            if ($row['id'] == $dataName) {
                return $row['title'];
                break;       
            } 
        }
    }
    

    
    /**
     * Эта функция выводит список всех данных которые можно запретить
     * так же выставляет флаг "active" к данным которые уже запрещены к просмотру 
     */
    public static function getUserRestrictedDataList($userId)
    {
        $permissioList = self::getRestrictedDataList(false);

        $userAccessList = self::getForbiddenUserData($userId);

        foreach($permissioList as $key => $row) {
            $thId = $row['id'];

            foreach($userAccessList as $uKey => $uRow) {
                if($thId == $uRow['data_name']) {
                    $permissioList[$key]['active'] = true;
                }
            }
        }

        return $permissioList;
    }


    /**
     * 
     */
    public static function getForbiddenUserData($userId)
    {
        return db::select([
            'table_name' => 'access_data',
            'col_list' => '*',
            'query' => [
                'base_query' => ' WHERE user_id = :userId '
            ],
            'bindList' => [
                'userId' => $userId
            ],
        ])->get();        
    }


    /**
     * @return mixed array|boolean
     */
    public static function getRestrictedDataList($dataName = false)
    {
        $getRestrictedDataList = [
            [
                'id' => 'th_buy_price',
                'title' => 'Alış qiyməti',
            ],
            [
                'id' => 'th_admin_password',
                'title' => 'Şifrə'
            ],
            [
                'id' => 'th_count',
                'title' => 'Say',
            ],
            [
                'id' => 'th_profit',
                'title' => 'Mənfəət',
            ],
            [
                'id' => 'th_total_circ_money',
                'title' => 'Ümumi dövriyyə',
            ],
            [
                'id' => 'th_rasxod',
                'title' => 'Xarc',
            ],
        ];

        return $getRestrictedDataList;
    }


    /**
     * 
     */
    public static function setUserDataAccess($userId, $rulesId, callable $callback) 
    {
        $getUserPriviliges = user::getCurrentUser('get_role');

        if($getUserPriviliges == 'admin') {
            return db::insert('access_data', [
                [
                    'user_id' => $userId,
                    'data_name' => $rulesId
                ]
            ]);
        }

        return $callback([
            'type' => 'error',
            'text' => 'You dont have priviliges for set access'
        ]);
    }


    /**
     * 
     */
    public static function unsetUserDataAccess($userId, $rulesId, callable $callback) 
    {
        $getUserPriviliges = user::getCurrentUser('get_role');


        if($getUserPriviliges == 'admin') {
            return db::delete([
                [
                    'table_name' => 'access_data',
                    'where' => ' access_data.user_id = :userId AND access_data.data_name = :rulesId ',
                    'bindList' => [
                        'userId' => $userId,
                        'rulesId' => $rulesId
                    ]
                ]
            ]);
        }

        return $callback([
            'type' => 'error',
            'text' => 'You dont have priviliges for delete access'
        ]);
    }  


    /**
     * @return boolean true/false.
     * @description
     * if true - access denied
     * if false - free access
     */
    public static function accessActionRoute(string $route, callable $callback) 
    {
        $getSessionUserId = User::getCurrentUser('get_id');


        if($getSessionUserId == false) {
            return $callback(true);
        }


        $res = db::select([
            'table_name' => 'access_action',
            'col_list' => '*',
            'query' => [
                'base_query' => " WHERE action_name=:routeName AND user_id=:user_id ",
            ],
            'bindList' => [
                'user_id'   => $getSessionUserId,
                'routeName' => $route
            ]
            ], ['bindType' => 'bindValue'])->first()->get();
            
        return $callback($res);
    }


    /**
     *  @return array
     */
    public static function getRestrictedActionList() 
    {
        $actionRoutList = [
            [
                'id' => 'addUser',
                'title' => 'Yeni satıcılar əlavə etmək',
            ],
            [
                'id' => 'editUser',
                'title' => 'Satıcı məlumatlarını redaktə etmək'
            ],
            [
                'id' => 'deleteUser',
                'title' => 'Satıcıları silmək'
            ],
            [
                'id' => 'addProduct',
                'title' => 'Yeni məhsul əlavə etmək'
            ],
            [
                'id' => 'editProduct',
                'title' => 'Məhsulu redaktə etmək'
            ],
            [
                'id' => 'deleteProducts',
                'title' => 'Məhsulu silmək'
            ],
            [
                'id' => 'editReport',
                'title' => 'Hesabatı redaktə etmək'
            ],
            [
                'id' => 'deleteOrder',
                'title' => 'Hesabatı silmək'
            ],
            [
                'id' => 'addExpense',
                'title' => 'Yeni xərclər əlavə etmək'
            ],
            [
                'id' => 'editExpense',
                'title' => 'Xərcləri redaktə etmək'
            ],   
            [
                'id' => 'deleteExpense',
                'title' => 'Xərcləri silmək'
            ], 
            [
                'id' => 'addTransfer',
                'title' => 'Yeni məhsul transferi əlavə etmək'
            ],  
            [
                'id' => 'addArrivalProducts',
                'title' => 'Malların alınması (Alış fakturası)'
            ],              
            [
                'id' => 'addWriteOff',
                'title' => 'Malların silinməı (Silinmə fakturası)'
            ],  
            [
                'id' => 'addCategory',
                'title' => 'Yeni kateqoriya əlavə etmək'
            ],  
            [
                'id' => 'editCategory',
                'title' => 'Kateqoriyanı redaktə etmək'
            ],  
            [
                'id' => 'deleteCategory',
                'title' => 'Kateqoriyanı silmək'
            ],    
            [
                'id' => 'addProvider',
                'title' => 'Yeni təchizatçı əlavə etmək'
            ],  
            [
                'id' => 'editProvider',
                'title' => 'Təchizatçını redaktə etmək'
            ],  
            [
                'id' => 'deleteProvider',
                'title' => 'Təchizatçını silmək'
            ],                                                  
                                              

        ];

        return $actionRoutList;
    }


    /**
     * Эта функция выводит список всех данных которые можно запретить
     * так же выставляет флаг "active" к данным которые уже запрещены к просмотру 
     */
    public static function getUserRestrictedActionList($userId)
    {
        $permissioList = self::getRestrictedActionList(false);

        $userAccessList = self::getForbiddenUserAction($userId);

        foreach($permissioList as $key => $row) {
            $thId = $row['id'];

            foreach($userAccessList as $uKey => $uRow) {
                if($thId == $uRow['action_name']) {
                    $permissioList[$key]['active'] = true;
                }
            }
        }

        return $permissioList;
    }


    /**
     * 
     */
    public static function getForbiddenUserAction($userId)
    {
        return db::select([
            'table_name' => 'access_action',
            'col_list' => '*',
            'query' => [
                'base_query' => ' WHERE user_id = :userId '
            ],
            'bindList' => [
                'userId' => $userId
            ],
        ])->get();        
    }


    /**
     * 
     */
    public static function unsetUserActionAccess($userId, $rulesId, callable $callback) 
    {
        $getUserPriviliges = user::getCurrentUser('get_role');

        if($getUserPriviliges == 'admin') {
            return db::delete([
                [
                    'table_name' => 'access_action',
                    'where' => ' access_action.user_id = :userId AND access_action.action_name = :rulesId ',
                    'bindList' => [
                        'userId' => $userId,
                        'rulesId' => $rulesId
                    ]
                ]
            ]);
        }

        return $callback([
            'type' => 'error',
            'text' => 'You dont have priviliges for delete access'
        ]);
    }     

    /**
     * 
     */
    public static function setUserActionAccess($userId, $rulesId, callable $callback) 
    {
        $getUserPriviliges = user::getCurrentUser('get_role');

        if($getUserPriviliges == 'admin') {
            return db::insert('access_action', [
                [
                    'user_id' => $userId,
                    'action_name' => $rulesId
                ]
            ]);
        }

        return $callback([
            'type' => 'error',
            'text' => 'You dont have priviliges for set access'
        ]);
    }    

    
    /**
     * old function name access_request_uri
     * 
     * @param string $url - путь к странице  
     */
    public function checkPagePremission($url, $user_id) 
    {
        // //получаем роль пользователя
        // $user_role = $this->getCurrentUser('get_role');
        // // $user_id = $this->getCurrentUser('get_id');

        // //если не админ
        // if($user_role !== 'admin') {
        //     $base_uri = basename($url);

        //     $access_page = db::select([
        //         'table_name' => 'user_access_pages',
        //         'col_list' => '*',
        //         'query' => [
        //             'base_query' => " WHERE user_id = :user_id 
        //                               AND access_page_base_link = :base_link "
        //         ],
        //         'bindList' => [
        //             ':user_id' => $user_id,
        //             ':base_link' => $base_uri
        //         ] 
        //     ])->get();
            
        //     if(count($access_page) > 0) {
        //         echo "Xəta! <br> Bu əməliyyatı yerinə yetirmək üçün yetərli hüquqlarınız yoxdur.";
        //         return false;
        //     } else {
        //         return true;
        //     }
        // }
    
        return true;
    }


    /**
     * данные товаров с учетом правилами 
     *  
     * old function name query_clear_by_user_access
    */
    public function cleanseInaccessibleData($arr, $th) 
    {
        $query = $arr['query'];
        $access = $arr['access']['get_data'];
        // $th  = get_th_list();
        $res = [];
    
        foreach($access as $key => $val) {
        
            $th_this = $th[$key];
            $check_access = $th_this['is_title'];
    
            if($check_access == false) {
                foreach($query as $q_key => $q_val) {
                    unset($query[$q_key][$val]);
                }	
            } 
        }
        return $query;
    }
    
    //проверка достпа запросов
    function access_request_action($uri) {
        //получаем роль юзера сессии
        $user_role = $this->getCurrentUser('get_role');
        //получаем файл к которому был сделан запрос
        $uri = basename($uri);
        $access_list = [];
    
        //зпарещенные страница для seller 
        if($user_role === 'seller') {
            $access_list = array(
                'admin.php', 				//админ панель
                'add_user.php', 			//добавлять сотрудников
                'update_user.php', 			//изменять их ифу
                'add_stock.php', 			//добавлять товары
                'update_product.php', 		//изменять товары **old version
                'edit_product.php', 		//изменять товары
                'delete_products.php',		//удалчть товары 
                'return_report.php', 		//возврат товаров
                'delete_report.php', 		//удлаять отчет,
                'add_seller.php',
                'delete_seller.php',
                'edit_seller.php'	
            );
        }
    
    
    
        //зпарещенные страница для seller или admin_seller
        if($user_role === 'admin_seller') {
            $access_list = array(
                'add_user.php',
                'update_user.php'
            );
        }
    
        if($user_role === 'admin') {
            $access_list = array(
                //all rules available
            );
        }
    
        if(in_array($uri, $access_list)) {
            echo  json_encode(array(
                'error' => "Xəta! <br> Bu əməliyyatı yerinə yetirmək üçün yetərli hüquqlarınız yoxdur.", 
            ));
            exit();
            die();
        }
    }
    

    
    
    // проверка на доступ юзера к таблице возвращает переданнй арумент
    function is_check_data_premission($data, $value) {
        global $dbpdo;
    
        //id пользователя из сессии
        $user_id = $this->getCurrentUser('get_id');	
    
        //находим в базе и выводим всю нужную инфорацию по заголовку
        $get_header_info = $dbpdo->prepare('SELECT * FROM user_control
            INNER JOIN th_list ON th_list.th_description = :th_value
            INNER JOIN data_td_accsess ON data_td_accsess.td_tags_id = th_list.th_id AND data_td_accsess.user_id = :user_id 
            ');
        $get_header_info->bindValue('th_value', $data);
        $get_header_info->bindParam('user_id', $user_id);
        $get_header_info->execute();
    
        //если id имееться в базе то ничего не выводим; иначе вызываем функцию , которую передали (см.выше) 
        if($get_header_info->rowCount()>0) {
            return false;
        } else {
            return $value;
        }	
    }
    

    
    
    
    // проверка на доступ юзера к таблице
    function check_access_right($arr) {
        /**example 
        *
        *	check_access_right(array(
        *		'th_var' => 'th_count',
        *		'function' => 'render_td',
        *		'modify_class' => '',
        *		'table_data' => array(
        *			'td_value' 			=> $value,
        *			'td_parent_class'	=> 'table_stock',
        *			'td_link_class' 	=> 'stock_info_link_block',
        *			'td_mark_text'		=> ''
        *		)
        *	));	
        */
    
    
    
        global $dbpdo;
        //выводим кастомную функцию, которую передаем массивом, пример: $function = $arr->function; потом: return $function($args);
        $function = $arr['function'];
        //класс модификатор
        $modify_class = $arr['modify_class'];
        $th_description = $arr['th_var'];
    
        //table data
        $td = 'default';
        //данные для генерации табицы
        if(array_key_exists('table_data', $arr)) {
            $td = $arr['table_data'];
        }
    
        //id пользователя из сессии
        $user_id = $this->getCurrentUser('get_id');	
    
    
    
        //находим в базе и выводим всю нужную инфорацию по заголовку
        $get_header_info = $dbpdo->prepare('SELECT * FROM th_list WHERE th_description = :th_value');
        $get_header_info->bindValue('th_value', $th_description);
        $get_header_info->execute();
        $row_th_info = $get_header_info->fetch(\PDO::FETCH_LAZY);
    
        $th_info_id = $row_th_info->th_id;
        $th_info_name = $row_th_info->th_name;
        $th_info_description = $row_th_info->th_description;
    
    
        //проверяем в табице запретов на наичие id
        $get_right = $dbpdo->prepare('SELECT * FROM data_td_accsess WHERE td_tags_id = :access_th_id AND user_id = :user_id ');
        $get_right->bindParam('access_th_id', $th_info_id);
        $get_right->bindParam('user_id', $user_id);
        $get_right->execute();
    
        $row = $get_right->fetch(\PDO::FETCH_LAZY);
        //если id имееться в базе то ничего не выводим; иначе вызываем функцию , которую передали (см.выше) 
        if($get_right->rowCount()>0) {
            //nohing to do
        } else {
            //render html
            //если такая функция есть
            if(function_exists($function)) {
                $function(array('modify_class' => $modify_class, 'th_name' => $th_info_name, 'td' => $td));
            }
        }
    }
    
    
    
    //записуем доступ для пользователя  
    function add_user_access_rights($rights) {
        /** $action - запрос 
        * $data - массив с правами досиупа к данным таблицы для пользователя 
        * telplate
        *
        *	add_user_access_rights(array(
        *		'action' => 'page_access/data_access',
        *		'data' => $arr2,
        *		'user_id' => N
        *	));		
        **/
        global $dbpdo;
    
        //запрос для выполнения
        $action = $rights['action'];
        //данные для записи
        $data = $rights['data'];
        //id - пользователя
        $user_id = $rights['user_id'];
    
    
    
        if($action == 'data_access') {
            foreach ($data as $access) {
                $insert_access = $dbpdo->prepare('INSERT INTO data_td_accsess (td_id, user_id, td_tags_id, accsess_status) 
                    VALUES (NULL, :user_id, :access_param, 0) ');
                $insert_access->bindParam('user_id', $user_id);
                $insert_access->bindParam('access_param', $access);
                $insert_access->execute();
            }
        }
    
        if($action == 'page_access') {
            foreach ($data as $paccess) {
                $base_link = basename($paccess);
                $insert_access_page = $dbpdo->prepare('INSERT INTO user_access_pages 
                    (access_id, user_id, access_page_name, access_page_base_link) 
                    VALUES (NULL, :user_id, :full_page_link, :base_page_link) ');
                $insert_access_page->bindParam('user_id', $user_id);
                $insert_access_page->bindParam('full_page_link', $paccess);
                $insert_access_page->bindParam('base_page_link', $base_link);
                $insert_access_page->execute();			
            }
        }	
    }
    
    //Сбрасываем доступы пользователя
    function reset_user_all_access($arr) {
        global $dbpdo;
        /**
        *example 
        *	$arr = array(
        *		'id' => user id ,
        *		'action' => data/pageaccess,
        *		'action_state' => true 
        *	);
        */
        $id = $arr['id'];
        $action = $arr['action'];
        $action_state = $arr['action_state'];
    
        if($action_state == true) {
            if($action == 'data_access') {
                //DELTE data ACCESS
                $delete_data = $dbpdo->prepare('DELETE FROM data_td_accsess WHERE user_id = :u_id');
                $delete_data->bindParam('u_id', $id, \PDO::PARAM_INT);
                $delete_data->execute();
            }
    
            if($action === 'page_access') {
                //delete page access
                $delete_page = $dbpdo->prepare('DELETE FROM user_access_pages WHERE user_id = :u_id');
                $delete_page->bindParam('u_id', $id, \PDO::PARAM_INT);
                $delete_page->execute();	
            }
        }
    }
    //данные которы можно запретить в админке
    function page_data_access_list() {
        $data = array(
            'th_buy_price',
            'th_profit',
            'th_total_circ_money',
            'th_report_note',
            'th_rasxod',
            'th_provider'
        );
    
        return $data;
    }
    
    //получаемп список правил пользователя для редкатирования
    function get_user_access_list($var) {
        global $dbpdo;
        $var = (object) $var;
    
        /**example
        *	$var = array(
        *		'action'   => 'action'	 
        *		'data_arr' => $menu_query,
        *		'user_id'  => $user_id,
        *	);
        **/
    
        $data 		  = [];
        $active_link  = '';
        $user_id 	  = $var->user_id;
        $menu_query   = $var->data_arr;
        $action 	  = $var->action;
    
        switch ($action) {
            case 'access_page':
                foreach ($menu_query as $row) {
                    $title 		= $row['title'];
                    $link 		= $row['link'];
                    $base_link  = basename($link);
    
                    $access_page_list = $dbpdo->prepare('SELECT * FROM user_access_pages 
                                                         WHERE user_id = :user_id 
                                                         AND access_page_base_link = :base_link');
                    $access_page_list->bindParam('user_id', $user_id);
                    $access_page_list->bindParam('base_link', $base_link);
                    $access_page_list->execute();
    
                    if($access_page_list->rowCount()>0) {
                        $active_link = 'filter-active';
                    } else {
                        $active_link = '';
                    }
    
                    $menu_compare = array(
                        'title' 		=>  $title,
                        'value'			=>  $link,
                        'icon'			=>  '<img src="/assets/img/icon/lock-white.png">',
                        'id'			=>  '',
                        'text'			=>  '',
                        'modify_class' 	=>  $active_link,
                        'parent_class' 	=>  ''
                    );
    
                    get_access_page_list_tpl($menu_compare);
                }
            break;
    
            case 'data_td_accsess': 
                $get_all_user_th_access_lsit = $dbpdo->prepare('SELECT * FROM data_td_accsess WHERE user_id = :user_id');
                $get_all_user_th_access_lsit->bindParam('user_id', $user_id);
                $get_all_user_th_access_lsit->execute();
                if($get_all_user_th_access_lsit->rowCount()>0) {
                    while ($data_row = $get_all_user_th_access_lsit->fetch()) {
                        $data[] += $data_row['td_tags_id'];
                    }
                }
    
    
                $param_list = page_data_access_list();
    
                foreach ($param_list as $param ) {
                    $get_th = $dbpdo->prepare('SELECT * FROM th_list WHERE th_description = :param');
                    $get_th->bindParam('param', $param);
                    $get_th->execute();
                    $th_row = $get_th->fetch();
    
                    if($get_th->rowCount()>0) {
                        $title = $th_row['th_name'];
                        $value = $th_row['th_description'];
                        $id    = $th_row['th_id'];
    
                        if(array_keys($data, $id)) {
                            $data_active = 'filter-active';
                        } else {
                            $data_active = '';
                        }
    
                        $menu_compare = array(
                            'title' 		=>  $title,
                            'value'			=>  $id,
                            'id'			=> 	$id,
                            'icon'			=> '<img src="/assets/img/icon/lock-white.png">',
                            'text'			=>	'',
                            'modify_class' 	=> $data_active,
                            'parent_class' 	=> ''
                        );
    
                        get_access_page_list_tpl($menu_compare);
                    }
                }
            break;
    
        }
    
    }
    
    
    
    

    
    
    //массив в котором мы связываем поля в базе с ключами которые можно запретить и с
    function sanitze_user_data($data) {
    
    }
    
    function ls_generate_transaction_id() {
        $new_sault = rand(000000,999999);
        $new_sault2 = rand(000000,555555);
    
        $transaction_id = $new_sault * $new_sault2 / 2;
    
        return (int) $transaction_id;
    }    

}
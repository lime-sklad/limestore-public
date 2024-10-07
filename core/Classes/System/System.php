<?php
namespace Core\Classes\System;

use core\classes\dbWrapper\db;
use Core\Classes\Utils\Utils;

class System 
{
    /** 
     * открываем сессию и авторизуем пользователя
     * @param string $login
     * @param string $pass
    */
    public function auth($login, $password) 
    {
        $userData = db::select([
            'table_name' => 'user_control',
            'col_list' => '*',
            'query' => [
                'base_query' => '',
                'body' => ' WHERE `user_name` = :login ',
                'joins' => '',
                'sort_by' => ' LIMIT 1'
            ],
            'bindList' => array(
                'login' => $login
            )
        ])->first()->get();

        if(!empty($userData)) {
            if($userData['user_password'] == $password) {
                self::setUserSession($userData['user_id']);
            
                echo Utils::abort([
                    'success' => 'ok'
                ]);

                return true;
            } 	
        } 

        echo Utils::abort([
            'error' => 'Логин или пароль не правильный'
        ]);        
    }

    /**
     * 
     */
    public static function logout(callable $callback)
    {
        self::unsetUserSession();
        $callback(self::hasUserSession());
    }


    /**
     * @return boolean  true if session active 
     * @return boolean  false if session in not active
     */
    public static function hasUserSession()
    {
        if(isset($_SESSION['user'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * unset session
     * @return boolean
     */
    public static function unsetUserSession()
    {
        session_destroy();
        session_write_close();        
        unset($_SESSION['user']);
    }

    /**
     * 
     */
    public static function setUserSession($id)
    {
        $_SESSION['user'] = $id;
        $_SESSION['time_start_login'] = time();        
    }

  


    public static function loadAssets(array $assetsList = null) 
    {
        $assets = [
            'css' => [
                'fonts' => 'assets/css/fonts.css',
                'styleVar' => 'assets/css/style_var.css',
                'template' => 'assets/css/template.css',
                'animate' => 'assets/css/animate.min.css',
                'lineAwesome' => 'assets/lib/css_lib/line-awesome/css/line-awesome.min.css',
                'mainStyle' => 'assets/css/new.style.css',
                'responsive' => 'assets/css/responsive.css',
                'darktheme' => 'assets/css/dark.theme.css',
                // '' => 'assets/css/network-status.css',
             ],
            'script' => [
                'jquery' => 'assets/lib/js_lib/jquery-3.7.1.min.js',
                'jqueryPos' => 'assets/lib/js_lib/jquery.pos.js',
                'hotkey' => 'assets/js/utils/hotkey.js',
                'function' => 'assets/js/upd.function.js',
                'ajax' => 'assets/js/upd.ajax.js',
                
                
                'stockFunction' => 'assets/js/main/stock.function.js',
                'reportFunction' => 'assets/js/main/report.function.js',
                'cart' => 'assets/js/main/cart.js',
                'arrivalFunction' => 'assets/js/main/arrival.function.js',
                'writeOffFunction' => 'assets/js/main/write-off.function.js',
                'transferFunction' => 'assets/js/main/transfer.function.js',

                'utils' => 'assets/js/utils/utils.js',
                'stories'  => 'assets/js/utils/stories.js',
                
                'dark' => 'assets/js/utils/dark-theme.js',
                'xlsConvert' => 'assets/lib/xlsx-convert/xlsx.full.min.js',
                'chart' => 'assets/lib/chart/chart.min.js'
                // 'assets/js/utils/network-status.js',
            ]
        ];


        if($assetsList) {  
            foreach($assets as $type => $path) {
                $assets[$type] = array_intersect_key($path, array_flip($assetsList[$type]));
            }
        }

        return $assets;
    }

}
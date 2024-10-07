<?php
namespace Core\Classes\System;
use core\classes\dbWrapper\db;
use Core\Classes\Utils\Utils;
use GuzzleHttp\Client;

class Updater 
{

    /**
     * день недели когда проверять обновление
     * 1 - это понедельник
     * 2 - это вторник 
     * 4 - это четверг
     * и тд
     */        
    public static $UPDATE_CHECK_DAY_OF_WEEK_INDEX;

    // папка куда сохраняем бэкап
    public static $GET_PATH_TO_BACKUP_FOLDER;

    // папка куда сохраняется скачанный архив с обновлением 
    public static $GET_PATH_TO_TEMP_UPDATE_FOLDER;

    // путь к файлу с текущей версией 
    public static $GET_PATH_TO_LOCAL_VERSION_FILE;

    // ссылка на версию на сервере
    public static $GET_PATH_TO_SERVER_VERSION_FILE;

    // архив с обновлением
    public static $GET_PATH_TO_DOWNLOADED_UPDATE_FILE;


    public function __construct()
    { 
        self::$UPDATE_CHECK_DAY_OF_WEEK_INDEX       =  5;

        self::$GET_PATH_TO_BACKUP_FOLDER            =  $_SERVER['DOCUMENT_ROOT'].'/backup/';
        
        self::$GET_PATH_TO_TEMP_UPDATE_FOLDER       = $_SERVER['DOCUMENT_ROOT'].'/update/';
        
        self::$GET_PATH_TO_LOCAL_VERSION_FILE       = $_SERVER['DOCUMENT_ROOT'].'/version.txt';

        self::$GET_PATH_TO_SERVER_VERSION_FILE      = 'https://raw.githubusercontent.com/lime-sklad/update_limestore/master/version.txt';

        self::$GET_PATH_TO_DOWNLOADED_UPDATE_FILE   = 'https://github.com/lime-sklad/update_limestore/raw/master/testupdate.zip';
    }



    /**
     * получаем текушюю версию
     * @return int version
    **/
    public static function getCurrentVersion() 
    {
        return file_get_contents(self::$GET_PATH_TO_LOCAL_VERSION_FILE);
    }



    
    /**
     * проверяем есть ли обновление
     * @return false/true
     * 
     * old function name is_check_new_version
     */
    public static function hasNewVersion() {

        // если есть инетренет и в базе есть увидомление об обновлении
        if(self::hasUpdateNotify()) {
            return true;
        } 

        /**
         * если есть инетренет но в базе нет увидомления об обновлении
         * делаем запрос и проверяем обновление
         */
        else if (Utils::hasConnetion()) {
            $checked_version = self::getLastVersion();
            $current_version = self::getCurrentVersion();
        
            if($checked_version && $current_version) {
                return self::checkVersionForUpdates($current_version, $checked_version);
            }
        
            return false; 
        }

        return false;
    }



    /**
     * проверяем есть ли обновление (ПРОВЕРКА ПО ОПРЕДЕЛЕННОМУ ДНЮ НЕДЕЛИ)
     * @return false/true
     */
    public static function isCheckUpdate() 
    {

        // если есть инетренет и в базе есть увидомление об обновлении
        if((self::hasUpdateNotify())) {
            return true;
        } 

        /**
         * если есть инетренет но в базе нет увидомления об обновлении
         * делаем запрос и проверяем обновление
         */
        else if (Utils::hasConnetion() && self::hasUpdateNotify() == false) {
            if(Utils::getCurrentWeek() == self::$UPDATE_CHECK_DAY_OF_WEEK_INDEX) {
                $checked_version = self::getLastVersion();
                $current_version = self::getCurrentVersion();
        
                if($checked_version && $current_version) {
                    return self::checkVersionForUpdates($current_version, $checked_version);
                }
        
                return false;
            }        
        }

        return false;
    }



    /**
     * получаем версию
     */
    public static function getLastVersion() 
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$GET_PATH_TO_SERVER_VERSION_FILE);
        curl_setopt($ch, CURLPROTO_HTTPS,1);	
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);// таймаут в секундах
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 Safari/537.36 Edge/17.17134');

        $get_verison = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if($info['http_code'] == '200') {
            return trim($get_verison);
        } 
        
        return false;
    }




    /**
     * проверяет есть ли обновление сравнивая верссиии
     * @param int $currentVersion
     * @param int $serverVersion
     * @return boolean true/false
     * 
     * old function name is_new_version
     */
    public static function checkVersionForUpdates($current_version, $serverVersion) 
    {
        if($current_version !== $serverVersion) {
            self::appendUpdateNotify();
            return true;
        } 

        return false;
    }


    /**
     * добавляем увидобление в базу об обновлении
     * 
     * old function name insert_update_notify 
     */
    public static function appendUpdateNotify() 
    {
        return db::insert('ls_notify', [
            array(
                'notify_text' => 'new update' . date('d.m.Y'),
                'notify_type' => 'update'            
            )
        ]);
    }



    /**
     * получаем с базы данных увидомление об обновлении
     * 
     * old function name is_check_update_notify
     */
    public static function hasUpdateNotify() 
    {
        $res = db::select([
            'table_name' => 'ls_notify',
            'col_list' => '*',
            'query' => [
                'base_query' => ' WHERE notify_type = :notfy_type ',
                'body' => '',
                'joins' => '',
                'sort_by' => ' ORDER BY notify_id DESC'
            ],
            'bindList' => [
                ':notfy_type' => 'update'
            ]        

        ])->get();

        // в базе увидомление есть
        if(count($res) > 0) {
            return true;
        } 

        return false;
    }



    /**
     * Обновляем
     * 
     * old function name ls_update
     */
    public static function setupUpdate(callable $setUpCallable) 
    {
        if(Utils::hasConnetion()) {
            // проверяем наличие папки backup
            Utils::checkAndCreateDir(self::$GET_PATH_TO_BACKUP_FOLDER);
            // проверяем наличие папки update
            Utils::checkAndCreateDir(self::$GET_PATH_TO_TEMP_UPDATE_FOLDER);
        
            //скачиваем обновление            
            self::downloadUpdateFile(function($success) use ($setUpCallable) {
                if($success) {
                    // устанавливаем обновление
                    Utils::unpackZip(self::$GET_PATH_TO_TEMP_UPDATE_FOLDER.'update.zip', $_SERVER['DOCUMENT_ROOT'], function($already) use ($setUpCallable) {
                        if($already) {
                            self::resetUpdateNotify();
                            self::changeLocalVersion();
                            // выводим сообщение успех
                            return $setUpCallable(true);
                        }
                    });
                } else {
                    return $setUpCallable(false);
                }
                
            });

            return;
        } 

        return $setUpCallable(false);
    }



    
    /**
     * скачиваем обновление
     * 
     * old function name download_update_file
     */
    public static function downloadUpdateFile(callable $callback) {
        $client = new Client();

        $zipFile = self::$GET_PATH_TO_TEMP_UPDATE_FOLDER . "update.zip";

        $response = $client->request('GET', self::$GET_PATH_TO_DOWNLOADED_UPDATE_FILE, [
            'sink' => $zipFile,
            'timeout' => 120,
            'verify' => false // Отключаем проверку SSL, если требуется
        ]);
        
        // Проверка успешности запроса
        if ($response->getStatusCode() === 200) {
            return $callback(true);		
        } 
    
        return $callback(false);
    }


    /**
     * 
     * old function name reset_update_notify
     */
    public static function resetUpdateNotify() 
    {
        return db::delete([
            array(
                'table_name' => 'ls_notify',
                'where' => ' notify_type = :not_type ',
                'bindList' => [
                    ':not_type' => 'update'
                ]
            )
        ]);
    }


    /**
     * Изменить лоакльную версию программы
     */
    public static function changeLocalVersion()
    {
        file_put_contents(self::$GET_PATH_TO_LOCAL_VERSION_FILE, trim(self::getLastVersion()));	
    }
    
// все что ниже устаревшее
//-----------------------------------------------------------------------------------


/**
 * делаем бэкап
 */
function make_backup() {
  $update_config = update_config();
  $source = $_SERVER['DOCUMENT_ROOT'];
  $destination = $update_config['get_backup_dir'].'backup_'.date("d.m.Y").'.zip';
  // sql bakcup
  include $_SERVER['DOCUMENT_ROOT'].'/core/function/db.dump.php';

  if (!extension_loaded('zip') || !file_exists($source)) {
    return false;
  }
 
  $zip = new \ZipArchive();
  if (!$zip->open($destination, \ZIPARCHIVE::CREATE)) {
    return false;
  }
 
  $source = str_replace('\\', '/', realpath($source));
 
  if (is_dir($source) === true) {
    $files[] = $_SERVER['REQUEST_URI'];

    $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);
 
    foreach ($files as $file){
        $file = str_replace('\\', '/', $file);
 		$strip_folders = array(
            'backup', 
            'ajax_page_php', 
            'akssesuar', 
            'update.zip', 
            'charts', 
            'backup.zip', 
            'charts',
            '.git',
            '.vscode',
            'oldfiles',
            'test',
            '.gitignore',
            '1.diff',
            '1.txt',
            'test.php',
            'test2.php'
        );
        
 		$file = str_replace($strip_folders, ' ', $file);
        // Ignore "." and ".." folders
        if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
            continue;
        $file = realpath($file);
        $file = str_replace('\\', '/', $file);
         
        if (is_dir($file) === true){
            $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
        }else if (is_file($file) === true){
            $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
        }
    }
  } else if (is_file($source) === true){
    $zip->addFromString(basename($source), file_get_contents($source));
  }
  return $zip->close();
}









}
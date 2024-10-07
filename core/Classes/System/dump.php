<?php
namespace Core\Classes\System;
use Core\Classes\Utils\Utils;
use core\classes\dbWrapper\db;

class dump
{
    //общяя папка с бэкапами
    public static $PATH_TO_BACKUP_DIR;

    // папка для бэкапа базы данных
    public static $PATH_TO_BACKUP_DATABASE_DIR;

    // автоматическое название бэкапа
    public static $BACKUP_DATABASE_NAME;

    public static $PATH_TO_MYSQL_FILE;

    public function __construct()
    {
        self::$PATH_TO_BACKUP_DIR = $_SERVER['DOCUMENT_ROOT'] . '/backup/';
        
        self::$PATH_TO_BACKUP_DATABASE_DIR = $_SERVER['DOCUMENT_ROOT'] . '/backup/db/';
        
        self::$BACKUP_DATABASE_NAME = 'db'.date('-(d.m.Y)');
        
        self::$PATH_TO_MYSQL_FILE = dirname($_SERVER['DOCUMENT_ROOT']) . '/mysql/bin/mysqldump';       
    }


    /**
     * old function ls_db_dump 
     */
    public static function dumpDatabase() 
    {
        Utils::checkAndCreateDir(self::$PATH_TO_BACKUP_DIR);
        Utils::checkAndCreateDir(self::$PATH_TO_BACKUP_DATABASE_DIR);
               
        $save_to = self::$PATH_TO_BACKUP_DATABASE_DIR . self::$BACKUP_DATABASE_NAME . '.sql';
        
        if(exec(self::$PATH_TO_MYSQL_FILE." --user=".DBUSER." --password=".DBPASS." --host=".DBHOST." ".DBNAME." --result-file={$save_to} 2>&1", $output)) {
            return true;
        }
      
    }


/**
 * @deprecated
 */
 function is_db_backup_exists($db_dump_name, $db_dump_file_path) {
    $dump_name = $db_dump_file_path . $db_dump_name . '.sql';

    if(file_exists($dump_name)) {
        return true;
    } else {
        return false;
    }
 }   
}
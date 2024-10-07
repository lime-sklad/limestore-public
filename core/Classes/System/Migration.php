<?php 

namespace Core\Classes\System;
use core\classes\dbWrapper\db;
use Core\Classes\Utils\Utils;

class Migration
{
    /**
     * @param string $tableName название таблицы
     * @param callable $callback функция обратного вызова
     * @return boolean результат выполнения callback функции
     * @return boolean если таблица сушествует то выводится false иначе true
     */
    public static function hasTableExist($tableName, callable $callback) 
    {
        $check_db = db::$dbpdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = :database AND table_name = :table');
        $check_db->execute([
            'database' => db::$dbpdo->query('select database()')->fetchColumn(),
            'table' => $tableName
        ]);
        
       // Если таблица существует, то выводим false иначе true
       return $check_db->fetchColumn() > 0 ? $callback(false) : $callback($tableName);
    }


    /**
     * $columnList = [
     *  column name => column param,
     *  'vaga' => 'varchar(255) NOT NULL',
     *  'freeze' => 'varchar(255) NOT NULL',
     * ];
     */
    public static function hasTableColumnExist(string $tableName, array $columnList, callable $callback) 
    {
        foreach ($columnList as $col_name => $col_param) {
            $col_check = db::$dbpdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = :database AND table_name = :table_name AND column_name = :column_name');
            $col_check->execute([
                'database' => db::$dbpdo->query('select database()')->fetchColumn(),
                'table_name' => $tableName,
                'column_name' => $col_name
            ]);				
         
            $col_check->fetchColumn() > 0 ? $callback(false) 
            : $callback([
                'colName' => $col_name, 
                'colParam' => $col_param
            ]);
        }        
    }
    
    /**
     * Проверяем есть ли данные в базе
     * 
     * Это массив с данными которые нужно проверить и добавть в базу данных
     * Если проверка будет по определенным столбцам, то в запросе бинди например :title
     * Потом в этом массиве нужному ключу задем это же значение :title
     *  @param data = array(
     *       array(':title' => 'Kardfsdt', 'freeze' => 0, 'col_name2' => 0, 'col_name3' => 'danger'),
     *  );
     * 
     * 
     * Тут пишем сам запрос
     * Обьязательно bindList опеределяем даже если он пустой
     * Если в условии нужно дополнительно проверить по столбцу, то значение которое мы подготовим..
     * например AND freeze = :freeze - :frezee нужно написать ключом к значению в массиве $data который мы описали выше
     *  @param sql = [
     *       'table_name' => 'payment_method_list',
     *       'col_list' => '*',
     *       'query' => [
     *           'base_query' => ' WHERE title = :title GROUP BY id DESC'
     *       ],
     *       'bindList' => []
     *  ];
     * 
     * 
     * @return array callback мы получаем дынне которых нет в базе, отсеяв тем самым те, которые были 
     */
    public static function hasDataExist($sql, $data, callable $callback)
    {
        /**
         * Сначало перебираем основной массив в котором находятся данные
         * $index - это порядковы номерр вложенного мссива
         */
        foreach ($data as $index => $val) {
            /**
             * перебираем данные
             * $key - это ключ 
             * $row - это значение
             */
            foreach ($val as $key => $row) {
                // если ключ задан через :key
                if (substr($key, 0, 1) == ':') {
                    //биндим для запроса
                    $sql['bindList'][$key] = $row;

                    $cleanKey = str_replace(':', '', $key);

                    $data[$index][$cleanKey] = $row;

                    unset($data[$index][$key]);
                }
            }

            // делаем выборку
            $result = db::select($sql)->get();

            //выводим только те которых в базе нет
            if (count($result) <= 0) {
                $callback($data[$index]);
            }
        }
    }


    /**
     * @param string $tableName название таблицы
     * @param array $data массив с данными
     * 
     */
    public static function insertMigrationData($tableName, $data)
    {
        return db::insert($tableName, [
            $data
        ]);
    }



    /**
     * 
     */
    public static function createTable($sql) 
    {
        return db::$dbpdo->exec($sql);
    }

    /**
     * 
     */
    public static function alertTableColumn($tableName, $data) 
    {
        $columnName = $data['colName'];
        $columnParam = $data['colParam'];
    
        $insert_col_sql = "ALTER TABLE $tableName  ADD $columnName $columnParam ";
        db::$dbpdo->exec($insert_col_sql);        
    }


    /**
     * 
     */
    public static function deleteTable($tableName)
    {
        db::$dbpdo->exec("DROP TABLE $tableName");
    }


    /**
     * 
     */
    public static function renameTable($oldTableName, $newTableName)
    {
        db::$dbpdo->exec("RENAME TABLE $oldTableName TO $newTableName");
    }
}
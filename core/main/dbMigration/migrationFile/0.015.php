<?php 
use Core\Classes\System\Migration;
use core\classes\dbWrapper\db;

// тут мигрируем payment_method_list
Migration::hasTableExist('payment_method_list', function($noExist) {
    if($noExist) {
        Migration::createTable(
            'CREATE TABLE `payment_method_list` (
                `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `title` varchar(255) NOT NULL,
                `freeze` int(11) NOT NULL,
                `tags_id` varchar(64) NOT NULL,
                `visible` int(11) NOT NULL
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
        );
    }
});

$sql = [
    'table_name' => 'payment_method_list',
    'col_list' => '*',
    'query' => [
        'base_query' => ' WHERE title = :title ORDER BY id DESC '
    ],
    'bindList' => []
];

$data = [
	array(':title' => 'Nağd', 'visible' => 0, 'freeze' => 1, 'tags_id' => 'success'),
	array(':title' => 'Kart', 'visible' => 0, 'freeze' => 0, 'tags_id' => 'danger')
];

Migration::hasDataExist($sql, $data, function($notExist) {
    if($notExist) {
        Migration::insertMigrationData('payment_method_list', $notExist);
    }
});







/***************************th_list**************************************/


/** function_settings */

$fsql = [
    'table_name' => 'function_settting',
    'col_list' => '*',
    'query' => [
        'base_query' => ' WHERE sett_name = :sett_name ORDER BY sett_id DESC '
    ],
    'bindList' => []
];

$fdata = [
	array(':sett_name' => 'telegram_backup', 'sett_on' => 1),
];

Migration::hasDataExist($fsql, $fdata, function($notExist) {
    if($notExist) {
        Migration::insertMigrationData('function_settting', $notExist);
    }
});



// созданем новую таблицу
// Migration::hasTableExist('access_page_route', function($noExist) {
//     if($noExist) {
//         Migration::createTable(
//             'CREATE TABLE `access_page_route` (
//                 `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
//                 `route_name` varchar(255) NOT NULL,
//                 `user_id` int(11) NOT NULL,
//                 `description` varchar(64) NOT NULL,
//                 `visible` int(11) NOT NULL
//               ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
//         );
//     }
// });



// тут мигрируем payment_method_list
Migration::hasTableExist('access_action', function($noExist) {
    if($noExist) {
        Migration::createTable(
            'CREATE TABLE `access_action` (
                `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `action_name` varchar(255) NOT NULL,
                `user_id` int(11) NOT NULL,
                `description` varchar(64) NOT NULL,
                `visible` int(11) NOT NULL
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
        );
    }
});


// тут мигрируем payment_method_list
Migration::hasTableExist('access_data', function($noExist) {
    if($noExist) {
        Migration::createTable(
            'CREATE TABLE `access_data` (
                `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `data_name` varchar(255) NOT NULL,
                `user_id` int(11) NOT NULL,
                `description` varchar(64) NOT NULL,
                `visible` int(11) NOT NULL
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
        );
    }
});


Migration::hasTableExist('cart', function($noExist) {
    if($noExist == false) {
        Migration::deleteTable('cart');
    }
});

Migration::hasTableExist('customer', function($noExist) {
    if($noExist == false) {
        Migration::deleteTable('customer');
    }
});

Migration::hasTableExist('customer_basket', function($noExist) {
    if($noExist == false) {
        Migration::deleteTable('customer_basket');
    }
});

Migration::hasTableExist('debt_repayment', function($noExist) {
    if($noExist == false) {
        Migration::deleteTable('debt_repayment');
    }
});

Migration::hasTableExist('no_availible_order', function($noExist) {
    if($noExist == false) {
        Migration::deleteTable('no_availible_order');
    }
});

Migration::hasTableExist('user_access_pages', function($noExist) {
    if($noExist == false) {
        Migration::deleteTable('user_access_pages');
    }
});

Migration::hasTableExist('th_list', function($noExist) {
    if($noExist == false) {
        Migration::deleteTable('th_list');
    }
});

Migration::hasTableExist('data_td_accsess', function($noExist) {
    if($noExist == false) {
        Migration::deleteTable('data_td_accsess');
    }
});




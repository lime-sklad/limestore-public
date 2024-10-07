<?php
header('Content-type: application-json');

use Core\Classes\System\Updater;
use Core\Classes\Utils\Utils;

new Updater;

$view = $Render->view('/component/include_component.twig', [
    'renderComponent' => [
        '/component/notify/update/update-notify-row.twig' => [
            'hasUpdate' => Updater::hasNewVersion()
        ]
    ]
]);


$updateNav = $Render::view('/component/include_component.twig', [
    'renderComponent' => [
        '/component/widget/newVersionNotificationModal.twig' => [
            'hasNewVersion' => Updater::hasNewVersion()
        ],
    ],
]);

echo $utils::abort([
    'view' => trim($view),
    'nav' => $updateNav
]);

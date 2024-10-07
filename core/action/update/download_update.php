<?php
header('Content-type: application-json');

use Core\Classes\System\Updater;
use Core\Classes\Utils\Utils;

new Updater;


    if(isset($_POST['get_modal'])) {
        echo Utils::abort([
            'success' => $Render->view('/component/modal/update/load_update_loader.twig')
        ]);
    }

    if(isset($_POST['download'])) {
        if(Updater::hasNewVersion()) {
            Updater::setupUpdate(function($result) use ($Render) {
                if($result) {
                    echo Utils::abort([
                        'success' => $Render->view('/component/modal/update/update-success.twig', [
                            'success' => [
                                'text' => "Обновление установлено! \n Пожалуйста, перезагрузите программу!"
                            ]
                        ])
                    ]); 

                    return;
                } else {
                    echo Utils::abort([
                        'error' => $Render->view('/component/modal/update/update-error.twig', [
                            'error' => [
                                'text' => 'Проверьте подключение к интернету и попробуйте еще раз'
                            ]
                        ])
                    ]);         
                    
                }
            });
        }
    }



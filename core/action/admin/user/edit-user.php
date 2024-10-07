<?php
    use Core\Classes\Privates\accessManager;
use Core\Classes\Utils\Utils;

    header('Content-type: application/json');

    $prepare_data = $_POST['prepare_data'];

    if(array_key_exists('edit_seller_name', $prepare_data)) {
        if(!$user->isUsernameAvailable($prepare_data['edit_seller_name'])) {
            
            return $utils::abort([
                'type' => 'error',
                'text'	=> 'İstifadəçi artıq əlavə edilib'
            ]);

            die();
        } 
    }


    if(!empty($_POST['newAccessData'])) {
        $addedList = $_POST['newAccessData'];

        foreach($addedList as $rulesId) {
            accessManager::setUserDataAccess($prepare_data['seller_id'], $rulesId, function($hasAdmin) {
                if($hasAdmin) {
                    echo Utils::abort($hasAdmin);
                    die;
                }
            });
        }
    }

    if(!empty($_POST['delAccessData'])) {
        $removedAccess = $_POST['delAccessData'];

        foreach($removedAccess as $rulesId) {
            accessManager::unsetUserDataAccess($prepare_data['seller_id'], $rulesId, function($hasAdmin) {
                if($hasAdmin) {
                    echo Utils::abort($hasAdmin);
                    die;
                }
            });
        }
    }


    if(!empty($_POST['newAccessAction'])) {
        $addActionList = $_POST['newAccessAction'];

        foreach($addActionList as $actionId) {
            accessManager::setUserActionAccess($prepare_data['seller_id'], $actionId, function($hasAdmin) {
                if($hasAdmin) {
                    echo Utils::abort($hasAdmin);
                    die;
                }
            });
        }
    }


    if(!empty($_POST['delAccessAction'])) {
        $removedAccessAction = $_POST['delAccessAction'];

        foreach($removedAccessAction as $actionId) {
            accessManager::unsetUserActionAccess($prepare_data['seller_id'], $actionId, function($hasAdmin) {
                if($hasAdmin) {
                    echo Utils::abort($hasAdmin);
                    die;
                }
            });
        }
    }    


    // $user->editUser($prepare_data);

    return $utils::abort([
        'type' => 'success',
        'text' => 'Ok'
    ]);
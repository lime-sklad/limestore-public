<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/start.php';

    use Core\Classes\Privates\accessManager;
    use Core\Classes\Utils\Utils;

    accessManager::accessActionRoute('', function($hasAccess) {
        if($hasAccess) {
            echo Utils::abort([
                'type' => 'error',
                'text' => 'access denied!'
            ]);
    
            die;
        }
    });
    

    define('root_dir', $_SERVER['DOCUMENT_ROOT']);

    $menu = $main->getMenuList();

    if(isset($_POST['data_page_route'])) {
        $page = $_POST['data_page_route'];
        if(array_key_exists($page, $menu)) {
            $this_menu = $menu[$page];
            include root_dir.'/page/base.php';
        }    
    }


    if(isset($_POST['tab'])) {
        
        if(isset($_POST['page'])) {
            $page = $_POST['page'];
        }

        $get_tab = $_POST['tab'];
        
        
        $tab = $main->getTabs([$get_tab]);
        
        $tab_this = $tab[$get_tab];

        $type = $tab_this['type'];

        $link = $tab_this['tab_link'];

        if($accessManager->checkPagePremission($link, $user->getCurrentUser('get_id'))) {
            include root_dir.$link;
        }
    }



 
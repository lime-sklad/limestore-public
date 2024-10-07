<?php
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


// deprecated
// require_once 'function.php';
// require_once 'vendor/autoload.php';

// пока что уберем 
// require_once $_SERVER['DOCUMENT_ROOT'].'/core/function/update.function.php';
// require $_SERVER['DOCUMENT_ROOT'].'/core/main/check_license.php';

require_once 'start.php';

use Core\Classes\System\LicenseManager;
use Core\Classes\Utils\Utils;

$licenseManager->hasLicenseExpired(Utils::getDateDMY(), LicenseManager::getLicenseExpiredDate(), function($callback) {
    if($callback) {
        header("Location: license.php");  
		die;
    }
});



if (!isset($_SESSION['user'])) {
	$login_dir = '/login.php';
	header("Location: $login_dir");
	exit();
}


$updater = new \Core\Classes\System\Updater;


$image_dir = array_diff(scandir('assets/img/pattern/'), array('.', '..'));

$random_main_background_image = $image_dir[array_rand($image_dir, 1)];


echo $Render->view('/component/include_component.twig', [
	'renderComponent' => [
		'/component/index/head.twig' => [
			'lib_list' => $System->loadAssets(),
			'v' => time()
		],
		'/component/widget/notice.twig' => [
			//code
		],
		'/component/related_component/body_preloader.twig' => [
			//data
		],
		'/component/related_component/overlay.twig' => [
			//data
		],
	]
]);

echo $Render->view('/component/related_component/main_page.twig', [
	'renderComponent' => [

		'/component/index/show_current_version.twig' => [
			'current_version' => $updater->getCurrentVersion()
		],

		// sidebar
		'/component/index/sidebar.twig' => [
			'menu_list' => [
				'data' => $main->getMenuList()
			],
		],

		// main content
		'/component/container.twig' => [
			'includs' => [
				'renderMain' => [
					// header
					'/component/index/top_nav_content/top_nav.twig' => [
						'includs' => [					
							'renderTopNavComponent' => [
								'/component/index/top_nav_content/nav_list_options.twig' => [
									'username' => $user->getCurrentUser('get_name'),
									// вложеность в шаблоне, рендерим друигие шаблоны
									'includs' => [
										'stories' => [
											'/component/stories/stories.twig' => [
											]
										],												
										'renderUpdateNotify' => [
											'/component/notify/notify.twig' => [
												// 'expired_notify' => license_expired_notify(),
											]
										],
									],
								]
							],
						]
					],

					// menu
					'/component/main/menu_list.twig' => [
						'menu' => $main->getMenuList(),
						'main_image' => $random_main_background_image
					],

					// main
					'/component/main/main.twig' => [
						//data
					],

					// modal
					'/component/modal/modal_wrapper.twig' => [
						//data
					],


				]
			]
		]
	],
]);

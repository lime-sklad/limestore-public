<?php

use Core\Classes\Utils\Utils;

use function PHPSTORM_META\map;

require_once 'start.php';


if(isset($_SESSION['user'])) {
	require_once $_SERVER['DOCUMENT_ROOT']."/core/main/check_files.php";
	exit();
}

$image_dir = array_diff(scandir('assets/img/pattern/'), array('.', '..'));

$random_main_background_image = $image_dir[array_rand($image_dir, 1)];

	echo $Render->view('/component/include_component.twig', [
		'renderComponent' => [
			'/component/index/head.twig' => [
				'lib_list' => $System::loadAssets([
					'css' => [
						'fonts',
						'styleVar',
						'template',
						'animate',
						'lineAwesome',
						'mainStyle'
					],
					'script' => [
						'jquery',
						'function',
						'ajax'
					]
				]),
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

	$user_name_list = $user->getAllUser();
	$user_name_list = array_column($user_name_list, 'user_name');

	echo $Render->view('/component/container.twig', [
		'includs' => [
			'renderLogin' => [
				'/component/login/login_form.twig' => [
					'login' => $user_name_list,
					'main_image' => $random_main_background_image
				]
			]
		]
	]);

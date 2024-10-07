<?php

include 'start.php';

$image_dir = array_diff(scandir('assets/img/pattern/'), array('.', '..'));

$random_main_background_image = $image_dir[array_rand($image_dir, 1)];

echo $Render->view('/component/include_component.twig', [
    'renderComponent' => [
        '/component/index/head.twig' => [
            'lib_list' => [
                'css' => [
                    'assets/css/fonts.css',
                    'assets/css/style_var.css',
                    'assets/css/template.css',
                    'assets/css/animate.min.css',
                    'assets/lib/css_lib/line-awesome/assets/css/line-awesome.min.css',
                    'assets/css/new.style.css'
                ],
                'script' => [
                    'assets/lib/js_lib/jquery-3.3.1.min.js',
                    'assets/js/upd.ajax.js',
                    'assets/js/upd.function.js',
                                
                ]
            ],
            'v' => time() 
        ],
        '/component/license/license_active.twig' => [
            'user_licence_key' => $licenseManager::getLicenseSaultKey(),
            'main_image' => $random_main_background_image
        ],			
    ]
]); 
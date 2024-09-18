<?php 

return [
    // MainController
    [
        'method' => 'GET',
        'path' => '/',
        'controller' => [\Camagru\Controller\MainController::class, 'welcome'],
    ],
    [
        'method' => 'GET',
        'path' => '/about',
        'controller' => [\Camagru\Controller\MainController::class, 'about'],
    ],
    [
        'method' => 'GET',
        'path' => '/terms',
        'controller' => [\Camagru\Controller\MainController::class, 'terms'],
    ],
    // LoginController
    [
        'method' => 'GET',
        'path' => '/login',
        'controller' => [\Camagru\Controller\LoginController::class, 'index'],
    ],
    [
        'method' => 'POST',
        'path' => '/login',
        'controller' => [\Camagru\Controller\LoginController::class, 'login'],
    ],
    [
        'method' => 'POST',
        'path' => '/refresh_token',
        'controller' => [\Camagru\Controller\LoginController::class, 'refreshToken'],
    ],
    // RecoveryController
    [
        'method' => 'GET',
        'path' => '/recovery',
        'controller' => [\Camagru\Controller\RecoveryController::class, 'index'],
    ],
    [
        'method' => 'POST',
        'path' => '/recovery',
        'controller' => [\Camagru\Controller\RecoveryController::class, 'recovery'],
    ],
    [
        'method' => 'GET',
        'path' => '/recovery/password',
        'controller' => [\Camagru\Controller\RecoveryController::class, 'show'],
    ],
    [
        'method' => 'POST',
        'path' => '/recovery/password',
        'controller' => [\Camagru\Controller\RecoveryController::class, 'confirm'],
    ],
    // RegisterController
    [
        'method' => 'GET',
        'path' => '/register',
        'controller' => [\Camagru\Controller\RegisterController::class, 'index'],
    ],
    [
        'method' => 'POST',
        'path' => '/register',
        'controller' => [\Camagru\Controller\RegisterController::class, 'register'],
    ],
    // ConfirmController
    [
        'method' => 'GET',
        'path' => '/confirm',
        'controller' => [\Camagru\Controller\ConfirmController::class, 'index'],
    ],
    [
        'method' => 'POST',
        'path' => '/confirm/resend',
        'controller' => [\Camagru\Controller\ConfirmController::class, 'resend'],
    ],
    [
        'method' => 'GET',
        'path' => '/confirm/email',
        'controller' => [\Camagru\Controller\ConfirmController::class, 'show'],
    ],
    [
        'method' => 'POST',
        'path' => '/confirm/email',
        'controller' => [\Camagru\Controller\ConfirmController::class, 'confirm'],
    ],
    // ProfileController
    [
        'method' => 'GET',
        'path' => '/profile',
        'controller' => [\Camagru\Controller\ProfileController::class, 'index'],
    ],
    // GalleryController
    [
        'method' => 'GET',
        'path' => '/gallery',
        'controller' => [\Camagru\Controller\GalleryController::class, 'index'],
    ],
    [
        'method' => 'GET',
        'path' => '/gallery/{id}',
        'controller' => [\Camagru\Controller\GalleryController::class, 'show'],
    ],
    [
        'method' => 'DELETE',
        'path' => '/gallery/{id}',
        'controller' => [\Camagru\Controller\GalleryController::class, 'delete'],
    ],
    [
        'method' => 'GET',
        'path' => '/stickers',
        'controller' => [\Camagru\Controller\GalleryController::class, 'getAllStickers'],
    ],
    [
        'method' => 'GET',
        'path' => '/images/{slug}',
        'controller' => [\Camagru\Controller\GalleryController::class, 'getSticker'],
    ],
    [
        'method' => 'POST',
        'path' => '/upload/sticker',
        'controller' => [\Camagru\Controller\GalleryController::class, 'uploadSticker'],
    ],
    [
        'method' => 'POST',
        'path' => '/upload/gallery',
        'controller' => [\Camagru\Controller\GalleryController::class, 'uploadGallery'],
    ],
    // RelationController
    [
        'method' => 'POST',
        'path' => '/relation',
        'controller' => \Camagru\Controller\RelationController::class,
    ],
    // CommentController
    [
        'method' => 'POST',
        'path' => '/comment',
        'controller' => \Camagru\Controller\CommentController::class,
    ],
    // SettingController
    [
        'method' => 'GET',
        'path' => '/setting',
        'controller' => [\Camagru\Controller\SettingController::class, 'index'],
    ],
    [
        'method' => 'GET',
        'path' => '/setting/profile',
        'controller' => [\Camagru\Controller\SettingController::class, 'profile'],
    ],
    [
        'method' => 'GET',
        'path' => '/setting/activity',
        'controller' => [\Camagru\Controller\SettingController::class, 'activity'],
    ],
    [
        'method' => 'GET',
        'path' => '/setting/password',
        'controller' => [\Camagru\Controller\SettingController::class, 'password'],
    ],
    [
        'method' => 'PATCH',
        'path' => '/setting/profile',
        'controller' => [\Camagru\Controller\SettingController::class, 'changeProfile'],
    ],
    [
        'method' => 'PATCH',
        'path' => '/setting/password',
        'controller' => [\Camagru\Controller\SettingController::class, 'changePassword'],
    ],
];
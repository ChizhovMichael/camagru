<?php

declare(strict_types=1);

namespace Camagru\Common;

class Constant
{
    public const HEADER = [
        'links' => [
            ['name' => 'Sign up', 'link' => '/register'],
            ['name' => 'Sign in', 'link' => '/login'],
        ]
    ];

    public const FOOTER = [
        'company' => 'Camagru, Inc',
        'links' => [
            ['name' => 'Home', 'link' => '/'],
            ['name' => 'About', 'link' => '/about'],
            ['name' => 'Terms', 'link' => '/terms'],
        ]
    ];

    public const JPEG = 'image/jpeg';
    public const PNG = 'image/png';
    public const GIF = 'image/gif';

    public const ALLOWED_TYPES = [
        self::JPEG,
        self::PNG,
        self::GIF,
    ];

    public const STICKERS_PATH = 'stickers';

    public const GALLERY_PATH = 'gallery';

    public const IMAGE_PATH = '/images/';
}

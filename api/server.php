<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test
// application without having installed a "real" web server software here.

if (str_starts_with($uri, '/public') && file_exists(__DIR__.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
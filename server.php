<?php

/*
Project                        :   Ynex - Laravel Bootstrap 5 Premium Admin & Dashboard Template
Create Date                    :   26/09/2023
Author & Copyright Ownership   :   Spruko Technologies Private Limited 
Author URL                     :   https://themeforest.net/user/spruko
Support	                       :   https://support.spruko.com/
License Details                :   https://spruko.com/licenses-details
*/

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';

<?php

/**
 * App configuration
 */

define('BASE_PATH', 'http://localhost:8080/');
//define('BASE_PATH', 'https://artlabapi.vincentmaenhout.ikdoeict.be');

define('ALLOW_ORIGIN', 'http://localhost:5173');
//define('ALLOW_ORIGIN', 'https://artlab.laurensrousseau.ikdoeict.be');


define('SECRET_KEY', "Atlab.jimosdfqmjfiemojq@23412341234");
define('ACCESS_TOKEN_LIFETIME', 60 * 60);
define('REFRESH_TOKEN_LIFETIME', 24 * 60 * 60);
define('PIMG_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'profilePictures');
define('IMG_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'images');

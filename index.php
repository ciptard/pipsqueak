<?php

/**
* Path to the app directory.
*/
$app_path = './app';

/**
* Path to the cache directory.
*/
$cache_path = $app_path.'/cache';

/**
* Path to the templates directory.
*/
$templates_path = './templates';

/**
* Path to the content directory.
*/
$content_path = './content';

/**
* Root path
*/
define('DOCROOT', realpath(__DIR__).DIRECTORY_SEPARATOR);

/**
* Let's go...
*/
require DOCROOT.$app_path.'/bootstrap.php';

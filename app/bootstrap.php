<?php

define('DS', DIRECTORY_SEPARATOR);

// Define the global path constants
define('APPPATH', realpath(DOCROOT.$app_path).DS);
define('CACHEPATH', realpath(DOCROOT.$cache_path).DS);
define('TEMPLATESPATH', realpath(DOCROOT.$templates_path).DS);
define('CONTENTPATH', realpath(DOCROOT.$content_path).DS);
define('PUBLICPATH', realpath(DOCROOT.$public_path).DS);
define('PAGESPATH', CONTENTPATH.'pages'.DS);
define('RESOURCESPATH', CONTENTPATH.'resources'.DS);
define('GLOBALSPATH', CONTENTPATH.'global.txt');

// Deal with autoloading...
function autoload( $class )
{
    list($namespace) = explode('_', $class);
    if ( ! isset($namespace) or $namespace !== 'Twig' )
    {
        include_once APPPATH.'classes'.DS.str_replace('_',DS,strtolower($class)).'.php';  
    } 
}

spl_autoload_register('autoload', true, true );

// kick things off...

Pipsqueak::start();

$request = Request::factory(Config::get('cache_level'));

$request->execute();

$request->response();

Pipsqueak::finish();

/* End of file bootstrap.php */
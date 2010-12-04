<?php

define('DS', DIRECTORY_SEPARATOR);

// Define the global path constants
define('APPPATH', DOCROOT.$app_path.DS);
define('CACHEPATH', DOCROOT.$cache_path.DS);
define('TEMPLATESPATH', DOCROOT.$templates_path.DS);
define('CONTENTPATH', DOCROOT.$content_path.DS);
define('PAGESPATH', CONTENTPATH.'pages'.DS);
define('RESOURCESPATH', CONTENTPATH.'resources'.DS);
define('GLOBALSPATH', CONTENTPATH.'global.yml');

// Deal with autoloading...
function autoload( $class )
{
    list($namespace) = explode('_', $class);
    if ( ! isset($namespace) or $namespace !== 'Twig' )
    {
        include_once APPPATH.'classes/'.strtolower($class).'.php';  
    } 
}

spl_autoload_register('autoload', true, true );

// kick things off...

Pipsqueak::start();

$request = new Request();

$request->execute();

echo $request->response;

Pipsqueak::finish();

/* End of file bootstrap.php */
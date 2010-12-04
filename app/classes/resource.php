<?php defined('APPPATH') or exit('No direct script access allowed');


class Resource {
    
    private static $paths = array();

    public static function path( $uri )
    {
        $hashkey = md5($uri->string());
        
        if ( isset(self::$paths[$hashkey]) ) return self::$paths[$hashkey]; // already retrieved this path!
        
        $path = self::uri_to_path($uri);
                
        self::$paths[$hashkey] = file_exists($path) ? $path : NULL;
        
        return self::$paths[$hashkey];
    }
    
    private static function uri_to_path( $uri )
    {
        return RESOURCESPATH.$uri->string().'.'.Config::get('content_extension');   
    }

}

/* End of file classes/resource.php */
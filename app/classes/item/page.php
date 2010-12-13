<?php defined('APPPATH') or exit('No direct script access allowed');


class Item_Page extends Item {
            
    public static function path( $uri )
    {
        $hashkey = md5($uri->string());
        
        if ( isset(self::$paths[$hashkey]) ) return self::$paths[$hashkey]; // already retrieved this path!
        
        $match = glob(self::uri_to_path($uri));
        
        self::$paths[$hashkey] = count($match) ? $match[0] : NULL;
        
        return self::$paths[$hashkey];
    }
    
    public static function uri_to_path( $uri )
    {         
        $segments = $uri->segments();
        $path = PAGESPATH;
 
        if ( isset($segments[0]) and $segments[0] === '404' )
        {
            return PAGESPATH.'404/404.'.Config::get('content_extension');
        }

        if ( ! count($uri->segments()) )
        {
            $segments = array(Config::get('homepage_slug'));
        }

        foreach ( $segments as $segment )
        {
            $path .= '[1-9]*-'.$segment.DS;
        }
        
        $page_name = $uri->last_segment() ? $uri->last_segment() : Config::get('homepage_slug');

        $path .= $page_name.'.'.Config::get('content_extension');
            
        return $path;
    }

}

/* End of file classes/page.php */
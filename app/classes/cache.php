<?php defined('APPPATH') or exit('No direct script access allowed');


class Cache {
    
    const NONE      = 0;
    const DYNAMIC   = 1;
    const FULL      = 2;
    
    public static function exists( $uri, $type = 'site' )
    {
        return file_exists( self::cache_path( $uri, $type ) );
    }
    
    public static function retrieve( $uri, $type = 'site' )
    {
        $contents = file_get_contents( self::cache_path( $uri, $type ) );
        if ( $type == 'content' )
        {
            return unserialize( $contents );
        }
        return $contents;
    }
    
    public static function save( $uri, $type = 'site', $content )
    {
        if ( $type == 'content' )
        {
            return Helpers::file_force_contents( self::cache_path( $uri, $type ), serialize( $content ) );
        }
        return Helpers::file_force_contents( self::cache_path( $uri, $type ), $content );
    }
    
    public static function is_valid( $uri, $type, $compare )
    {
        if ( self::exists( $uri, $type ) )
        {
            $cache_mtime = filemtime( self::cache_path( $uri, $type ) );
            $file_mtime = is_int($compare) ? $compare : filemtime( $compare );
            return $cache_mtime > $file_mtime;
        }
        return FALSE;
    }
    
    public static function template_cache_mtime()
    {
        $dirmtime = Helpers::dirmtime( TEMPLATESPATH, TRUE );
        return  $dirmtime ? $dirmtime : 356137200;
    }
    
    private static function cache_path( $uri, $type )
    {
        if ( is_object($uri) ) $uri = $uri->string();
        $path = empty($uri) ? 'home' : $uri;
        return CACHEPATH.$type.DS.$path.'.cache';
    }
}

/* End of file classes/cache.php */
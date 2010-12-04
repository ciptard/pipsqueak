<?php defined('APPPATH') or exit('No direct script access allowed');


class Config {
    
    public static $items = array();
   
    public static function load()
    {       
        $app_config = require( APPPATH.'config.php' );
        
        if ( file_exists( DOCROOT.'config.php' ) )
        {
            $user_config = require( DOCROOT.'config.php' );
            self::$items = array_merge( $app_config, $user_config );
        }
        else
        {
            self::$items = $app_config;
        }
    }   
   
    public static function get( $item, $default = NULL )
    {
        if (isset(static::$items[$item]))
		{
			return static::$items[$item];
		}

		if (strpos($item, '.') !== false)
		{
			$parts = explode('.', $item);

			$return = false;
			foreach ($parts as $part)
			{
				if ($return === false and isset(static::$items[$part]))
				{
					$return = static::$items[$part];
				}
				elseif (isset($return[$part]))
				{
					$return = $return[$part];
				}
				else
				{
					return $default;
				}
			}
			return $return;
		}

		return $default;
    }
    
}

/* End of file classes/config.php */
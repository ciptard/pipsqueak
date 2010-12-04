<?php defined('APPPATH') or exit('No direct script access allowed');

class Pipsqueak {
    
    public static $version = '0.1';
    
    public static function start()
    {
        // start output buffering
        ob_start();
        
        // load up the configs
        Config::load();
    }
    
    
    public static function finish()
	{
        // end output buffering
		echo ob_get_clean();
	}

}

/* End of file classes/hyde.php */
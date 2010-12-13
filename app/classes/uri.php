<?php

class URI {
    
	public $uri = '';

	public $segments = '';
	
	public static $uri_prefix;
	
	public function __construct($uri = NULL)
	{
		if ($uri === NULL)
		{
			$uri = $this->detect();
		}
		
		$uri = trim($uri, '/');

		$parts = preg_split("/\\.([^.\\s]{2,4}$)/", $uri, NULL, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
        
        $this->uri = count($parts) ? $parts[0] : $uri;
        
        $this->format = count($parts) === 2 ? $parts[1] : 'html';
   
        if ( empty($this->uri) )
        {
            $this->segments = array();  
        }
        else
        {
            $this->segments = explode('/', $this->uri);
        }
	}
	
	public function segments()
	{
	    return $this->segments;
	}
	
	public function format()
	{
	    return $this->format;
	}
	
	public function last_segment()
	{
	    $num = count($this->segments);
	    
	    if ( ! $num )
	    {
	        return NULL;
	    }

	    return $this->segments[$num-1];
	}
	
	public function string()
	{
	    return $this->uri;
	}
	
	public function detect()
	{
	    $index_file = Config::get('index_file');
	    
	    self::$uri_prefix = empty($index_file) ? $index_file : '';
	    
        if ( ! empty($_SERVER['PATH_INFO']) )
        {
            $uri = $_SERVER['PATH_INFO']; // use it if we got it...
        }
        else
        {
            if ( isset($_SERVER['REQUEST_URI']) )
            {
                $uri = $_SERVER['REQUEST_URI'];
                
                if ( ! empty($index_file) )
                {
                   $uri = str_replace( $index_file, '', $uri );
                }
                
                list($uri) = explode('?',$uri);
            }
            else
            {
                throw new Exception('The URI cannot be detected.');
            }
        }

		return $uri;
	}

	public function __toString()
	{
		return $this->uri;
	}
}

/* End of file uri.php */
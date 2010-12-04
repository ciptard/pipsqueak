<?php

class URI {
    
	public $uri = '';

	public $segments = '';
	
	public function __construct($uri = NULL)
	{
		if ($uri === NULL)
		{
			$uri = static::detect();
		}
		
		$this->uri = trim($uri, '/');

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
	
	public static function detect()
	{
		if ( ! empty($_SERVER['PATH_INFO']))
		{
			$uri = $_SERVER['PATH_INFO'];
		}
		else
		{
			if (isset($_SERVER['REQUEST_URI']))
			{
				$uri = $_SERVER['REQUEST_URI'];
			}
			else
			{
				throw new Exception('Unable to detect the URI.');
			}
		}

		$uri = str_replace(array('//', '../'), '/', $uri);

		return $uri;
	}

	public function __toString()
	{
		return $this->uri;
	}
}

/* End of file uri.php */
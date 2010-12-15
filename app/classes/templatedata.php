<?php defined('APPPATH') or exit('No direct script access allowed');

class TemplateData {
    
    protected static $pages = NULL;
    
    protected static $resources = array();
    
    public $uri;
    
    public function __construct()
    {
        $this->uri = new URI();
    }
    
    public function pages()
    {
        if ( ! self::$pages ) 
        {
            self::$pages = new TemplateData_PagesIterator();
        }
        
        return self::$pages;
    }
    
    public function __call($name, $arguments)
    {
        if ( ! isset( $this->resources[$name] ) )
        {
            if ( is_dir( RESOURCESPATH.$name ) )
            {
                $this->resources[$name] = new TemplateData_ResourcesIterator(RESOURCESPATH.$name);
            }
        }
        if ( isset($this->resources[$name] ) )
        {
            return $this->resources[$name];
        }
    }

}

/* End of file classes/data.php */
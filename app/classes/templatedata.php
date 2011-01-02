<?php defined('APPPATH') or exit('No direct script access allowed');

class TemplateData {
        
    public $uri;
    
    public function __construct()
    {
        $this->uri = new URI();
    }
    
    public function pages()
    {        
        return new TemplateData_PagesIterator();
    }
    
    public function __call($name, $arguments)
    {
        if ( is_dir( RESOURCESPATH.$name ) )
        {
            return new TemplateData_ResourcesIterator(RESOURCESPATH.$name);
        }
    }

}

/* End of file classes/data.php */
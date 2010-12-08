<?php defined('APPPATH') or exit('No direct script access allowed');

class TemplateData {
    
    protected static $pages = NULL;
    
    public function pages()
    {
        if ( ! self::$pages ) 
        {
            self::$pages = new TemplateData_PagesIterator();
        }
        
        return self::$pages;
    }
    


}

/* End of file classes/data.php */
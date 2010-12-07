<?php defined('APPPATH') or exit('No direct script access allowed');

class Request_NoCache extends Request {
    
    public function __construct()
    {
        $this->cache = FALSE;
        return parent::__construct();
    }
   
    protected function render()
    {
       $this->get_content_path();
       
       $this->parse_content();

       $this->get_template();
       
       $this->parse_globals();
           
       $this->render_template();
    }
    
}

/* End of file classes/request/nocache.php */
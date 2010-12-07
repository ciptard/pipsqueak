<?php defined('APPPATH') or exit('No direct script access allowed');

class Request_Cached extends Request {
   
    protected function render()
    {
        // grab page cache if it exists
        if ( Cache::exists($this->uri, 'site') )
        {
            $this->response = Cache::retrieve($this->uri);
            return;
        }
        
        // otherwise...
        
        $this->get_content_path();
        
        if ( Cache::exists($this->uri, 'content') )
        {
            $this->content = Cache::retrieve($this->uri, 'content');
        }
        else
        {
            $this->parse_content();
        }
        
        $this->get_template();
        
        if ( Cache::exists('_globals','content') )
        {
            $this->globals = Cache::retrieve('_globals','content');
        }
        else
        {
            $this->parse_globals();
        }
        
        $this->render_template();
        
    }

            
}

/* End of file classes/request/cached.php */
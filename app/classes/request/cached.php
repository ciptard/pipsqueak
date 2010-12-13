<?php defined('APPPATH') or exit('No direct script access allowed');

class Request_Cached extends Request {
   
    protected function render()
    {
        // grab page cache if it exists
        if ( Cache::exists($this->uri, 'site') )
        {
            if ( $this->check_etag() ) return;
            
            $this->response = Cache::retrieve($this->uri);
            return;
        }
        
        // otherwise...
        
        $this->get_item();

        $this->get_content();
        
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
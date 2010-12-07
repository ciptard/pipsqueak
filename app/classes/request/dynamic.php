<?php defined('APPPATH') or exit('No direct script access allowed');

class Request_Dynamic extends Request {
   
    protected function render()
    {
        $this->get_content_path();
        
        $content_cache_is_valid  = Cache::is_valid($this->uri, 'content', $this->content_path);
        $template_cache_is_valid = Cache::is_valid($this->uri, 'site', TEMPLATESPATH);
        $globals_cache_is_valid  = Cache::is_valid('_globals','content', GLOBALSPATH);

        if ( Cache::exists($this->uri, 'site')
                and $content_cache_is_valid
                and $template_cache_is_valid
                and $globals_cache_is_valid )
        {
            // if all checks are ok, serve up the cached version.
            // note *any* change to the templates means we don't serve up the cached content
            // as we can't follow the template include/embed paths.
            $this->response = Cache::retrieve($this->uri);
            return;
        }

        if ( $content_cache_is_valid )
        {
            // the cached content file is still valid so lets use that.
            $this->content = Cache::retrieve($this->uri, 'content');
        }
        else
        {
            // otherwise lets parse the content afresh...
            $this->parse_content();
        }
        
        $this->get_template();
        
        if ( $globals_cache_is_valid )
        {
            // we have a valid cache of the globals file.
            $this->globals = Cache::retrieve('_globals','content');
        }
        else
        {
            $this->parse_globals();
        }
        
        $this->render_template();
    }

            
}

/* End of file classes/request/nocache.php */
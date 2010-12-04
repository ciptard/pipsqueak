<?php defined('APPPATH') or exit('No direct script access allowed');

class Request {
    
    public $uri;
    
    public $response;
    
    public $content_path;
    
    public $content_type;
    
    public $content = array();
    
    public $globals = array();
    
    public $template;
    
    private $twig;
    
    public function __construct()
    {        
        $this->uri = new URI();
        
        $this->cache_level = Config::get('cache_level');
    }
    
    public function execute()
    {
        $page_cache_exists = Cache::exists($this->uri, 'site');
        
        if ( $this->cache_level === Cache::FULL and $page_cache_exists )
        {
            // if caching is set to full no cache invalidation checks are required,
            // so just send the cached response if it exists.
            $this->response = Cache::retrieve($this->uri);
            return;
        }
        
        // Otherwise...
        
        if ( Page::path($this->uri) )
        {
            $this->content_path = Page::path($this->uri); // this is a request for a specific page.
            $this->content_type = 'page';
        }
        elseif ( Resource::path($this->uri) )
        {
            $this->content_path = Resource::path($this->uri); // this is a request for a resource.
            $this->content_type = 'resource';
        }
        else
        {
            // can't find anything, throw a 404 error.
            $this->show_404();
            return;
        }
  
        $content_cache_is_valid  = Cache::is_valid($this->uri, 'content', $this->content_path);
        $template_cache_is_valid = Cache::is_valid($this->uri, 'site', Cache::template_cache_mtime());
        $globals_cache_is_valid  = Cache::is_valid('_globals','content', GLOBALSPATH);
                
        if ( $this->cache_level === Cache::DYNAMIC
                and $page_cache_exists
                and $content_cache_is_valid
                and $template_cache_is_valid
                and $globals_cache_is_valid )
        {
            // caching is dynamic and all checks are ok, serve up the cached version!
            // note *any* change to the templates means we don't serve up the cached content
            // as we can't follow the template include/embed paths.
            $this->response = Cache::retrieve($this->uri);
            return;
        }
        
        // Now we should have the correct path to the appropriate content file.
        
        if ( $this->cache_level === Cache::FULL and Cache::exists($this->uri, 'content') )
        {
            // if full caching is enabled and a cached version of the parsed content file exists, grab it.
            $this->content = Cache::retrieve($this->uri, 'content');
        }
        elseif ( $this->cache_level === Cache::DYNAMIC and $content_cache_is_valid )
        {
            // otherwise if we have dynamic caching and the cached content file is still valid then lets use that.
            $this->content = Cache::retrieve($this->uri, 'content');
        }
        else
        {
            // otherwise lets parse the content afresh...
            try
            {
                $this->content = Content::parse( $this->content_path );
                if ( $this->cache_level > Cache::NONE ) Cache::save($this->uri, 'content', $this->content);
            }
            catch( Exception $e)
            {
                $this->show_404();
                return;
            }
        }
                
        if ( isset($this->content['template']) )
        {   
            $this->content['template'] = str_replace('.html','',$this->content['template']).'.html';
            
            if ( file_exists( TEMPLATESPATH.$this->content['template'] ) )
            {
                $this->template_path = $this->content['template'];
                
                // template exists, so lets get the global configs, if there are any
                
                $globals_file_exists = file_exists(GLOBALSPATH);
   
                if ( $this->cache_level > Cache::NONE and Cache::exists('_globals','content') )
                {                
                    if ( $this->cache_level === Cache::FULL or ( $this->cache_level === Cache::DYNAMIC and $globals_cache_is_valid ) )
                    {
                        // we have a valid cache of the globals file.
                        $this->globals = Cache::retrieve('_globals','content');
                    }
                    elseif ( $this->cache_level === Cache::DYNAMIC and $globals_file_exists )
                    {
                        // globals file cache is invalid
                        $this->globals = Content::parse( GLOBALSPATH );
                        if ( $this->cache_level > Cache::NONE ) Cache::save('_globals', 'content', $this->globals);
                    }
                }
                elseif ( $globals_file_exists )
                {
                    // no cache exists
                    $this->globals = Content::parse( GLOBALSPATH );
                    if ( $this->cache_level > Cache::NONE ) Cache::save('_globals', 'content', $this->globals);
                }

                // we now have a content file path and a template file path... can render the template

                $template = Template::factory();
                 
                $this->response = $template->render($this->template_path, array_merge($this->content,array('global'=>$this->globals)));

                if ( $this->cache_level > Cache::NONE ) Cache::save($this->uri, 'site', $this->response);
                return;
            }

        }
                
        // TODO: handle lack of template a bit more gently, have some sensible defaults to fall back to.
         
        $this->show_404();
        return;
    }
    
    private function send_response()
    {
        echo $this->response;
    }
        
    private function show_404()
    {
        // TODO: need to come up with a strategy for determining which page to display for 404 errors
        echo '404!';
        exit();
    }
            
}

/* End of file classes/request.php */
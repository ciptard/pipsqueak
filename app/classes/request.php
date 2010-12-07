<?php defined('APPPATH') or exit('No direct script access allowed');

class Request {
    
    public $uri;
        
    public $response;
    
    public $content_path;
    
    public $content_type;
    
    public $content = array();
    
    public $globals = array();
    
    public $template;
    
    protected $cache = TRUE;
    
    public static function factory( $cache_level )
    {
        switch( $cache_level )
        {
            case Cache::FULL:
                return new Request_Cached();
            break;
            
            case Cache::DYNAMIC:
                return new Request_Dynamic();
            break;
            
            case Cache::NONE:
                return new Request_NoCache();
            break;
        }
    }
        
    protected function __construct()
    {        
        $this->uri = new URI();
    }
    
    public function execute()
    {
        try
        {
            $this->render();
        }
        catch( Exception $e )
        {
            $this->show_404();
        }
    }
    
    protected function get_content_path()
    {
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
            throw new Exception('404');
        }
    }

    protected function get_template()
    {
        if ( isset($this->content['template']) && file_exists( TEMPLATESPATH.$this->content['template'] ) )
        {
            $this->template_path = $this->content['template'];
        }
        else
        {
            throw new Exception('404'); // TODO: handle this more gracefully with fallback templates
        }
    }
    
    protected function parse_content()
    {
        if ( $this->content_path )
        {
            $this->content = Content::parse($this->content_path);
            if ( $this->cache ) Cache::save($this->uri, 'content', $this->content);            
        }
    }
    
    protected function parse_globals()
    {
        if ( file_exists(GLOBALSPATH) )
        {
            $this->globals = Content::parse(GLOBALSPATH);
            if ( $this->cache ) Cache::save('_globals', 'content', $this->globals);
        }
    }
    
    protected function render_template()
    {
        $template = Template::factory();
        $this->response = $template->render($this->template_path, array_merge($this->content,array('global'=>$this->globals)));
        if ( $this->cache ) Cache::save($this->uri, 'site', $this->response);
    }
    
    protected function render()
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
            throw new Exception('404');
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
            $this->content = Content::parse( $this->content_path );
            if ( $this->cache_level > Cache::NONE ) Cache::save($this->uri, 'content', $this->content);
        }
                
        if ( isset($this->content['template']) && file_exists( TEMPLATESPATH.$this->content['template'] ) )
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
                
        // TODO: handle lack of template a bit more gently, have some sensible defaults to fall back to.
        
        throw new Exception('404');
    }
    
    public function response()
    {
        echo $this->response;
    }
        
    private function show_404()
    {
        header('HTTP/1.0 404 Not Found');
        
        // if there is a 404 content page, display that. 404 pages are not cached.
        $uri = new URI('404');

        if ( Page::path($uri) )
        {
            try
            {
                $content = Content::parse( Page::path($uri) );
                if ( isset($content['template']) && file_exists( TEMPLATESPATH.$content['template'] ) )
                {
                    $template = Template::factory();
                    echo $template->render($content['template'], array_merge($content,array('global'=>$this->globals)));
                    exit();
                }
            }
            catch( Exception $e )
            {
                // ...
            }
        }
        
        if ( file_exists(PUBLICPATH.'404.html') )
        {
            include(PUBLICPATH.'404.html');
        }
        else
        {
            echo '<h1>404 Error</h1><p>The page you were looking for could not be found.</p>';            
        }
                
        exit();
    }
            
}

/* End of file classes/request.php */
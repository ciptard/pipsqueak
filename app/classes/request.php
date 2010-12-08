<?php defined('APPPATH') or exit('No direct script access allowed');

abstract class Request {
    
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
    
    public function response()
    {
        echo $this->response;
    }
    
    protected abstract function render();
    
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
        $this->response = $template->render($this->template_path, array_merge($this->content,array('global'=>$this->globals,'pip'=>new TemplateData())));
        if ( $this->cache ) Cache::save($this->uri, 'site', $this->response);
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
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
    
    protected function get_item()
    {
        $this->item = Item::factory($this->uri);
        
        if ( ! $this->item )
        {
            // can't find anything, throw a 404 error.
            throw new Exception('404');            
        }
    }

    protected function get_template()
    {
        $this->template_path = $this->item->template_path();
        
        if ( ! $this->template_path )
        {
            throw new Exception('404'); // TODO: handle this more gracefully with fallback templates
        }
    }
    
    protected function get_content()
    {
        $this->content = $this->item->content( $this->cache );
        if ( $this->cache ) Cache::save($this->uri, 'content', $this->content);            
    }
    
    protected function parse_globals()
    {
        if ( file_exists(GLOBALSPATH) )
        {
            $this->globals = Helpers::parse_contents(GLOBALSPATH);
            if ( $this->cache ) Cache::save('_globals', 'content', $this->globals);
        }
    }
    
    protected function render_template()
    {
        try
        {
            $template = $this->item->template();
            
            $this->response = $template->render(array_merge($this->content,array('global'=>$this->globals,'pip'=>new TemplateData())));
            
            if ( $this->cache ) Cache::save($this->uri, 'site', $this->response);
        }
        catch ( Exception $e )
        {
            $this->show_404();
        }
    }
        
    private function show_404()
    {
        header('HTTP/1.0 404 Not Found');
        
        // if there is a 404 content page, display that. 404 pages are not cached.
        $uri = new URI('404');

        if ( Item_Page::path($uri) )
        {
            try
            {
                $item = new Item_Page($uri);
                
                $content = $item->content( $this->cache );
                
                $template = $item->template();

                echo $template->render(array_merge($content,array('global'=>$this->globals,'pip'=>new TemplateData())));
                
                exit();
     
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
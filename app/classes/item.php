<?php defined('APPPATH') or exit('No direct script access allowed');

require_once( APPPATH.'vendor/sfYaml/sfYamlParser.php' );
require_once( APPPATH.'vendor/phpMarkdownExtra/markdown.php' );

class Item {
    
    protected static $paths = array();
    
    protected $content = NULL;
    
    protected $path;
    
    public static function factory( $uri )
    {
        if ( Item_Page::path($uri) )
        {
            // this is a request for a specific page.
           return new Item_Page($uri);
        }
        elseif ( Item_Resource::path($uri) )
        {
            // this is a request for a resource.
            return new Item_Resource($uri);
        }
        return NULL;
    }
    
    public function __construct( $uri )
    {
        $self = get_class($this);
        
        $this->path = $self::path($uri);
        $this->uri = $uri;
    }
        
    public function template()
    {
        if ( isset( $this->template ) )
        {
            return $this->template;
        }
        
        if ( $this->content === NULL ) $this->content();
        
        if ( isset($this->content['template']) && file_exists( TEMPLATESPATH.$this->content['template'] ) )
        {
            $this->template = Template::factory();
            $this->template->set_path($this->content['template']);
            return $this->template;
        }
        
        throw new Exception('Template could not be instantiated');
    }
    
    public function content( $use_cached = TRUE )
    {
        if ( ! $this->content )
        {
            if ( $use_cached and Cache::is_valid($this->uri, 'content', $this->path) )
            {
                $this->content = Cache::retrieve($this->uri, 'content');
            }
            else
            {
                $this->content = Helpers::parse_contents($this->path);
            }
        }

        return $this->content;
    }
}

/* End of file classes/content.php */
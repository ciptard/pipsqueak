<?php defined('APPPATH') or exit('No direct script access allowed');

class TemplateData_Item {
    
    protected $resource_item = NULL;
    
    protected $data;
        
    function __construct( $type, $path, $data = array() )
    {
        $this->type = $type;
        $this->path = $path == Config::get('homepage_slug') ? '' : $path;
        $this->uri = new URI($path);
        $this->data = $data;

        $this->url = empty( $this->path ) ? '/' : str_replace('//','/','/'.Config::get('index_file').'/'.$this->path);
    }
    
    public function __get($name)
    {        
        if ( ! array_key_exists( $name, $this->data ) and $this->resource_item === NULL )
        {
            $this->get_resource_item();
        }
        
        return array_key_exists( $name, $this->data ) ? $this->data[$name] : '';
    }
    
    public function __isset( $name )
    {
        if ( ! array_key_exists( $name, $this->data ) and $this->resource_item === NULL )
        {
            $this->get_resource_item();
        }
        
        return array_key_exists( $name, $this->data );
    }
    
    protected function get_resource_item()
    {
        $classname = 'Item_'.ucwords($this->type);
        
        $this->resource_item = new $classname($this->uri);
        
        $content = $this->resource_item->content( Config::get('cache_level') );
        
        if ( is_array($content) )
        {
            $this->data = array_merge( $this->data, $content );
        }
    }
    
}

/* End of file classes/templatedata/item.php */
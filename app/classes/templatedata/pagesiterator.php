<?php defined('APPPATH') or exit('No direct script access allowed');

class TemplateData_PagesIterator implements RecursiveIterator, Countable {
    
    protected $pages = NULL;
    
    protected $filter;
    
    protected $key;
    protected $valid;
    protected $current;
    
    public function __construct($pages = NULL)
    {
        if ( $pages ) $this->pages = $pages;
    }
    
    public function under($path = NULL)
    {
        if ( $this->pages === NULL ) $this->pages = $this->get_pages();

        if ( ! $path )
        {
            return $this;
        }
        else
        {
            $segments = explode('/',$path);
            $path = '';
            $result = $this->pages;
            
            foreach( $segments as $segment )
            {
                $path = trim($path.'/'.$segment,'/');
                $result = $result[$path]['children'];
            }
        
        }
        
        return new TemplateData_PagesIterator($result);
    }
    
    public function next()
    {
        if ( $this->pages === NULL ) $this->pages = $this->get_pages();
        $this->key++;
        $this->valid = FALSE !== ($this->current = next($this->pages));
    }

    public function rewind()
    {
        if ( $this->pages === NULL ) $this->pages = $this->get_pages();
        $this->key = 0;
        $this->valid = FALSE !== ($this->current = reset($this->pages));
    }

    public function valid()
    {
        return $this->valid;
    }

    public function key()
    {
        return $this->key;
    }

    public function current()
    {
        return $this->current;
    }
    
    public function count()
    {
        return count($this->pages);
    }
    
    public function hasChildren()
    {
        return isset($this->current['children']);
    }

    public function getChildren()
    {
        return new TemplateData_PagesIterator($this->current['children']);
    }
    
    protected function get_pages()
    {   
        $iterator = new RecursiveDirectoryIterator(PAGESPATH);
        return $this->arrayize($iterator);
    }
    
    protected function arrayize(RecursiveDirectoryIterator $iterator)
    {
        $array = array();
        
        foreach ( $iterator as $file )
        {
            if ( $file->isDir() )
            {
                $path = preg_replace('/\d+?\-/','',str_replace(PAGESPATH, '', $file->getPathname()));
                $segments = explode( DS, $path );
                
                $current = array(
                    'slug'      => end($segments),
                    'level'     => count($segments),
                    'path'     => $path
                );
                                  
                $children = $this->arrayize($iterator->getChildren());
                
                if ( count($children) ) $current['children'] = $children;
                
                $array[$path] = $current;
            }            
        }
        
        return $array;
    }
    
    public function __toString()
    {
        return '';
    }
       
}

/* End of file classes/data.php */
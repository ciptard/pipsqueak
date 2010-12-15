<?php defined('APPPATH') or exit('No direct script access allowed');

abstract class TemplateData_DataIterator implements RecursiveIterator, Countable {
    
    protected $data = NULL;
    
    protected $filter;
    
    protected $key;
    protected $valid;
    protected $current;
    
    abstract protected function get_data();
    
    abstract protected function arrayize(RecursiveDirectoryIterator $iterator);
    
    public function __construct($data = NULL)
    {
        if ( $data ) $this->data = $data;
    }
        
    public function next()
    {
        if ( $this->data === NULL ) $this->data = $this->get_data();
        $this->key++;
        $this->valid = FALSE !== ($this->current = next($this->data));
    }

    public function rewind()
    {
        if ( $this->data === NULL ) $this->data = $this->get_data();
        $this->key = 0;
        $this->valid = FALSE !== ($this->current = reset($this->data));
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
        return count($this->data);
    }
    
    public function hasChildren()
    {
        return isset($this->current['children']);
    }

    public function getChildren()
    {
        $self = get_class($this);
        return new $self($this->current['children']);
    }
            
    public function __toString()
    {
        return '';
    }
       
}

/* End of file classes/data.php */
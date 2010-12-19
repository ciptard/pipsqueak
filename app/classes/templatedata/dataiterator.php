<?php defined('APPPATH') or exit('No direct script access allowed');

abstract class TemplateData_DataIterator implements RecursiveIterator, Countable {
    
    protected $data = NULL;
    
    protected $filters = array();
    
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
        if ( $this->data === NULL ) $this->data = $this->get_data();
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
    
    
    // filters
    
    protected function filter( $data )
    {
        $self = get_class($this);
        
        if ( count($this->filters) )
        {
            foreach ( $this->filters as $filter )
            {        
                $data = call_user_func_array(array($this, 'filter_'.$filter['name']), array_merge( array($data), $filter['args'] ) );
            }
        }

        return $data;
    }
    
    public function under($path = NULL)
    {
        $this->filters[] = array('name'=>'under','args'=>array($path));
        return $this;
    }
    
    public function limit( $limit )
    {
        $this->filters[] = array('name'=>'limit','args'=>array($limit));
        return $this;
    }
    
    public function start( $start )
    {
        $this->filters[] = array('name'=>'start','args'=>array($start));
        return $this;
    }
    
    public function to( $limit )
    {
        $this->filters[] = array('name'=>'limit','args'=>array($limit));
        return $this;
    }
    
    public function from( $start )
    {
        $this->filters[] = array('name'=>'start','args'=>array($start));
        return $this;
    }
    
    // these should be overridden in child classes to supply a specific implementation
    
    protected function filter_under( $data, $path )
    {
        return $data;
    }
    
    protected function filter_limit( $data, $limit )
    {
        return array_slice($data, 0, $limit);
    }
    
    protected function filter_start( $data, $start )
    {
        return array_slice($data, $start);
    }
            
}

/* End of file classes/data.php */
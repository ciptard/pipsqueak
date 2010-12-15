<?php defined('APPPATH') or exit('No direct script access allowed');

class TemplateData_PagesIterator extends TemplateData_DataIterator {
    
    public function under($path = NULL)
    {
        if ( $this->data === NULL ) $this->data = $this->get_data();

        if ( ! $path )
        {
            return $this;
        }
        else
        {
            $segments = explode('/',$path);
            $path = '';
            $result = $this->data;
            
            foreach( $segments as $segment )
            {
                $path = trim($path.'/'.$segment,'/'); 
                $result = count($result[$path]->children) ? $result[$path]->children : array();
            }
        }
       
        if ( count($result) )
        {
            $self = get_class($this);
            return new $self($result);            
        }
        return array();
    }
    
    protected function get_data()
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
                 );                 

                 $children = $this->arrayize($iterator->getChildren());
            
                 $current['children'] = count($children) ? $children : array();
                 
                 $array[$path] = new Templatedata_Item( 'page', $path, $current );
             }
         }

         return $array;
     }
    
}

/* End of file classes/templatedata/pagesiterator.php */
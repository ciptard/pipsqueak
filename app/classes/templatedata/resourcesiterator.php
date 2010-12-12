<?php defined('APPPATH') or exit('No direct script access allowed');

class TemplateData_ResourcesIterator extends TemplateData_DataIterator {
    
    public function __construct($data = NULL)
    {
        if ( $data && is_array($data) )
        {
            $this->data = $data;   
        }
        elseif ( is_string($data) )
        {
            $this->path = $data;
            $this->data = $this->get_data();
        }
    }
    
    protected function get_data()
    {   
        $iterator = new RecursiveDirectoryIterator($this->path);
        return $this->arrayize($iterator);
    }
    
    protected function arrayize(RecursiveDirectoryIterator $iterator)
     {
         $array = array();

         foreach ( $iterator as $file )
         {
             $path = str_replace(RESOURCESPATH, '', $file->getPathname());
             $segments = explode( DS, $path );
             
             $current = array(
                 'slug'      => end($segments),
                 'path'     => $path
             );
             
             if ( $file->isDir() )
             {
                 $children = $this->arrayize($iterator->getChildren());
 
                 if ( count($children) ) $current['children'] = $children;                             
             }
 
             $array[$path] = $current;    
         }

         return $array;
     }    
}

/* End of file classes/templatedata/resourcesiterator.php */
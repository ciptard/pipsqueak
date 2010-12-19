<?php defined('APPPATH') or exit('No direct script access allowed');

class TemplateData_PagesIterator extends TemplateData_DataIterator {
    
    protected function filter_under( $data, $path )
    {
        if ( $path )
        {            
            $segments = explode('/',$path);
            $path = '';
            $result = $data;
            
            foreach( $segments as $segment )
            {
                $path = trim($path.'/'.$segment,'/'); 
                $result = count($result[$path]->children) ? $result[$path]->children : array();
            }
            
            return $result;
        }
        return $data;
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
             if ( $file->isDir() and $file->getFilename() !== '404' )
             {
                 $path = preg_replace('/\d+?\./','',str_replace(PAGESPATH, '', $file->getPathname()));
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
<?php defined('APPPATH') or exit('No direct script access allowed');

class TemplateData_PagesIterator extends TemplateData_DataIterator {
    
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
                     'path'     => $path
                 );

                 $children = $this->arrayize($iterator->getChildren());

                 if ( count($children) ) $current['children'] = $children;

                 $array[$path] = $current;
             }
         }

         return $array;
     }
    
}

/* End of file classes/templatedata/pagesiterator.php */
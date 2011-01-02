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
        }
    }
    
    protected function filter_under( $data, $path )
    {
        if ( $path )
        {            
            $result = array();
            $path = '/'.trim($path,'/');
            
            foreach( $data as $item_path => $item )
            {
                if ( strpos( $item_path, $path ) === 0 )
                {
                    $result[$item_path] = $item;
                }
            }
            return $result;
        }
        return $data;
    }
    
    protected function filter_sort( $data, $dir )
    {
        if ( strtolower($dir) == 'desc' ) ksort($data);
        return $data;
    }
    
    protected function get_data()
    {   
        $iterator = new RecursiveDirectoryIterator($this->path);
        $data = $this->arrayize($iterator);
        krsort($data);
        return $this->filter($data);
    }
    
    protected function arrayize(RecursiveDirectoryIterator $iterator)
    {
        $files = new RecursiveIteratorIterator($iterator);
        $array = array();
        
        foreach( $files as $file )
        {
            $type = str_replace(RESOURCESPATH, '', $this->path );

            $path = str_replace($this->path, '', $file->getPathname());
            
            $info = pathinfo($path);
            $segments = explode( DS, $path );
             
            $current = array(
                'slug'          => $info['filename'],
                'path'          => $type.$info['dirname'],
                'groups'        => $segments,
            );

            $uripath = str_replace('//','/',$info['dirname'].'/'.$info['filename']);    

            $array[$uripath] = new Templatedata_Item( 'resource', $type.$uripath, $current );  
        }
        
        return $array;
    }    
}

/* End of file classes/templatedata/resourcesiterator.php */
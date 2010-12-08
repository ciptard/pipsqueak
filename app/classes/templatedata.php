<?php defined('APPPATH') or exit('No direct script access allowed');

class TemplateData {
    
    public function pages()
    {
        $iterator = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator(PAGESPATH),
                        RecursiveIteratorIterator::SELF_FIRST);
                        
        foreach($iterator as $fileObject) {
            if( $fileObject->isDir() ) {
                $files[] = str_replace(PAGESPATH, '', $fileObject->getPathname());
            }
        }
        
        return $files;
    }

}

/* End of file classes/data.php */
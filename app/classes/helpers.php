<?php defined('APPPATH') or exit('No direct script access allowed');

class Helpers {
    
    function newer_file_exists( $path, $unixtimestamp )
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach($files as $file)
        {
            if ( $file->getMTime() > $unixtimestamp ) return TRUE;
        }
        return FALSE;
    }
    
    // http://www.php.net/manual/en/function.file-put-contents.php#84180
    function file_force_contents($dir, $contents)
    {
        $parts = explode('/', $dir);
        $file = array_pop($parts);
        $dir = '';
        foreach($parts as $part)
        {
            if(!is_dir($dir .= "/$part")) mkdir($dir);
        }
        file_put_contents("$dir/$file", $contents);
    }
    
}

/* End of file classes/helpers.php */
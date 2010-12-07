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
    
    /*
     * http://www.php.net/manual/en/function.glob.php#101043
     */
    public static function globistr($string = '', $mbEncoding = ''){
        $return = "";
        if($mbEncoding !== ''){
            $string = mb_convert_case($string,MB_CASE_LOWER,$mbEncoding);
        }
        else{
            $string = strtolower($string);
        }
        $mystrlen = strlen($string);
        for($i=0;$i<$mystrlen;$i++){
            if($mbEncoding !== ''){
                $myChar = mb_substr($string,$i,1,$mbEncoding);
                $myUpperChar = mb_convert_case($myChar,MB_CASE_UPPER,$mbEncoding);
            }else{
                $myChar = substr($string,$i,1);
                $myUpperChar = strtoupper($myChar);
            }
            if($myUpperChar !== $myChar){
                $return .= '['.$myChar.$myUpperChar.']'; 
            }else{
                $return .= $myChar;
            }
        }
        return $return;
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
<?php defined('APPPATH') or exit('No direct script access allowed');

class Helpers {
    
    public static function newer_file_exists( $path, $unixtimestamp )
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach($files as $file)
        {
            if ( $file->getMTime() > $unixtimestamp ) return TRUE;
        }
        return FALSE;
    }
    
    // http://www.php.net/manual/en/function.file-put-contents.php#84180
    public static function file_force_contents($dir, $contents)
    {
        $parts = explode('/', $dir);
        $file = array_pop($parts);
        $dir = '';
        foreach($parts as $part)
        {
            if(!is_dir($dir .= "/$part"))
            {
                mkdir($dir);  
                chmod($dir, 0771);
            } 
        }
        file_put_contents("$dir/$file", $contents);
        chmod("$dir/$file", 0644);
    }
    
    public static function parse_contents( $path )
    {
        if ( ! file_exists( $path ) ) return array();
        
        $yml = file_get_contents($path);
        
        $sections = preg_split("/(\+{3,}[\s]{0,}\r?\n)/", $yml);
        
        $section_count = count($sections);
        
        if ( $section_count > 1 )
        {
            // we have some content sections in there
            $yml = $sections[0]; // the YAML block is the first one.
            $sections = array_slice($sections, 1); // all other blocks are content blocks
        }
        else
        {
            $sections = NULL; // no special content sections
        }
                
        $yamlparser = new sfYamlParser();
        $result = $yamlparser->parse($yml);

        if ( is_array($result) )
        {
            foreach( $result as &$item )
            {
                if ( is_string($item) and substr_count($item,"\n") )
                {
                    // need to remove indentation here or the MD parser will think it's all code!
                    $cleanstring = '';
                    $count = 0; 
                 
                    foreach(preg_split("/(\r?\n)/", $item) as $line)
                    {    
                        $cleanstring .= ltrim($line)."\n";
                    }
                    $item = Markdown($cleanstring);
                }
            }
        }
        
        if ( $sections )
        {
            for( $i = 0; $i < count($sections); $i++ )
            {
                $key = 'content'.( $i > 0 ? $i+1 : '' );
                $result[$key] = Markdown($sections[$i]);
            }
        }
        
        return $result;
    }
    
}

/* End of file classes/helpers.php */
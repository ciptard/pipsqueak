<?php defined('APPPATH') or exit('No direct script access allowed');

require_once( APPPATH.'vendor/sfYaml/sfYamlParser.php' );
require_once( APPPATH.'vendor/phpMarkdownExtra/markdown.php' );

class Content {
    
    private static $paths = array();
    
    public static function parse( $path )
    {
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

/* End of file classes/content.php */
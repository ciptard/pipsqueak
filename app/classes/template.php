<?php defined('APPPATH') or exit('No direct script access allowed');

class Template {
    
	private static $instance = false;
	
	private static $loader;
	
	private $twig;
	
	private $template;

	public static function factory()
	{
	    if ( ! self::$instance )
		{
		    require_once( APPPATH.'vendor/Twig/Autoloader.php' );
            Twig_Autoloader::register();

            self::$loader = new Twig_Loader_Filesystem(array(TEMPLATESPATH));
            
			static::$instance = new Template();
		}
		return static::$instance;
	}
    
    private function __construct()
    {
        $this->twig = new Twig_Environment(self::$loader, array(
           'cache' => CACHEPATH.'templates',
           'debug' => Config::get('debug'),
           'auto_reload' => Config::get('cache_level') === Cache::FULL ? FALSE : TRUE,
           'charset' => Config::get('charset'),
        ));
    }
    
    public function set_path( $template_path )
    {
        $this->template = $this->twig->loadTemplate($template_path);
    }
    
    public function render( $content )
    {
        if ( $this->template )
        {
            return $this->template->render($content);
        }
        throw new Exception('Template path has not been set');
    }

}

/* End of file classes/template.php */
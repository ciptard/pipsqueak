<?php defined('APPPATH') or exit('No direct script access allowed');

class Template {
    
	private static $instance = false;
	
	private static $loader;
	
	private $twig;

	public static function factory()
	{
	    if ( ! self::$instance )
		{
		    require_once( APPPATH.'vendor/Twig/Autoloader.php' );
            Twig_Autoloader::register();

            self::$loader = new Template_loader(array(TEMPLATESPATH));
            
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
    
    public function render( $template_path, $content )
    {
        $template = $this->twig->loadTemplate($template_path);
        return $template->render($content);
    }

}

/* End of file classes/template.php */
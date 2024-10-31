<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class CodextentWooRepShortcode {
	
	static $add_script;
	var $wpvepdf_attr = false;

	static function init() {
		
		add_filter('widget_text', 'do_shortcode');
		add_shortcode('cdxwoorp', array(new CodextentWooRepShortcode(), 'cdxfreewoorep_shortcode_fun'));
		add_action('wp_enqueue_scripts', array(__CLASS__, 'register_script_style'));
		add_action('wp_enqueue_scripts', array(__CLASS__, 'print_style'));
		
	}

	public function cdxfreewoorep_shortcode_fun($attr) {
						
		$num_product  = (isset($attr['num_product']))?(int)$attr['num_product']:4;
		$theme      = (isset($attr['theme']))?$attr['theme']:'theme-list-view';
		
		
		switch($theme){
		
			case 'theme-list-view':
				$html     = cdxfreewoorep_theme_list_view($num_product);
			break;	
			case 'theme-hover':
				$html 	= cdxfreewoorep_theme_hover($num_product);
			break;
			default:
				$html     = cdxfreewoorep_theme_list_view($num_product);
			break;	
			
		}
			
		return $html;
						
	}

	static function register_script_style() {
		wp_register_style('cdx-html-lib',  CE_CDXWOOFREEREP_PLUGIN_URL. '/assets/css/ce.css');
		wp_register_style('cdxrep-front',  CE_CDXWOOFREEREP_PLUGIN_URL. '/assets/css/codextent-woo-recently-purchased.css');
	}

	static function print_style(){
		
		wp_enqueue_style('cdx-html-lib');	
		wp_enqueue_style('cdxrep-front');	
	}
	
}

CodextentWooRepShortcode::init();
?>
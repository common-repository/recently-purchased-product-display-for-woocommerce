<?php
/*
Plugin Name: Woocommerce Recently Purchased Product Display
Plugin URI: http://codextent.com
description: Woocommerce Recently Purchased Product Display using Widgets and Shortcode.
Version: 1.0
Author: Codextent
Author URI: http://codextent.com
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//Required Contant
define( 'CE_CDXWOOFREEREP_PLUGIN_PATH',			dirname(__FILE__).'/' );
define( 'CE_CDXWOOFREEREP_PLUGIN_URL',			 untrailingslashit( plugins_url( '/', __FILE__ )) );
define( 'CE_CDXWOOFREEREP_PLUGIN_BASENAME',  		plugin_basename( __FILE__ ));
define( 'CE_CDXWOOFREEREP_VERSION',  				'1.0');

//Templates
require_once(CE_CDXWOOFREEREP_PLUGIN_PATH.'templates.php');
//Shortcode
require_once(CE_CDXWOOFREEREP_PLUGIN_PATH.'shortcode.php');

class CodextentWooRep extends WP_Widget {

	public function __construct() {
		// Instantiate the parent object
		parent::__construct(
			'codextent-woo-rep', // Base ID
			__('Woocommerce Recently Purchased Product Display', 'codextent-woo-rep'), // Name
			array( 'description' => __( 'Woocommerce Recently Purchased products display. Widget will work only for completed order.', 'codextent-woo-rep' ), ) // Args
		);
		
		add_action('wp_enqueue_scripts', array(__CLASS__, 'cdxfreewoorep_register_script_style'));	
		
	}
	
	/**
	 * Scripts for widgets resut HTML.
	 */
	public static function cdxfreewoorep_register_script_style(){
		wp_register_style('cdx-html-lib',  CE_CDXWOOFREEREP_PLUGIN_URL. '/assets/css/ce.css');
		wp_register_style('cdxrep-front',  CE_CDXWOOFREEREP_PLUGIN_URL. '/assets/css/codextent-woo-recently-purchased.css');
		wp_enqueue_style('cdx-html-lib');	
		wp_enqueue_style('cdxrep-front');	
	}
	
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		
		if ( !session_id() )
		add_action( 'init', 'session_start' );	
		
		extract( $args );
		$widget_title = apply_filters('widget_title', $instance['codextent_woo_rep_title']);
				
		if(isset($before_widget)){echo $before_widget;} 
		if ( $widget_title ) {echo $before_title . $widget_title . $after_title; }
		
		//Widget content Start
		$num_product = $instance['codextent_woo_rep_num_products'];
		$theme = $instance['codextent_woo_rep_layout'];
		
		switch($theme){
		
			case 'theme-list-view':
				$html = cdxfreewoorep_theme_list_view($num_product);
			break;	
			case 'theme-hover':
				$html = cdxfreewoorep_theme_hover($num_product);
			break;
			default:
				$html = cdxfreewoorep_theme_list_view($num_product);
			break;	
			
		}
		echo $html;
		//Widget content End
		
		if(isset($after_widgets)){echo $after_widgets;}
		
	}
	
	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		// Output admin widget options form
		
		if ( isset( $instance[ 'codextent_woo_rep_title' ] ) ) { $codextent_woo_rep_title = $instance[ 'codextent_woo_rep_title' ]; } 
		else { $codextent_woo_rep_title = __( '', 'codextent-woo-rep' ); }
		
		
		if ( isset( $instance[ 'codextent_woo_rep_layout' ] ) ) { $codextent_woo_rep_layout = $instance[ 'codextent_woo_rep_layout' ]; } 
		else { $codextent_woo_rep_layout = __( 'theme-list-view', 'codextent-woo-rep' ); }
		
		if ( isset( $instance[ 'codextent_woo_rep_num_products' ] ) ) { $codextent_woo_rep_num_products = $instance[ 'codextent_woo_rep_num_products' ]; } 
		else { $codextent_woo_rep_num_products = __( '', 'codextent-woo-rep' ); }
		?>
        
		<p>
		<label for="<?php echo $this->get_field_id( 'codextent_woo_rep_title' ); ?>"><?php _e( 'Title' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'codextent_woo_rep_title' ); ?>" 
        name="<?php echo $this->get_field_name( 'codextent_woo_rep_title' ); ?>" type="text" value="<?php echo esc_attr( $codextent_woo_rep_title ); ?>">
		</p>
        
        <p>
		<label for="<?php echo $this->get_field_id( 'codextent_woo_rep_num_products' ); ?>"><?php _e( 'Number of Products' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'codextent_woo_rep_num_products' ); ?>" 
        name="<?php echo $this->get_field_name( 'codextent_woo_rep_num_products' ); ?>" type="text" value="<?php echo esc_attr( $codextent_woo_rep_num_products ); ?>">
		</p>
                
        <p>
		<label for="<?php echo $this->get_field_id( 'codextent_woo_rep_layout' ); ?>"><?php _e( 'Layout' ); ?></label> 
		
        <select class="widefat" id="<?php echo $this->get_field_id( 'codextent_woo_rep_layout' ); ?>" name="<?php echo $this->get_field_name( 'codextent_woo_rep_layout' ); ?>" >
        	<option <?php if($codextent_woo_rep_layout=='theme-list-view'){echo 'selected="selected"';} ?> value="theme-list-view">Theme - List View</option>
        	<option <?php if($codextent_woo_rep_layout=='theme-hover'){echo 'selected="selected"';} ?> value="theme-hover">Theme - Hover</option>
        </select>
        
		</p>
		<?php 
		
	}
	
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		// Save widget options
		
		$instance = array();
		$instance['codextent_woo_rep_title'] = ( ! empty( $new_instance['codextent_woo_rep_title'] ) ) ? 
		strip_tags( $new_instance['codextent_woo_rep_title'] ) : '';
		
		$instance['codextent_woo_rep_layout'] = ( ! empty( $new_instance['codextent_woo_rep_layout'] ) ) ? 
		strip_tags( $new_instance['codextent_woo_rep_layout'] ) : '';
		
		$instance['codextent_woo_rep_num_products'] = ( ! empty( $new_instance['codextent_woo_rep_num_products'] ) ) ? 
		strip_tags( $new_instance['codextent_woo_rep_num_products'] ) : '';
		
		return $instance;
	}
	
}

function codextent_woo_rep() {
	register_widget( 'CodextentWooRep' );
}

add_action( 'widgets_init', 'codextent_woo_rep' );
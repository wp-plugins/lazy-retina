<?php
/*
Plugin Name: Lazy Retina
Description: Add retina sizes to Wordpress and lazy load images with unveil.js.
Author: Finn Dohrn
Author URI: http://www.bit01.de
Plugin URI: http://www.bit01.de/blog/lazy-retina/
Version: 1.0.1
*/

defined('ABSPATH') OR exit;

/* Hooks */
add_action(
	'plugins_loaded',
	array(
		'Lazy_Retina',
		'create_instance'
	)
);

add_action('admin_init', 
	array(
	'Settings',
	'lazy_retina_admin_init'
	)
);

register_activation_hook(
	__FILE__,
	array(
		'Lazy_Retina',
		'install'
	)
);

register_deactivation_hook(
	__FILE__,
	array(
		'Lazy_Retina',
		'remove'
	)
);

/* Load the autoloader function */
spl_autoload_register( 'lazy_retina_autoloader' );

function lazy_retina_autoloader( $class ) {
    $class = strtolower( str_replace('_', '-', $class) );
    if ( file_exists ( plugin_dir_path( __FILE__ ) . 'inc/class-' . $class . '.php' ) ){
        include( plugin_dir_path( __FILE__ ) . 'inc/class-' . $class . '.php');
    }
}

?>
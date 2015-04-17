<?php
/*
Plugin Name: popBounce
Plugin URI: http://coldturkeygroup.com/
Description: popBounce is a WordPress plugin build on ouiBounce to help increase conversion rates.
Version: 1.0.1
Author: Cold Turkey Group
Author URI: http://coldturkeygroup.com/
Text Domain: popbounce
*/

if ( ! defined( 'POPBOUNCE_OPTION_KEY' ) )
	define( 'POPBOUNCE_OPTION_KEY', 'popbounce' );

if ( ! defined( 'POPBOUNCE_VERSION_NUM' ) )
	define( 'POPBOUNCE_VERSION_NUM', '1.0.1' );

if ( ! defined( 'POPBOUNCE_VERSION_KEY' ) )
	define( 'POPBOUNCE_VERSION_KEY', POPBOUNCE_OPTION_KEY . '_version' );

// Store the plugin version for upgrades
add_option( POPBOUNCE_VERSION_KEY, POPBOUNCE_VERSION_NUM );

if ( ! defined( 'POPBOUNCE_PLUGIN_NAME' ) )
	define( 'POPBOUNCE_PLUGIN_NAME', 'popBounce' );

if ( ! defined( 'POPBOUNCE_TD' ) )
	define( 'POPBOUNCE_TD', 'popbounce' );

if ( ! defined( 'POPBOUNCE_FILE' ) )
	define( 'POPBOUNCE_FILE', __FILE__ );

if ( ! defined( 'POPBOUNCE_PATH' ) )
	define( 'POPBOUNCE_PATH', plugin_dir_path( __FILE__ ) );

require_once( POPBOUNCE_PATH . 'admin/class-register.php' );

/**
 * Class Popbounce_Init
 *
 * Basic initializer class for popBounce
 */
class Popbounce_Init {

	/**
	 * Load required functions based on admin or frontend
	 */
	function __construct()
	{
		if ( is_admin() ) {
			add_action( 'plugins_loaded', [ $this, 'admin_init' ], 14 );
		} else {
			add_action( 'plugins_loaded', [ $this, 'frontend_init' ], 14 );
		}
	}

	/**
	 * Initialize required admin classes
	 */
	function admin_init()
	{
		require_once( POPBOUNCE_PATH . 'admin/class-admin-options.php' );
		require_once( POPBOUNCE_PATH . 'admin/class-meta.php' );
	}

	/**
	 * Initialize required frontend classes
	 */
	function frontend_init()
	{
		require_once( POPBOUNCE_PATH . 'frontend/class-frontend.php' );
	}

}

new Popbounce_Init();
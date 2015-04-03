<?php

/**
 * Class Popbounce_Register
 *
 * Class for registering our hooks
 */
class Popbounce_Register {

	/**
	 * Constructor for the registration class
	 */
	function __construct()
	{
		register_deactivation_hook( POPBOUNCE_FILE, [ $this, 'plugin_deactivation' ] );
	}

	/**
	 * Clean up settings on deactivation
	 */
	function plugin_deactivation()
	{
		delete_option( POPBOUNCE_VERSION_KEY );
	}
}

new Popbounce_Register();
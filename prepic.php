<?php
/*
Plugin Name: PrePic
Description: A WordPress plugin designed to make it easy to resize, crop, pre-process and cleanup images from media library.
Version:     1.0.0
Author:      Andrei Surdu
 */

// Do not allow direct access to this file.
if ( ! function_exists( 'add_action' ) ) {
	die();
}

if ( ! function_exists( 'prepic' ) ) {
	/*
	-------------------------------------------------------------------------------
	Constants 
	-------------------------------------------------------------------------------
	*/
	define( 'PREPIC_FILE', __FILE__ );
	define( 'PREPIC_PATH', plugin_dir_path( __FILE__ ) );
	define( 'PREPIC_URI', plugin_dir_url( __FILE__ ) );

	/*
	-------------------------------------------------------------------------------
	Load the engine
	-------------------------------------------------------------------------------
	*/
	require_once PREPIC_PATH . 'src/load.php';

	/*
	-------------------------------------------------------------------------------
	Include helper functions
	-------------------------------------------------------------------------------
	*/
	require_once PREPIC_PATH . 'src/functions.php';

	/*
	-------------------------------------------------------------------------------
	Create the admin page
	-------------------------------------------------------------------------------
	*/
	new PrePic\Admin();
}

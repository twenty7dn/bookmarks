<?php
/**
 * Plugin Name: Twenty7 Degrees North Bookmarks Plugin
 * Description: Adds a bookmark block to the editor.
 * Author: David M. Coleman
 * Author URI: https://davidmc.io/
 * Version: 3.0.0
 */
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * Plugin depends on "David M. Coleman — Imgix" and "Advanced Custom Fields Pro"
 * It will be disabled if they're not active
 */
add_action('plugins_loaded', function () {

	if (! class_exists( 'Imgix' ) || ! function_exists( 'get_field' ) ) {

		deactivate_plugins('davidmc-bookmarks/davidmc-bookmarks.php');

		add_action('admin_notices', function() {
			printf(
				'<div class="error"><p>%s</p></div>',
				'<strong>Bookmarks</strong> requires the following plugins:'.
				'<ul><li><a href="https://advancedcustomfields.com/">Advanced Custom Fields Pro</a></li><li><a href="https://github.com/twenty7dn/imgix/">Imgix</a></li></ul>'
			);
		});

	} else {

		require( plugin_dir_path( __FILE__ ) . 'inc/acf.php' ); // Advanced Custom Fields

		// Enqueue styles in the block editor
		function bookmark_editor_styles() {
			add_theme_support( 'editor-styles' );
			add_editor_style( plugin_dir_url( __FILE__ ) . 'assets/css/preview.css' );
		}
		add_action( 'after_setup_theme', 'bookmark_editor_styles', 10, 4 );
		
		// Enqueue styles on the frontend		
		function bookmark_frontend_styles() {
			// wp_enqueue_style( 'bookmark', plugin_dir_url( __FILE__ ) . 'assets/css/block.css', array() );
		}
		add_action( 'wp_enqueue_scripts', 'bookmark_frontend_styles', 10, 4 );

		require( plugin_dir_path( __FILE__ ) . 'inc/class-bookmarks.php' );

		foreach ( glob( plugin_dir_path( __FILE__ ) . 'inc/sites/*.php' ) as $site ) {
			require( $site );
		}

	}

});
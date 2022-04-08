<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * Assets enqueued:
 * 1. blocks.style.build.css - Frontend + Backend.
 * 2. blocks.build.js - Backend.
 * 3. blocks.editor.build.css - Backend.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function megamenu_cgb_block_assets() { // phpcs:ignore
	// Register block styles for both frontend + backend.
	wp_register_style(
		'megamenu-cgb-style-css', // Handle.
		plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
		is_admin() ? array( 'wp-editor' ) : null, // Dependency to include the CSS after it.
		null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
	);

	// Register block editor script for backend.
	wp_register_script(
		'megamenu-cgb-block-js', // Handle.
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ), // Dependencies, defined above.
		null, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
		true // Enqueue the script in the footer.
	);

	// Register block editor styles for backend.
	wp_register_style(
		'megamenu-cgb-block-editor-css', // Handle.
		plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ), // Block editor CSS.
		array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
		null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
	);

	// WP Localized globals. Use dynamic PHP stuff in JavaScript via `cgbGlobal` object.
	wp_localize_script(
		'megamenu-cgb-block-js',
		'cgbGlobal', // Array containing dynamic data for a JS Global.
		array(
			'pluginDirPath' => plugin_dir_path( __DIR__ ),
			'pluginDirUrl'  => plugin_dir_url( __DIR__ ),
			// Add more data here that you want to access from `cgbGlobal` object.
		)
	);

	/**
	 * Register Gutenberg block on server-side.
	 *
	 * Register the block on server-side to ensure that the block
	 * scripts and styles for both frontend and backend are
	 * enqueued when the editor loads.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type#enqueuing-block-scripts
	 * @since 1.16.0
	 */
	register_block_type(
		'cgb/block-megamenu',
		array(
			// Enqueue blocks.style.build.css on both frontend & backend.
			'style'           => 'megamenu-cgb-style-css',
			// Enqueue blocks.build.js in the editor only.
			'editor_script'   => 'megamenu-cgb-block-js',
			// Enqueue blocks.editor.build.css in the editor only.
			'editor_style'    => 'megamenu-cgb-block-editor-css',
			'render_callback' => 'render_megamenu_block',
		)
	);
}

// Hook: Block assets.
add_action( 'init', 'megamenu_cgb_block_assets' );

function render_megamenu_block( $attributes ) {
	// print_r(wp_get_nav_menu_items($attributes['selected_nav']));
	ob_start();
	if ( isset( $attributes['selected_nav'] ) ) {
		/* $wpmm_options = get_option( 'wpmm_options' );
		echo '<pre>';
		print_r($wpmm_options);
		echo '</pre>';

		echo '<pre>';
		print_r(wp_get_nav_menu_object($attributes['selected_nav']));
		echo '</pre>'; */
		echo '<div class="megamenu_wrap">';
		wp_nav_menu( array(
			'menu'   => $attributes['selected_nav'],
			'walker' => new wp_megamenu(),
		) );
		echo '</div>';
		/* echo '<ul class="megamenu_wrap">';
		foreach ( wp_get_nav_menu_items( $attributes['selected_nav'] ) as $nav_item ) {
			echo '<li><a href="' . get_permalink( $nav_item->object_id ) . '">' . $nav_item->title . '</a></li>';
		}
		echo '</ul>'; */
	} else {
		echo 'Select a navigation item.';
	}
	return ob_get_clean();
}


// create new endpoint route
add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'wp/v2',
			'menu',
			array(
				'methods'             => 'GET',
				'callback'            => function() {
					return wp_get_nav_menus();
				},
				'permission_callback' => '__return_true',
			),
		);
	}
);

// create custom function to return nav menu
function custom_wp_menu() {
	// return wp_get_nav_menu_items('MegaMenu');
	return wp_get_nav_menus();
	// return get_nav_menu_locations();
}


<?php
/**
 * Plugin Name: Planet4 - Blocks
 * Description: Creates all the blocks that will be available for usage by Shortcake.
 * Plugin URI: http://github.com/greenpeace/planet4-plugin-blocks
 * Version: 1.38.0
 * Php Version: 7.0
 *
 * Author: Greenpeace International
 * Author URI: http://www.greenpeace.org/
 * Text Domain: planet4-blocks
 *
 * License:     GPLv3
 * Copyright (C) 2018 Greenpeace International
 *
 * @package P4BKS
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || die( 'Direct access is forbidden !' );


/*
========================
	C O N S T A N T S
========================
*/
if ( ! defined( 'P4BKS_REQUIRED_PHP' ) ) {
	define( 'P4BKS_REQUIRED_PHP', '7.0' );
}
if ( ! defined( 'P4BKS_REQUIRED_PLUGINS' ) ) {
	define(
		'P4BKS_REQUIRED_PLUGINS',
		[
			'timber'    => [
				'min_version' => '1.3.0',
				'rel_path'    => 'timber-library/timber.php',
			],
			'shortcake' => [
				'min_version' => '0.7.0',
				'rel_path'    => 'shortcode-ui/shortcode-ui.php',
			],
		]
	);
}
if ( ! defined( 'P4BKS_PLUGIN_BASENAME' ) ) {
	define( 'P4BKS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'P4BKS_PLUGIN_DIRNAME' ) ) {
	define( 'P4BKS_PLUGIN_DIRNAME', dirname( P4BKS_PLUGIN_BASENAME ) );
}
if ( ! defined( 'P4BKS_PLUGIN_DIR' ) ) {
	define( 'P4BKS_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . P4BKS_PLUGIN_DIRNAME );
}
if ( ! defined( 'P4BKS_PLUGIN_NAME' ) ) {
	define( 'P4BKS_PLUGIN_NAME', 'Planet4 - Blocks' );
}
if ( ! defined( 'P4BKS_PLUGIN_SHORT_NAME' ) ) {
	define( 'P4BKS_PLUGIN_SHORT_NAME', 'Blocks' );
}
if ( ! defined( 'P4BKS_PLUGIN_SLUG_NAME' ) ) {
	define( 'P4BKS_PLUGIN_SLUG_NAME', 'blocks' );
}
if ( ! defined( 'P4BKS_INCLUDES_DIR' ) ) {
	define( 'P4BKS_INCLUDES_DIR', P4BKS_PLUGIN_DIR . '/includes/' );
}
if ( ! defined( 'P4BKS_ADMIN_DIR' ) ) {
	define( 'P4BKS_ADMIN_DIR', plugins_url( P4BKS_PLUGIN_DIRNAME . '/admin/' ) );
}
if ( ! defined( 'P4BKS_LANGUAGES' ) ) {
	define(
		'P4BKS_LANGUAGES',
		[
			'en_US' => 'English',
			'el_GR' => 'Ελληνικά',
		]
	);
}
if ( ! defined( 'P4BKS_COVERS_NUM' ) ) {
	define( 'P4BKS_COVERS_NUM', 30 );
}
if ( ! defined( 'P4BKS_ALLOWED_PAGETYPE' ) ) {
	define( 'P4BKS_ALLOWED_PAGETYPE', [ 'page' ] );
}
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	define( 'WP_UNINSTALL_PLUGIN', P4BKS_PLUGIN_BASENAME );
}

require_once __DIR__ . '/vendor/autoload.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';


/*
==========================
	L O A D  P L U G I N
==========================
*/
P4BKS\Loader::get_instance(
	[
		// --- Add here your own Block Controller ---
		'P4BKS\Controllers\Blocks\ContentFourColumn_Controller',
		'P4BKS\Controllers\Blocks\StaticFourColumn_Controller',
		'P4BKS\Controllers\Blocks\TwoColumn_Controller',
		'P4BKS\Controllers\Blocks\Tasks_Controller',
		'P4BKS\Controllers\Blocks\HappyPoint_Controller',
		'P4BKS\Controllers\Blocks\Subheader_Controller',
		'P4BKS\Controllers\Blocks\SplitTwoColumns_Controller',
		'P4BKS\Controllers\Blocks\Media_Controller',
		'P4BKS\Controllers\Blocks\CarouselHeader_Controller',
		'P4BKS\Controllers\Blocks\Covers_Controller',
		'P4BKS\Controllers\Blocks\NewCovers_Controller',
		'P4BKS\Controllers\Blocks\Articles_Controller',
		'P4BKS\Controllers\Blocks\Carousel_Controller',
		'P4BKS\Controllers\Blocks\ContentThreeColumn_Controller',
		'P4BKS\Controllers\Blocks\CampaignThumbnail_Controller',
		'P4BKS\Controllers\Blocks\TakeActionBoxout_Controller',
		'P4BKS\Controllers\Blocks\SubMenu_Controller',
		'P4BKS\Controllers\Blocks\Cookies_Controller',
		'P4BKS\Controllers\Blocks\SocialMedia_Controller',
		'P4BKS\Controllers\Blocks\Counter_Controller',
		'P4BKS\Controllers\Blocks\Gallery_Controller',
		'P4BKS\Controllers\Blocks\Columns_Controller',
	],
	'P4BKS\Views\View',
	'P4BKS\Command\ShortcodeReplacer'
);


/*
==========================
	P L U G I N  R E P O R T
==========================
*/
add_action( 'admin_menu', 'plugin_blocks_report_view' );

/**
 * Adds blocks usage menu in admin.
 */
function plugin_blocks_report_view() {
	add_menu_page( 'Plugin Blocks Usage', 'Plugin Blocks Usage', 'manage_options', 'plugin_blocks_report', 'plugin_blocks_report' );
}

/**
 * Filters array elements on being a shortcake shortcode
 *
 * @param string $shortcode The shortcode.
 * @return bool
 */
function is_shortcake( $shortcode ) {
	$found = strpos( $shortcode, 'shortcake' );
	if ( false !== $found ) {
		return true;
	}
}

/**
 * Finds blocks usage in pages/posts.
 */
function plugin_blocks_report() {
	global $wpdb, $shortcode_tags;

	// Array filtering on shortcake shortcodes.
	$blocks = array_filter( array_keys( $shortcode_tags ), 'is_shortcake' );
	sort( $blocks );

	// phpcs:disable
	foreach ( $blocks as $block ) {
		$block = substr( $block, 10 );
		$shortcode = '%[shortcake_' . $wpdb->esc_like( $block ) . '%';
		$sql       = $wpdb->prepare(
			"SELECT ID, post_title
			FROM `wp_posts` 
			WHERE post_status = 'publish' 
			AND `post_content` LIKE %s
			ORDER BY post_title",
			$shortcode );

		$results = $wpdb->get_results( $sql );

		// Confusion between old and new covers.
		if ( 'covers' === $block ) {
			$block = 'Take Action Covers - Old block';
		}

		echo '<hr>';
		echo '<h2>' . ucfirst( str_replace( '_', ' ', $block ) ) . '</h2>';

		foreach ( $results as $result ) {
			echo '<a href="post.php?post=' . $result->ID . '&action=edit" >' . $result->post_title . '</a>';
			echo '<br>';
		}
	}
	// phpcs:enable
}

/**
 * Finds blocks usage in pages/posts.
 */
function plugin_blocks_report_json() {
	global $wpdb, $shortcode_tags;

	$cache_key = 'plugin-blocks-report';
	$report    = wp_cache_get( $cache_key );

	if ( ! $report ) {
		// Array filtering on shortcake shortcodes.
		$blocks = array_filter( array_keys( $shortcode_tags ), 'is_shortcake' );

		$report = [];

		// phpcs:disable
		foreach ( $blocks as $block ) {

			$block     = substr( $block, 10 );
			$shortcode = '%[shortcake_' . $wpdb->esc_like( $block ) . '%';
			$sql       = $wpdb->prepare(
				"SELECT count(ID) AS cnt
				FROM `wp_posts` 
				WHERE post_status = 'publish' 
				AND `post_content` LIKE %s", $shortcode );

			$results = $wpdb->get_var( $sql );

			$report[ ucfirst( str_replace( '_', ' ', $block ) ) ] = $results;

		}
		wp_cache_add( $cache_key, $report, '', 3600 );

	}
	return $report;

	// phpcs:enable
}


/**
 * Register API route for report of blocks usage in pages/posts.
 */
function plugin_blocks_report_register_rest_route() {
	register_rest_route(
		'plugin_blocks/v1',
		'/plugin_blocks_report/',
		[
			'methods'  => 'GET',
			'callback' => 'plugin_blocks_report_json',
		]
	);
}

add_action( 'rest_api_init', 'plugin_blocks_report_register_rest_route' );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'replace-shortcodes',
		function ( $args ) {

			// Supply a post ID as first argument to update a single, specific post.
			$post_id = $args[0] ?? null;

			try {
				WP_CLI::log( 'Replacing shortcodes...' );

				$updater = new P4BKS\Command\ShortcodeReplacer();
				$updated = $updater->replace_all( $post_id );

				if ( $post_id ) {
					if ( $updated ) {
						WP_CLI::success( "Replaced shortcodes in post $post_id" );
					} else {
						WP_CLI::log( "No shortcodes replaced in post $post_id" );
					}
				} else {
					WP_CLI::success( "Replaced shortcodes in $updated posts" );
				}
			} catch ( \Error $e ) {
				WP_CLI::error( $e->getMessage() );
			} catch ( \Exception $e ) {
				WP_CLI::log( 'Exception: ' . $e->getMessage() );
			}
		}
	);
}

<?php
/**
 * Plugin Name: Planet4 - Blocks
 * Description: Creates all the blocks that will be available for usage by Shortcake.
 * Plugin URI: http://github.com/greenpeace/planet4-plugin-blocks
 * Version: 1.43.0
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
if ( ! defined( 'P4BKS_TEMPLATE_OVERRIDE_SUBDIR' ) ) {
	define( 'P4BKS_TEMPLATE_OVERRIDE_SUBDIR', '/templates/plugins/planet4-plugin-blocks/includes/' );
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
	define( 'P4BKS_ALLOWED_PAGETYPE', [ 'page', 'campaign' ] );
}
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	define( 'WP_UNINSTALL_PLUGIN', P4BKS_PLUGIN_BASENAME );
}

require_once __DIR__ . '/classes/class-loader.php';
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
		'P4BKS\Controllers\Blocks\Timeline_Controller',
		'P4BKS\Controllers\Menu\Settings_Controller',
		'P4BKS\Controllers\Menu\Blocks_Usage_Controller',
	],
	'P4BKS\Views\View',
	'P4BKS\Command\ShortcodeReplacer',
	'P4BKS\Command\Controller'
);

add_action( 'rest_api_init', 'plugin_blocks_report_register_rest_route' );

/**
 * Finds blocks usage in pages/posts.
 */
function plugin_blocks_report_json() {
	global $wpdb, $shortcode_tags;

	$cache_key = 'plugin-blocks-report';
	$report    = wp_cache_get( $cache_key );

	if ( ! $report ) {
		// Array filtering on shortcake shortcodes.
		$blocks = array_filter(
			array_keys( $shortcode_tags ),
			function ( $shortcode ) {
				$found = strpos( $shortcode, 'shortcake' );
				return false !== $found ? true : false;
			}
		);

		$report = [];

		// phpcs:disable
		foreach ( $blocks as $block ) {

			$block     = substr( $block, 10 );
			$shortcode = '%[shortcake_' . $wpdb->esc_like( $block ) . ' %';
			$sql       = $wpdb->prepare(
				"SELECT count(ID) AS cnt
                FROM `wp_posts` 
                WHERE post_status = 'publish' 
                AND `post_content` LIKE %s", $shortcode );
			$results = $wpdb->get_var( $sql );

			$report[ ucfirst( str_replace( '_', ' ', $block ) ) ] = $results;

		}

		// Add to the report a breakdown of different styles for carousel Header
		$sql = "SELECT count(ID) AS cnt
                FROM ". $wpdb->prefix . "posts 
                WHERE post_status = 'publish'
                AND `post_content` REGEXP 'shortcake_carousel_header'
                AND ID NOT IN (SELECT ID
                    FROM ". $wpdb->prefix . "posts 
                    WHERE post_status = 'publish'
                    AND `post_content` REGEXP 'shortcake_carousel_header.*full-width-classic')";
		$cnt = $wpdb->get_var( $sql );
		$report['CarouselHeader-Zoom-And-Slide'] = $cnt;
		$sql = "SELECT count(ID) AS cnt
                FROM ". $wpdb->prefix . "posts 
                WHERE post_status = 'publish'
                AND `post_content` REGEXP 'shortcake_carousel_header.*full-width-classic'";
		$cnt = $wpdb->get_var( $sql );
		$report['CarouselHeader-Full-Width-Classic'] = $cnt;

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

<?php
/**
 * Plugin Name: Planet4 - Blocks
 * Description: Creates all the blocks that will be available for usage by Shortcake.
 * Plugin URI: http://github.com/greenpeace/planet4-plugin-engagingnetworks
 * Version: 0.1.10
 * Php Version: 7.0
 *
 * Author: Greenpeace International
 * Author URI: http://www.greenpeace.org/
 * Text Domain: planet4-blocks
 *
 * License:     GPLv3
 * Copyright (C) 2017 Greenpeace International
 */


// Exit if accessed directly.
defined( 'ABSPATH' ) or die( 'Direct access is forbidden !' );


/* ========================
      C O N S T A N T S
   ======================== */
if ( ! defined( 'P4BKS_REQUIRED_PHP' ) )        define( 'P4BKS_REQUIRED_PHP',        '7.0' );
if ( ! defined( 'P4BKS_REQUIRED_PLUGINS' ) )    define( 'P4BKS_REQUIRED_PLUGINS',    [
	'timber' => [
		'min_version' => '1.3.0',
		'rel_path' => 'timber-library/timber.php',
	],
	'shortcake' => [
		'min_version' => '0.7.0',
		'rel_path' => 'shortcake/shortcode-ui.php',
	],
] );
if ( ! defined( 'P4BKS_PLUGIN_BASENAME' ) )     define( 'P4BKS_PLUGIN_BASENAME',    plugin_basename( __FILE__ ) );
if ( ! defined( 'P4BKS_PLUGIN_DIRNAME' ) )      define( 'P4BKS_PLUGIN_DIRNAME',     dirname( P4BKS_PLUGIN_BASENAME ) );
if ( ! defined( 'P4BKS_PLUGIN_DIR' ) )          define( 'P4BKS_PLUGIN_DIR',         WP_PLUGIN_DIR . '/' . P4BKS_PLUGIN_DIRNAME );
if ( ! defined( 'P4BKS_PLUGIN_NAME' ) )         define( 'P4BKS_PLUGIN_NAME',        'Planet4 - Blocks' );
if ( ! defined( 'P4BKS_PLUGIN_SHORT_NAME' ) )   define( 'P4BKS_PLUGIN_SHORT_NAME',  'Blocks' );
if ( ! defined( 'P4BKS_PLUGIN_SLUG_NAME' ) )    define( 'P4BKS_PLUGIN_SLUG_NAME',   'blocks' );
if ( ! defined( 'P4BKS_INCLUDES_DIR' ) )        define( 'P4BKS_INCLUDES_DIR',       P4BKS_PLUGIN_DIR . '/includes/' );
if ( ! defined( 'P4BKS_ADMIN_DIR' ) )           define( 'P4BKS_ADMIN_DIR',          plugins_url( P4BKS_PLUGIN_DIRNAME . '/admin/' ) );
if ( ! defined( 'P4BKS_LANGUAGES' ) )           define( 'P4BKS_LANGUAGES',          [
	'en_US' => 'English',
	'el_GR' => 'Ελληνικά',
] );
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )       define( 'WP_UNINSTALL_PLUGIN',      P4BKS_PLUGIN_BASENAME );

require_once __DIR__ . '/vendor/autoload.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

/* ==========================
      L O A D  P L U G I N
   ========================== */
P4BKS\P4BKS_Loader::get_instance( [
	'P4BKS\Controllers\Blocks\P4BKS_Blocks_TwoColumn_Controller',
	'P4BKS\Controllers\Blocks\P4BKS_Blocks_HappyPoint_Controller',
	'P4BKS\Controllers\Blocks\P4BKS_Blocks_MediaBlock_Controller',
	'P4BKS\Controllers\Menu\P4BKS_Settings_Controller',
], 'P4BKS\Views\P4BKS_View' );

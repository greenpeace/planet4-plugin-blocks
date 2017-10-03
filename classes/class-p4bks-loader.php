<?php

namespace P4BKS;

if ( ! class_exists( 'P4BKS_Loader' ) ) {

	/**
	 * Class P4BKS_Loader
	 *
	 * This class checks requirements and if all are met then it hooks the plugin.
	 */
	final class P4BKS_Loader {

		/** @var P4BKS_Loader $instance */
		private static $instance;
		/** @var array $services */
		private $services;
		/** @var string $required_php */
		private $required_php = P4BKS_REQUIRED_PHP;
		/** @var array $required_plugins */
		private $required_plugins = P4BKS_REQUIRED_PLUGINS;


		/**
		 * Singleton creational pattern.
		 * Makes sure there is only one instance at all times.
		 *
		 * @param array  $services The Controller services to inject.
		 * @param string $view_class The View class name.
		 *
		 * @return P4BKS_Loader
		 */
		public static function get_instance( $services = array(), $view_class ) : P4BKS_Loader {
			! isset( self::$instance ) and self::$instance = new self( $services, $view_class );
			return  self::$instance;
		}

		/**
		 * Creates the plugin's loader object.
		 * Checks requirements and if its ok it hooks the hook_plugin method on the 'init' action which fires
		 * after WordPress has finished loading but before any headers are sent.
		 * Most of WP is loaded at this stage (but not all) and the user is authenticated.
		 *
		 * @param array  $services The Controller services to inject.
		 * @param string $view_class The View class name.
		 */
		private function __construct( $services = array(), $view_class ) {
			$this->services = $services;
			$view = new $view_class();

			if ( $this->services ) {
				foreach ( $this->services as $service ) {
					( new $service( $view ) )->load();
				}
			}
			$this->check_requirements();
		}

		/**
		 * Hooks the plugin.
		 */
		private function hook_plugin() {
			add_action( 'admin_menu', array( $this, 'load_i18n' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_assets' ) );
			// Provide hook for other plugins.
			do_action( 'p4bks_action_loaded' );
		}

		/**
		 * Checks plugin requirements.
		 * If requirements are met then hook the plugin.
		 */
		private function check_requirements() {

			if ( is_admin() ) {         // If we are on the admin panel.
				// Run the version check. If it is successful, continue with hooking under 'init' the initialization of this plugin.
				if ( $this->check_required_php() ) {
					$plugins = [
						'not_found' => [],
						'not_updated' => [],
					];
					if ( $this->check_required_plugins( $plugins ) ) {
						$this->hook_plugin();
					} elseif ( $plugins['not_found'] || $plugins['not_updated'] ) {

						deactivate_plugins( P4BKS_PLUGIN_BASENAME );
						$count = 0;
						$message = '<div class="error fade">' .
						               '<u>' . esc_html( P4BKS_PLUGIN_NAME ) . ' > ' . esc_html__( 'Requirements Error(s)', 'planet4-blocks' ) . '</u><br /><br />';

						foreach ( $plugins['not_found'] as $plugin ) {
							$message .=     '<br/><strong>' . (++$count) . '. ' . esc_html( $plugin['Name'] ) . '</strong> ' . esc_html__( 'plugin needs to be installed and activated.', 'planet4-blocks' ) . '<br />';
						}
						foreach ( $plugins['not_updated'] as $plugin ) {
							$message .= '<br/><strong>' . (++$count) . '. ' . esc_html( $plugin['Name'] ) . '</strong><br />' .
							            esc_html__( 'Minimum version ', 'planet4-blocks' ) . '<strong>' . esc_html( $plugin['min_version'] ) . '</strong>' .
							            '<br/>' . esc_html__( 'Current version ', 'planet4-blocks' ) . '<strong>' . esc_html( $plugin['Version'] ) . '</strong><br />';
						}

						$message .= '</div><br />';
						wp_die(
							$message, 'Plugin Requirements Error', array(
								'response' => \WP_Http::OK,
								'back_link' => true,
							)
						);
					}
				} else {
					deactivate_plugins( P4BKS_PLUGIN_BASENAME );
					wp_die(
						'<div class="error fade">' .
						'<strong>' . esc_html__( 'PHP Requirements Error', 'planet4-blocks' ) . '</strong><br /><br />' . esc_html( P4BKS_PLUGIN_NAME . __( ' requires a newer version of PHP.', 'planet4-blocks' ) ) . '<br />' .
						'<br/>' . esc_html__( 'Minimum required version of PHP: ', 'planet4-blocks' ) . '<strong>' . esc_html( $this->required_php ) . '</strong>' .
						'<br/>' . esc_html__( 'Running version of PHP: ', 'planet4-blocks' ) . '<strong>' . esc_html( phpversion() ) . '</strong>' .
						'</div>', 'Plugin Requirements Error', array(
							'response' => \WP_Http::OK,
							'back_link' => true,
						)
					);
				}
			}
		}

		/**
		 * Check if the server's php version is less than the required php version.
		 *
		 * @return bool true if version check passed or false otherwise.
		 */
		private function check_required_php() : bool {
			return version_compare( phpversion(), $this->required_php, '>=' );
		}

		/**
		 * Check if the version of a plugin is less than the required version.
		 *
		 * @param array $plugins Will contain information for those plugins whose requirements are not met.
		 *
		 * @return bool true if version check passed or false otherwise.
		 */
		private function check_required_plugins( &$plugins ) : bool {
			$required_plugins = $this->required_plugins;

			if ( is_array( $required_plugins ) && $required_plugins ) {
				foreach ( $required_plugins as $required_plugin ) {
					$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $required_plugin['rel_path'] );

					if ( ! is_plugin_active( $required_plugin['rel_path'] ) ) {
						array_push( $plugins['not_found'], array_merge( $plugin_data, $required_plugin ) );
					} elseif ( ! version_compare( $plugin_data['Version'], $required_plugin['min_version'], '>=' ) ) {
						array_push( $plugins['not_updated'], array_merge( $plugin_data, $required_plugin ) );
					}
				}
				if( count( $plugins ) > 0 ) {
					return false;
				}
			}
			return true;
		}

		/**
		 * Load assets only on the admin pages of the plugin.
		 *
		 * @param string $hook The slug name of the current admin page.
		 */
		public function load_admin_assets( $hook ) {
			// Load the assets only on the plugin's pages.
			if ( strpos( $hook, P4BKS_PLUGIN_SLUG_NAME ) === false ) {
				return;
			}

			wp_enqueue_script( 'p4bks_admin_jquery', '//code.jquery.com/jquery-3.2.1.min.js', array(), '3.2.1', true );
			wp_enqueue_style( 'p4bks_admin_style', P4BKS_ADMIN_DIR . 'css/admin.css', array(), '0.1' );
			wp_enqueue_script( 'p4bks_admin_script', P4BKS_ADMIN_DIR . 'js/admin.js', array(), '0.1', true );
		}

		/**
		 * Load internationalization (i18n) for this plugin.
		 * References: http://codex.wordpress.org/I18n_for_WordPress_Developers
		 */
		public function load_i18n() {
			load_plugin_textdomain( 'planet4-blocks', false, P4BKS_PLUGIN_DIRNAME . '/languages/' );
		}

		/**
		 * Make clone magic method private, so nobody can clone instance.
		 */
		private function __clone() {}

		/**
		 * Make wakeup magic method private, so nobody can unserialize instance.
		 */
		private function __wakeup() {}
	}

} else {
	deactivate_plugins( P4BKS_PLUGIN_BASENAME );
	wp_die(
		'<div class="error fade">' .
		'<u>' . esc_html( P4BKS_PLUGIN_NAME ) . esc_html__( 'Conflict Error', 'planet4-blocks' ) . '</u><br /><br />' . esc_html__( 'Class P4BKS_Loader already exists.', 'planet4-blocks' ) . '<br />' .
		'</div>', 'Plugin Conflict Error', array(
			'response' => \WP_Http::OK,
			'back_link' => true,
		)
	);
}

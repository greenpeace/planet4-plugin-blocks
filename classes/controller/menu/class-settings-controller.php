<?php
/**
 * Settings class
 *
 * @package P4BKS
 * @since 0.1.0
 */

namespace P4BKS\Controllers\Menu;

if ( ! class_exists( 'Settings_Controller' ) ) {

	/**
	 * Class Settings_Controller
	 */
	class Settings_Controller extends Controller {

		/**
		 * Create menu/submenu entry.
		 */
		public function create_admin_menu() {

			$current_user = wp_get_current_user();

			if ( in_array( 'administrator', $current_user->roles, true ) && current_user_can( 'manage_options' ) ) {
				add_menu_page(
					__( 'Blocks', 'planet4-blocks-backend' ),
					__( 'Blocks', 'planet4-blocks-backend' ),
					'manage_options',
					P4BKS_PLUGIN_SLUG_NAME,
					[ $this, 'prepare_settings' ],
					'dashicons-layout'
				);
			}
			add_action( 'admin_init', array( $this, 'register_settings' ) );
		}

		/**
		 * Render the settings page of the plugin.
		 */
		public function prepare_settings() {
			$this->view->settings(
				[
					'settings'            => get_option( 'p4bks_main_settings' ),
					'available_languages' => P4BKS_LANGUAGES,
				]
			);
		}

		/**
		 * Register and store the settings and their data.
		 */
		public function register_settings() {
			$args = array(
				'type'              => 'string',
				'group'             => 'p4bks_main_settings_group',
				'description'       => 'Planet 4 - Blocks settings',
				'sanitize_callback' => [ $this, 'valitize' ],
				'show_in_rest'      => false,
			);
			register_setting( 'p4bks_main_settings_group', 'p4bks_main_settings', $args );
		}

		/**
		 * Validates and sanitizes the settings input.
		 *
		 * @param array $settings The associative array with the settings that are registered for the plugin.
		 *
		 * @return mixed Array if validation is ok, false if validation fails.
		 */
		public function valitize( $settings ) {
			if ( $this->validate( $settings ) ) {
				$this->sanitize( $settings );
			}
			return $settings;
		}

		/**
		 * Validates the settings input.
		 *
		 * @param array $settings The associative array with the settings that are registered for the plugin.
		 *
		 * @return bool
		 */
		public function validate( $settings ) : bool {
			$has_errors = false;
			return ! $has_errors;
		}

		/**
		 * Sanitizes the settings input.
		 *
		 * @param array $settings The associative array with the settings that are registered for the plugin.
		 */
		public function sanitize( &$settings ) {
			if ( $settings ) {
				foreach ( $settings as $name => $setting ) {
					$settings[ $name ] = sanitize_text_field( $setting );
				}
			}
		}

		/**
		 * Loads the saved language.
		 *
		 * @param string $locale Current locale.
		 *
		 * @return string The new locale.
		 */
		public function set_locale( $locale ) : string {
			$main_settings = get_option( 'p4bks_main_settings' );
			return $main_settings['p4bks_lang'] ?? $locale;
		}
	}
}

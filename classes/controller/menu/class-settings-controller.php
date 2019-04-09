<?php
/**
 * Settings class
 *
 * @package P4BKS\Controllers\Menu
 * @since 1.40.0
 */

namespace P4BKS\Controllers\Menu;

use P4BKS\Command\ShortcodeReplacer;

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
		}

		/**
		 * Render the settings page of the plugin.
		 */
		public function prepare_settings() {
			$data      = [];
			$validated = $this->handle_submit( $data );
			if ( $validated ) {
				$this->view->settings( $data );
			}
		}

		/**
		 * Handle form submit.
		 *
		 * @param mixed[] $data The form data.
		 *
		 * @return bool Array if validation is ok, false if validation fails.
		 */
		public function handle_submit( &$data ) : bool {
			// CSRF protection.
			$nonce   = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
			$page_id = filter_input( INPUT_POST, 'p4bks_page_id', FILTER_SANITIZE_NUMBER_INT );

			$data['nonce_action'] = 'convert-blocks-nonce';
			$data['form_submit']  = 0;

			if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
				$data['form_submit'] = 1;

				if ( ! wp_verify_nonce( $nonce, 'convert-blocks-nonce' ) ) {
					$data['message'] = __( 'Nonce verification failed!', 'planet4-blocks-backend' );
					return false;
				} else {
					$data['message'] = $this->replace_shortcodes( [ $page_id ] );
				}
			}
			return true;
		}

		/**
		 * Replace shortcodes in one or more Pages.
		 *
		 * @param array $page_ids Array with the ids of the pages that need shortcode replacements.
		 *
		 * @return string
		 */
		public function replace_shortcodes( $page_ids = [] ) : string {

			// Supply a page ID as first argument to update a single, specific page.
			$page_id = $page_ids[0] ?? null;

			try {
				$replacer = new ShortcodeReplacer();
				try {
					$updated = $replacer->replace_all( $page_id );
				} catch ( \Exception $e ) {
					return __( 'Exception: ', 'planet4-blocks-backend' ) . $e->getMessage();
				}

				if ( $page_id ) {
					if ( $updated ) {
						// translators: %d = The page ID.
						return sprintf( __( 'Replaced shortcodes in page %d', 'planet4-blocks-backend' ), $page_id );
					} else {
						// translators: %d = The page ID.
						return sprintf( __( 'No shortcodes replaced in page %d', 'planet4-blocks-backend' ), $page_id );
					}
				} else {
					// translators: %d = Number of pages that shortcode replacement took place in.
					return sprintf( __( 'Replaced shortcodes in %d pages ', 'planet4-blocks-backend' ), $updated );
				}
			} catch ( \Error $e ) {
				return $e->getMessage();
			} catch ( \Exception $e ) {
				return __( 'Exception: ', 'planet4-blocks-backend' ) . $e->getMessage();
			}
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
	}
}

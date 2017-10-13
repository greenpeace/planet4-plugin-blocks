<?php

namespace P4BKS\Controllers\Blocks;

use P4BKS\Views\View;

if ( ! class_exists( '\\P4BKS\\Controllers\\Blocks\\Controller' ) ) {

	/**
	 * Class Controller
     *
	 * @package P4BKS\Controllers\Blocks
	 */
	abstract class Controller {

		protected $block_name = 'default';

		/** @var View $view */
		protected $view;

		/**
		 * Creates the plugin's controller object.
		 * Avoid putting hooks inside the constructor, to make testing easier.
		 *
		 * @param View $view The view object.
         *
         * @since 0.1.0
		 */
		public function __construct( View $view ) {
			$this->view = $view;
		}

		/**
		 *
         * @since 0.1.0
		 */
		public function load() {
			// Check to see if Shortcake is running, with an admin notice if not.
			add_action( 'init', array( $this, 'shortcode_ui_detection' ) );
			// Register the shortcodes.
			add_action( 'init', array( $this, 'shortcode_ui_register_shortcodes' ) );
			// Add Two Column element in UI
			add_action( 'register_shortcode_ui', array( $this, 'prepare_fields' ) );
		}

		/**
		 * If Shortcake isn't active, then add an administration notice.
		 *
		 * This check is optional. The addition of the shortcode UI is via an action hook that is only called in Shortcake.
		 * So if Shortcake isn't active, you won't be presented with errors.
		 *
		 * Here, we choose to tell users that Shortcake isn't active, but equally you could let it be silent.
		 *
		 * Why not just self-deactivate this plugin? Because then the shortcodes would not be registered either.
		 *
		 * @since 0.1.0
		 */
		public function shortcode_ui_detection() {
			if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
				add_action( 'admin_notices', array( $this, 'shortcode_ui_notices' ) );
			}
		}

		/**
		 * Display an administration notice if the user can activate plugins.
		 *
		 * If the user can't activate plugins, then it's poor UX to show a notice they can't do anything to fix.
		 *
		 * @since 0.1.0
		 */
		public function shortcode_ui_notices() {
			if ( current_user_can( 'activate_plugins' ) ) {
				?>
				<div class="error message">
					<p><?php esc_html_e( 'Shortcode UI plugin must be active for Planet4 - Blocks plugin to work.', 'planet4-blocks' ); ?></p>
				</div>
				<?php
			}
		}

		/**
		 * Register shortcodes
		 *
		 * This registration is done independently of any UI that might be associated with them, so it always happens, even if
		 * Shortcake is not active.
		 *
		 * @since 0.1.0
		 */
		public function shortcode_ui_register_shortcodes() {
			// Define the callback for the advanced shortcode.
			add_shortcode( 'shortcake_' . $this->block_name, array( $this, 'prepare_template' ) );
		}

		/**
		 * Shortcode UI setup for the twocolumn shortcode.
		 *
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 *
		 * This example shortcode has many editable attributes, and more complex UI.
		 *
		 * @since 0.1.0
		 */
		abstract public function prepare_fields();

		/**
		 * Callback for the shortcode.
		 * It renders the shortcode based on supplied attributes.
		 *
		 * @param array  $fields
		 * @param string $content
		 * @param string $shortcode_tag
		 *
		 * @since 0.1.0
		 *
		 * @return string
		 */
		abstract public function prepare_template( $fields, $content, $shortcode_tag ) : string;
	}
}

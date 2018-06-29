<?php

namespace P4BKS\Controllers\Blocks;

use P4BKS\Views\View;

if ( ! class_exists( 'Controller' ) ) {

	/**
	 * Class Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 */
	abstract class Controller {

		/** @const string BLOCK_NAME
		 * The block's name.
		 */
		const BLOCK_NAME = 'default';

		/** @var View $view */
		protected $view;

		/**
		 * Creates the plugin's controller object.
		 * Avoid putting hooks inside the constructor, to make testing easier.
		 *
		 * @param View $view The view object.
		 */
		public function __construct( View $view ) {
			$this->view = $view;
		}

		/**
		 * Hooks all the needed functions to load the block.
		 */
		public function load() {
			// Check to see if Shortcake is running, with an admin notice if not.
			add_action( 'init', array( $this, 'shortcode_ui_detection' ) );
			// Register the shortcodes.
			add_action( 'init', array( $this, 'shortcode_ui_register_shortcodes' ) );
			// Add Two Column element in UI.
			add_action( 'register_shortcode_ui', array( $this, 'prepare_fields' ) );

			// Register an admin render callback for previewing in the wysiwyg.
			add_action( 'wp_ajax_p4bks_preview_render_' . static::BLOCK_NAME, array( $this, 'prepare_admin_preview' ) );
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
		 */
		public function shortcode_ui_notices() {
			if ( current_user_can( 'activate_plugins' ) ) {
				?>
				<div class="error message">
					<p><?php esc_html_e( 'Shortcode UI plugin must be active for Planet4 - Blocks plugin to work.', 'planet4-blocks-backend' ); ?></p>
				</div>
				<?php
			}
		}

		/**
		 * Register shortcodes
		 *
		 * This registration is done independently of any UI that might be associated with them, so it always happens, even if
		 * Shortcake is not active.
		 */
		public function shortcode_ui_register_shortcodes() {

			/**
			 * If we're in a request to admin render shortcodes, use a preview iframe for render so we
			 * can load in all the site styles.
			 *
			 * No clean filters/actions to check here so manually checking for ajax, action and user privs
			 */
			if (
				wp_doing_ajax()
				&& is_user_logged_in()
				&& wp_get_current_user()->has_cap( 'edit_posts' )
				&& isset( $_REQUEST['action'] )
				&& $_REQUEST['action'] === 'bulk_do_shortcode'
			) {
				// Render a preview iframe using a wrapper method.
				add_shortcode( 'shortcake_' . static::BLOCK_NAME, array( $this, 'prepare_template_preview_iframe' ) );
			} else {
				// Render using the default method.
				add_shortcode( 'shortcake_' . static::BLOCK_NAME, array( $this, 'prepare_template' ) );
			}
		}

		/**
		 * Shortcode UI setup for the twocolumn shortcode.
		 *
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 *
		 * This example shortcode has many editable attributes, and more complex UI.
		 */
		abstract public function prepare_fields();

		/**
		 * Output markup of an iframe to render shortcode when previewing in admin edit screen.
		 *
		 * We need to load through iframe to enqueue frontend styles without breaking admin ui
		 *
		 * @param array  $fields         Associative array of shortcode paramaters.
		 * @param string $content        The content of the shortcode block for content wrapper shortcodes only.
		 * @param string $shortcode_tag  The name of the shortcode.
		 * @return string                The html markup for the shortcode preview iframe.
		 */
		public function prepare_template_preview_iframe( $fields, $content, $shortcode_tag ) {
			$preview_args   = [
				'_tag'     => $shortcode_tag,
				'_content' => $content,
				'_post_id' => get_the_ID(),
				'_nonce'   => wp_create_nonce( 'p4bks_preview_render_' . static::BLOCK_NAME ),
				'action'   => 'p4bks_preview_render_' . static::BLOCK_NAME,
			];
			$request_params = ! is_null( $fields ) && ! empty( $fields ) ? array_merge( $fields, $preview_args ) : $preview_args;
			$request        = add_query_arg(
				$request_params,
				admin_url( 'admin-ajax.php' )
			);

			ob_start();
			?>
			<iframe width="100%" src="<?php echo esc_url( $request ); ?>" onload="this.style.height = this.contentWindow.document.body.scrollHeight + 'px';"></iframe>
			<?php
			return ob_get_clean();
		}

		/**
		 * Render preview markup for the shortcode when loading through an iframe.
		 *
		 * Takes a custom admin ajax request to render a shortcode and outputs shortcode
		 * via $this->prepare_template along with wrapper html and enqueued frontend styles and scripts.
		 */
		public function prepare_admin_preview() {

			// Shortcode UI not callable.
			if ( ! is_callable( 'Shortcode_UI', 'get_shortcode' ) ) {
				exit;
			}

			// Don't do anything if an invalid nonce.
			if ( ! wp_verify_nonce( $_GET['_nonce'], 'p4bks_preview_render_' . static::BLOCK_NAME ) ) {
				exit;
			}

			$tag                 = isset( $_GET['_tag'] ) ? sanitize_text_field( $_GET['_tag'] ) : '';
			$content             = isset( $_GET['_content'] ) ? wp_kses_post( $_GET['_content'] ) : '';
			$post_id             = isset( $_GET['_post_id'] ) && is_numeric( $_GET['_post_id'] ) ? absint( $_GET['_post_id'] ) : false;
			$shortcode_object    = \Shortcode_UI::get_instance()->get_shortcode( $tag );
			$shortcode_attrs     = is_array( $shortcode_object ) && is_array( $shortcode_object['attrs'] ) ? $shortcode_object['attrs'] : [];
			$shortcode_attr_keys = wp_list_pluck( $shortcode_attrs, 'attr' );
			$fields              = [];

			// Filter out any $_GET params not used by the shortcode.
			foreach ( $shortcode_attr_keys as $attr_key ) {
				if ( isset( $_GET[ $attr_key ] ) ) {
					$fields[ $attr_key ] = $_GET[ $attr_key ];
				}
			}

			$current_post = get_post( $post_id );

			// Setup postdata incase it's needed during shortcode render.
			if ( $current_post ) {
				// @codingStandardsIgnoreStart
				global $post;
				$post = $current_post;
				setup_postdata( $post );
				// @codingStandardsIgnoreEnd
			}

			?>
			<html>
				<head>
					<?php do_action( 'wp_head' ); ?>
				</head>
				<body style="background-color: transparent;">
					<?php echo $this->prepare_template( $fields, $content, $tag ); ?>
				</body>
				<footer>
					<?php do_action( 'wp_footer' ); ?>
				</footer>
			</html>
			<?php

			// Ajax callbacks need to call exit.
			exit;
		}

		/**
		 * Callback for the shortcode.
		 * It renders the shortcode based on supplied attributes.
		 *
		 * @param array  $fields         Associative array of shortcode paramaters.
		 * @param string $content        The content of the shortcode block for content wrapper shortcodes only.
		 * @param string $shortcode_tag  The name of the shortcode.
		 *
		 * @return string                The html markup for the shortcode preview iframe
		 */
		abstract public function prepare_template( $fields, $content, $shortcode_tag ) : string;
	}
}

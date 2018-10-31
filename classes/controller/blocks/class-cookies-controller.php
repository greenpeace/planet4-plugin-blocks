<?php
/**
 * Cookies block class
 *
 * @link URL
 *
 * @package P4BKS
 * @since 1.9
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'Cookies_Controller' ) ) {

	/**
	 * Class Cookies_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 *
	 * @since 1.9
	 */
	class Cookies_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'cookies';

		/**
		 * Shortcode UI setup for cookies shortcode.
		 *
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 *
		 * This example shortcode has many editable attributes, and more complex UI.
		 *
		 * @since 1.0.0
		 */
		public function prepare_fields() {

			$fields = [
				[
					'label'       => __( 'Cookies title', 'planet4-blocks-backend' ),
					'description' => __( 'Main title for cookies block', 'planet4-blocks-backend' ),
					'attr'        => 'title',
					'type'        => 'text',
				],
				[
					'label'       => __( 'Cookies general description', 'planet4-blocks-backend' ),
					'description' => __( 'Main description for cookies block', 'planet4-blocks-backend' ),
					'attr'        => 'description',
					'type'        => 'text',
				],
				[
					'label'       => __( 'Necessary Cookies Name', 'planet4-blocks-backend' ),
					'description' => __( 'Label for the \'necessary cookies\' checkbox', 'planet4-blocks-backend' ),
					'attr'        => 'necessary_cookies_name',
					'type'        => 'text',
				],
				[
					'label'       => __( 'Necessary Cookies Description', 'planet4-blocks-backend' ),
					'description' => __( 'Description for the \'necessary cookies\' paragraph', 'planet4-blocks-backend' ),
					'attr'        => 'necessary_cookies_description',
					'type'        => 'text',
				],
				[
					'label'       => __( 'All Cookies Name', 'planet4-blocks-backend' ),
					'description' => __( 'Label for the \'all cookies\' checkbox', 'planet4-blocks-backend' ),
					'attr'        => 'all_cookies_name',
					'type'        => 'text',
				],
				[
					'label'       => __( 'All Cookies Description', 'planet4-blocks-backend' ),
					'description' => __( 'Description for the \'all cookies\' paragraph', 'planet4-blocks-backend' ),
					'attr'        => 'all_cookies_description',
					'type'        => 'text',
				],
			];

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				'label'         => __( 'Cookies', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/cookies.png' ) . '" />',
				'attrs'         => $fields,
				'post_type'     => P4BKS_ALLOWED_PAGETYPE,
			];

			shortcode_ui_register_for_shortcode( 'shortcake_' . self::BLOCK_NAME, $shortcode_ui_args );
		}

		/**
		 * Get all the data that will be needed to render the block correctly.
		 *
		 * @param array  $attributes This is the array of fields of this block.
		 * @param string $content This is the post content.
		 * @param string $shortcode_tag The shortcode tag of this block.
		 *
		 * @return array The data to be passed in the View.
		 */
		public function prepare_data( $attributes, $content = '', $shortcode_tag = 'shortcake_' . self::BLOCK_NAME ) : array {
			wp_enqueue_script( 'cookies', P4BKS_ADMIN_DIR . 'js/cookies.js', [], '0.1', true );

			$block_data = [
				'data' => [
					'title'                         => $attributes['title'] ?? '',
					'description'                   => $attributes['description'] ?? '',
					'necessary_cookies_name'        => $attributes['necessary_cookies_name'] ?? '',
					'necessary_cookies_description' => $attributes['necessary_cookies_description'] ?? '',
					'all_cookies_name'              => $attributes['all_cookies_name'] ?? '',
					'all_cookies_description'       => $attributes['all_cookies_description'] ?? '',
				],
			];
			return $block_data;
		}
	}
}


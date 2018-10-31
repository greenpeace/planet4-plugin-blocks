<?php
/**
 * Subheader block class
 *
 * @package P4BKS
 * @since 0.1.14
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'Subheader_Controller' ) ) {

	/**
	 * Class Subheader_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 * @since 0.1.14
	 */
	class Subheader_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'subheader';

		/**
		 * Shortcode UI setup for the subheader shortcode.
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 */
		public function prepare_fields() {
			$fields = array(
				array(
					'label' => __( 'Title', 'planet4-blocks-backend' ),
					'attr'  => 'title',
					'type'  => 'text',
				),
				array(
					'label' => __( 'Description', 'planet4-blocks-backend' ),
					'attr'  => 'description',
					'type'  => 'textarea',
				),
			);

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = array(
				'label'         => __( 'Subheader', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/icon_subheader.png' ) . '" />',
				'attrs'         => $fields,
				'post_type'     => P4BKS_ALLOWED_PAGETYPE,
			);

			shortcode_ui_register_for_shortcode( 'shortcake_' . self::BLOCK_NAME, $shortcode_ui_args );
		}

		/**
		 * Get all the data that will be needed to render the block correctly.
		 *
		 * @param array  $fields This is the array of fields of this block.
		 * @param string $content This is the post content.
		 * @param string $shortcode_tag The shortcode tag of this block.
		 *
		 * @return array The data to be passed in the View.
		 */
		public function prepare_data( $fields, $content = '', $shortcode_tag = 'shortcake_' . self::BLOCK_NAME ) : array {

			$fields = shortcode_atts(
				[
					'title'       => '',
					'description' => '',
				],
				$fields,
				$shortcode_tag
			);

			$data = [
				'fields' => $fields,
			];

			return $data;
		}
	}
}

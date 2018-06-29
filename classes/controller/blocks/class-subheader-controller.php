<?php

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'Subheader_Controller' ) ) {

	/**
	 * Class Subheader_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
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
		 * Callback for the shortcake_twocolumn shortcode.
		 * It renders the shortcode based on supplied attributes.
		 *
		 * @param array  $fields Array of fields that are to be used in the template.
		 * @param string $content The content of the post.
		 * @param string $shortcode_tag The shortcode tag (shortcake_blockname).
		 *
		 * @return string The complete html of the block
		 */
		public function prepare_template( $fields, $content, $shortcode_tag ) : string {

			$fields = shortcode_atts( array(
				'title'       => '',
				'description' => '',
			), $fields, $shortcode_tag );

			$data = [
				'fields' => $fields,
			];

			// Shortcode callbacks must return content, hence, output buffering here.
			ob_start();
			$this->view->block( self::BLOCK_NAME, $data );

			return ob_get_clean();
		}
	}
}

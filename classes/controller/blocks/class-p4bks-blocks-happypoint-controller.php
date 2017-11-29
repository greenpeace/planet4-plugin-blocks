<?php

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'P4BKS_Blocks_HappyPoint_Controller' ) ) {

	/**
	 * Class P4BKS_Blocks_HappyPoint_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 */
	class P4BKS_Blocks_HappyPoint_Controller extends P4BKS_Blocks_Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'happy_point';

		/**
		 * Shortcode UI setup for the happypoint shortcode.
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 */
		public function prepare_fields() {
			$fields = array(
				array(
					'label'       => __( 'Background', 'planet4-blocks' ),
					'attr'        => 'background',
					'type'        => 'attachment',
					'libraryType' => array( 'image' ),
					'addButton'   => __( 'Select Background Image', 'planet4-blocks' ),
					'frameTitle'  => __( 'Select Background Image', 'planet4-blocks' ),
				),
				array(
					'label' => __( 'Opacity % . Number between 1 and 100. If you leave it empty 30 will be used', 'planet4-blocks' ),
					'attr'  => 'opacity',
					'type'  => 'number',
					'meta'  => array(
						'data-test' => 30,
					),
				),
				array(
					'label' => __( 'Boxout Title', 'planet4-blocks' ),
					'attr'  => 'boxout_title',
					'type'  => 'text',
				),
				array(
					'label' => __( 'Boxout Description', 'planet4-blocks' ),
					'attr'  => 'boxout_descr',
					'type'  => 'text',
				),
				array(
					'label' => __( 'Boxout Link Text', 'planet4-blocks' ),
					'attr'  => 'boxout_link_text',
					'type'  => 'text',
				),
				array(
					'label' => __( 'Boxout Link Url', 'planet4-blocks' ),
					'attr'  => 'boxout_link_url',
					'type'  => 'text',
				),
				array(
					'label' => __( 'Use mailing list iframe', 'planet4-blocks' ),
					'attr'  => 'mailing_list_iframe',
					'type'  => 'checkbox',
					'value' => 'true'
				)
			);

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = array(
				'label'         => __( 'Happy Point', 'planet4-blocks' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/icon_happy_point.png' ) . '" />',
				'attrs'         => $fields,
			);

			shortcode_ui_register_for_shortcode( 'shortcake_' . self::BLOCK_NAME, $shortcode_ui_args );
		}

		/**
		 * Callback for the shortcake_twocolumn shortcode.
		 * It renders the shortcode based on supplied attributes.
		 *
		 * @param array $fields Array of fields that are to be used in the template.
		 * @param string $content The content of the post.
		 * @param string $shortcode_tag The shortcode tag (shortcake_blockname).
		 *
		 * @return string The complete html of the block
		 */
		public function prepare_template( $fields, $content, $shortcode_tag ): string {

			$fields = shortcode_atts( array(
				'background'          => '',
				'opacity'             => 30,
				'boxout_title'        => '',
				'boxout_descr'        => '',
				'boxout_link_text'    => '',
				'boxout_link_url'     => '',
				'mailing_list_iframe' => '',
			), $fields, $shortcode_tag );

			if ( ! is_numeric( $fields['opacity'] ) ) {
				$fields['opacity'] = 30;
			}

			$opacity = number_format( ( $fields['opacity'] / 100 ), 1 );

			$fields['background_html']     = wp_get_attachment_image( $fields['background'] );
			$fields['background']          = $this->get_css_image( $fields['background'] );
			$fields['engaging_network_id'] = get_option( 'engaging_network_form_id', '' ) ? get_option( 'engaging_network_form_id' ) : '';
			$fields['opacity']             = $opacity;

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

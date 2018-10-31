<?php
/**
 * Content Three Column block class
 *
 * @package P4BKS
 * @since 0.1.13
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'ContentThreeColumn_Controller' ) ) {

	/**
	 * Class ContentThreeColumn_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 * @since 0.1.13
	 */
	class ContentThreeColumn_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'content_three_column';

		/**
		 * Shortcode UI setup for the ThreeColumn shortcode.
		 *
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 *
		 * @since 0.1.0
		 */
		public function prepare_fields() {
			$fields = [
				[
					'label' => __( 'Title', 'planet4-blocks-backend' ),
					'attr'  => 'title',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Enter title', 'planet4-blocks-backend' ),
					],
				],
				[
					'label' => __( 'Description', 'planet4-blocks-backend' ),
					'attr'  => 'description',
					'type'  => 'textarea',
					'meta'  => [
						'placeholder' => __( 'Enter description', 'planet4-blocks-backend' ),
					],
				],
				[
					'label'       => __( 'Image 1', 'planet4-blocks-backend' ),
					'attr'        => 'image_1',
					'type'        => 'attachment',
					'libraryType' => [ 'image' ],
					'addButton'   => __( 'Select First Image', 'planet4-blocks-backend' ),
					'frameTitle'  => __( 'Select First Image', 'planet4-blocks-backend' ),
				],
				[
					'label'       => __( 'Image 2', 'planet4-blocks-backend' ),
					'attr'        => 'image_2',
					'type'        => 'attachment',
					'libraryType' => [ 'image' ],
					'addButton'   => __( 'Select Second Image', 'planet4-blocks-backend' ),
					'frameTitle'  => __( 'Select Second Image', 'planet4-blocks-backend' ),
				],
				[
					'label'       => __( 'Image 3', 'planet4-blocks-backend' ),
					'attr'        => 'image_3',
					'type'        => 'attachment',
					'libraryType' => [ 'image' ],
					'addButton'   => __( 'Select Third Image', 'planet4-blocks-backend' ),
					'frameTitle'  => __( 'Select Third Image', 'planet4-blocks-backend' ),
				],
			];

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				'label'         => __( 'Three Columns', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/three_columns.png' ) . '" />',
				'attrs'         => $fields,
				'post_type'     => P4BKS_ALLOWED_PAGETYPE,
			];

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

			for ( $i = 1; $i < 4; $i++ ) {
				$img_array = wp_get_attachment_image_src( $fields[ "image_$i" ], 'medium_large' );
				if ( $img_array ) {
					$fields[ "alt_$i" ]   = get_post_meta( $fields[ "image_$i" ], '_wp_attachment_image_alt', true );
					$fields[ "image_$i" ] = $img_array[0];
				}
			}

			$data = [
				'fields' => $fields,
			];
			return $data;
		}
	}
}

<?php

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'ContentThreeColumn_Controller' ) ) {

	/**
	 * Class ContentThreeColumn_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
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
				]
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
		 * Callback for the shortcode.
		 * It renders the shortcode based on supplied attributes.
		 *
		 * @param array  $fields This contains array of all data added.
		 * @param string $content This is the post content.
		 * @param string $shortcode_tag The shortcode block of three column.
		 *
		 * @since 0.1.0
		 *
		 * @return string All the data used for the html.
		 */
		public function prepare_template( $fields, $content, $shortcode_tag ) : string {

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

			// Shortcode callbacks must return content, hence, output buffering here.
			ob_start();
			$this->view->block( self::BLOCK_NAME, $data );

			return ob_get_clean();
		}
	}
}

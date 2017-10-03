<?php

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'P4BKS_Blocks_CarouselSplit_Controller' ) ) {

	/**
	 * Class P4BKS_Blocks_CarouselSplit_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 */
	class P4BKS_Blocks_CarouselSplit_Controller extends P4BKS_Blocks_Controller {


		/**
		 * Override this method in order to give your block its own name.
		 */
		public function load() {
			$this->block_name = 'carousel_split';
			parent::load();
		}

		/**
		 * Shortcode UI setup for the carousel split shortcode.
		 *
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 *
		 * @since 0.1.0
		 */
		public function prepare_fields() {

			// This block will have 4 different columns with same fields.
			$fields = [
				[
					'label'       => __( 'Images', 'shortcode-ui' ),
					'attr'        => 'multiple_images',
					'type'        => 'attachment',
					'libraryType' => [ 'image' ],
					'multiple'    => true,
					'addButton'   => __( 'Select Images for Carousel', 'shortcode-ui' ),
					'frameTitle'  => __( 'Select Images for Carousel', 'shortcode-ui' ),
				],
			];

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				// translators: A block that contains a carousel with split images.
				'label'         => __( 'Carousel Split', 'planet4-blocks' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/icons/carousel_split.png' ) . '" />',
				'attrs'         => $fields,
			];

			shortcode_ui_register_for_shortcode( 'shortcake_' . $this->block_name, $shortcode_ui_args );
		}

		/**
		 * Callback for the carousel split shortcode.
		 * It renders the shortcode based on supplied attributes.
		 *
		 * @param array  $attributes    Defined attributes array for this shortcode.
		 * @param string $content       Content.
		 * @param string $shortcode_tag Shortcode tag name.
		 *
		 * @return string Returns the compiled template.
		 */
		public function prepare_template( $attributes, $content, $shortcode_tag ) : string {

			$images = [];
			$images_ids = explode( ',',$attributes['multiple_images'] );
			foreach ( $images_ids as $image_id ) {

				$temp_array = wp_get_attachment_image_src( $image_id );
				if ( false !== $temp_array && ! empty( $temp_array ) ) {

					$temp_image = wp_prepare_attachment_for_js( $image_id );
					$image = [];
					$image[0] = $temp_image['url'];
					$image[1] = $temp_image['title'];
					$image[2] = $temp_image['caption'];
					$images[] = $image;
				}
			}

			$block_data = [
				'fields'              => $images,
				'available_languages' => P4BKS_LANGUAGES,
				'domain'              => 'planet4-blocks',
			];

			// Shortcode callbacks must return content, hence, output buffering here.
			ob_start();
			$this->view->block( $this->block_name, $block_data );

			return ob_get_clean();
		}
	}
}

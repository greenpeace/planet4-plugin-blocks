<?php

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'CarouselHeader_Controller' ) ) {

	/**
	 * Class CarouselHeader_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 */
	class CarouselHeader_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'carousel_header';

		/**
		 * Shortcode UI setup for carousel header shortcode.
		 *
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 *
		 * This example shortcode has many editable attributes, and more complex UI.
		 *
		 * @since 1.0.0
		 */
		public function prepare_fields() {

			// This block will have 4 different images/content with same fields.
			$fields = [];

			for ( $i = 1; $i < 5; $i++ ) {
				$field   = [
					[
						// translators: placeholder needs to represent the ordinal of the image, eg. 1st, 2nd etc.
						'label'       => sprintf( __( 'Select file for %s image', 'planet4-blocks' ),  $i ),
						'attr'        => 'image_' . $i,
						'type'        => 'attachment',
						'libraryType' => [ 'image' ],
						'addButton'   => __( 'Select Image', 'shortcode-ui' ),
						'frameTitle'  => __( 'Select Image', 'shortcode-ui' ),
					],
					[
						'label' => __( 'Header', 'planet4-blocks' ),
						'attr'  => 'header_' . $i,
						'type'  => 'text',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the image, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter header of %s image', 'planet4-blocks' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					],
					[
						'label' => __( 'Subheader', 'planet4-blocks' ),
						'attr'  => 'subheader_' . $i,
						'type'  => 'text',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the image, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter subheader of %s image', 'planet4-blocks' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					],
					[
						'label' => __( 'Description', 'planet4-blocks' ),
						'attr'  => 'description_' . $i,
						'type'  => 'textarea',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the image, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter description of %s image', 'planet4-blocks' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					],
					[
						'label' => __( 'Text for link', 'planet4-blocks' ),
						'attr'  => 'link_text_' . $i,
						'type'  => 'url',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the image, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter link text for %s image', 'planet4-blocks' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					],
					[
						'label' => __( 'Url for link', 'planet4-blocks' ),
						'attr'  => 'link_url_' . $i,
						'type'  => 'url',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the image, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter link url for %s image', 'planet4-blocks' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					],
				];
				$fields  = array_merge( $fields, $field );
			}

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				// translators: A block that contains 4 different columns each one with title and description.
				'label'         => __( 'Carousel Header', 'planet4-blocks' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/carousel_header.png' ) . '" />',
				'attrs'         => $fields,
			];

			shortcode_ui_register_for_shortcode( 'shortcake_' . self::BLOCK_NAME, $shortcode_ui_args );
		}

		/**
		 * Callback for carousel header shortcode.
		 * It renders the shortcode based on supplied attributes.
		 *
		 * @param array  $attributes    Defined attributes array for this shortcode.
		 * @param string $content       Content.
		 * @param string $shortcode_tag Shortcode tag name.
		 *
		 * @return string Returns the compiled template.
		 */
		public function prepare_template( $attributes, $content, $shortcode_tag ) : string {

			$attributes_temp = [];
			for ( $i = 1; $i < 5; $i++ ) {
				$temp_array      = [
					"header_$i"      => $attributes[ "header_$i" ] ?? '',
					"subheader_$i"   => $attributes[ "header_$i" ] ?? '',
					"description_$i" => $attributes[ "description_$i" ] ?? '',
					"image_$i"       => $attributes[ "image_$i" ] ?? '',
					"link_text_$i"   => $attributes[ "link_text_$i" ] ?? '',
					"link_url_$i"    => $attributes[ "link_url_$i" ] ?? '',
				];
				$attributes_temp = array_merge( $attributes_temp, $temp_array );
			}
			$attributes = shortcode_atts( $attributes_temp, $attributes, $shortcode_tag );

			$total_images = 0;
			for ( $i = 1; $i < 5; $i++ ) {
				$image_id   = $attributes[ "image_$i" ];
				$temp_array = wp_get_attachment_image_src( $image_id, 'p4-extra-large' );
				if ( false !== $temp_array && ! empty( $temp_array ) ) {
					$attributes[ "image_$i" ] = $temp_array[0];
					$total_images++;
				}
				$temp_image                     = wp_prepare_attachment_for_js( $image_id );
				$attributes[ "image_${i}_alt" ] = $temp_image['alt'] ?? '';
			}
			$attributes['total_images'] = $total_images;

			$block_data = [
				'fields' => $attributes,
				'domain' => 'planet4-blocks',
			];

			// Shortcode callbacks must return content, hence, output buffering here.
			ob_start();
			$this->view->block( self::BLOCK_NAME, $block_data );

			return ob_get_clean();
		}
	}
}

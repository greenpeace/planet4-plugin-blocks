<?php
/**
 * Carousel Header block class
 *
 * @package P4BKS
 * @since 0.1.14
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'CarouselHeader_Controller' ) ) {

	/**
	 * Class CarouselHeader_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 * @since 0.1.14
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
				$field  = [
					[
						// translators: placeholder needs to represent the ordinal of the image, eg. 1st, 2nd etc.
						'label'       => sprintf( __( 'Select file for %s image', 'planet4-blocks-backend' ), $i ),
						'attr'        => 'image_' . $i,
						'type'        => 'attachment',
						'libraryType' => [ 'image' ],
						'addButton'   => __( 'Select Image', 'planet4-blocks-backend' ),
						'frameTitle'  => __( 'Select Image', 'planet4-blocks-backend' ),
					],
					[
						'label'   => sprintf(
							// translators: placeholder needs to represent the ordinal of the image, eg. 1st, 2nd etc.
							__( 'Select focus point for image %s', 'planet4-blocks-backend' ),
							$i
						) .
							'<img src="' . esc_url( plugins_url( '/planet4-plugin-blocks/admin/images/grid_9.png' ) ) . '" />',

						'attr'    => 'focus_image_' . $i,
						'type'    => 'select',
						'options' => [
							[
								'value' => 'left top',
								'label' => __( '1 - Top Left', 'planet4-blocks-backend' ),
							],
							[
								'value' => 'center top',
								'label' => __( '2 - Top Center', 'planet4-blocks-backend' ),
							],
							[
								'value' => 'right top',
								'label' => __( '3 - Top Right', 'planet4-blocks-backend' ),
							],
							[
								'value' => 'left center',
								'label' => __( '4 - Middle Left', 'planet4-blocks-backend' ),
							],
							[
								'value' => 'center center',
								'label' => __( '5 - Middle Center', 'planet4-blocks-backend' ),
							],
							[
								'value' => 'right center',
								'label' => __( '6 - Middle Right', 'planet4-blocks-backend' ),
							],
							[
								'value' => 'left bottom',
								'label' => __( '7 - Bottom Left', 'planet4-blocks-backend' ),
							],
							[
								'value' => 'center bottom',
								'label' => __( '8 - Bottom Center', 'planet4-blocks-backend' ),
							],
							[
								'value' => 'right bottom',
								'label' => __( '9 - Bottom Right', 'planet4-blocks-backend' ),
							],
						],
					],
					[
						'label' => __( 'Header', 'planet4-blocks-backend' ),
						'attr'  => 'header_' . $i,
						'type'  => 'text',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the image, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter header of %s image', 'planet4-blocks-backend' ), $i ),
							'data-plugin' => 'planet4-blocks',
							'maxlength'   => 40,
						],
					],
					[
						'label' => __( 'Subheader', 'planet4-blocks-backend' ),
						'attr'  => 'subheader_' . $i,
						'type'  => 'text',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the image, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter subheader of %s image', 'planet4-blocks-backend' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					],
					[
						'label' => __( 'Description', 'planet4-blocks-backend' ),
						'attr'  => 'description_' . $i,
						'type'  => 'textarea',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the image, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter description of %s image', 'planet4-blocks-backend' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					],
					[
						'label' => __( 'Text for link', 'planet4-blocks-backend' ),
						'attr'  => 'link_text_' . $i,
						'type'  => 'url',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the image, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter link text for %s image', 'planet4-blocks-backend' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					],
					[
						'label' => __( 'Url for link', 'planet4-blocks-backend' ),
						'attr'  => 'link_url_' . $i,
						'type'  => 'url',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the image, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter link url for %s image', 'planet4-blocks-backend' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					],
				];
				$fields = array_merge( $fields, $field );
			}

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				// translators: A block that contains 4 different columns each one with title and description.
				'label'         => __( 'Carousel Header', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/carousel_header.png' ) . '" />',
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

			$attributes_temp = [];
			for ( $i = 1; $i < 5; $i++ ) {
				$temp_array      = [
					"header_$i"      => $attributes[ "header_$i" ] ?? '',
					"subheader_$i"   => $attributes[ "subheader_$i" ] ?? '',
					"description_$i" => $attributes[ "description_$i" ] ?? '',
					"image_$i"       => $attributes[ "image_$i" ] ?? '',
					"focus_image_$i" => $attributes[ "focus_image_$i" ] ?? '',
					"link_text_$i"   => $attributes[ "link_text_$i" ] ?? '',
					"link_url_$i"    => $attributes[ "link_url_$i" ] ?? '',
				];
				$attributes_temp = array_merge( $attributes_temp, $temp_array );
			}
			$attributes = shortcode_atts( $attributes, $attributes_temp, $shortcode_tag );

			$total_images = 0;
			for ( $i = 1; $i < 5; $i++ ) {
				if ( array_key_exists( "image_$i", $attributes ) ) {
					$image_id   = $attributes[ "image_$i" ];
					$temp_array = wp_get_attachment_image_src( $image_id, 'retina-large' );
					if ( false !== $temp_array && ! empty( $temp_array ) ) {
						$attributes[ "image_$i" ]          = $temp_array[0];
						$attributes[ "image_${i}_srcset" ] = wp_get_attachment_image_srcset( $image_id, 'retina-large', wp_get_attachment_metadata( $image_id ) );
						$attributes[ "image_${i}_sizes" ]  = wp_calculate_image_sizes( 'retina-large', null, null, $image_id );
						$total_images++;
					}
					$temp_image                     = wp_prepare_attachment_for_js( $image_id );
					$attributes[ "image_${i}_alt" ] = $temp_image['alt'] ?? '';
				}
			}
			$attributes['total_images'] = $total_images;

			$block_data = [
				'fields' => $attributes,
			];
			return $block_data;
		}
	}
}

<?php
/**
 * Carousel Split block class
 *
 * @package P4BKS
 * @since 0.1.11
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'CarouselSplit_Controller' ) ) {

	/**
	 * Class CarouselSplit_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 * @since 0.1.11
	 */
	class CarouselSplit_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'carousel_split';

		/**
		 * Shortcode UI setup for the carousel split shortcode.
		 *
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 *
		 * @since 0.1.0
		 */
		public function prepare_fields() {

			$fields = [
				[
					'label'       => __( 'Images', 'planet4-blocks-backend' ),
					'attr'        => 'multiple_images',
					'type'        => 'attachment',
					'libraryType' => [ 'image' ],
					'multiple'    => true,
					'addButton'   => __( 'Select Images for Carousel', 'planet4-blocks-backend' ),
					'frameTitle'  => __( 'Select Images for Carousel', 'planet4-blocks-backend' ),
				],
			];

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				// translators: A block that contains a carousel with split images.
				'label'         => __( 'Carousel Split', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/carousel_split.png' ) . '" />',
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

			$images          = [];
			$multiple_images = $attributes['multiple_images'] ?? '';
			$images_ids      = explode( ',', $multiple_images );
			foreach ( $images_ids as $image_id ) {

				$temp_array = wp_get_attachment_image_src( $image_id, 'full' );
				if ( false !== $temp_array && ! empty( $temp_array ) ) {

					$temp_image       = wp_prepare_attachment_for_js( $image_id );
					$temp_meta        = wp_get_attachment_metadata( $image_id );
					$image            = [];
					$image['url']     = $temp_image['url'];
					$image['title']   = $temp_image['title'];
					$image['caption'] = $temp_image['caption'];
					$image['alt']     = $temp_image['alt'];
					$image['credit']  = $temp_meta['image_meta']['copyright'];
					$image['srcset']  = wp_get_attachment_image_srcset( $image_id, 'full', wp_get_attachment_metadata( $image_id ) );
					$image['sizes']   = wp_calculate_image_sizes( 'full', null, null, $image_id );
					$images[]         = $image;
				}
			}

			$block_data = [
				'fields' => $images,
			];
			return $block_data;
		}
	}
}

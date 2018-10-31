<?php
/**
 * Cookies block class
 *
 * @package P4BKS
 * @since 1.0.0
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'MediaBlock_Controller' ) ) {

	/**
	 * Class MediaBlock_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 */
	class MediaBlock_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'media_block';

		/**
		 * Shortcode UI setup for the tasks shortcode.
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
					'label'       => __( 'Select Image for the media block', 'planet4-blocks-backend' ),
					'attr'        => 'attachment',
					'type'        => 'attachment',
					'libraryType' => [ 'image' ],
					'addButton'   => __( 'Select Image', 'planet4-blocks-backend' ),
					'frameTitle'  => __( 'Select Image', 'planet4-blocks-backend' ),
				],
			];

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				// translators: A block that displays an image that supports transparency and blends with the background.
				'label'         => __( 'Media block', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/media_block.png' ) . '" />',
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

			$image_id           = $attributes['attachment'];
			$image              = wp_get_attachment_image_src( $image_id, 'full' );
			$fields             = [];
			$fields['image']    = '';
			$fields['alt_text'] = '';

			if ( false !== $image && ! empty( $image ) ) {
				$fields['image']        = $image[0];
				$fields['alt_text']     = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
				$fields['image_srcset'] = wp_get_attachment_image_srcset( $image_id, 'full', wp_get_attachment_metadata( $image_id ) );
				$fields['image_sizes']  = wp_calculate_image_sizes( 'full', null, null, $image_id );
			}

			$block_data = [
				'fields' => $fields,
			];
			return $block_data;
		}
	}
}

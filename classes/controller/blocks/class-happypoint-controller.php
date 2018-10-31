<?php
/**
 * Happy point block class
 *
 * @package P4BKS
 * @since 0.1.14
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'HappyPoint_Controller' ) ) {

	/**
	 * Class HappyPoint_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 * @since 0.1.14
	 */
	class HappyPoint_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'happy_point';

		/**
		 * Shortcode UI setup for the happypoint shortcode.
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 */
		public function prepare_fields() {
			$fields = [
				[
					'label'       => __( 'Background', 'planet4-blocks-backend' ),
					'attr'        => 'background',
					'type'        => 'attachment',
					'libraryType' => [ 'image' ],
					'addButton'   => __( 'Select Background Image', 'planet4-blocks-backend' ),
					'frameTitle'  => __( 'Select Background Image', 'planet4-blocks-backend' ),
				],
				[
					'label'   => __( 'Select focus point for background image', 'planet4-blocks-backend' ) . '<img src="' . esc_url( plugins_url( '/planet4-plugin-blocks/admin/images/grid_9.png' ) ) . '" />',
					'attr'    => 'focus_image',
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
					'label' => __( '<i>We use an overlay to fade the image back. Use a number between 1 and 100,<br /> the higher the number, the more faded the image will look. If you leave this <br/> empty, the default of 30 will be used.</i>', 'planet4-blocks-backend' ),
					'attr'  => 'opacity',
					'type'  => 'number',
					'meta'  => [
						'data-test' => 30,
					],
				],
				[
					'label' => __( 'Use mailing list iframe', 'planet4-blocks-backend' ),
					'attr'  => 'mailing_list_iframe',
					'type'  => 'checkbox',
				],
				[
					'label'       => __( 'Iframe url', 'planet4-blocks-backend' ),
					'attr'        => 'iframe_url',
					'type'        => 'url',
					'encode'      => true,
					'description' => __(
						'If a url is set in this field and the \'mailing list iframe\' option is enabled, it will override the planet4 engaging network setting.',
						'planet4-blocks-backend'
					),
					'meta'        => [
						'placeholder' => __( 'Enter iframe url', 'planet4-blocks-backend' ),
					],
				],
			];

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				'label'         => __( 'Happy Point', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/icon_happy_point.png' ) . '" />',
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

			$shortcode_atts_pairs = [
				'background'          => '',
				'opacity'             => 30,
				'focus_image'         => $fields['focus_image'] ?? 'center center',
				'mailing_list_iframe' => '',
				'iframe_url'          => $fields['iframe_url'] ?? '',
			];

			$fields = shortcode_atts( $shortcode_atts_pairs, $fields, $shortcode_tag );

			if ( ! is_numeric( $fields['opacity'] ) ) {
				$fields['opacity'] = 30;
			}

			$opacity = number_format( ( $fields['opacity'] / 100 ), 1 );

			$options                       = get_option( 'planet4_options' );
			$p4_happy_point_bg_image       = $options['happy_point_bg_image_id'] ?? '';
			$image_id                      = '' !== $fields['background'] ? $fields['background'] : $p4_happy_point_bg_image;
			$img_meta                      = wp_get_attachment_metadata( $image_id );
			$fields['background_src']      = wp_get_attachment_image_src( $image_id, 'retina-large' );
			$fields['background_srcset']   = wp_get_attachment_image_srcset( $image_id, 'retina-large', $img_meta );
			$fields['background_sizes']    = wp_calculate_image_sizes( 'retina-large', null, null, $image_id );
			$fields['engaging_network_id'] = $options['engaging_network_form_id'] ?? '';
			$fields['opacity']             = $opacity;
			$fields['default_image']       = get_bloginfo( 'template_directory' ) . '/images/happy-point-block-bg.jpg';

			$data = [
				'fields' => $fields,
			];
			return $data;
		}
	}
}

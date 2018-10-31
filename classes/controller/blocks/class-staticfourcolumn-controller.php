<?php
/**
 * Static Four column block class
 *
 * @package P4BKS
 * @since 0.1.11
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'StaticFourColumn_Controller' ) ) {

	/**
	 * Class StaticFourColumn_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 * @since 0.1.11
	 */
	class StaticFourColumn_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'static_four_column';

		/**
		 * Shortcode UI setup for static four column shortcode.
		 *
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 *
		 * This example shortcode has many editable attributes, and more complex UI.
		 *
		 * @since 1.0.0
		 */
		public function prepare_fields() {

			// This block will have 4 different columns with same fields.
			$fields = [];
			$field  = [
				[
					'label' => __( 'Title', 'planet4-blocks-backend' ),
					'attr'  => 'title',
					'type'  => 'text',
				],
			];
			$fields = array_merge( $fields, $field );

			for ( $i = 1; $i < 5; $i++ ) {
				$field  = [
					[
						// translators: placeholder needs to represent the ordinal of the column, eg. 1st, 2nd etc.
						'label'       => sprintf( __( 'Select Image for %s column', 'planet4-blocks-backend' ), $i ),
						'attr'        => 'attachment_' . $i,
						'type'        => 'attachment',
						'libraryType' => [ 'image' ],
						'addButton'   => __( 'Select Image', 'planet4-blocks-backend' ),
						'frameTitle'  => __( 'Select Image', 'planet4-blocks-backend' ),
					],
					[
						'label' => __( 'Title', 'planet4-blocks-backend' ),
						'attr'  => 'title_' . $i,
						'type'  => 'text',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the column, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter title of %s column', 'planet4-blocks-backend' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					],
					[
						'label' => __( 'Description', 'planet4-blocks-backend' ),
						'attr'  => 'description_' . $i,
						'type'  => 'textarea',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the column, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter description of %s column', 'planet4-blocks-backend' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					],
					[
						'label' => __( 'Text for link', 'planet4-blocks-backend' ),
						'attr'  => 'link_text_' . $i,
						'type'  => 'url',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the column, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter %s link text', 'planet4-blocks-backend' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					],
					[
						'label' => __( 'Url for link', 'planet4-blocks-backend' ),
						'attr'  => 'link_url_' . $i,
						'type'  => 'url',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the column, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter %s link url', 'planet4-blocks-backend' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					],
				];
				$fields = array_merge( $fields, $field );
			}

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				// translators: A block that contains 4 different columns each one with title and description.
				'label'         => __( 'Static Four Column', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/static_four_column.png' ) . '" />',
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

			$title           = $attributes['title'] ?? '';
			$attributes_temp = [];
			for ( $i = 1; $i < 5; $i++ ) {
				$temp_array      = [
					"title_$i"       => $attributes[ "title_$i" ] ?? '',
					"description_$i" => $attributes[ "description_$i" ] ?? '',
					"attachment_$i"  => $attributes[ "attachment_$i" ] ?? '',
					"link_text_$i"   => $attributes[ "link_text_$i" ] ?? '',
					"link_url_$i"    => $attributes[ "link_url_$i" ] ?? '',
				];
				$attributes_temp = array_merge( $attributes_temp, $temp_array );
			}
			$attributes = shortcode_atts( $attributes_temp, $attributes, $shortcode_tag );

			for ( $i = 1; $i < 5; $i++ ) {
				$temp_array = wp_get_attachment_image_src( $attributes[ "attachment_$i" ], 'thumbnail' );
				if ( false !== $temp_array && ! empty( $temp_array ) ) {
					$attributes[ "attachment_$i" ] = $temp_array[0];
				}
			}

			$block_data = [
				'title'               => $title,
				'fields'              => $attributes,
				'available_languages' => P4BKS_LANGUAGES,
			];
			return $block_data;
		}
	}
}

<?php
/**
 * Columns block class
 *
 * @package P4BKS
 * @since 0.1.3
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'Columns_Controller' ) ) {

	/**
	 * Class Columns_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 * @since 0.1.3
	 */
	class Columns_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'columns';

		const TASK_TEMPLATE_NAME = 'tasks';

		const LAYOUT_NO_IMAGE = 'no_image';
		const LAYOUT_TASKS    = 'tasks';
		const LAYOUT_ICONS    = 'icons';
		const LAYOUT_IMAGES   = 'image';
		const MAX_COLUMNS     = 4;

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
					'attr'        => 'columns_block_style',
					'label'       => __( 'What style of column do you need?', 'planet4-blocks-backend' ),
					'description' => __( 'Change the style of column that you wish to display.', 'planet4-blocks-backend' ),
					'type'        => 'p4_radio',
					'options'     => [
						[
							'value' => static::LAYOUT_NO_IMAGE,
							'label' => __( 'No Image', 'planet4-blocks-backend' ),
							'desc'  => 'Optional headers, description text and buttons in a column display.',
							'image' => esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/columns-no_images.jpg' ),
						],
						[
							'value' => static::LAYOUT_TASKS,
							'label' => __( 'Tasks', 'planet4-blocks-backend' ),
							'desc'  => 'Used on Take Action pages, this display has ordered tasks, and call to action buttons.',
							'image' => esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/columns-tasks.jpg' ),
						],
						[
							'value' => static::LAYOUT_ICONS,
							'label' => __( 'Icons', 'planet4-blocks-backend' ),
							'desc'  => 'For more static content, this display has an icon, header, description and text link.',
							'image' => esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/columns-icons.jpg' ),
						],
						[
							'value' => static::LAYOUT_IMAGES,
							'label' => __( 'Images', 'planet4-blocks-backend' ),
							'desc'  => 'For more static content, this display has an image, header, description and text link.',
							'image' => esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/columns-images.jpg' ),
						],
					],
				],
				[
					'label' => __( 'Title', 'planet4-blocks-backend' ),
					'attr'  => 'columns_title',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Enter block title', 'planet4-blocks-backend' ),
						'data-plugin' => 'planet4-blocks',
					],
				],
				[
					'label' => __( 'Description', 'planet4-blocks-backend' ),
					'attr'  => 'columns_description',
					'type'  => 'textarea',
					'meta'  => [
						'placeholder' => __( 'Enter block description', 'planet4-blocks-backend' ),
						'data-plugin' => 'planet4-blocks',
					],
				],
			];

			// This block will have 4 different columns with same fields.
			for ( $i = 1; $i <= static::MAX_COLUMNS; $i++ ) {

				$fields[] =
					[
						'label' => sprintf(
							// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
							__(
								'<i>In order for the column to appear at least <strong>Header or Body</strong> has to be filled.</i><br /><br />Column %s: Header',
								'planet4-blocks-backend'
							),
							$i
						),
						'attr'  => 'title_' . $i,
						'type'  => 'text',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter header of %s column', 'planet4-blocks-backend' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					];

				$fields[] =
					[
						// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
						'label' => sprintf( __( 'Column %s: Body', 'planet4-blocks-backend' ), $i ),
						'attr'  => 'description_' . $i,
						'type'  => 'textarea',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter body of %s column', 'planet4-blocks-backend' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					];

				$fields[] =
					[
						// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
						'label'       => sprintf( __( 'Column %s: Image', 'planet4-blocks-backend' ), $i ),
						'attr'        => 'attachment_' . $i,
						'type'        => 'attachment',
						'libraryType' => [ 'image' ],
						'addButton'   => __( 'Select Image', 'planet4-blocks-backend' ),
						'frameTitle'  => __( 'Select Image', 'planet4-blocks-backend' ),
					];

				$fields[] =
					[
						// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
						'label' => sprintf( __( 'Column %s: Button/CtA Link', 'planet4-blocks-backend' ), $i ),
						'attr'  => 'link_' . $i,
						'type'  => 'url',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter link for column %s', 'planet4-blocks-backend' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					];

				$fields[] =
					[
						'label'       => __( 'Open link in new tab', 'planet4-blocks-backend' ),
						'attr'        => 'link_new_tab_' . $i,
						// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
						'description' => sprintf( __( 'Open Column %s: Button/CtA Link in a new tab', 'planet4-blocks-backend' ), $i ),
						'type'        => 'checkbox',
						'value'       => 'false',
					];

				$fields[] =
					[
						// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
						'label' => sprintf( __( 'Column %s: Button/CtA Text', 'planet4-blocks-backend' ), $i ),
						'attr'  => 'cta_text_' . $i,
						'type'  => 'text',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter text of button/link for column %s', 'planet4-blocks-backend' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					];
			}

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				// translators: A block that contains different columns each one with title and description and an image.
				'label'         => __( 'Columns', 'planet4-blocks-backend' ),
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

			$attributes_temp = [
				'columns_block_style' => $attributes['columns_block_style'] ?? static::LAYOUT_NO_IMAGE,
				'columns_title'       => $attributes['columns_title'] ?? '',
				'columns_description' => $attributes['columns_description'] ?? '',
			];

			// Used to determine how many columns were set in the backend for this shortcode.
			$columns_set = 0;
			for ( $i = 1; $i <= static::MAX_COLUMNS; $i++ ) {
				$column_atts     = [
					"title_$i"        => $attributes[ "title_$i" ] ?? '',
					"description_$i"  => $attributes[ "description_$i" ] ?? '',
					"attachment_$i"   => $attributes[ "attachment_$i" ] ?? '',
					"cta_text_$i"     => $attributes[ "cta_text_$i" ] ?? '',
					"link_$i"         => $attributes[ "link_$i" ] ?? '',
					"link_new_tab_$i" => $attributes[ "link_new_tab_$i" ] ?? '',
				];
				$attributes_temp = array_merge( $attributes_temp, $column_atts );

				if ( ! empty( $attributes[ "title_$i" ] ) ) {
					$columns_set = $i;
				}
			}
			$attributes                  = shortcode_atts( $attributes_temp, $attributes, $shortcode_tag );
			$attributes['no_of_columns'] = $columns_set;

			// Define the image size that will be used, based on layout chosen and number of columns.
			$columns_block_style = $attributes['columns_block_style'];
			if ( static::LAYOUT_NO_IMAGE !== $columns_block_style ) {

				if ( static::LAYOUT_TASKS === $columns_block_style || static::LAYOUT_IMAGES === $columns_block_style ) {
					if ( $columns_set >= 2 ) {
						$image_size = 'articles-medium-large';
					} else {
						$image_size = 'large';
					}
				} elseif ( static::LAYOUT_ICONS === $columns_block_style ) {
					$image_size = 'thumbnail';
				}
				for ( $i = 1; $i <= static::MAX_COLUMNS; $i ++ ) {
					list( $src ) = wp_get_attachment_image_src( $attributes[ "attachment_$i" ], $image_size );
					if ( $src ) {
						$attributes[ "attachment_$i" ] = $src;
					}
				}
			}

			$block_data = [
				'fields'              => $attributes,
				'available_languages' => P4BKS_LANGUAGES,
			];
			return $block_data;
		}
	}
}

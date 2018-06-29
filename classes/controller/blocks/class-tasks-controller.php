<?php

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'Tasks_Controller' ) ) {

	/**
	 * Class Tasks_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 */
	class Tasks_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'tasks';

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
					'label' => __( 'Tasks Title', 'planet4-blocks-backend' ),
					'attr'  => 'tasks_title',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Enter tasks title', 'planet4-blocks-backend' ),
						'data-plugin' => 'planet4-blocks',
					],
				],
				[
					'label' => __( 'Tasks Description', 'planet4-blocks-backend' ),
					'attr'  => 'tasks_description',
					'type'  => 'textarea',
					'meta'  => [
						'placeholder' => __( 'Enter tasks description', 'planet4-blocks-backend' ),
						'data-plugin' => 'planet4-blocks',
					],
				],
			];

			// This block will have 4 different columns with same fields.
			for ( $i = 1; $i < 5; $i++ ) {

				$fields[] =
					[
						// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
						'label' => sprintf( __( 'Task %s: Title <br> 
									<i>Title is mandatory. In order for the task to be appeared title has to be filled.</i>', 'planet4-blocks-backend' ), $i ),
						'attr'  => 'title_' . $i,
						'type'  => 'text',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter title of %s task/column', 'planet4-blocks-backend' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					];

				$fields[] =
					[
						// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
						'label' => sprintf( __( 'Task %s: Description', 'planet4-blocks-backend' ), $i ),
						'attr'  => 'description_' . $i,
						'type'  => 'textarea',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter description of %s task/column', 'planet4-blocks-backend' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					];

				$fields[] =
					[
						// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
						'label'       => sprintf( __( 'Select Image for %s task/column', 'planet4-blocks-backend' ), $i ),
						'attr'        => 'attachment_' . $i,
						'type'        => 'attachment',
						'libraryType' => [ 'image' ],
						'addButton'   => __( 'Select Image', 'planet4-blocks-backend' ),
						'frameTitle'  => __( 'Select Image', 'planet4-blocks-backend' ),
					];

				$fields[] =
					[
						// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
						'label' => sprintf( __( 'Task %s: Button Text', 'planet4-blocks-backend' ), $i ),
						'attr'  => 'button_text_' . $i,
						'type'  => 'text',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter button text of %s task/column', 'planet4-blocks-backend' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					];

				$fields[] =
					[
						// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
						'label' => sprintf( __( 'Task %s: Button Link', 'planet4-blocks-backend' ), $i ),
						'attr'  => 'button_link_' . $i,
						'type'  => 'url',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the task/column, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter button link of %s task/column', 'planet4-blocks-backend' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					];
			}

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				// translators: A block that contains different columns each one with title and description and an image.
				'label'         => __( 'Take action tasks', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/take_action_tasks.png' ) . '" />',
				'attrs'         => $fields,
				'post_type'     => P4BKS_ALLOWED_PAGETYPE,
			];

			shortcode_ui_register_for_shortcode( 'shortcake_' . self::BLOCK_NAME, $shortcode_ui_args );
		}

		/**
		 * Callback for the tasks shortcode.
		 * It renders the shortcode based on supplied attributes.
		 *
		 * @param array  $attributes    Defined attributes array for this shortcode.
		 * @param string $content       Content.
		 * @param string $shortcode_tag Shortcode tag name.
		 *
		 * @return string Returns the compiled template.
		 */
		public function prepare_template( $attributes, $content, $shortcode_tag ) : string {

			$attributes_temp = [
				'tasks_title'        => $attributes['tasks_title'] ?? '',
				'tasks_description'  => $attributes['tasks_description'] ?? '',
			];
			for ( $i = 1; $i < 5; $i++ ) {
				$temp_array = [
					"title_$i"       => $attributes[ "title_$i" ] ?? '',
					"description_$i" => $attributes[ "description_$i" ] ?? '',
					"attachment_$i"  => $attributes[ "attachment_$i" ] ?? '',
					"button_text_$i" => $attributes[ "button_text_$i" ] ?? '',
					"button_link_$i" => $attributes[ "button_link_$i" ] ?? '',
				];
				$attributes_temp = array_merge( $attributes_temp, $temp_array );
			}
			$attributes = shortcode_atts( $attributes_temp, $attributes, $shortcode_tag );

			for ( $i = 1; $i < 5; $i++ ) {
				$temp_array = wp_get_attachment_image_src( $attributes[ "attachment_$i" ], 'medium' );
				if ( false !== $temp_array && ! empty( $temp_array ) ) {
					$attributes[ "attachment_$i" ] = $temp_array[0];
				}
			}

			$block_data = [
				'fields'              => $attributes,
				'available_languages' => P4BKS_LANGUAGES,
			];

			// Shortcode callbacks must return content, hence, output buffering here.
			ob_start();
			$this->view->block( self::BLOCK_NAME, $block_data );

			return ob_get_clean();
		}
	}
}
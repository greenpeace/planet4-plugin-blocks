<?php

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( '\\P4BKS\\Controllers\\Blocks\\Tasks_Controller' ) ) {

	/**
	 * Class Tasks_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 */
	class Tasks_Controller extends Controller {

		/**
		 * Override this method in order to give your block its own name.
		 */
		public function load() {
			$this->block_name = 'tasks';
			parent::load();
		}

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

			// This block will have 4 different columns with same fields.
			$fields = [];
			for ( $i = 1; $i < 5; $i++ ) {
				$field = [
					[
						'label' => __( 'Title', 'planet4-blocks' ),
						'attr'  => 'title_' . $i,
						'type'  => 'text',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the column, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter title of #%s column', 'planet4-blocks' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					],
					[
						'label' => __( 'Description', 'planet4-blocks' ),
						'attr'  => 'description_' . $i,
						'type'  => 'textarea',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the column, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter description of #%s column', 'planet4-blocks' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					],
					[
						// translators: placeholder needs to represent the ordinal of the column, eg. 1st, 2nd etc.
						'label'       => sprintf( __( 'Select Image for #%s column', 'planet4-blocks' ),  $i ),
						'attr'        => 'attachment_' . $i,
						'type'        => 'attachment',
						'libraryType' => [ 'image' ],
						'addButton'   => __( 'Select Image', 'shortcode-ui' ),
						'frameTitle'  => __( 'Select Image', 'shortcode-ui' ),
					],
					[
						'label' => __( 'Button text', 'planet4-blocks' ),
						'attr'  => 'button_text_' . $i,
						'type'  => 'text',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the column, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter button text of #%s column', 'planet4-blocks' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					],
					[
						'label' => __( 'Button link', 'planet4-blocks' ),
						'attr'  => 'button_link_1' . $i,
						'type'  => 'url',
						'meta'  => [
							// translators: placeholder needs to represent the ordinal of the column, eg. 1st, 2nd etc.
							'placeholder' => sprintf( __( 'Enter button link of #%s column', 'planet4-blocks' ), $i ),
							'data-plugin' => 'planet4-blocks',
						],
					],
				];
				$fields = array_merge( $fields, $field );
			}

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				// translators: A block that contains different columns each one with title and description and an image.
				'label'         => __( 'Take action tasks', 'planet4-blocks' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/take_action_tasks.png' ) . '" />',
				'attrs'         => $fields,
			];

			shortcode_ui_register_for_shortcode( 'shortcake_' . $this->block_name, $shortcode_ui_args );
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

			$attributes_temp = [];
			for ( $i = 1; $i < 5; $i++ ) {
				$temp_array = [
					"title_$i"       => $attributes[ "title_$i" ],
					"description_$i" => wpautop( $attributes[ "description_$i" ] ),
					"attachment_$i"  => $attributes[ "attachment_$i" ],
					"button_text_$i" => $attributes[ "button_text_$i" ],
					"button_link_$i" => $attributes[ "button_link_$i" ],
				];
				$attributes_temp = array_merge( $attributes_temp, $temp_array );
			}
			$attributes = shortcode_atts( $attributes_temp, $attributes, $shortcode_tag );

			for ( $i = 1; $i < 5; $i++ ) {
				$temp_array = wp_get_attachment_image_src( $attributes[ "attachment_$i" ] );
				if ( false !== $temp_array && ! empty( $temp_array ) ) {
					$attributes[ "attachment_$i" ] = $temp_array[0];
				}
			}

			$block_data = [
				'fields'              => $attributes,
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
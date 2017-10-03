<?php

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'P4BKS_Blocks_TwoColumn_Controller' ) ) {

	class P4BKS_Blocks_TwoColumn_Controller extends P4BKS_Blocks_Controller {


		/**
		 * Override this method in order to give your block its own name.
		 */
		public function load() {
			// --- Set here the name of your block ---
			$this->block_name = 'two_columns';
			parent::load();
		}

		/**
		 * Shortcode UI setup for the twocolumn shortcode.
		 *
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 *
		 * @since 0.1.0
		 */
		public function prepare_fields() {
			/*
			 * Define the UI for attributes of the shortcode. Optional.
			 *
			 * If no UI is registered for an attribute, then the attribute will
			 * not be editable through Shortcake's UI. However, the value of any
			 * unregistered attributes will be preserved when editing.
			 *
			 * Each array must include 'attr', 'type', and 'label'.
			 * * 'attr' should be the name of the attribute.
			 * * 'type' options include: text, checkbox, textarea, radio, select, email,
			 *     url, number, and date, post_select, attachment, color.
			 * * 'label' is the label text associated with that input field.
			 *
			 * Use 'meta' to add arbitrary attributes to the HTML of the field.
			 *
			 * Use 'encode' to encode attribute data. Requires customization in shortcode callback to decode.
			 *
			 * Depending on 'type', additional arguments may be available.
			 */
			$fields = array(
				// First column fields.
				array(
					'label' => esc_html__( 'Title', 'planet4-blocks' ),
					'attr'  => 'title_1',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => esc_html__( 'Enter title of first column', 'planet4-blocks' ),
						'data-plugin' => 'planet4-blocks',
					),
				),
				array(
					'label' => esc_html__( 'Description', 'planet4-blocks' ),
					'attr'  => 'description_1',
					'type'  => 'textarea',
					'meta'  => array(
						'placeholder' => esc_html__( 'Enter description of first column', 'planet4-blocks' ),
						'data-plugin' => 'planet4-blocks',
					),
				),
				array(
					'label' => __( 'Button text', 'planet4-blocks' ),
					'attr'  => 'button_text_1',
					'type'  => 'text',
					'value' => __( 'Find a way to make change', 'planet4-blocks'),
					'meta'  => array(
						'data-plugin' => 'planet4-blocks',
					),
				),
				array(
					'label' => __( 'Button link', 'planet4-blocks' ),
					'attr'  => 'button_link_1',
					'type'  => 'url',
					'meta'  => array(
						'placeholder' => esc_html__( 'Enter button link of first column', 'planet4-blocks' ),
						'data-plugin' => 'planet4-blocks',
					),
				),

				// Second column fields.
				array(
					'label' => esc_html__( 'Title', 'planet4-blocks' ),
					'attr'  => 'title_2',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => esc_html__( 'Enter title of second column', 'planet4-blocks' ),
					),
				),
				array(
					'label' => esc_html__( 'Description', 'planet4-blocks' ),
					'attr'  => 'description_2',
					'type'  => 'textarea',
					'meta'  => array(
						'placeholder' => esc_html__( 'Enter description of second column', 'planet4-blocks' ),
					),
				),
				array(
					'label' => __( 'Button text', 'planet4-blocks' ),
					'attr'  => 'button_text_2',
					'type'  => 'text',
					'value' => __( 'Explore the issues', 'planet4-blocks'),
					'meta'  => array(
						'data-plugin' => 'planet4-blocks',
					),
				),
				array(
					'label' => __( 'Button link', 'planet4-blocks' ),
					'attr'  => 'button_link_2',
					'type'  => 'url',
					'meta'  => array(
						'placeholder' => esc_html__( 'Enter button link of second column', 'planet4-blocks' ),
						'data-plugin' => 'planet4-blocks',
					),
				),
			);

			/*
			 * Define the Shortcode UI arguments.
			 */
			$shortcode_ui_args = array(
				/*
				 * How the shortcode should be labeled in the UI. Required argument.
				 */
				'label' => esc_html__( 'Two Columns', 'planet4-blocks' ),

				/*
				 * Include an icon with your shortcode. Optional.
				 * Use a dashicon, or full HTML (e.g. <img src="/path/to/your/icon" />).
				 */
				'listItemImage' => 'dashicons-editor-table',

				/*
				 * Define the UI for attributes of the shortcode. Optional.
				 * See above, to where the the assignment to the $fields variable was made.
				 */
				'attrs' => $fields,
			);

			shortcode_ui_register_for_shortcode( 'shortcake_' . $this->block_name, $shortcode_ui_args );
		}

		/**
		 * Callback for the shortcode.
		 * It renders the shortcode based on supplied attributes.
		 *
		 * @param array  $fields Array of fields that are to be used in the template.
		 * @param string $content The content of the post.
		 * @param string $shortcode_tag The shortcode tag (shortcake_two_columns).
		 *
		 * @since 0.1.0
		 *
		 * @return string
		 */
		public function prepare_template( $fields, $content, $shortcode_tag ) : string {

			$data = [
				'fields' => array_map( 'wp_kses_post', $fields),
			];
			// Shortcode callbacks must return content, hence, output buffering here.
			ob_start();
			$this->view->block( $this->block_name, $data );

			return ob_get_clean();
		}
	}
}

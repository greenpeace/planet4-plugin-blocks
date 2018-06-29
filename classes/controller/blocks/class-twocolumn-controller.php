<?php

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'TwoColumn_Controller' ) ) {

	/**
	 * Class TwoColumn_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 */
	class TwoColumn_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'two_columns';

		/**
		 * Shortcode UI setup for the twocolumn shortcode.
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
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
			$fields = [
				// First column fields.
				[
					'label' => __( 'Title', 'planet4-blocks-backend' ),
					'attr'  => 'title_1',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Enter title of first column', 'planet4-blocks-backend' ),
						'data-plugin' => 'planet4-blocks',
					],
				],
				[
					'label' => __( 'Description', 'planet4-blocks-backend' ),
					'attr'  => 'description_1',
					'type'  => 'textarea',
					'meta'  => [
						'placeholder' => __( 'Enter description of first column', 'planet4-blocks-backend' ),
						'data-plugin' => 'planet4-blocks',
					],
				],
				[
					'label' => __( 'Button text', 'planet4-blocks-backend' ),
					'attr'  => 'button_text_1',
					'type'  => 'text',
					'meta'  => [
						'data-plugin' => 'planet4-blocks',
					],
				],
				[
					'label' => __( 'Button link', 'planet4-blocks-backend' ),
					'attr'  => 'button_link_1',
					'type'  => 'url',
					'meta'  => [
						'placeholder' => esc_html__( 'Enter button link of first column', 'planet4-blocks-backend' ),
						'data-plugin' => 'planet4-blocks',
					],
				],

				// Second column fields.
				[
					'label' => __( 'Title', 'planet4-blocks-backend' ),
					'attr'  => 'title_2',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Enter title of second column', 'planet4-blocks-backend' ),
					],
				],
				[
					'label' => __( 'Description', 'planet4-blocks-backend' ),
					'attr'  => 'description_2',
					'type'  => 'textarea',
					'meta'  => [
						'placeholder' => __( 'Enter description of second column', 'planet4-blocks-backend' ),
					],
				],
				[
					'label' => __( 'Button text', 'planet4-blocks-backend' ),
					'attr'  => 'button_text_2',
					'type'  => 'text',
					'meta'  => [
						'data-plugin' => 'planet4-blocks',
					],
				],
				[
					'label' => __( 'Button link', 'planet4-blocks-backend' ),
					'attr'  => 'button_link_2',
					'type'  => 'url',
					'meta'  => [
						'placeholder' => __( 'Enter button link of second column', 'planet4-blocks-backend' ),
						'data-plugin' => 'planet4-blocks',
					],
				],
			];

			/*
			 * Define the Shortcode UI arguments.
			 */
			$shortcode_ui_args = [

				/*
				 * How the shortcode should be labeled in the UI. Required argument.
				 */
				'label' => __( 'Two Columns', 'planet4-blocks-backend' ),

				/*
				 * Include an icon with your shortcode. Optional.
				 * Use a dashicon, or full HTML (e.g. <img src="/path/to/your/icon" />).
				 */
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/' . self::BLOCK_NAME . '.png' ) . '" />',

				/*
				 * Define the UI for attributes of the shortcode. Optional.
				 * See above, to where the the assignment to the $fields variable was made.
				 */
				'attrs' => $fields,
				'post_type' => P4BKS_ALLOWED_PAGETYPE,
			];

			shortcode_ui_register_for_shortcode( 'shortcake_' . self::BLOCK_NAME, $shortcode_ui_args );
		}

		/**
		 * Callback for the shortcode.
		 * It renders the shortcode based on supplied attributes.
		 *
		 * @param array  $fields Array of fields that are to be used in the template.
		 * @param string $content The content of the post.
		 * @param string $shortcode_tag The shortcode tag (shortcake_two_columns).
		 *
		 * @return string The html markup for the block.
		 */
		public function prepare_template( $fields, $content, $shortcode_tag ) : string {

			$data = [
				'fields' => $fields,
			];
			// Shortcode callbacks must return content, hence, output buffering here.
			ob_start();
			$this->view->block( self::BLOCK_NAME, $data );

			return ob_get_clean();
		}
	}
}

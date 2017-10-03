<?php

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'P4BKS_Blocks_MediaBlock_Controller' ) ) {

	/**
	 * Class P4BKS_Blocks_MediaBlock_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 */
	class P4BKS_Blocks_MediaBlock_Controller extends P4BKS_Blocks_Controller {

		/**
		 * Override this method in order to give your block its own name.
		 */
		public function load() {
			$this->block_name = 'media_block';
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

			$fields = [
				[
					'label'       => __( 'Select Image for the media block', 'planet4-blocks' ),
					'attr'        => 'attachment',
					'type'        => 'attachment',
					'libraryType' => [ 'image' ],
					'addButton'   => __( 'Select Image', 'shortcode-ui' ),
					'frameTitle'  => __( 'Select Image', 'shortcode-ui' ),
				],
			];

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				// translators: A block that displays an image that supports transparency and blends with the background.
				'label'         => __( 'Media block', 'planet4-blocks' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/icons/media_block.png' ) . '" />',
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
		public function prepare_template( $attributes, $content, $shortcode_tag ): string {

			$temp_array = wp_get_attachment_image_src( $attributes['attachment'] );
			if ( false !== $temp_array && ! empty( $temp_array ) ) {
				$attributes['attachment'] = $temp_array[0];
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

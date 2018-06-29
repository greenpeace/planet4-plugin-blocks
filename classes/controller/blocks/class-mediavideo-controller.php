<?php

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'MediaVideo_Controller' ) ) {

	/**
	 * Class MediaVideo_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 */
	class MediaVideo_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'media_video';

		/**
		 * Shortcode UI setup for the Mediavideo shortcode.
		 *
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 *
		 * @since 0.1.0
		 */
		public function prepare_fields() {
			$fields = array(
				array(
					'label' => __( 'Video Title', 'planet4-blocks-backend' ),
					'attr'  => 'video_title',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => __( 'Enter video title', 'planet4-blocks-backend' ),
					),
				),
				array(
					'label' => __( 'Youtube ID', 'planet4-blocks-backend' ),
					'attr'  => 'youtube_id',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => __( 'Enter youtube video id', 'planet4-blocks-backend' ),
					),
				)
			);

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = array(
				'label'         => __( 'Youtube Video', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/media_video.jpg' ) . '" />',
				'attrs'         => $fields,
				'post_type'     => P4BKS_ALLOWED_PAGETYPE,
			);

			shortcode_ui_register_for_shortcode( 'shortcake_' . self::BLOCK_NAME, $shortcode_ui_args );
		}

		/**
		 * Callback for the shortcode.
		 * It renders the shortcode based on supplied attributes.
		 *
		 * @param array  $fields This contains array of all data added.
		 * @param string $content This is the post content.
		 * @param string $shortcode_tag The shortcode block of mediavideo.
		 *
		 * @since 0.1.0
		 *
		 * @return string All the data used for the html.
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

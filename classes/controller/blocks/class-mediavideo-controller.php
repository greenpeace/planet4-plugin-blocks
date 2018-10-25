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
			$fields = [
				[
					'label' => __( 'Video Title', 'planet4-blocks-backend' ),
					'attr'  => 'video_title',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Enter video title', 'planet4-blocks-backend' ),
					],
				],
				[
					'label'       => __( 'Video URL/Youtube ID', 'planet4-blocks-backend' ),
					'attr'        => 'youtube_id',
					'type'        => 'text',
					'description' => __( 'Allowed media type in Video URL - video/mp4.' ),
					'meta'        => [
						'placeholder' => __( 'Enter Video URL or Youtube video id', 'planet4-blocks-backend' ),
					],
				],
				[
					'label'       => __( 'Video poster image [Optional]', 'planet4-blocks-backend' ),
					'attr'        => 'video_poster_img',
					'type'        => 'attachment',
					'libraryType' => [ 'image' ],
					'addButton'   => __( 'Select Video Poster Image', 'planet4-blocks-backend' ),
					'frameTitle'  => __( 'Select Video Poster Image', 'planet4-blocks-backend' ),
					'description' => __( 'Applicable for non youtube video only.' ),
				],
			];

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				'label'         => __( 'Video block', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/media_video.jpg' ) . '" />',
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

			// Check video url.
			if ( false === strstr( $fields['youtube_id'], '/' ) ) {
				// Case 1 : Youtube video id.
				$fields['is_youtube_video'] = true;
			} else {
				if ( preg_match( '/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/i', $fields['youtube_id'], $matches ) ) {
					// Case 2 : Youtube video URL.
					if ( isset( $matches[5] ) && $matches[5] ) {
						// Extract youtube video ID and use with youtube embed url format.
						$fields['youtube_id']       = $matches[5];
						$fields['is_youtube_video'] = true;
					}
				} else {
					// Case 3 : Video URL other than Youtube (GP media library video etc).
					$fields['is_youtube_video'] = false;
					if ( $fields['video_poster_img'] ) {
						$fields['video_poster_img_src'] = wp_get_attachment_image_src( $fields['video_poster_img'], 'large' );
					} else {
						$fields['video_poster_img_src'] = '';
					}
				}
			}

			$data = [
				'fields' => $fields,
			];
			return $data;
		}
	}
}

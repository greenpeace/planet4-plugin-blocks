<?php
/**
 * Media block class
 *
 * @package P4BKS
 * @since 0.1.13
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'Media_Controller' ) ) {

	/**
	 * Class Media_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 * @since 0.1.13
	 */
	class Media_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'media_video';

		/**
		 * Shortcode UI setup for the Media shortcode.
		 *
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 *
		 * @since 0.1.0
		 */
		public function prepare_fields() {
			$fields = [
				[
					'label' => __( 'Media Title', 'planet4-blocks-backend' ),
					'attr'  => 'video_title',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Enter video title', 'planet4-blocks-backend' ),
					],
				],
				[
					'label'       => __( 'Media URL/ID', 'planet4-blocks-backend' ),
					'attr'        => 'youtube_id',
					'type'        => 'text',
					'description' => __( 'Can be a YouTube, Vimeo or Soundcloud URL or an mp4, mp3 or wav file URL.', 'planet4-blocks-backend' ),
					'meta'        => [
						'placeholder' => __( 'Enter URL', 'planet4-blocks-backend' ),
					],
				],
				[
					'label'       => __( 'Video poster image [Optional]', 'planet4-blocks-backend' ),
					'attr'        => 'video_poster_img',
					'type'        => 'attachment',
					'libraryType' => [ 'image' ],
					'addButton'   => __( 'Select Video Poster Image', 'planet4-blocks-backend' ),
					'frameTitle'  => __( 'Select Video Poster Image', 'planet4-blocks-backend' ),
					'description' => __( 'Applicable for .mp4 video URLs only.', 'planet4-blocks-backend' ),
				],
			];

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				'label'         => __( 'Media block', 'planet4-blocks-backend' ),
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
			$media_url        = $fields['youtube_id'];
			$url_path_segment = wp_parse_url( $media_url, PHP_URL_PATH );

			// Assume that a non-URL is a YouTube video ID, for back compat.
			if ( false === strstr( $media_url, '/' ) ) {
				$media_url = "https://www.youtube.com/watch?v={$media_url}";
			}

			if ( preg_match( '/^(https?)?:\/\/soundcloud.com\//i', $media_url ) ) {
				// Soundcloud track URL (differentiated for styling purposes).
				$type       = 'audio';
				$embed_html = wp_oembed_get( $media_url );
			} elseif ( preg_match( '/\.mp4$/', $url_path_segment ) ) {
				// Bare video URL.
				$type   = 'video';
				$poster = empty( $fields['video_poster_img'] )
					? '' : wp_get_attachment_image_src( $fields['video_poster_img'], 'large' );

				$embed_html = wp_video_shortcode(
					[
						'src'    => $media_url,
						'poster' => $poster[0],
					]
				);
			} elseif ( preg_match( '/\.(mp3|wav|ogg)$/', $url_path_segment ) ) {
				// Bare audio URL.
				$type       = 'audio';
				$embed_html = wp_audio_shortcode( [ 'src' => $media_url ] );
			} else {
				$type       = 'video';
				$embed_html = wp_oembed_get( $media_url );
			}

			$data = [
				'fields' => [
					'title'      => $fields['video_title'],
					'embed_html' => $embed_html,
					'type'       => $type,
				],
			];
			return $data;
		}
	}
}

<?php
/**
 * Social media block class
 *
 * @package P4BKS
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'SocialMedia_Controller' ) ) {

	/**
	 * Class SocialMedia_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 */
	class SocialMedia_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'social_media';

		const ALLOWED_OEMBED_PROVIDERS = [
			'twitter',
			'facebook',
			'instagram',
		];

		/**
		 * Hooks all the needed functions to load the block.
		 */
		public function load() {
			parent::load();
			add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_assets' ] );
		}


		/**
		 * Load assets only on the admin pages of the plugin.
		 *
		 * @param string $hook The slug name of the current admin page.
		 */
		public function load_admin_assets( $hook ) {

			if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
				return;
			}

		}

		/**
		 * Shortcode UI setup
		 */
		public function prepare_fields() {
			$fields = [
				[
					'label'       => __( 'Title', 'planet4-blocks-backend' ),
					'attr'        => 'title',
					'type'        => 'text',
					'description' => __( '(Optional)', 'planet4-blocks-backend' ),
					'meta'        => [
						'placeholder' => __( 'Enter title', 'planet4-blocks-backend' ),
					],
				],
				[
					'label'       => __( 'Description', 'planet4-blocks-backend' ),
					'attr'        => 'description',
					'type'        => 'textarea',
					'description' => __( '(Optional)', 'planet4-blocks-backend' ),
				],
				[
					'label'       => __( 'Embed type', 'planet4-blocks-backend' ),
					'attr'        => 'embed_type',
					'type'        => 'radio',
					'options'     => [
						[
							'value' => 'oembed',
							'label' => __( 'oEmbed', 'planet4-blocks-backend' ),
						],
						[
							'value' => 'facebook_page',
							'label' => __( 'Facebook page', 'planet4-blocks-backend' ),
						],
					],
					'description' => __( 'Select oEmbed for the following types of social media<br>- Twitter: tweet, profile, list, collection, likes, moment<br>- Facebook: post, activity, photo, video, media, question, note<br>- Instagram: image', 'planet4-blocks-backend' ),
				],
				[
					'label'   => __( 'What Facebook page content would you like to display?', 'planet4-blocks-backend' ),
					'attr'    => 'facebook_page_tab',
					'type'    => 'select',
					'options' => [
						[
							'value' => 'timeline',
							'label' => __( 'Timeline', 'planet4-blocks-backend' ),
						],
						[
							'value' => 'events',
							'label' => __( 'Events', 'planet4-blocks-backend' ),
						],
						[
							'value' => 'messages',
							'label' => __( 'Message input', 'planet4-blocks-backend' ),
						],
					],
				],
				[
					'label' => __( 'URL', 'planet4-blocks-backend' ),
					'attr'  => 'social_media_url',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Enter URL', 'planet4-blocks-backend' ),
					],
				],
				[
					'label'   => __( 'Alignment', 'planet4-blocks-backend' ),
					'attr'    => 'alignment_class',
					'type'    => 'select',
					'options' => [
						[
							'value' => '',
							'label' => __( 'None', 'planet4-blocks-backend' ),
						],
						[
							'value' => 'alignleft',
							'label' => __( 'Left', 'planet4-blocks-backend' ),
						],
						[
							'value' => 'aligncenter',
							'label' => __( 'Center', 'planet4-blocks-backend' ),
						],

						[
							'value' => 'alignright',
							'label' => __( 'Right', 'planet4-blocks-backend' ),
						],
					],
				],
			];

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				'label'         => __( 'Social Media', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/social_media.png' ) . '" />',
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
			$title             = $fields['title'] ?? '';
			$description       = $fields['description'] ?? '';
			$url               = $fields['social_media_url'] ?? '';
			$embed_type        = $fields['embed_type'] ?? 'oembed';
			$alignment_class   = $fields['alignment_class'] ?? '';
			$facebook_page_tab = $fields['facebook_page_tab'] ?? 'timeline';

			$data = [
				'title'           => $title,
				'description'     => $description,
				'alignment_class' => $alignment_class,
			];

			if ( $url ) {
				if ( 'oembed' === $embed_type ) {
					// need to remove . so instagr.am becomes instagram.
					$provider = preg_replace( '#(^www\.)|(\.com$)|(\.)#', '', strtolower( wp_parse_url( $url, PHP_URL_HOST ) ) );

					if ( in_array( $provider, self::ALLOWED_OEMBED_PROVIDERS, true ) ) {
						$data['embed_code'] = wp_oembed_get( $url );
					}
				} elseif ( 'facebook_page' === $embed_type ) {
					$data['facebook_page_url'] = $url;
					$data['facebook_page_tab'] = $facebook_page_tab;
				}
			}

			return $data;
		}
	}
}

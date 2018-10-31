<?php
/**
 * Campaign Thumbnail block class
 *
 * @package P4BKS
 * @since 0.1.14
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'CampaignThumbnail_Controller' ) ) {

	/**
	 * Class CampaignThumbnail_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 * @since 0.1.14
	 */
	class CampaignThumbnail_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'campaign_thumbnail';

		/**
		 * Shortcode UI setup for the CampaignThumbnail shortcode.
		 *
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 */
		public function prepare_fields() {
			$fields = [
				[
					'label' => __( 'Title', 'planet4-blocks-backend' ),
					'attr'  => 'title',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Enter title', 'planet4-blocks-backend' ),
					],
				],
			];

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				'label'         => __( 'Campaign Thumbnail', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/campaign_thumbnail.png' ) . '" />',
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

			// If $fields['category_id'] exists then we are on Campaign Page, else we are on Issue Page.
			if ( ! empty( $fields['category_id'] ) ) {
				$category         = get_category( $fields['category_id'] );
				$context_tags     = get_queried_object();
				$options          = get_option( 'planet4_options' );
				$parent_id        = $options['explore_page'];
				$explore_children = [];

				if ( 0 !== absint( $parent_id ) ) {
					$args             = [
						'post_type'   => 'page',
						'post_parent' => $parent_id,
					];
					$explore_children = get_children( $args );
				}

				$page_id = '';

				// Here, we are getting issue page ID.
				if ( $explore_children ) {
					foreach ( $explore_children as $pages ) {
						if ( $pages->post_name === $category->slug ) {
							$page_id = $pages->ID;
							break;
						}
					}
				}

				$tags = wp_get_post_tags( $page_id );

				if ( $tags ) {

					$i = 1;
					foreach ( $tags as $tag ) {
						if ( $context_tags->slug !== $tag->slug ) {
							$tag_remapped  = [
								'name' => html_entity_decode( $tag->name ),
								'slug' => $tag->slug,
								// phpcs:ignore
								'href' => get_tag_link( $tag )
							];
							$attachment_id = get_term_meta( $tag->term_id, 'tag_attachment_id', true );

							if ( ! empty( $attachment_id ) ) {
								$tag_remapped['image']    = wp_get_attachment_image_src( $attachment_id, 'medium_large' );
								$tag_remapped['alt_text'] = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
							}

							$fields['tags'][] = $tag_remapped;

							if ( 3 === $i ) {
								break;
							}

							$i ++;

						}
					}
				}
			} else {

				$tags = get_the_tags();
				if ( $tags ) {
					$tags = array_slice( $tags, 0, 3 );

					foreach ( $tags as $tag ) {
						$tag_remapped  = [
							'name' => html_entity_decode( $tag->name ),
							'slug' => $tag->slug,
							// phpcs:ignore
							'href' => get_tag_link( $tag ),
						];
						$attachment_id = get_term_meta( $tag->term_id, 'tag_attachment_id', true );

						if ( ! empty( $attachment_id ) ) {
							$tag_remapped['image']    = wp_get_attachment_image_src( $attachment_id, 'medium_large' );
							$tag_remapped['alt_text'] = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
						}

						$fields['tags'][] = $tag_remapped;
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

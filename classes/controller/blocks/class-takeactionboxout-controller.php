<?php
/**
 * Split Two Columns block class
 *
 * @package P4BKS
 * @since 0.1.16
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'TakeActionBoxout_Controller' ) ) {

	/**
	 * Class TakeActionBoxout_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 * @since 0.1.16
	 */
	class TakeActionBoxout_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'take_action_boxout';

		/**
		 * Shortcode UI setup for the shortcode.
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 */
		public function prepare_fields() {

			// Get the id of the ACT page. We need this to get the children posts/pages of the ACT Page.
			$options       = get_option( 'planet4_options' );
			$parent_act_id = $options['act_page'];

			$arguments = [];
			if ( 0 !== absint( $parent_act_id ) ) {
				$act_page  = get_post( $parent_act_id );
				$arguments = [
					'post_type'     => 'page',
					'post_name__in' => [ $act_page->post_name ],
				];
			}

			// Initialize variable.
			$take_action_pages_args = [];
			$query                  = new \WP_Query( $arguments );

			// If ACT Page is found construct arguments array for the select box to be used below.
			if ( $query->have_posts() ) {
				$posts                  = $query->get_posts();
				$post                   = $posts[0];
				$take_action_pages_args = [
					'post_type'   => 'page',
					'post_parent' => $post->ID,
				];
			} else {
				$take_action_pages_args = [
					'post_type' => 'page',
				];
			}

			$fields = [
				[
					'attr'        => 'take_action_page',
					'label'       => __( 'Select a Take Action Page', 'planet4-blocks-backend' ),
					'description' => __( 'Associate this block with a Take Action page', 'planet4-blocks-backend' ),
					'type'        => 'post_select',
					'query'       => $take_action_pages_args,  // Filter select options only with ACT page children.
				],
			];

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				'label'         => __( 'Take Action Boxout', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/take_action_boxout.png' ) . '" />',
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
			$page_id = $fields['take_action_page'] ?? '';

			$args = [
				'p'         => intval( $page_id ), // ID of a page, post.
				'post_type' => 'any',
			];

			// Try to find the page that the user selected.
			$query = new \WP_Query( $args );
			$page  = null;
			$tag   = null;

			// If page is found populate the necessary fields for the block.
			if ( $query->have_posts() ) {
				$posts   = $query->get_posts();
				$page    = $posts[0];
				$wp_tags = wp_get_post_tags( $page->ID );
				$tags    = [];

				if ( is_array( $wp_tags ) && $wp_tags ) {
					foreach ( $wp_tags as $wp_tag ) {
						$tags[] = [
							'name' => $wp_tag->name,
							'link' => get_tag_link( $wp_tag ),
						];
					}
				}
			}

			$options = get_option( 'planet4_options' );

			// Populate variables.
			$block = [
				'campaigns' => $tags,
				'title'     => null === $page ? '' : $page->post_title,
				'excerpt'   => null === $page ? '' : $page->post_excerpt,
				'link'      => null === $page ? '' : get_permalink( $page ),
				'link_text' => $options['take_action_covers_button_text'] ?? __( 'take action', 'planet4-blocks' ),
				'image'     => null === $page ? '' : get_the_post_thumbnail_url( $page, 'large' ),
			];

			$data = [
				'boxout' => $block,
			];
			return $data;
		}
	}
}

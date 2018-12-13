<?php
/**
 * Split Two Columns block class
 *
 * @package P4BKS
 * @since 0.1.11
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'ContentFourColumn_Controller' ) ) {

	/**
	 * Class ContentFourColumn_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 * @since 0.1.11
	 */
	class ContentFourColumn_Controller extends Controller {

		const POSTS_LIMIT = 100;
		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'content_four_column';

		/**
		 * Shortcode UI setup for content four column shortcode.
		 *
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 *
		 * This example shortcode has many editable attributes, and more complex UI.
		 *
		 * @since 1.0.0
		 */
		public function prepare_fields() {

			$checkboxes                 = [];
			$planet4_article_type_terms = get_terms(
				[
					'hide_empty' => false,
					'orderby'    => 'name',
					'taxonomy'   => 'p4-page-type',
				]
			);

			// Construct a checkbox for each p4-page-type.
			if ( ! empty( $planet4_article_type_terms ) ) {
				foreach ( $planet4_article_type_terms as $term ) {
					$checkboxes [] = [
						'attr'        => 'p4_page_type_' . str_replace( '-', '_', $term->slug ),
						'label'       => $term->name . ' Posts',
						'description' => 'Use Posts that belong to ' . $term->name . ' type to populate the content of this block',
						'type'        => 'checkbox',
						'value'       => 'false',
					];
				}
			}

			$fields = [
				[
					'label' => __( 'Title. <i>If it is not defined, title will default to \'Publications\'</i>', 'planet4-blocks-backend' ),
					'attr'  => 'title',
					'type'  => 'text',
					'meta'  => [
						// translators: placeholder needs to represent the ordinal of the column, eg. 1st, 2nd etc.
						'placeholder' => __( 'Enter title for this block', 'planet4-blocks-backend' ),
						'data-plugin' => 'planet4-blocks',
					],
				],
				[
					'attr'        => 'select_tag',
					'label'       => __( 'Select a Tag', 'planet4-blocks-backend' ),
					'description' => __( 'Associate this block with Posts that have a specific Tag', 'planet4-blocks-backend' ),
					'type'        => 'term_select',
					'taxonomy'    => 'post_tag',
					'multiple'    => true,
				],
			];

			if ( ! empty( $checkboxes ) ) {
				$fields = array_merge( $fields, $checkboxes );
			}

			$fields[] = [
				'label'       => __( 'Number of Posts displayed', 'planet4-blocks-backend' ),
				'description' => __(
					'Show 1 Row: Displays 4 Posts on desktop and 3 Posts on mobile.<br> Show 2 Rows: Displays 8 Posts on desktop and 6 Posts on mobile.<br>
(Another Row will be revealed each time the Load More button is clicked)<br>Show All Rows: Displays all available Posts on desktop and 4 Posts on mobile.',
					'planet4-blocks-backend'
				),
				'attr'        => 'posts_view',
				'type'        => 'select',
				'options'     => [
					[
						'value' => '0',
						'label' => __( 'Show 1 Row', 'planet4-blocks-backend' ),
					],
					[
						'value' => '3',
						'label' => __( 'Show 2 Rows', 'planet4-blocks-backend' ),
					],
					[
						'value' => '1',
						'label' => __( 'Show All Posts', 'planet4-blocks-backend' ),
					],
				],
			];

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				'label'         => __( 'Content Four Column', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/content_four_column.png' ) . '" />',
				'attrs'         => $fields,
				'post_type'     => [''],
			];

			shortcode_ui_register_for_shortcode( 'shortcake_' . self::BLOCK_NAME, $shortcode_ui_args );
		}

		/**
		 * Get all the data that will be needed to render the block correctly.
		 *
		 * @param array  $attributes This is the array of fields of this block.
		 * @param string $content This is the post content.
		 * @param string $shortcode_tag The shortcode tag of this block.
		 *
		 * @return array The data to be passed in the View.
		 */
		public function prepare_data( $attributes, $content = '', $shortcode_tag = 'shortcake_' . self::BLOCK_NAME ) : array {

			$raw_tags   = $attributes['select_tag'] ?? '';
			$post_types = [];

			// Filter p4_page_type keys from attributes array.
			$post_types_temp = array_filter(
				(array) $attributes,
				function ( $key ) {
					return strpos( $key, 'p4_page_type' ) === 0;
				},
				ARRAY_FILTER_USE_KEY
			);

			// If any p4_page_type was selected extract the term's slug to be used in the wp query below.
			if ( ! empty( $post_types_temp ) ) {
				foreach ( $post_types_temp as $type => $value ) {
					if ( 'true' === $value ) {
						$post_types[] = str_replace(
							'_',
							'-',
							str_replace( 'p4_page_type_', '', $type )
						);
					}
				}
			}

			// If any tag is selected convert the value to an array of tag ids.
			if ( empty( $raw_tags ) || ! preg_split( '/^\d+(,\d+)*$/', $raw_tags ) ) {
				$tag_ids = [];
			} else {
				$tag_ids = explode( ',', $raw_tags );
			}

			$posts_array = [];
			$query_args  = [
				'order'         => 'DESC',
				'orderby'       => 'date',
				'no_found_rows' => true,
				'numberposts'   => self::POSTS_LIMIT,
			];

			// Get all posts with the specific tags.
			// Construct the arguments array for the query.
			if ( ! empty( $tag_ids ) && ! empty( $post_types ) ) {

				$query_args['tax_query'] = [
					'relation' => 'AND',
					[
						'taxonomy' => 'post_tag',
						'field'    => 'term_id',
						'terms'    => $tag_ids,
					],
					[
						'taxonomy' => 'p4-page-type',
						'field'    => 'slug',
						'terms'    => $post_types,
					],
				];
			} elseif ( ! empty( $tag_ids ) && empty( $post_types ) ) {

				$query_args['tax_query'] = [
					[
						'taxonomy' => 'post_tag',
						'field'    => 'term_id',
						'terms'    => $tag_ids,
					],
				];
			} elseif ( empty( $tag_ids ) && ! empty( $post_types ) ) {

				$query_args['tax_query'] = [
					[
						'taxonomy' => 'p4-page-type',
						'field'    => 'slug',
						'terms'    => $post_types,
					],
				];
			}

			// If tax_query has been defined in the arguments array, then make a query based on these arguments.
			if ( array_key_exists( 'tax_query', $query_args ) ) {

				// Construct a WP_Query object and make a query based on the arguments array.
				$query = new \WP_Query();
				$posts = $query->query( $query_args );

				if ( ! empty( $posts ) ) {

					foreach ( $posts as $post ) {

						$post->alt_text  = '';
						$post->thumbnail = '';
						$post->srcset    = '';

						if ( has_post_thumbnail( $post ) ) {
							$post->thumbnail = get_the_post_thumbnail_url( $post, 'medium' );
							$img_id          = get_post_thumbnail_id( $post );
							$post->srcset    = wp_get_attachment_image_srcset( $img_id, 'full', wp_get_attachment_metadata( $img_id ) );
							$post->alt_text  = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
						}

						$post->permalink = get_permalink( $post );
						$posts_array[]   = $post;
					}
				}
			}

			$block_data = [
				'title'      => ! empty( $attributes['title'] ) ? $attributes['title'] : __( 'Publications', 'planet4-blocks' ),
				'posts'      => $posts_array,
				'posts_view' => isset( $attributes['posts_view'] ) ? intval( $attributes['posts_view'] ) : 1,
			];
			return $block_data;
		}
	}
}

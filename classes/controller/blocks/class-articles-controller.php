<?php
/**
 * Articles block class
 *
 * @package P4BKS
 * @since 0.1.14
 */

namespace P4BKS\Controllers\Blocks;

use Timber\Timber;

if ( ! class_exists( 'Articles_Controller' ) ) {

	/**
	 * Class Articles_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 * @since 0.1.14
	 */
	class Articles_Controller extends Controller {

		/**
		 * The block name constant.
		 *
		 * @const string BLOCK_NAME
		 */
		const BLOCK_NAME = 'articles';
		/**
		 * The maximum number of articles to load with Load more button.
		 */
		const MAX_ARTICLES = 100;

		/**
		 * Hooks all the needed functions to load the block.
		 */
		public function load() {
			parent::load();
			add_action( 'wp_ajax_load_more', [ $this, 'load_more' ] );
			add_action( 'wp_ajax_nopriv_load_more', [ $this, 'load_more' ] );
		}

		/**
		 * Shortcode UI setup for the article shortcode.
		 *
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 *
		 * @since 0.1.0
		 */
		public function prepare_fields() {

			$options              = get_option( 'planet4_options' );
			$article_title        = $options['articles_block_title'] ?? __( 'Related Articles', 'planet4-blocks' );
			$article_button_title = $options['articles_block_button_title'] ?? __( 'READ ALL THE NEWS', 'planet4-blocks' );

			$fields = [
				[
					'label' => __( 'Title', 'planet4-blocks-backend' ) .
								'<p class="field-caption">' . __( 'Your default is set to', 'planet4-blocks-backend' ) .
								' [ ' . $article_title . ' ]</p>',
					'attr'  => 'article_heading',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Override default title', 'planet4-blocks-backend' ),
					],
				],
				[
					'label' => __( 'Button Text', 'planet4-blocks-backend' ) .
								'<p class="field-caption">' . __( 'Your default is set to', 'planet4-blocks-backend' ) .
								' [ ' . $article_button_title . ' ]</p>',
					'attr'  => 'read_more_text',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Override button text', 'planet4-blocks-backend' ),
					],
				],
				[
					'label' => __( 'Description', 'planet4-blocks-backend' ),
					'attr'  => 'articles_description',
					'type'  => 'textarea',
					'meta'  => [
						'placeholder' => __( 'Enter description', 'planet4-blocks-backend' ),
					],
				],
				[
					'label' => __( 'Button Link', 'planet4-blocks-backend' ),
					'attr'  => 'read_more_link',
					'type'  => 'url',
					'meta'  => [
						'placeholder' => __( 'Add read more button link', 'planet4-blocks-backend' ),
					],
				],
				[
					'label'       => __( 'Post types', 'planet4-blocks-backend' ),
					'attr'        => 'post_types',
					'type'        => 'term_select',
					'taxonomy'    => 'p4-page-type',
					'placeholder' => __( 'Search for post types', 'planet4-blocks-backend' ),
					'multiple'    => true,
					'meta'        => [
						'select2_options' => [
							'allowClear'         => true,
							'placeholder'        => __( 'Select post types', 'planet4-blocks-backend' ),
							'closeOnSelect'      => true,
							'minimumInputLength' => 0,
						],
					],
				],
				[
					'attr'        => 'button_link_new_tab',
					'label'       => __( 'Open in a new Tab', 'planet4-blocks-backend' ),
					'description' => __( 'Open button link in new tab', 'planet4-blocks-backend' ),
					'type'        => 'checkbox',
					'value'       => 'false',
				],
				[
					'label'    => __( 'Tags', 'planet4-blocks-backend' ),
					'attr'     => 'tags',
					'type'     => 'term_select',
					'taxonomy' => 'post_tag',
					'multiple' => true,
					'meta'     => [
						'select2_options' => [
							'allowClear'         => true,
							'placeholder'        => __( 'Select Tags', 'planet4-blocks-backend' ),
							'closeOnSelect'      => true,
							'minimumInputLength' => 0,
						],
					],
				],
				[
					'label' => __( 'Article Count', 'planet4-blocks-backend' ),
					'attr'  => 'article_count',
					'type'  => 'number',
					'meta'  => [
						'placeholder' => __( 'Enter articles count', 'planet4-blocks-backend' ),
						'min'         => 1,
						'max'         => self::MAX_ARTICLES,
					],
				],
				[
					'attr'        => 'ignore_categories',
					'label'       => 'Ignore Categories',
					'description' => 'Ignore categories when filtering posts to populate the content of this block',
					'type'        => 'checkbox',
					'value'       => 'false',
				],
				[
					'label'    => '<hr class="hr-dashed"><br><p>' . __( 'Manual Override', 'planet4-blocks-backend' ) . '</p>' .
									'<p class="field-caption">' .
									__(
										'CAUTION: Adding articles individually will override the automatic functionality of this block.
For good user experience, please include at least three articles so that spacing and alignment of the design remains in tact.',
										'planet4-blocks-backend'
									) . '</p>',
					'attr'     => 'posts',
					'type'     => 'post_select',
					'multiple' => 'multiple',
					'query'    => [
						'post_type' => 'post',
						'orderby'   => 'post_title',
						'order'     => 'ASC',
					],
					'meta'     => [
						'select2_options' => [
							'allowClear'             => true,
							'placeholder'            => __( 'Search for posts', 'planet4-blocks-backend' ),
							'closeOnSelect'          => false,
							'minimumInputLength'     => 0,
							'multiple'               => true,
							'maximumSelectionLength' => 20,
							'width'                  => '80%',
						],
					],
				],
			];

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				'label'         => __( 'Articles', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/home_news.jpg' ) . '" />',
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
			// Read more button links to search results if no link is specified.
			$tag_id = $fields['tags'] ?? '';

			// Article block default text setting.
			$options              = get_option( 'planet4_options' );
			$article_title        = $options['articles_block_title'] ?? __( 'Related Articles', 'planet4-blocks' );
			$article_button_title = $options['articles_block_button_title'] ?? __( 'READ ALL THE NEWS', 'planet4-blocks' );
			$exclude_post_id      = (int) ( $fields['exclude_post_id'] ?? '' );

			$fields['article_heading']      = $fields['article_heading'] ?? $article_title;
			$fields['read_more_text']       = $fields['read_more_text'] ?? $article_button_title;
			$fields['article_count']        = ( empty( $fields['article_count'] ) || $fields['article_count'] < 0 ) ? 3 : $fields['article_count'];
			$fields['articles_description'] = $fields['articles_description'] ?? '';

			// Filter p4_page_type keys from fields attributes array.
			$post_types_temp = $this->filter_post_types( $fields );

			// Six scenarios for filtering posts.
			// 1) inside tag page - Get posts that have the specific tag assigned.
			// Add extra check for post_types and posts attributes to ensure that the block is rendered from a tag page.
			// 2) inside post - Get results excluding specific post.
			// 3) post types - Get posts by post types specified using checkboxes in backend - old behavior.
			// 4) post types or tags - Get posts by post types or tags defined from select boxes - new behavior.
			// 5) specific posts - Get posts by ids specified in backend - new behavior / manual override.
			// 6) issue page - Get posts based on page's tags.
			$all_posts = false;
			if ( is_tag() && '' !== $tag_id &&
				2 === count(
					array_diff(
						[
							'post_types',
							'posts',
						],
						array_keys( $fields )
					)
				) ) {
				$args = $this->filter_posts_for_tag_page( $fields );
			} elseif ( ! empty( $exclude_post_id ) ) {
				$args = $this->filter_posts_by_page_types( $fields );
			} elseif ( ! empty( $post_types_temp ) ) {
				$args = $this->filter_posts_by_page_types( $fields );
			} elseif ( ( isset( $fields['post_types'] ) && '' !== $fields['post_types'] ) ||
						( isset( $fields['tags'] ) && '' !== $fields['tags'] ) ) {
				$args = $this->filter_posts_by_page_types_or_tags( $fields );
			} elseif ( isset( $fields['posts'] ) && '' !== $fields['posts'] ) {
				$args = $this->filter_posts_by_ids( $fields );
			} else {
				$args = $this->filter_posts_by_pages_tags( $fields );
			}

			// Get max posts.
			$args['numberposts'] = self::MAX_ARTICLES;

			// Ignore rule, arguments contain suppress_filters.
			// phpcs:ignore$fields['article_count']
			$all_posts    = wp_get_recent_posts( $args );
			$total_pages  = 0 !== $fields['article_count'] ? ceil( count( (array) $all_posts ) / $fields['article_count'] ) : 0;
			$sliced_posts = array_slice( $all_posts, 0, $fields['article_count'] );
			$recent_posts = [];

			// Populate posts array for frontend template if results have been returned.
			if ( false !== $sliced_posts ) {
				$recent_posts = $this->populate_post_items( $sliced_posts );
			}

			$dataset = urldecode( http_build_query( $args, '', ' ' ) );
			$dataset = explode( ' ', $dataset );

			$data = [
				'fields'       => $fields,
				'recent_posts' => $recent_posts,
				'total_pages'  => $total_pages,
				'nonce_action' => 'load_more',
				'dataset'      => $dataset,
			];

			return $data;
		}

		/**
		 * Callback for Lazy-loading the next results.
		 * Gets the paged posts that belong to the next page/load and are to be used with the twig template.
		 */
		public function load_more() {

			// If this is an ajax call.
			if ( wp_doing_ajax() ) {
				$nonce   = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING );
				$page    = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT );
				$dataset = filter_input_array( INPUT_GET );
				/** @var \P4_Post[] $recent_posts */
				$recent_posts = [];

				// CSRF check.
				if ( wp_verify_nonce( $nonce, 'load_more' ) ) {
					Timber::$locations = P4BKS_INCLUDES_DIR;

					if ( isset( $dataset['args'] ) ) {
						foreach ( $dataset['args'] as $key => $value ) {
							if ( false !== strpos( $key, '[', true ) ) {
								$new_key                       = strstr( $key, '[', true );
								$dataset['args'][ $new_key ][] = $value;
								unset( $dataset['args'][ $key ] );
							}
						}
						unset( $dataset['args']['page'] );
						unset( $dataset['args']['total'] );

						$dataset['args']['numberposts'] = $dataset['args']['article_count'];
						if ( $page ) {
							$dataset['args']['paged'] = $page;
						}
					}
					$recent_posts = Timber::get_posts( $dataset['args'], 'P4_Post', true );

					if ( $recent_posts ) {
						foreach ( $recent_posts as $key => $recent_post ) {
							if ( ! is_null( $recent_post->thumbnail ) && $recent_post->thumbnail instanceof \Timber\Image ) {
								$img_id                       = $recent_post->thumbnail->id;
								$dimensions                   = wp_get_attachment_metadata( $img_id );
								$recent_post->thumbnail_ratio = ( isset( $dimensions['height'] ) && $dimensions['height'] > 0 ) ? $dimensions['width'] / $dimensions['height'] : 1;
								$recent_post->alt_text        = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
							}
							Timber::render(
								[ 'teasers/tease-articles.twig' ],
								[
									'key'         => $key,
									'recent_post' => $recent_post,
								]
							);
						}
					}
				}
				wp_die();
			}
		}

		/**
		 * Populate selected posts for frontend template.
		 *
		 * @param array $posts Selected posts.
		 *
		 * @return array
		 */
		private function populate_post_items( $posts ) {
			$recent_posts = [];

			if ( $posts ) {
				foreach ( $posts as $recent ) {
					$recent['alt_text'] = '';
					// TODO - Update this method to use P4_Post functionality to get P4_User.
					$author_override           = get_post_meta( $recent['ID'], 'p4_author_override', true );
					$recent['author_name']     = '' === $author_override ? get_the_author_meta( 'display_name', $recent['post_author'] ) : $author_override;
					$recent['author_url']      = '' === $author_override ? get_author_posts_url( $recent['post_author'] ) : '#';
					$recent['author_override'] = $author_override;

					if ( has_post_thumbnail( $recent['ID'] ) ) {
						$img_id                    = get_post_thumbnail_id( $recent['ID'] );
						$dimensions                = wp_get_attachment_metadata( $img_id );
						$recent['thumbnail_ratio'] = ( isset( $dimensions['height'] ) && $dimensions['height'] > 0 ) ? $dimensions['width'] / $dimensions['height'] : 1;
						$recent['alt_text']        = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
					}

					// TODO - Update this method to use P4_Post functionality to get Tags/Terms.
					$wp_tags = wp_get_post_tags( $recent['ID'] );

					$tags = [];

					if ( $wp_tags ) {
						foreach ( $wp_tags as $wp_tag ) {
							$tags_data['name'] = $wp_tag->name;
							$tags_data['slug'] = $wp_tag->slug;
							$tags_data['link'] = get_tag_link( $wp_tag );
							$tags[]            = $tags_data;
						}
					}

					$recent['tags'] = $tags;
					$page_type_data = get_the_terms( $recent['ID'], 'p4-page-type' );
					$page_type      = '';
					$page_type_id   = '';

					if ( $page_type_data && ! is_wp_error( $page_type_data ) ) {
						$page_type    = $page_type_data[0]->name;
						$page_type_id = $page_type_data[0]->term_id;
					}

					$recent['page_type']    = $page_type;
					$recent['page_type_id'] = $page_type_id;
					$recent['link']         = get_permalink( $recent['ID'] );

					$recent_posts[] = $recent;
				}
			}

			return $recent_posts;
		}

		/**
		 * Filter posts based on post ids.
		 *
		 * @param array $fields Block fields values.
		 *
		 * @return array|false
		 */
		private function filter_posts_by_ids( &$fields ) {

			$post_ids = $fields['posts'] ?? '';

			// If post_ids is empty or is not a comma separated integers string then make post_ids an empty array.
			if ( empty( $post_ids ) || ! preg_split( '/^\d+(,\d+)*$/', $post_ids ) ) {
				$post_ids = [];
			} else {
				$post_ids = explode( ',', $post_ids );
			}

			if ( ! empty( $post_ids ) ) {

				// Get all posts with arguments.
				$args = [
					'orderby'          => 'post__in',
					'post_status'      => 'publish',
					'post__in'         => $post_ids,
					'suppress_filters' => false,
				];

				return $args;
			}

			return false;
		}

		/**
		 * Filter posts based on post types (p4_page_type terms).
		 *
		 * @param array $fields Block fields values.
		 *
		 * @return array
		 */
		private function filter_posts_by_page_types( &$fields ) {

			$exclude_post_id   = (int) ( $fields['exclude_post_id'] ?? '' );
			$ignore_categories = $fields['ignore_categories'] ?? 'false';
			$options           = get_option( 'planet4_options' );

			// Get page categories.
			$post_categories   = get_the_category();
			$category_id_array = [];
			foreach ( $post_categories as $category ) {
				$category_id_array[] = $category->term_id;
			}

			// If any p4_page_type was selected extract the term's slug to be used in the wp query below.
			$post_types = $this->filter_post_types( $fields );

			// Get page/post tags.
			$post_tags = get_the_tags();

			// Get all posts with arguments.
			$args = [
				'orderby'          => 'date',
				'post_status'      => 'publish',
				'suppress_filters' => false,
			];

			if ( 'true' !== $ignore_categories ) {
				if ( $category_id_array ) {
					$args['category__in'] = $category_id_array;
				}
			}

			// For post page block so current main post will exclude.
			if ( $exclude_post_id ) {
				$args['post__not_in'] = [ $exclude_post_id ];
			}

			if ( ! empty( $post_types ) ) {
				$args['tax_query'] = [
					[
						'taxonomy' => 'p4-page-type',
						'field'    => 'slug',
						'terms'    => $post_types,
					],
				];
			}

			// For posts and pages, display related articles based on current post/page tags.
			$current_post_type = get_post_type();
			if ( 'post' === $current_post_type || ( 'page' === $current_post_type ) ) {
				if ( $post_tags ) {
					$tag_id_array = [];
					foreach ( $post_tags as $tag ) {
						$tag_id_array[] = $tag->term_id;
					}
					$args['tag__in'] = $tag_id_array;
				}
			}

			return $args;
		}

		/**
		 * Filter posts based on post types (p4_page_type terms).
		 *
		 * @param array $fields Block fields values.
		 *
		 * @return array
		 */
		private function filter_posts_by_page_types_or_tags( &$fields ) {

			$ignore_categories = $fields['ignore_categories'] ?? 'false';
			$options           = get_option( 'planet4_options' );

			// Get page categories.
			$post_categories   = get_the_category();
			$category_id_array = [];
			foreach ( $post_categories as $category ) {
				$category_id_array[] = $category->term_id;
			}

			// If any p4_page_type was selected extract the term's slug to be used in the wp query below.
			// post_types attribute filtering.
			$post_types = $fields['post_types'] ?? '';

			if ( empty( $post_types ) || ! preg_split( '/^\d+(,\d+)*$/', $post_types ) ) {
				$post_types = [];
			} else {
				$post_types = explode( ',', $post_types );
			}

			// Get user defined tags from backend.
			$tags = $fields['tags'] ?? '';

			// If tags is empty or is not a comma separated integers string then define tags as empty.
			if ( empty( $tags ) || ! preg_split( '/^\d+(,\d+)*$/', $tags ) ) {
				$tags = [];
			} else {
				// Explode comma separated list of tag ids and get an array of \WP_Terms objects.
				$tags = get_tags( [ 'include' => $tags ] );
			}

			// If user has not provided any tag, use post's tags.
			if ( empty( $tags ) ) {
				// Get page/post tags.
				$tags = get_the_tags();
			}

			// Get all posts with arguments.
			$args = [
				'orderby'          => 'date',
				'post_status'      => 'publish',
				'suppress_filters' => false,
			];

			if ( 'true' !== $ignore_categories ) {
				if ( $category_id_array ) {
					$args['category__in'] = $category_id_array;
				}
			}

			// Add filter for p4-page-type terms.
			if ( ! empty( $post_types ) ) {
				$args['tax_query'] = [
					[
						'taxonomy' => 'p4-page-type',
						'field'    => 'term_id',
						'terms'    => $post_types,
					],
				];
			}

			// For posts and pages, display related articles based on current post/page tags.
			$current_post_type = get_post_type();
			if ( 'post' === $current_post_type || 'page' === $current_post_type ) {
				if ( $tags ) {
					$tag_id_array = [];
					foreach ( $tags as $tag ) {
						$tag_id_array[] = $tag->term_id;
					}
					$args['tag__in'] = $tag_id_array;
				}
			}

			return $args;
		}

		/**
		 * Filter posts based for a specific tag page.
		 *
		 * @param array $fields Block fields values.
		 *
		 * @return array|false
		 */
		private function filter_posts_for_tag_page( &$fields ) {

			$tag_id = $fields['tags'] ?? '';
			$tag    = get_tag( $tag_id );

			if ( $tag instanceof \WP_Term ) {
				// Get all posts with arguments.
				$args = [
					'orderby'          => 'date',
					'post_status'      => 'publish',
					'suppress_filters' => false,
				];

				$args['tag__in'] = [ (int) $tag_id ];

				return $args;
			}

			return false;
		}

		/**
		 * Filter posts based on page's/post's tags.
		 *
		 * @param array $fields Block fields values.
		 *
		 * @return array
		 */
		private function filter_posts_by_pages_tags( &$fields ) {

			// Get all posts with arguments.
			$args = [
				'orderby'          => 'date',
				'post_status'      => 'publish',
				'suppress_filters' => false,
			];

			// Get page/post tags.
			$post_tags = get_the_tags();

			// For posts and pages, display related articles based on current post/page tags.
			$current_post_type = get_post_type();

			if ( 'post' === $current_post_type || 'page' === $current_post_type ) {
				if ( $post_tags ) {
					$tag_id_array = [];
					foreach ( $post_tags as $tag ) {
						$tag_id_array[] = $tag->term_id;
					}
					$args['tag__in'] = $tag_id_array;
				}
			}

			return $args;
		}

		/**
		 * Extract p4 page type terms from block's attributes (checkboxes - old behavior).
		 *
		 * @param array $fields Block fields values.
		 *
		 * @return array
		 */
		private function filter_post_types( $fields ) {
			// Filter p4_page_type keys from attributes array.
			$post_types_temp = array_filter(
				(array) $fields,
				function ( $key ) {
					return strpos( $key, 'p4_page_type' ) === 0;
				},
				ARRAY_FILTER_USE_KEY
			);

			$post_types = [];
			// If any p4_page_type was selected extract the term's slug to be used in the wp query.
			if ( ! empty( $post_types_temp ) ) {
				foreach ( $post_types_temp as $type => $value ) {
					if ( 'true' === $value ) {
						$post_type    = str_replace(
							'_',
							'-',
							str_replace( 'p4_page_type_', '', $type )
						);
						$post_types[] = $post_type;
					}
				}
			}

			return $post_types;
		}
	}

}

<?php
/**
 * Articles block class
 *
 * @package P4BKS
 * @since 0.1.14
 */

namespace P4BKS\Controllers\Blocks;

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

			add_action(
				'enqueue_shortcode_ui',
				function () {
					wp_enqueue_script( 'submenu-view', P4BKS_ADMIN_DIR . 'js/submenu_heading_view.js', [ 'shortcode-ui' ], '0.1', true );
					wp_enqueue_script( 'blocks-ui', P4BKS_ADMIN_DIR . 'js/blocks-ui.js', [ 'shortcode-ui' ], '0.1', true );
				}
			);
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
			$article_count        = $options['articles_count'] ?? 3;
			$exclude_post_id      = (int) ( $fields['exclude_post_id'] ?? '' );

			$fields['article_heading']      = $fields['article_heading'] ?? $article_title;
			$fields['read_more_text']       = $fields['read_more_text'] ?? $article_button_title;
			$fields['article_count']        = $fields['article_count'] ?? $article_count;
			$fields['articles_description'] = $fields['articles_description'] ?? '';
			$fields['manual_override']      = false; // Define if specific posts where set in backend.

			// Filter p4_page_type keys from fields attributes array.
			$post_types_temp = $this->filter_post_types( $fields );

			// Six scenarios for filtering posts.
			// 1) inside tag page - Get posts that have the specific tag assigned.
			// 2) inside post - Get results excluding specific post.
			// 3) post types - Get posts by post types specified using checkboxes in backend - old behavior.
			// 4) post types or tags - Get posts by post types or tags defined from select boxes - new behavior.
			// 5) specific posts - Get posts by ids specified in backend - new behavior / manual override.
			// 6) issue page - Get posts based on page's tags.
			$all_posts = false;
			if ( is_tag() && '' !== $tag_id ) {
				$all_posts = $this->filter_posts_for_tag_page( $fields );
			} elseif ( ! empty( $exclude_post_id ) ) {
				$all_posts = $this->filter_posts_by_page_types( $fields );
			} elseif ( ! empty( $post_types_temp ) ) {
				$all_posts = $this->filter_posts_by_page_types( $fields );
			} elseif ( ( isset( $fields['post_types'] ) && '' !== $fields['post_types'] ) ||
						( isset( $fields['tags'] ) && '' !== $fields['tags'] ) ) {
				$all_posts = $this->filter_posts_by_page_types_or_tags( $fields );
			} elseif ( isset( $fields['posts'] ) && '' !== $fields['posts'] ) {
				$all_posts                 = $this->filter_posts_by_ids( $fields );
				$fields['manual_override'] = true;
			} else {
				$all_posts = $this->filter_posts_by_pages_tags( $fields );
			}

			$recent_posts = [];

			// Populate posts array for frontend template if results have been returned.
			if ( false !== $all_posts ) {
				$recent_posts = $this->populate_post_items( $all_posts );
			}

			$data = [
				'fields'       => $fields,
				'recent_posts' => $recent_posts,
			];
			return $data;
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
					$recent['alt_text']        = '';
					$recent['thumbnail']       = '';
					$author_override           = get_post_meta( $recent['ID'], 'p4_author_override', true );
					$recent['author']          = '' === $author_override ? get_the_author_meta( 'display_name', $recent['post_author'] ) : $author_override;
					$recent['author_url']      = '' === $author_override ? get_author_posts_url( $recent['post_author'] ) : '#';
					$recent['author_override'] = $author_override;

					if ( has_post_thumbnail( $recent['ID'] ) ) {
						$recent['thumbnail']       = get_the_post_thumbnail_url( $recent['ID'], 'articles-medium-large' );
						$img_id                    = get_post_thumbnail_id( $recent['ID'] );
						$dimensions                = wp_get_attachment_metadata( $img_id );
						$recent['thumbnail_ratio'] = ( isset( $dimensions['height'] ) && $dimensions['height'] > 0 ) ? $dimensions['width'] / $dimensions['height'] : 1;
						$recent['alt_text']        = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
					}

					$wp_tags = wp_get_post_tags( $recent['ID'] );

					$tags = [];

					if ( $wp_tags ) {
						foreach ( $wp_tags as $wp_tag ) {
							$tags_data['name'] = $wp_tag->name;
							$tags_data['slug'] = $wp_tag->slug;
							$tags_data['href'] = get_tag_link( $wp_tag );
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
					$recent['permalink']    = get_permalink( $recent['ID'] );

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

			$fields['read_more_link'] = $fields['read_more_link'] ?? rtrim( get_home_url(), '/' ) . '/?s=&orderby=post_date&f[ctype][Post]=3';
			$post_ids                 = $fields['posts'] ?? '';

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

				// Ignore rule, arguments contain suppress_filters.
				// phpcs:ignore
				return wp_get_recent_posts( $args );
			}

			return false;
		}

		/**
		 * Filter posts based on post types (p4_page_type terms).
		 *
		 * @param array $fields Block fields values.
		 *
		 * @return array|false
		 */
		private function filter_posts_by_page_types( &$fields ) {

			$read_more_link = ( ! empty( $fields['read_more_link'] ) ) ? $fields['read_more_link'] : rtrim( get_home_url(), '/' ) . '/?s=&orderby=post_date&f[ctype][Post]=3';

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

			// On other than tag page, read more link should lead to search page-preselected with current page categories/tags.
			$read_more_filter = '';
			if ( 'true' !== $ignore_categories ) {
				if ( $post_categories ) {
					foreach ( $post_categories as $category ) {
						// For issue page.
						if ( $category->parent === (int) $options['issues_parent_category'] ) {
							$read_more_filter .= '&f[cat][' . $category->name . ']=' . $category->term_id;
						}
					}
				}
			}

			if ( ! empty( $post_types ) ) {
				// We cannot filter search for more than one pagetype, so use the last one.
				$read_more_post_type = end( $post_types );
				$page_type           = get_term_by( 'slug', wp_unslash( $read_more_post_type ), 'p4-page-type' );
				$read_more_filter   .= $page_type instanceof \WP_Term ? '&f[ptype][' . $page_type->slug . ']=' . $page_type->term_id : '';
			}

			if ( '' === $read_more_filter ) {
				// For normal page and post.
				if ( $post_tags ) {
					foreach ( $post_tags as $tag ) {
						$read_more_filter .= '&f[tag][' . $tag->name . ']=' . $tag->term_id;
					}
				}
			}

			$read_more_link           = $fields['read_more_link'] ?? $read_more_link . $read_more_filter;
			$fields['read_more_link'] = $read_more_link;

			// Get all posts with arguments.
			$args = [
				'numberposts'      => $fields['article_count'],
				'orderby'          => 'date',
				'post_status'      => 'publish',
				'suppress_filters' => false,
			];

			if ( 'true' !== $ignore_categories ) {
				if ( $category_id_array ) {
					$category_ids     = implode( ',', $category_id_array );
					$args['category'] = '( ' . $category_ids . ' )';
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

			// Ignore rule, arguments contain suppress_filters.
			// phpcs:ignore
			return wp_get_recent_posts( $args );
		}

		/**
		 * Filter posts based on post types (p4_page_type terms).
		 *
		 * @param array $fields Block fields values.
		 *
		 * @return array|false
		 */
		private function filter_posts_by_page_types_or_tags( &$fields ) {

			$read_more_link    = ( ! empty( $fields['read_more_link'] ) ) ? $fields['read_more_link'] : rtrim( get_home_url(), '/' ) . '/?s=&orderby=post_date&f[ctype][Post]=3';
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

			// On other than tag page, read more link should lead to search page-preselected with current page categories/tags.
			$read_more_filter = '';
			if ( 'true' !== $ignore_categories ) {
				if ( $post_categories ) {
					foreach ( $post_categories as $category ) {
						// For issue page.
						if ( $category->parent === (int) $options['issues_parent_category'] ) {
							$read_more_filter .= '&f[cat][' . $category->name . ']=' . $category->term_id;
						}
					}
				}
			}

			if ( ! empty( $post_types ) ) {
				// We cannot filter search for more than one pagetype, so use the last one.
				$read_more_post_type = end( $post_types );
				$page_type_data      = get_term_by( 'term_id', $read_more_post_type, 'p4-page-type' );
				if ( $page_type_data instanceof \WP_Term ) {
					$read_more_filter .= '&f[ptype][' . $page_type_data->slug . ']=' . $page_type_data->term_id;
				}
			}

			if ( '' === $read_more_filter ) {
				// For normal page and post.
				if ( $tags ) {
					foreach ( $tags as $tag ) {
						$read_more_filter .= '&f[tag][' . $tag->name . ']=' . $tag->term_id;
					}
				}
			}

			$read_more_link           = $fields['read_more_link'] ?? $read_more_link . $read_more_filter;
			$fields['read_more_link'] = $read_more_link;

			// Get all posts with arguments.
			$args = [
				'numberposts'      => $fields['article_count'],
				'orderby'          => 'date',
				'post_status'      => 'publish',
				'suppress_filters' => false,
			];

			if ( 'true' !== $ignore_categories ) {
				if ( $category_id_array ) {
					$category_ids     = implode( ',', $category_id_array );
					$args['category'] = '( ' . $category_ids . ' )';
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

			// Ignore rule, arguments contain suppress_filters.
			// phpcs:ignore
			return wp_get_recent_posts( $args );
		}

		/**
		 * Filter posts based for a specific tag page.
		 *
		 * @param array $fields Block fields values.
		 *
		 * @return array|false
		 */
		private function filter_posts_for_tag_page( &$fields ) {

			$tag_id                   = $fields['tags'] ?? '';
			$tag                      = get_tag( $tag_id );
			$tag_filter               = $tag instanceof \WP_Term ? '&f[tag][' . $tag->name . ']=' . $tag_id : '';
			$read_more_link           = ( ! empty( $fields['read_more_link'] ) ) ? $fields['read_more_link'] : rtrim( get_home_url(), '/' ) . '/?s=&orderby=post_date&f[ctype][Post]=3' . $tag_filter;
			$fields['read_more_link'] = $read_more_link;

			if ( $tag instanceof \WP_Term ) {
				// Get all posts with arguments.
				$args = [
					'numberposts'      => $fields['article_count'],
					'orderby'          => 'date',
					'post_status'      => 'publish',
					'suppress_filters' => false,
				];

				$args['tag__in'] = [ (int) $tag_id ];

				// Ignore rule, arguments contain suppress_filters.
				// phpcs:ignore
				return wp_get_recent_posts( $args );
			}

			return false;
		}

		/**
		 * Filter posts based on page's/post's tags.
		 *
		 * @param array $fields Block fields values.
		 *
		 * @return array|false
		 */
		private function filter_posts_by_pages_tags( &$fields ) {

			// Get all posts with arguments.
			$args = [
				'numberposts'      => $fields['article_count'],
				'orderby'          => 'date',
				'post_status'      => 'publish',
				'suppress_filters' => false,
			];

			// Get page/post tags.
			$post_tags = get_the_tags();

			// For posts and pages, display related articles based on current post/page tags.
			$current_post_type = get_post_type();
			$read_more_filter  = '';

			if ( 'post' === $current_post_type || 'page' === $current_post_type ) {
				if ( $post_tags ) {
					$tag_id_array = [];
					foreach ( $post_tags as $tag ) {
						$tag_id_array[]    = $tag->term_id;
						$read_more_filter .= '&f[tag][' . $tag->name . ']=' . $tag->term_id;
					}
					$args['tag__in'] = $tag_id_array;
				}
			}

			$read_more_link           = ( ! empty( $fields['read_more_link'] ) ) ? $fields['read_more_link'] : rtrim( get_home_url(), '/' ) . '/?s=&orderby=post_date&f[ctype][Post]=3' . $read_more_filter;
			$fields['read_more_link'] = $read_more_link;

			// Ignore rule, arguments contain suppress_filters.
			// phpcs:ignore
			return wp_get_recent_posts( $args );
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

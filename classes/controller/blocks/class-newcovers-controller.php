<?php
/**
 * New covers block class
 *
 * @package P4BKS
 * @since 1.19
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'NewCovers_Controller' ) ) {

	/**
	 * Class NewCovers_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 * @since 1.19
	 */
	class NewCovers_Controller extends Controller {

		const POSTS_LIMIT = 50;

		/**
		 * Block name.
		 *
		 * @const string BLOCK_NAME.
		 */
		const BLOCK_NAME = 'newcovers';

		/**
		 * Hooks all the needed functions to load the block.
		 */
		public function load() {
			parent::load();
			add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_assets' ] );
			add_action( 'wp_ajax_planet4_blocks_post_field', [ $this, 'action_wp_ajax_shortcode_ui_post_field' ] );
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

			wp_enqueue_style( 'p4bks_admin_style_blocks', P4BKS_ADMIN_DIR . 'css/admin_blocks.css', [], '0.1' );
			add_action(
				'enqueue_shortcode_ui',
				function () {
					wp_enqueue_script( 'submenu-view', P4BKS_ADMIN_DIR . 'js/submenu_heading_view.js', [ 'shortcode-ui' ], '0.1', true );
					wp_enqueue_script( 'blocks-ui', P4BKS_ADMIN_DIR . 'js/blocks-ui.js', [ 'shortcode-ui' ], '0.1', true );
				}
			);
		}

		/**
		 * Load underscore templates to footer.
		 */
		public function print_admin_footer_scripts() {
			echo $this->get_template( 'submenu' ); // WPCS: XSS ok.
		}

		/**
		 * Shortcode UI setup for the shortcode.
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 *
		 * @codeCoverageIgnore
		 */
		public function prepare_fields() {

			$fields = [
				[
					'attr'    => 'cover_type',
					'label'   => __( 'What style of cover do you need?', 'planet4-blocks-backend' ),
					'type'    => 'p4_radio',
					'options' => [
						[
							'value' => '1',
							'label' => __( 'Take Action Covers', 'planet4-blocks-backend' ),
							'desc'  => 'Take action covers pull the featured image, tags, have a 25 character excerpt and have a call to action button',
							'image' => esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/take_action_covers.png' ),
						],
						[
							'value' => '2',
							'label' => __( 'Campaign Covers', 'planet4-blocks-backend' ),
							'desc'  => 'Campaign covers pull the associated image and hashtag from the system tag definitions.',
							'image' => esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/campaign_covers.png' ),
						],
						[
							'value' => '3',
							'label' => __( 'Content Covers', 'planet4-blocks-backend' ),
							'desc'  => 'Content covers pull the image from the post.',
							'image' => esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/content_covers.png' ),
						],
					],
				],
				[
					'label' => __( 'Title', 'planet4-blocks-backend' ),
					'attr'  => 'title',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Enter title', 'planet4-blocks-backend' ),
						'data-plugin' => 'planet4-blocks',
					],
				],
				[
					'attr'        => 'tags',
					'label'       => __( 'Select Tags', 'planet4-blocks-backend' ),
					'description' => __( 'Associate this block with Actions that have specific Tags', 'planet4-blocks-backend' ),
					'type'        => 'term_select',
					'taxonomy'    => 'post_tag',
					'multiple'    => true,
					'meta'        => [
						'select2_options' => [
							'allowClear'         => true,
							'placeholder'        => __( 'Select Tags', 'planet4-blocks-backend' ),
							'closeOnSelect'      => true,
							'minimumInputLength' => 0,
						],
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
					'label' => __( 'Description', 'planet4-blocks-backend' ),
					'attr'  => 'description',
					'type'  => 'textarea',
					'meta'  => [
						'placeholder' => __( 'Enter description', 'planet4-blocks-backend' ),
						'data-plugin' => 'planet4-blocks',
					],
				],
				[
					'label'       => __( 'Number displayed', 'planet4-blocks-backend' ),
					'description' => __( 'Another Row will be revealed each time the User clicks the Load More button.', 'planet4-blocks-backend' ),
					'attr'        => 'covers_view',
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
							'label' => __( 'Show All Covers', 'planet4-blocks-backend' ),
						],
					],
				],
				[
					'label'    => '<hr class="hr-dashed"><br><p>' .
						__( 'Manual Override', 'planet4-blocks-backend' ) . '</p>' .
						'<p class="field-caption">' .
						__(
							'CAUTION: Adding covers manually will override the automatic functionality.<br>DRAG & DROP: Drag and drop to reorder cover display priority.',
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
							'width'                  => '60%',
						],
					],
				],
			];

			/*
			 * Define the Shortcode UI arguments.
			 */
			$shortcode_ui_args = [

				/*
				 * How the shortcode should be labeled in the UI. Required argument.
				 */
				'label'         => __( 'Covers', 'planet4-blocks-backend' ),

				/*
				 * Include an icon with your shortcode. Optional.
				 * Use a dashicon, or full HTML (e.g. <img src="/path/to/your/icon" />).
				 */
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/covers.png' ) . '" />',

				/*
				 * Define the UI for attributes of the shortcode. Optional.
				 * See above, to where the the assignment to the $fields variable was made.
				 */
				'attrs'         => $fields,
				'post_type'     => P4BKS_ALLOWED_PAGETYPE,
			];

			shortcode_ui_register_for_shortcode( 'shortcake_' . self::BLOCK_NAME, $shortcode_ui_args );
		}

		/**
		 * Get all the data that will be needed to render the block correctly.
		 *
		 * @param array  $fields  This is the array of fields of this block.
		 * @param string $content This is the post content.
		 * @param string $shortcode_tag The shortcode tag of this block.
		 *
		 * @return array The data to be passed in the View.
		 */
		public function prepare_data( $fields, $content = '', $shortcode_tag = 'shortcake_' . self::BLOCK_NAME ) : array {
			$cover_type = $fields['cover_type'] ?? '';
			$covers     = false;
			if ( '1' === $cover_type ) {
				$covers = $this->populate_posts_for_act_pages( $fields );
			} elseif ( '2' === $cover_type ) {
				$covers = $this->populate_posts_for_campaigns( $fields );
			} elseif ( '3' === $cover_type ) {
				$covers = $this->populate_posts_for_cfc( $fields );
			}

			$covers_view = isset( $fields['covers_view'] ) ? intval( $fields['covers_view'] ) : 1;

			$data = [
				'fields'      => $fields,
				'covers'      => $covers,
				'cover_type'  => $cover_type,
				'covers_view' => $covers_view,
			];

			return $data;
		}

		/**
		 * Get posts that are act page children.
		 *
		 * @param array $fields  This is the array of fields of this block.
		 *
		 * @return \WP_Post[]
		 */
		private function filter_posts_for_act_pages( $fields ) {
			$tag_ids       = $fields['tags'] ?? '';
			$options       = get_option( 'planet4_options' );
			$parent_act_id = $options['act_page'];

			if ( 0 !== absint( $parent_act_id ) ) {
				$args = [
					'post_type'        => 'page',
					'post_status'      => 'publish',
					'post_parent'      => $parent_act_id,
					'orderby'          => [
						'menu_order' => 'ASC',
						'date'       => 'DESC',
						'title'      => 'ASC',
					],
					'suppress_filters' => false,
					'numberposts'      => self::POSTS_LIMIT,
				];
				// If user selected a tag to associate with the Take Action page covers.
				if ( $tag_ids ) {
					$tag_ids         = explode( ',', $tag_ids );
					$args['tag__in'] = $tag_ids;
				}

				// Ignore sniffer rule, arguments contain suppress_filters.
				// phpcs:ignore
				return get_posts( $args );
			}

			return [];
		}

		/**
		 * Get specific posts.
		 *
		 * @param array $fields  This is the array of fields of this block.
		 *
		 * @return \WP_Post[]
		 */
		private function filter_posts_by_ids( $fields ) {
			$post_ids = $fields['posts'] ?? '';

			// If post_ids is empty or is not a comma separated integers string then make post_ids an empty array.
			$post_ids = $this->split_to_integers( $post_ids );

			if ( ! empty( $post_ids ) ) {

				// Get all posts with arguments.
				$args = [
					'orderby'          => 'post__in',
					'post_status'      => 'publish',
					'post__in'         => $post_ids,
					'suppress_filters' => false,
					'numberposts'      => self::POSTS_LIMIT,
				];

				// If cover type is take action pages set post_type to page.
				if ( isset( $fields['cover_type'] ) && '1' === $fields['cover_type'] ) {
					$args['post_type'] = 'page';
				}

				// Ignore sniffer rule, arguments contain suppress_filters.
				// phpcs:ignore
				return get_posts( $args );
			}

			return [];
		}

		/**
		 * Get posts for content four column.
		 *
		 * @param array $fields  This is the array of fields of this block.
		 *
		 * @return \WP_Post[]
		 */
		private function filter_posts_for_cfc( $fields ) {

			$tags       = $fields['tags'] ?? '';
			$post_types = $fields['post_types'] ?? '';

			// If post_ids is empty or is not a comma separated integers string then make post_ids an empty array.
			// If any tag is selected convert the value to an array of tag ids.
			$post_types = $this->split_to_integers( $post_types );
			$tag_ids    = $this->split_to_integers( $tags );

			$query_args = [
				'post_type'      => 'post',
				'orderby'        => [
					'date'  => 'DESC',
					'title' => 'ASC',
				],
				'no_found_rows'  => true,
				'posts_per_page' => self::POSTS_LIMIT,
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
						'field'    => 'term_id',
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
						'field'    => 'term_id',
						'terms'    => $post_types,
					],
				];
			}

			// If tax_query has been defined in the arguments array, then make a query based on these arguments.
			if ( array_key_exists( 'tax_query', $query_args ) ) {

				// Construct a WP_Query object and make a query based on the arguments array.
				$query = new \WP_Query();
				$posts = $query->query( $query_args );
				return $posts;
			}

			return [];
		}

		/**
		 * Populate posts for campaign thumbnail template.
		 *
		 * @param array $fields  This is the array of fields of this block.
		 *
		 * @return array
		 */
		private function populate_posts_for_campaigns( &$fields ) {

			// Get user defined tags from backend.
			$tag_ids = $fields['tags'] ?? '';

			// If tags is empty or is not a comma separated integers string then define tags as empty.
			if ( empty( $tag_ids ) || 1 !== preg_match( '/^\d+(,\d+)*$/', $tag_ids ) ) {
				$tags = [];
			} else {
				// Explode comma separated list of tag ids and get an array of \WP_Terms objects.
				$tags = get_tags( [ 'include' => $tag_ids ] );
			}

			$covers = [];

			foreach ( $tags as $tag ) {
				$tag_remapped  = [
					'name' => html_entity_decode( $tag->name ),
					'slug' => $tag->slug,
					'href' => get_tag_link( $tag ),
				];
				$attachment_id = get_term_meta( $tag->term_id, 'tag_attachment_id', true );

				if ( ! empty( $attachment_id ) ) {
					$tag_remapped['image']    = wp_get_attachment_image_src( $attachment_id, 'medium_large' );
					$tag_remapped['alt_text'] = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
				}

				$covers[] = $tag_remapped;
			}

			return $covers;
		}

		/**
		 * Populate posts for take action covers template.
		 *
		 * @param array $fields  This is the array of fields of this block.
		 *
		 * @return array
		 */
		private function populate_posts_for_act_pages( &$fields ) {

			$post_ids = $fields['posts'] ?? '';
			$options  = get_option( 'planet4_options' );

			if ( '' !== $post_ids ) {
				$actions = $this->filter_posts_by_ids( $fields );
			} else {
				$actions = $this->filter_posts_for_act_pages( $fields );
			}

			$covers = [];

			if ( $actions ) {
				$cover_button_text = $options['take_action_covers_button_text'] ?? __( 'Take Action', 'planet4-blocks' );

				foreach ( $actions as $action ) {
					$tags    = [];
					$wp_tags = wp_get_post_tags( $action->ID );

					if ( is_array( $wp_tags ) && $wp_tags ) {
						foreach ( $wp_tags as $wp_tag ) {
							$tags[] = [
								'name' => $wp_tag->name,
								'href' => get_tag_link( $wp_tag ),
							];
						}
					}
					$covers[] = [
						'tags'        => $tags,
						'title'       => get_the_title( $action ),
						'excerpt'     => get_the_excerpt( $action ),
						// Note: WordPress removes shortcodes from auto-generated excerpts.
						'image'       => get_the_post_thumbnail_url( $action, 'large' ),
						'button_text' => $cover_button_text,
						'button_link' => get_permalink( $action->ID ),
					];
				}
				$fields['button_text'] = __( 'Load More', 'planet4-blocks' );
			}

			return $covers;
		}

		/**
		 * Populate posts for content four column template.
		 *
		 * @param array $fields  This is the array of fields of this block.
		 *
		 * @return array
		 */
		private function populate_posts_for_cfc( &$fields ) {

			$post_ids = $fields['posts'] ?? '';

			if ( '' !== $post_ids ) {
				$posts = $this->filter_posts_by_ids( $fields );
			} else {
				$posts = $this->filter_posts_for_cfc( $fields );
			}

			$posts_array = [];

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

			return $posts_array;
		}

		/**
		 * Shortcake's altered ajax handler for post_select fields.
		 * Ajax handler for select2 post field queries.
		 * Output JSON containing post data.
		 * Requires that shortcode, attr and nonce are passed.
		 * Requires that the field has been correctly registred and can be found in $this->post_fields
		 * Supports passing page number and search query string.
		 */
		public function action_wp_ajax_shortcode_ui_post_field() {

			$nonce = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : null; // WPCS: CSRF ok.
			$type  = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : '0'; // WPCS: CSRF ok.

			$response = array(
				'items'          => array(),
				'found_items'    => 0,
				'items_per_page' => 0,
			);

			if ( ! wp_verify_nonce( $nonce, 'shortcode_ui_field_post_select' ) ) {
				wp_send_json_error( $response );
			}

			// Change query args based on cover type.
			if ( '1' === $type ) {
				$options     = get_option( 'planet4_options' );
				$act_page_id = $options['act_page'] ?? '';
				$query_args  = [
					'orderby'          => 'post_title',
					'order'            => 'ASC',
					'post_type'        => 'page',
					'suppress_filters' => false,
				];

				if ( 0 !== absint( $act_page_id ) ) {
					$query_args['post_parent'] = $act_page_id;
				}
			} else {
				$query_args = [
					'post_type' => 'post',
					'orderby'   => 'post_title',
					'order'     => 'ASC',
				];
			}

			// Hardcoded query args.
			$query_args['fields'] = 'ids';
			$query_args['perm']   = 'readable';

			if ( isset( $_GET['page'] ) ) {
				$query_args['paged'] = sanitize_text_field( $_GET['page'] );
			}

			if ( ! empty( $_GET['s'] ) ) {
				$query_args['s'] = sanitize_text_field( wp_unslash( $_GET['s'] ) );
			}

			if ( ! empty( $_GET['include'] ) ) {
				$post__in                          = is_array( $_GET['include'] ) ? $_GET['include'] : explode( ',', $_GET['include'] );
				$query_args['post__in']            = array_map( 'intval', $post__in );
				$query_args['orderby']             = 'post__in';
				$query_args['ignore_sticky_posts'] = true;
				$query_args['post_type']           = [ 'post', 'page' ];
			}

			$query = new \WP_Query( $query_args );
			foreach ( $query->posts as $post_id ) {
				$text = html_entity_decode( get_the_title( $post_id ) );

				array_push(
					$response['items'],
					[
						'id'   => $post_id,
						'text' => $text,
					]
				);
			}

			$response['found_items']    = $query->found_posts;
			$response['items_per_page'] = $query->query_vars['posts_per_page'];

			wp_send_json_success( $response );

		}

		/**
		 * Split a comma separated list of integers.
		 *
		 * @param string $string The string to be separated by delimiter.
		 *
		 * @return array
		 */
		private function split_to_integers( $string ) {
			if ( empty( $string ) || 1 !== preg_match( '/^\d+(,\d+)*$/', $string ) ) {
				return [];
			}

			return explode( ',', $string );

		}
	}
}

<?php
/**
 * Covers block class
 *
 * @package P4BKS
 * @since 0.1.13
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'Covers_Controller' ) ) {

	/**
	 * Class Covers_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 * @since 0.1.13
	 */
	class Covers_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'covers';

		/**
		 * Shortcode UI setup for the shortcode.
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
						'data-plugin' => 'planet4-blocks',
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
					'attr'        => 'select_tag',
					'label'       => __( 'Select Tags', 'planet4-blocks-backend' ),
					'description' => __( 'Associate this block with Actions that have specific Tags', 'planet4-blocks-backend' ),
					'type'        => 'term_select',
					'taxonomy'    => 'post_tag',
					'multiple'    => true,
				],
				[
					'label'       => __( 'Number of covers displayed', 'planet4-blocks-backend' ),
					'description' => __(
						'Show 1 Row: Displays 3 Covers on desktop and 2 Covers on mobile.<br>Show 2 Rows: Displays 6 Covers on desktop and 4 covers on mobile.<br>
(Another Row will be revealed each time the Load More button is clicked)<br>Show All Covers: Displays all available Covers on desktop and 4 Covers on mobile.',
						'planet4-blocks-backend'
					),
					'attr'        => 'covers_view',
					'type'        => 'select',
					'options'     => [
						[
							'value' => '0',
							// translators: placeholder is a number.
							'label' => __( 'Show 1 Row', 'planet4-blocks-backend' ),
						],
						[
							'value' => '3',
							// translators: placeholder is a number.
							'label' => __( 'Show 2 Rows', 'planet4-blocks-backend' ),
						],
						[
							'value' => '1',
							'label' => __( 'Show All Covers', 'planet4-blocks-backend' ),
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
				'label'         => __( 'Take Action Covers', 'planet4-blocks-backend' ),

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
		 * @param array  $fields This is the array of fields of this block.
		 * @param string $content This is the post content.
		 * @param string $shortcode_tag The shortcode tag of this block.
		 *
		 * @return array The data to be passed in the View.
		 */
		public function prepare_data( $fields, $content = '', $shortcode_tag = 'shortcake_' . self::BLOCK_NAME ) : array {
			$select_tags = $fields['select_tag'] ?? '';

			$options       = get_option( 'planet4_options' );
			$parent_act_id = $options['act_page'];
			$actions       = [];

			if ( 0 !== absint( $parent_act_id ) ) {
				$args = [
					'post_type'        => 'page',
					'post_status'      => 'publish',
					'post_parent'      => $parent_act_id,
					'orderby'          => 'menu_order',
					'order'            => 'ASC',
					'suppress_filters' => false,
					'numberposts'      => P4BKS_COVERS_NUM,
				];
				// If user selected a tag to associate with the Take Action page covers.
				if ( $select_tags ) {
					$tag_ids         = explode( ',', $select_tags );
					$args['tag__in'] = $tag_ids;
				}
				// phpcs:ignore
				$actions = get_posts( $args );
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
						'excerpt'     => get_the_excerpt( $action ),    // Note: WordPress removes shortcodes from auto-generated excerpts.
						'image'       => get_the_post_thumbnail_url( $action, 'large' ),
						'button_text' => $cover_button_text,
						'button_link' => get_permalink( $action->ID ),
					];
				}
				$fields['button_text'] = __( 'Load More ...', 'planet4-blocks' );
			}

			$covers_view = isset( $fields['covers_view'] ) ? intval( $fields['covers_view'] ) : 1;

			$data = [
				'fields'      => $fields,
				'covers'      => $covers,
				'covers_view' => $covers_view,
			];
			return $data;
		}
	}
}

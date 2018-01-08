<?php

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'SplitTwoColumns_Controller' ) ) {

	/**
	 * Class SplitTwoColumns_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 */
	class SplitTwoColumns_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'split_two_columns';

		/**
		 * Shortcode UI setup for the shortcode.
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 */
		public function prepare_fields() {

			$focus_options = [
				[ 'value' => 'left top',      'label' => __( '1 - Top Left', 'planet4-blocks' ) ],
				[ 'value' => 'center top',    'label' => __( '2 - Top Center', 'planet4-blocks' ) ],
				[ 'value' => 'right top',     'label' => __( '3 - Top Right', 'planet4-blocks' ) ],
				[ 'value' => 'left center',   'label' => __( '4 - Middle Left', 'planet4-blocks' ) ],
				[ 'value' => 'center center', 'label' => __( '5 - Middle Center', 'planet4-blocks' ) ],
				[ 'value' => 'right center',  'label' => __( '6 - Middle Right', 'planet4-blocks' ) ],
				[ 'value' => 'left bottom',   'label' => __( '7 - Bottom Left', 'planet4-blocks' ) ],
				[ 'value' => 'center bottom', 'label' => __( '8 - Bottom Center', 'planet4-blocks' ) ],
				[ 'value' => 'right bottom',  'label' => __( '9 - Bottom Right', 'planet4-blocks' ) ],
			];

			$option_values     = get_option( 'planet4_options' );
			$issue_category_id = $option_values['issues_parent_category'] ?? '';
			$categories        = [];
			if ( 0 !== absint( $issue_category_id ) ) {
				$categories   = get_categories( [
					'parent'    => $issue_category_id,                  // Get the dynamic id of the 'Issue' category.
					'orderby'   => 'name',
					'order'     => 'ASC',
				] );
			}

			$options = [];
			if ( $categories ) {
				foreach ( $categories as $category ) {
					$issue = get_page_by_title( $category->name );      // Category and Issue need to have the same name.
					if ( $issue ) {
						$options[] = [
							'value' => (string) $issue->ID,
							'label' => get_the_title( $issue->ID ),
						];
					}
				}
			}

			$fields = [
				[
					'attr'        => 'select_issue',
					'label'       => __( 'Select an Issue', 'planet4-blocks' ),
					'description' => __( 'Associate this block to the Issue that it will talk about', 'planet4-blocks' ),
					'type'        => 'select',
					'options'     => $options,
				],
				[
					'attr'        => 'select_tag',
					'label'       => __( 'Select a Tag', 'planet4-blocks' ),
					'description' => __( 'Associate the selected Issue with a Tag', 'planet4-blocks' ),
					'type'        => 'term_select',
					'taxonomy'    => 'post_tag',
				],
				[
					'label'       => __( 'Select focus point for issue image', 'planet4-blocks' ) . '<img src="' . esc_url( plugins_url( '/planet4-plugin-blocks/admin/images/grid_9.png' ) ) . '" />',
					'attr'        => 'focus_issue_image',
					'type'        => 'select',
					'options'     => $focus_options,
				],
				[
					'label'       => __( 'Select focus point for campaign image', 'planet4-blocks' ) . '<img src="' . esc_url( plugins_url( '/planet4-plugin-blocks/admin/images/grid_9.png' ) ) . '" />',
					'attr'        => 'focus_tag_image',
					'type'        => 'select',
					'options'     => $focus_options,
				],
				[
					'label' => __( 'Description', 'planet4-blocks' ),
					'attr'  => 'description',
					'type'  => 'textarea',
					'meta'  => [
						'placeholder' => __( 'Enter description', 'planet4-blocks' ),
					],
					'description' => __( '(Optional)', 'planet4-blocks' ),
				],
				[
					'label' => __( 'Button text', 'planet4-blocks' ),
					'attr'  => 'button_text',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Enter button text', 'planet4-blocks' ),
					],
					'description' => __( '(Optional)', 'planet4-blocks' ),
				],
				[
					'label' => __( 'Button link', 'planet4-blocks' ),
					'attr'  => 'button_link',
					'type'  => 'url',
					'meta'  => [
						'placeholder' => __( 'Enter button link', 'planet4-blocks' ),
					],
					'description' => __( '(Optional)', 'planet4-blocks' ),
				],

			];

			/*
			 * Define the Shortcode UI arguments.
			 */
			$shortcode_ui_args = [

				/*
				 * How the shortcode should be labeled in the UI. Required argument.
				 */
				'label' => __( 'Split Two Columns', 'planet4-blocks' ),

				/*
				 * Include an icon with your shortcode. Optional.
				 * Use a dashicon, or full HTML (e.g. <img src="/path/to/your/icon" />).
				 */
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/' . self::BLOCK_NAME . '.png' ) . '" />',

				/*
				 * Define the UI for attributes of the shortcode. Optional.
				 * See above, to where the the assignment to the $fields variable was made.
				 */
				'attrs' => $fields,
			];

			shortcode_ui_register_for_shortcode( 'shortcake_' . self::BLOCK_NAME, $shortcode_ui_args );
		}

		/**
		 * Callback for the shortcode.
		 * It renders the shortcode based on supplied attributes.
		 *
		 * @param array  $fields Array of fields that are to be used in the template.
		 * @param string $content The content of the post.
		 * @param string $shortcode_tag The shortcode tag.
		 *
		 * @return string
		 */
		public function prepare_template( $fields, $content, $shortcode_tag ) : string {
			$issue_id = absint( $fields['select_issue'] );
			$issue_meta_data = get_post_meta( $issue_id );

			$tag_id        = absint( $fields['select_tag'] );
			$tag           = get_term( $tag_id );
			$attachment_id = get_term_meta( $tag_id, 'tag_attachment_id', true );

			$data = $issue_meta_data ? [
				'issue' => [
					'title'       => $issue_meta_data['p4_title'][0] ?? get_the_title( $issue_id ),
					'description' => $issue_meta_data['p4_description'][0] ?? '',
					'image'       => get_the_post_thumbnail_url( $issue_id ),
					'link_text'   => __( 'Learn more about this issue', 'planet4-blocks' ),
					'link_url'    => get_permalink( $issue_id ),
					'focus'       => $fields['focus_issue_image'] ?? '',
				],
				'campaign' => [
					'image'       => wp_get_attachment_url( $attachment_id ),
					'srcset'      => wp_calculate_image_srcset( ['1118', '746'], wp_get_attachment_image_src( $attachment_id, 'full' )[0], wp_get_attachment_metadata( $attachment_id ) ),
					'name'        => $tag->name,
					'link'        => get_tag_link( $tag ),
					'description' => $fields['description'] ?? $tag->description,
					'button_text' => $fields['button_text'] ?? __( 'Get Involved', 'planet4-blocks' ),
					'button_link' => $fields['button_link'] ?? get_tag_link( $tag ),
					'focus'       => $fields['focus_tag_image'] ?? '',
				],
			] : [];

			// Shortcode callbacks must return content, hence, output buffering here.
			ob_start();
			$this->view->block( self::BLOCK_NAME, $data );

			return ob_get_clean();
		}
	}
}

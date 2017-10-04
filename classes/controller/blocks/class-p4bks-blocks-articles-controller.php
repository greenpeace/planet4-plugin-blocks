<?php

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'P4BKS_Blocks_Articles_Controller' ) ) {

	class P4BKS_Blocks_Articles_Controller extends P4BKS_Blocks_Controller {


		/**
		 * Override this method in order to give your block its own name.
		 */
		public function load() {
			$this->block_name = 'articles';
			parent::load();
		}

		/**
		 * Shortcode UI setup for the article shortcode.
		 *
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 *
		 * @since 0.1.0
		 */
		public function prepare_fields() {
				$fields = array(
					array(
						'label' => esc_html__( 'Article Heading', 'planet4-blocks' ),
						'attr'  => 'article_heading',
						'type'  => 'text',
						'meta'  => array(
							'placeholder' => esc_html__( 'Enter article heading', 'planet4-blocks' ),
						),
					),
					array(
						'label' => esc_html__( 'Article Count', 'planet4-blocks' ),
						'attr'  => 'article_count',
						'type'  => 'text',
						'meta'  => array(
							'placeholder' => esc_html__( 'Enter articles count', 'planet4-blocks' ),
						),
					),
				);

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = array(
				'label' => esc_html__( 'Articles', 'planet4-blocks' ),
				'listItemImage' => 'dashicons-editor-table',
				'attrs' => $fields,
			);

			shortcode_ui_register_for_shortcode( 'shortcake_' . $this->block_name, $shortcode_ui_args );
		}

		/**
		 * Callback for the shortcode.
		 * It renders the shortcode based on supplied attributes.
		 *
		 * @param array  $fields
		 * @param string $content
		 * @param string $shortcode_tag
		 *
		 * @since 0.1.0
		 *
		 * @return string
		 */
		public function prepare_template( $fields, $content, $shortcode_tag ) : string {

			// Get all posts with arguments.
			$args = array(
				'numberposts' => $fields['article_count'],
				'order' => 'ASC',
				'orderby' => 'title',
			);
			$all_posts = wp_get_recent_posts( $args );

			foreach ( $all_posts as $recent ) {
				if ( has_post_thumbnail( $recent['ID'] ) ) {
						$thumbnail_image = get_the_post_thumbnail_url( $recent['ID'],'single-post-thumbnail' );
						$recent['thumbnail'] = $thumbnail_image;
				}
				$recent['tags'] = wp_get_post_tags( $recent['ID'] );
				$recent['category'] = get_the_category( $recent['ID'] );
				$recent_posts[] = $recent;
			}

			$data = [
				'fields' => array_map( 'wp_kses_post', $fields ),
				'recent_posts'  => $recent_posts,
			];

			// Shortcode callbacks must return content, hence, output buffering here.
			ob_start();
			$this->view->block( $this->block_name, $data );

			return ob_get_clean();
		}
	}
}

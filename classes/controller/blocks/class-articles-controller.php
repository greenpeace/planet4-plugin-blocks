<?php

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'Articles_Controller' ) ) {

	/**
	 * Class Articles_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 */
	class Articles_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'articles';

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
					'label' => __( 'Article Heading', 'planet4-blocks' ),
					'attr'  => 'article_heading',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => __( 'Enter article heading', 'planet4-blocks' ),
					),
				),
				array(
					'label' => __( 'Article Count', 'planet4-blocks' ),
					'attr'  => 'article_count',
					'type'  => 'number',
					'meta'  => array(
						'placeholder' => __( 'Enter articles count', 'planet4-blocks' ),
					),
				),
			);

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = array(
				'label'         => __( 'Articles', 'planet4-blocks' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/home_news.jpg' ) . '" />',
				'attrs'         => $fields,
			);

			shortcode_ui_register_for_shortcode( 'shortcake_' . self::BLOCK_NAME, $shortcode_ui_args );
		}

		/**
		 * Callback for the shortcode.
		 * It renders the shortcode based on supplied attributes.
		 *
		 * @param array  $fields This contains array of article shortcake block field.
		 * @param string $content This is the post content.
		 * @param string $shortcode_tag The shortcode block of article.
		 *
		 * @since 0.1.0
		 *
		 * @return string All the data used for the html.
		 */
		public function prepare_template( $fields, $content, $shortcode_tag ) : string {

			$fields['article_count'] = ( ! empty( $fields['article_count'] ) ) ? $fields['article_count'] : 3;

			//Get page categories
			$post_categories         = get_the_category();

			$category_id_array = [];
			foreach ( $post_categories as $category ) {
				$category_id_array[] = $category->term_id;
			}

			if ( $category_id_array ) {
				$category_ids = implode( ',', $category_id_array );
			}

			// Get all posts with arguments.
			$args = [
				'numberposts' => $fields['article_count'],
				'orderby'     => 'date',
				'category'    => '( ' . $category_ids . ' )'
			];

			$all_posts = wp_get_recent_posts( $args );

			if ( $all_posts ) {
				foreach ( $all_posts as $recent ) {
					$recent['alt_text']  = '';
					$recent['thumbnail'] = '';
					$recent['author']    = get_the_author_meta( 'display_name', $recent['post_author'] );

					if ( has_post_thumbnail( $recent['ID'] ) ) {
						$recent['thumbnail'] = get_the_post_thumbnail_url( $recent['ID'], 'single-post-thumbnail' );
						$img_id              = get_post_thumbnail_id( $recent['ID'] );
						$recent['alt_text']  = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
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
					$wp_categories  = get_the_category( $recent['ID'] );

					$categories = [];
					if ( $wp_categories ) {
						foreach ( $wp_categories as $wp_category ) {
							$category_data['name'] = $wp_category->name;
							$category_data['slug'] = $wp_category->slug;
							$category_data['href'] = get_category_link( $wp_category );
							$categories[]          = $category_data;
						}
					}

					$recent['category']  = $categories;
					$recent['permalink'] = get_permalink( $recent['ID'] );
					$recent_posts[]      = $recent;
				}
			}

			$data = [
				'fields'        => $fields,
				'recent_posts'  => $recent_posts,
				'domain'        => 'planet4-blocks',
			];

			// Shortcode callbacks must return content, hence, output buffering here.
			ob_start();
			$this->view->block( self::BLOCK_NAME, $data );

			return ob_get_clean();
		}
	}
}

<?php

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'Carousel_Controller' ) ) {

	/**
	 * Class Carousel_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 */
	class Carousel_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'carousel';

		/**
		 * Override this method in order to give your block its own name.
		 */
		public function load() {
			add_filter( 'attachment_fields_to_edit', array( $this, 'add_image_attachment_fields_to_edit' ), null, 2 );
			add_filter( 'attachment_fields_to_save', array( $this, 'add_image_attachment_fields_to_save' ), null, 2 );
			parent::load();
		}

		/**
		 * Add custom media metadata fields.
		 *
		 * @param array    $form_fields An array of fields included in the attachment form.
		 * @param \WP_Post $post The attachment record in the database.
		 * @return array Final array of form fields to use.
		 */
		function add_image_attachment_fields_to_edit( $form_fields, $post ) {

			// Add a Credit field
			$form_fields['credit_text'] = array(
				'label' => __( 'Credit' ),
				'input' => 'text', // this is default if "input" is omitted
				'value' => get_post_meta( $post->ID, '_credit_text', true ),
				'helps' => __( 'The owner of the image.' ),
			);

			return $form_fields;
		}

		/**
		 * Save custom media metadata fields
		 *
		 * @param \WP_Post $post The $post data for the attachment.
		 * @param array    $attachment The $attachment part of the form $_POST ($_POST[attachments][postID]).
		 *
		 * @return \WP_Post $post
		 */
		function add_image_attachment_fields_to_save( $post, $attachment ) {
			if ( isset( $attachment['credit_text'] ) ) {
				update_post_meta( $post['ID'], '_credit_text', $attachment['credit_text'] );
			}

			return $post;
		}

		/**
		 * Shortcode UI setup for the carousel shortcode.
		 *
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 */
		public function prepare_fields() {
			$fields = array(
				array(
					'label' => __( 'Title', 'planet4-blocks' ),
					'attr'  => 'carousel_block_title',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => __( 'Enter title', 'planet4-blocks' ),
					),
				),
				array(
					'label'       => __( 'Select Carousel Images', 'planet4-blocks' ),
					'attr'        => 'multiple_image',
					'type'        => 'attachment',
					'libraryType' => array( 'image' ),
					'multiple'    => true,
					'addButton'   => 'Select Carousel Images',
					'frameTitle'  => 'Select Carousel Images',
				)
			);

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = array(
				'label'         => __( 'Carousel', 'planet4-blocks' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/take_action_carousel.png' ) . '" />',
				'attrs'         => $fields,
			);

			shortcode_ui_register_for_shortcode( 'shortcake_' . self::BLOCK_NAME, $shortcode_ui_args );
		}

		/**
		 * Callback for the shortcode.
		 * It renders the shortcode based on supplied attributes.
		 *
		 * @param array  $fields This contains array of multiple image field.
		 * @param string $content This is the post content.
		 * @param string $shortcode_tag The shortcode block of carousel.
		 *
		 * @since 0.1.0
		 *
		 * @return string All the data used for the html.
		 */
		public function prepare_template( $fields, $content, $shortcode_tag ) : string {

			$explode_multiple_image_array = explode( ',',$fields['multiple_image'] );
			$images_data = array();

			foreach ( $explode_multiple_image_array as $image_id ) {

				$image_data_array             = wp_get_attachment_image_src( $image_id, 'large' );
				$images_data['image_src']     = $image_data_array[0];
				$images_data['image_srcset']  = wp_get_attachment_image_srcset( $image_id, 'large', wp_get_attachment_metadata( $image_id ) );
				$images_data['image_sizes']   = wp_calculate_image_sizes( 'large', null, null, $image_id );
				$images_data['alt_text']      = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
				$image_metadata               = get_post( $image_id );
				$attachment_fields            = get_post_custom( $image_id );
				$images_data['credits']       = ( isset( $attachment_fields['_credit_text'][0] ) && ! empty( $attachment_fields['_credit_text'][0] ) ) ? $attachment_fields['_credit_text'][0] : '';
				$images_data['title']         = $image_metadata->post_title;
				$images_data['description']   = $image_metadata->post_content;

				$images[] = $images_data;
			}

			$data = [
				'title'  => ( isset( $fields['carousel_block_title'] ) && ! empty( $fields['carousel_block_title'] ) ) ? $fields['carousel_block_title'] : '',
				'images' => $images,
				'domain' => 'planet4-blocks',
			];

			// Shortcode callbacks must return content, hence, output buffering here.
			ob_start();
			$this->view->block( self::BLOCK_NAME, $data );

			return ob_get_clean();
		}
	}
}

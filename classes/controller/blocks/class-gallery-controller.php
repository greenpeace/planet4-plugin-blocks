<?php
/**
 * Gallery block class
 *
 * @package P4BKS
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'Gallery_Controller' ) ) {

	/**
	 * Class Gallery_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 */
	class Gallery_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'gallery';

		const LAYOUT_SLIDER        = 1;
		const LAYOUT_THREE_COLUMNS = 2;
		const LAYOUT_GRID          = 3;

		/**
		 * Hooks all the needed functions to load the block.
		 */
		public function load() {
			add_filter( 'attachment_fields_to_edit', [ $this, 'add_image_attachment_fields_to_edit' ], null, 2 );
			add_filter( 'attachment_fields_to_save', [ $this, 'add_image_attachment_fields_to_save' ], null, 2 );
			add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_assets' ] );
			parent::load();
		}

		/**
		 * Add custom media metadata fields.
		 *
		 * @param array    $form_fields An array of fields included in the attachment form.
		 * @param \WP_Post $post The attachment record in the database.
		 *
		 * @return array Final array of form fields to use.
		 */
		public function add_image_attachment_fields_to_edit( $form_fields, $post ) {

			// Add a Credit field.
			$form_fields['credit_text'] = [
				'label' => __( 'Credit', 'planet4-blocks-backend' ),
				'input' => 'text', // this is default if "input" is omitted.
				'value' => get_post_meta( $post->ID, '_credit_text', true ),
				'helps' => __( 'The owner of the image.', 'planet4-blocks-backend' ),
			];

			return $form_fields;
		}

		/**
		 * Save custom media metadata fields
		 *
		 * @param \WP_Post $post        The $post data for the attachment.
		 * @param array    $attachment  The $attachment part of the form $_POST ($_POST[attachments][postID]).
		 *
		 * @return \WP_Post $post
		 */
		public function add_image_attachment_fields_to_save( $post, $attachment ) {
			if ( isset( $attachment['credit_text'] ) ) {
				update_post_meta( $post['ID'], '_credit_text', $attachment['credit_text'] );
			}

			return $post;
		}

		/**
		 * Shortcode UI setup for the gallery shortcode.
		 */
		public function prepare_fields() {
			$fields = [
				[
					'attr'        => 'gallery_block_style',
					'label'       => __( 'What style of gallery do you need?', 'planet4-blocks-backend' ),
					'description' => __( 'Change the style of gallery that you wish to display your images in.', 'planet4-blocks-backend' ),
					'type'        => 'p4_radio',
					'options'     => [
						[
							'value' => static::LAYOUT_SLIDER,
							'label' => __( 'Slider', 'planet4-blocks-backend' ),
							'desc'  => 'The slider is a carousel of images. For more than 5 images, consider using a grid.',
							'image' => esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/gallery-slider.jpg' ),
						],
						[
							'value' => static::LAYOUT_THREE_COLUMNS,
							'label' => __( '3 Column', 'planet4-blocks-backend' ),
							'desc'  => 'The 3 column image display is great for accentuating text, and telling a visual story.',
							'image' => esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/gallery-3-column.jpg' ),
						],
						[
							'value' => static::LAYOUT_GRID,
							'label' => __( 'Grid', 'planet4-blocks-backend' ),
							'desc'  => 'The grid shows thumbnails of lots of images. Good to use when showing lots of activity.',
							'image' => esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/gallery-grid.jpg' ),
						],
					],
				],
				[
					'label' => __( 'Title', 'planet4-blocks-backend' ),
					'attr'  => 'gallery_block_title',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Enter title', 'planet4-blocks-backend' ),
					],
				],
				[
					'label' => __( 'Description', 'planet4-blocks-backend' ),
					'attr'  => 'gallery_block_description',
					'type'  => 'textarea',
					'meta'  => [
						'placeholder' => __( 'Enter description for this gallery', 'planet4-blocks-backend' ),
						'data-plugin' => 'planet4-blocks',
					],
				],
				[
					'label'       => __( 'Select Gallery Images', 'planet4-blocks-backend' ),
					'description' => 'Select images in the order you want them to appear.',
					'attr'        => 'multiple_image',
					'type'        => 'attachment',
					'libraryType' => [ 'image' ],
					'multiple'    => true,
					'addButton'   => 'Select Gallery Images',
					'frameTitle'  => 'Select Gallery Images',
				],
				[
					'label' => __( 'Image focus points', 'planet4-blocks-backend' ),
					'attr'  => 'gallery_block_focus_points',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Enter image focus points', 'planet4-blocks-backend' ),
					],
				],
			];

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				'label'         => __( 'Gallery', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/take_action_carousel.png' ) . '" />',
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

			$gallery_style       = $fields['gallery_block_style'] ?? static::LAYOUT_SLIDER;
			$gallery_title       = $fields['gallery_block_title'] ?? '';
			$gallery_description = $fields['gallery_block_description'] ?? '';
			$images              = [];

			if ( isset( $fields['multiple_image'] ) ) {
				$exploded_images = explode( ',', $fields['multiple_image'] );
			} else {
				$exploded_images = [];
			}

			if ( isset( $fields['gallery_block_focus_points'] ) ) {
				$img_focus_points = json_decode( str_replace( "'", '"', $fields['gallery_block_focus_points'] ), true );
			} else {
				$img_focus_points = [];
			}

			$images_dimensions = [];
			$image_sizes       = [
				self::LAYOUT_SLIDER        => 'retina-large',
				self::LAYOUT_THREE_COLUMNS => 'medium_large',
				self::LAYOUT_GRID          => 'medium',
			];

			foreach ( $exploded_images as $image_id ) {
				$image_size = $image_sizes[ $fields['gallery_block_style'] ];
				$image_data = [];

				$image_data_array           = wp_get_attachment_image_src( $image_id, $image_size );
				$image_data['image_src']    = $image_data_array ? $image_data_array[0] : '';
				$image_data['image_srcset'] = wp_get_attachment_image_srcset( $image_id, $image_size, wp_get_attachment_metadata( $image_id ) );
				$image_data['image_sizes']  = wp_calculate_image_sizes( $image_size, null, null, $image_id );
				$image_data['alt_text']     = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
				$image_data['caption']      = wp_get_attachment_caption( $image_id );
				$image_data['focus_image']  = $img_focus_points[ $image_id ] ?? '';
				$attachment_fields          = get_post_custom( $image_id );
				$image_data['credits']      = '';
				if ( isset( $attachment_fields['_credit_text'][0] ) && ! empty( $attachment_fields['_credit_text'][0] ) ) {
					$image_data['credits'] = $attachment_fields['_credit_text'][0];
					if ( ! is_numeric( strpos( $attachment_fields['_credit_text'][0], '©' ) ) ) {
						$image_data['credits'] = '© ' . $image_data['credits'];
					}
				}

				if ( count( (array) $image_data_array ) >= 3 ) {
					$images_dimensions[] = $image_data_array[1];
					$images_dimensions[] = $image_data_array[2];
				}

				$images[] = $image_data;
			}

			$gallery_id = 'gallery_' . uniqid();

			$data = [
				'id'          => $gallery_id,
				'layout'      => $gallery_style,
				'title'       => $gallery_title,
				'description' => $gallery_description,
				'images'      => $images,
			];

			return $data;
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
					$image_focus_points = [
						'landscape'        => [
							[
								'value' => 'left top',
								'label' => __( '1 - Top Left', 'planet4-blocks-backend' ),
							],
							[
								'value' => 'center top',
								'label' => __( '2 - Top Center', 'planet4-blocks-backend' ),
							],
							[
								'value' => 'right top',
								'label' => __( '3 - Top Right', 'planet4-blocks-backend' ),
							],
							[
								'value' => 'left center',
								'label' => __( '4 - Middle Left', 'planet4-blocks-backend' ),
							],
							[
								'value' => 'center center',
								'label' => __( '5 - Middle Center', 'planet4-blocks-backend' ),
							],
							[
								'value' => 'right center',
								'label' => __( '6 - Middle Right', 'planet4-blocks-backend' ),
							],
							[
								'value' => 'left bottom',
								'label' => __( '7 - Bottom Left', 'planet4-blocks-backend' ),
							],
							[
								'value' => 'center bottom',
								'label' => __( '8 - Bottom Center', 'planet4-blocks-backend' ),
							],
							[
								'value' => 'right bottom',
								'label' => __( '9 - Bottom Right', 'planet4-blocks-backend' ),
							],
						],
						'portrait'         => [
							[
								'value' => 'top',
								'label' => __( '1 - Top', 'planet4-blocks-backend' ),
							],
							[
								'value' => 'center',
								'label' => __( '2 - Center', 'planet4-blocks-backend' ),
							],
							[
								'value' => 'bottom',
								'label' => __( '3 - Bottom', 'planet4-blocks-backend' ),
							],
						],
						'options_img_grid' => [
							'landscape' => esc_url( plugins_url( '/planet4-plugin-blocks/admin/images/grid_9.png' ) ),
							'portrait'  => esc_url( plugins_url( '/planet4-plugin-blocks/admin/images/grid_3.png' ) ),
						],
						'label'            => __( 'Select focus point', 'planet4-blocks-backend' ),
					];

					wp_localize_script( 'blocks-ui', 'image_focus_points', $image_focus_points );
				}
			);
		}
	}
}

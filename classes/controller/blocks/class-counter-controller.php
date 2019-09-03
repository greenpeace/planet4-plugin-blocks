<?php
/**
 * Counter block class
 *
 * @link URL
 *
 * @package P4BKS
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'Counter_Controller' ) ) {

	/**
	 * Class Counter_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 */
	class Counter_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'counter';

		/** @const array BLOCK_ALLOWED_POST_TYPES */
		const BLOCK_ALLOWED_POST_TYPES = [ 'page', 'campaign', 'post' ];

		/**
		 * Shortcode UI setup
		 */
		public function prepare_fields() {
			$fields = [
				[
					'label' => __( 'Title', 'planet4-blocks-backend' ),
					'attr'  => 'title',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Enter title', 'planet4-blocks-backend' ),
					],
				],
				[
					'label' => __( 'Description', 'planet4-blocks-backend' ),
					'attr'  => 'description',
					'type'  => 'textarea',
					'meta'  => [
						'placeholder' => __( 'Enter description', 'planet4-blocks-backend' ),
					],
				],
				[
					'attr'    => 'style',
					'label'   => __( 'What style of counter do you need?', 'planet4-blocks-backend' ),
					'type'    => 'p4_radio',
					'options' => [
						[
							'value' => 'plain',
							'label' => __( 'Text only', 'planet4-blocks-backend' ),
							'image' => esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/counter_th_text.png' ),
						],
						[
							'value' => 'bar',
							'label' => __( 'Progress bar', 'planet4-blocks-backend' ),
							'image' => esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/counter_th_bar.png' ),
						],
						[
							'value' => 'arc',
							'label' => __( 'Progress dial', 'planet4-blocks-backend' ),
							'image' => esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/counter_th_arc.png' ),
						],
						[
							'value' => 'en-forms-bar',
							'label' => __( 'Progress bar inside EN Form', 'planet4-blocks-backend' ),
							'image' => esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/counter_th_bar.png' ),
						],
					],
				],
				[
					'label' => __( 'Completed', 'planet4-blocks-backend' ),
					'attr'  => 'completed',
					'type'  => 'number',
					'meta'  => [
						'placeholder' => __( 'e.g. number of signatures', 'planet4-blocks-backend' ),
						'data-plugin' => 'planet4-blocks',
					],
				],
				[
					'label' => __( 'Completed API URL', 'planet4-blocks-backend' ),
					'attr'  => 'completed_api',
					'type'  => 'url',
					'meta'  => [
						'placeholder' => __( 'API URL of completed number. If filled in will overide the "Completed" field', 'planet4-blocks-backend' ),
						'data-plugin' => 'planet4-blocks',
					],
				],
				[
					'label' => __( 'Target', 'planet4-blocks-backend' ),
					'attr'  => 'target',
					'type'  => 'number',
					'meta'  => [
						'placeholder' => __( 'e.g. target no. of signatures', 'planet4-blocks-backend' ),
						'data-plugin' => 'planet4-blocks',
					],
				],
				[
					'label'       => __( 'Text', 'planet4-blocks-backend' ),
					'attr'        => 'text',
					'type'        => 'textarea',
					'description' => __( 'These placeholders can be used: ', 'planet4-blocks-backend' ) .
								'<code>%completed%</code>, <code>%target%</code>, <code>%remaining%</code>',
					'meta'        => [
						'placeholder' => __( 'e.g. "signatures collected of %target%"', 'planet4-blocks-backend' ),
						'data-plugin' => 'planet4-blocks',
					],
				],
			];

			$shortcode_ui_args = [
				'label'         => __( 'Counter', 'planet4-blocks-backend' ),
				// TODO make preview thumbnail image.
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/counter.png' ) . '" />',
				'attrs'         => $fields,
				'post_type'     => self::BLOCK_ALLOWED_POST_TYPES,
			];

			shortcode_ui_register_for_shortcode( 'shortcake_' . self::BLOCK_NAME, $shortcode_ui_args );
		}

		/**
		 * Get all the data that will be needed to render the block correctly.
		 *
		 * @param array  $fields This is the array of fields of the block.
		 * @param string $content This is the post content.
		 * @param string $shortcode_tag The shortcode tag of the block.
		 *
		 * @return array The data to be passed in the View.
		 */
		public function prepare_data( $fields, $content, $shortcode_tag ) : array {

			$completed = isset( $fields['completed'] ) ? floatval( $fields['completed'] ) : 0;
			$target    = isset( $fields['target'] ) ? floatval( $fields['target'] ) : 0;

			if ( array_key_exists( 'completed_api', $fields ) ) {
				$response_api = wp_safe_remote_get( $fields['completed_api'] );
				if ( is_array( $response_api ) ) {
					$response_body = json_decode( $response_api['body'], true );
					if ( is_array( $response_body ) && array_key_exists( 'unique_count', $response_body ) && is_int( $response_body['unique_count'] ) ) {
						$completed = floatval( $response_body['unique_count'] );
					}
				}
			}

			$remaining = $target > $completed ? $target - $completed : 0;

			// Note: something seems to strip out sensible delimiters like {}, <>, [] and $$ in the WYSIWYG.
			$fields['completed'] = number_format( $completed );
			$fields['target']    = number_format( $target );
			$fields['remaining'] = number_format( $remaining );
			$fields['percent']   = $target > 0 ? round( $completed / $target * 100 ) : 0;

			return [
				'fields' => $fields,
			];
		}
	}
}

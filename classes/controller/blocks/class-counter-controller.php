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
		const BLOCK_NAME = 'counter';

		/**
		 * Shortcode UI setup
		 */
		public function prepare_fields() {
			$fields = [
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
					'type'        => 'text',
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
				'post_type'     => P4BKS_ALLOWED_PAGETYPE,
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
			$completed = floatval( $fields['completed'] );
			$target    = floatval( $fields['target'] );

			$fields['percent'] = $target > 0 ? round( $completed / $target * 100 ) : 0;
			$fields['text']    = str_replace(
				// Note: something seems to strip out sensible delimiters like {}, <>, [] and $$ in the WYSIWYG.
				[ '%completed%', '%target%', '%remaining%' ],
				[ number_format( $completed ), number_format( $target ), number_format( $target - $completed ) ],
				$fields['text']
			);

			return [
				'fields' => $fields,
			];
		}
	}
}

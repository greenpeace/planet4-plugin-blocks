<?php
/**
 * Submenu block class
 *
 * @package P4BKS
 * @since 1.3
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'SubMenu_Controller' ) ) {

	/**
	 * Class SubMenu_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 * @since 1.3
	 */
	class SubMenu_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'submenu';

		/**
		 * Hooks all the needed functions to load the block.
		 */
		public function load() {
			parent::load();
			add_action( 'admin_print_footer_scripts-post.php', [ $this, 'print_admin_footer_scripts' ], 1 );
			add_action( 'admin_print_footer_scripts-post-new.php', [ $this, 'print_admin_footer_scripts' ], 1 );
		}

		/**
		 * Load underscore templates to footer.
		 */
		public function print_admin_footer_scripts() {
			echo $this->get_template( 'submenu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Shortcode UI setup for content four column shortcode.
		 *
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 *
		 * This example shortcode has many editable attributes, and more complex UI.
		 *
		 * @since 1.0.0
		 */
		public function prepare_fields() {

			$heading_options = [
				[
					'value' => '0',
					'label' => __( 'None', 'planet4-blocks-backend' ),
				],
			];
			for ( $i = 1; $i < 7; $i ++ ) {
				$heading_options[] = [
					'value' => (string) $i,
					'label' => __( 'Heading', 'planet4-blocks-backend' ) . " $i",
				];
			}

			$fields = [
				[
					'block_heading'     => __( 'Anchor Link Submenu', 'planet4-blocks-backend' ),
					'block_description' => __(
						'An in-page table of contents to help users have a sense of what\'s on the page and let them jump to a topic they are interested in.',
						'planet4-blocks-backend'
					),
					'attr'              => 'submenu_style',
					'label'             => __( 'What style of menu do you need?', 'planet4-blocks-backend' ),
					'description'       => __( 'Associate this block with Posts that have a specific Tag', 'planet4-blocks-backend' ),
					'type'              => 'p4_radio',
					'options'           => [
						[
							'value' => '1',
							'label' => __( 'Long full-width', 'planet4-blocks-backend' ),
							'desc'  => 'Use: on long pages (more than 5 screens) when list items are long (+ 10 words)<br>No max items<br>recommended.',
							'image' => esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/submenu-long.jpg' ),
						],
						[
							'value' => '2',
							'label' => __( 'Short full-width', 'planet4-blocks-backend' ),
							'desc'  => 'Use: on long pages (more than 5 screens) when list items are short (up to 5 words)<br>No max items<br>recommended.',
							'image' => esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/submenu-short.jpg' ),
						],
						[
							'value' => '3',
							'label' => __( 'Short sidebar', 'planet4-blocks-backend' ),
							'desc'  => 'Use: on long pages (more than 5 screens) when list items are short (up to 10 words)<br>Max items<br>recommended: 9',
							'image' => esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/submenu-sidebar.jpg' ),
						],
					],
				],
				[
					'label'       => __( '<h3>Submenu title</h3>', 'planet4-blocks-backend' ),
					'description' => '<i>(Optional)</i><br><br><hr>',
					'attr'        => 'title',
					'type'        => 'text',
					'meta'        => [
						'placeholder' => __( 'Enter title for this block', 'planet4-blocks-backend' ),
						'data-plugin' => 'planet4-blocks',
					],
				],
			];

			foreach ( range( 1, 3 ) as $i ) {
				array_push(
					$fields,
					[
						'attr'    => "heading$i",
						'label'   => __( '<b>Submenu item</b>', 'planet4-blocks-backend' ),
						'type'    => 'p4_select',
						'options' => $heading_options,
					],
					[
						'attr'  => "link$i",
						// phpcs:ignore WordPress.WP.I18n.MissingArgDomain
						'label' => __( 'Link' ),
						'type'  => 'p4_checkbox',
						'value' => 'false',
					],
					[
						'attr'    => "style$i",
						'label'   => __( 'List style', 'planet4-blocks-backend' ),
						'type'    => 'p4_select',
						'options' => [
							'none'   => __( 'None', 'planet4-blocks-backend' ),
							'bullet' => __( 'Bullet', 'planet4-blocks-backend' ),
							'number' => __( 'Number', 'planet4-blocks-backend' ),
						],
					]
				);
			}

			// Define the Shortcode UI arguments.
			$shortcode_ui_args = [
				'label'         => __( 'Submenu', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/submenu.png' ) . '" />',
				'attrs'         => $fields,
				'post_type'     => P4BKS_ALLOWED_PAGETYPE,
			];

			shortcode_ui_register_for_shortcode( 'shortcake_' . self::BLOCK_NAME, $shortcode_ui_args );
		}

		/**
		 * Get all the data that will be needed to render the block correctly.
		 *
		 * @param array  $attributes This is the array of fields of this block.
		 * @param string $content This is the post content.
		 * @param string $shortcode_tag The shortcode tag of this block.
		 *
		 * @return array The data to be passed in the View.
		 */
		public function prepare_data( $attributes, $content = '', $shortcode_tag = 'shortcake_' . self::BLOCK_NAME ) : array {

			global $post;

			$content = $post->post_content;
			$menu    = $this->parse_post_content( $content, $attributes );

			wp_enqueue_script( 'submenu', P4BKS_ADMIN_DIR . 'js/submenu.js', [ 'jquery' ], '0.2', true );
			wp_localize_script( 'submenu', 'submenu', $menu );

			$block_data = [
				'title' => $attributes['title'] ?? '',
				'menu'  => $menu,
				'style' => $attributes['submenu_style'] ?? '1',
			];
			return $block_data;
		}

		/**
		 * Parse post's content to extract headings and build menu
		 *
		 * @param string $content Post content.
		 * @param array  $attributes Submenu block attributes.
		 *
		 * @return array
		 */
		private function parse_post_content( $content, $attributes ) {

			// Validate, if $content is empty.
			if ( ! $content ) {
				return [];
			}

			// make array of heading level metadata keyed by tag name.
			$heading_meta = [];
			foreach ( range( 1, 3 ) as $heading_num ) {
				$heading = $this->heading_attributes( $heading_num, $attributes );
				if ( ! $heading ) {
					break;
				}
				$heading['level']                = $heading_num;
				$heading_meta[ $heading['tag'] ] = $heading;
			}

			$dom = new \DOMDocument();
			$dom->loadHtml( $content );
			$xpath = new \DOMXPath( $dom );

			// get all the headings as an array of nodes.
			$xpath_expression = '//' . join( ' | //', array_keys( $heading_meta ) );
			$node_list        = $xpath->query( $xpath_expression );
			$nodes            = iterator_to_array( $node_list );

			// process nodes array recursively to build menu.
			return $this->build_menu( 1, $nodes, $heading_meta );
		}

		/**
		 * Extract shortcode attributes for given heading level.
		 *
		 * @param int   $menu_level Level 1, 2 or 3.
		 * @param array $attributes Shortcode UI attributes.
		 *
		 * @return array|null associative array or null if menu level is not configured
		 */
		private function heading_attributes( $menu_level, $attributes ) {
			return empty( $attributes[ 'heading' . $menu_level ] )
				? null
				: [
					'heading' => $attributes[ 'heading' . $menu_level ],
					'tag'     => 'h' . $attributes[ 'heading' . $menu_level ],
					'link'    => $attributes[ 'link' . $menu_level ] ?? false,
					'style'   => $attributes[ 'style' . $menu_level ] ?? 'none',
				];
		}

		/**
		 * Process flat array of DOM nodes to build up menu tree structure.
		 *
		 * @param int        $current_level Current menu nesting level.
		 * @param \DOMNode[] $nodes Array of heading DOM nodes, passed by reference.
		 * @param array      $heading_meta Metadata about each heading tag.
		 *
		 * @return array menu tree structure
		 */
		private function build_menu( $current_level, &$nodes, $heading_meta ) {
			$menu = [];

			// phpcs:ignore Squiz.PHP.DisallowSizeFunctionsInLoops.Found
			while ( count( $nodes ) ) {
				// consider first node in the list but don't remove it yet.
				$node = $nodes[0];

				$heading = $heading_meta[ $node->nodeName ];
				if ( $heading['level'] > $current_level ) {
					if ( count( $menu ) === 0 ) {
						// we're skipping over a heading level so create an empty node.
						$menu[] = new \stdClass();
					}
					$menu[ count( $menu ) - 1 ]->children = $this->build_menu( $current_level + 1, $nodes, $heading_meta );
				} elseif ( $heading['level'] < $current_level ) {
					return $menu;
				} else {
					$menu[] = $this->create_menu_item( $node->nodeValue, $heading['tag'], $heading['link'], $heading['style'] );

					// remove node from list only once it has been added to the menu.
					array_shift( $nodes );
				}
			}

			return $menu;
		}

		/**
		 * Create a std object representing a node/heading.
		 *
		 * @param string      $text Heading/menu item text.
		 * @param string      $type  Type/name of the tag.
		 * @param bool|string $link True if this menu item should link to the heading.
		 * @param string      $style List style for menu item.
		 *
		 * @return \stdClass
		 */
		private function create_menu_item( $text, $type, $link, $style ) {
			$menu_obj           = new \stdClass();
			$menu_obj->text     = utf8_decode( $text );
			$menu_obj->hash     = md5( $text );
			$menu_obj->type     = $type;
			$menu_obj->style    = $style;
			$menu_obj->link     = filter_var( $link, FILTER_VALIDATE_BOOLEAN );
			$menu_obj->id       = sanitize_title( utf8_decode( $text ) );
			$menu_obj->children = [];

			return $menu_obj;
		}
	}
}

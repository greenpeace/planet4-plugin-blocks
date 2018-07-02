<?php

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'SubMenu_Controller' ) ) {

	/**
	 * Class SubMenu_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
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
			add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_assets' ] );
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
			add_action( 'enqueue_shortcode_ui', function () {
				wp_enqueue_script( 'submenu-view', P4BKS_ADMIN_DIR . 'js/submenu_heading_view.js', [ 'shortcode-ui' ] );
				wp_enqueue_script( 'blocks-ui', P4BKS_ADMIN_DIR . 'js/blocks-ui.js', [ 'shortcode-ui' ] );
			} );
		}

		/**
		 * Get underscore template from filesystem.
		 *
		 * @param string $template Template name.
		 *
		 * @return bool|string
		 */
		private function get_template( $template ) {

			$template = P4BKS_PLUGIN_DIR . '/admin/templates/' . $template . '.tpl.php';
			if ( file_exists( $template ) ) {
				$contents = file_get_contents( $template );

				return false !== $contents ? $contents : '';
			}

			return '';
		}

		/**
		 * Load underscore templates to footer.
		 */
		public function print_admin_footer_scripts() {
			echo $this->get_template( 'submenu' );
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
					'block_description' => __( 'An in-page table of contents to help users have a sense of what\'s on
												the page and let them jump to a topic they are interested in.', 'planet4-blocks-backend' ),
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
				[
					'attr'    => 'heading1',
					'label'   => __( '<b>Submenu item #1</b>' ),
					'type'    => 'p4_select',
					'options' => $heading_options,
				],
				[
					'attr'  => 'link1',
					'label' => __( 'Link' ),
					'type'  => 'p4_checkbox',
					'value' => 'false',
				],
				[
					'attr'    => 'heading2',
					'label'   => __( '<b>Submenu item #2</b>' ),
					'type'    => 'p4_select',
					'options' => $heading_options,
				],
				[
					'attr'  => 'link2',
					'label' => __( 'Link' ),
					'type'  => 'p4_checkbox',
					'value' => 'false',
				],
			];

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
		 * Callback for submenu shortcode.
		 * It renders the shortcode based on supplied attributes.
		 *
		 * @param array  $attributes    Defined attributes array for this shortcode.
		 * @param string $content       Content.
		 * @param string $shortcode_tag Shortcode tag name.
		 *
		 * @return string Returns the compiled template.
		 */
		public function prepare_template( $attributes, $content, $shortcode_tag ) : string {

			global $post;

			$content  = $post->post_content;
			$heading1 = $attributes['heading1'];
			$heading2 = $attributes['heading2'];
			$link1    = $attributes['link1'] ?? 'false';
			$link2    = $attributes['link2'] ?? 'false';

			$menu = $this->parse_post_content( $content, [ $heading1, $heading2 ], [ $link1, $link2 ] );

			wp_enqueue_script( 'submenu', P4BKS_ADMIN_DIR . 'js/submenu.js' );
			wp_localize_script( 'submenu', 'submenu', $menu );

			$block_data = [
				'title' => $attributes['title'] ?? '',
				'menu'  => $menu,
				'style' => $attributes['submenu_style'] ?? '1',
			];

			// Shortcode callbacks must return content, hence, output buffering here.
			ob_start();
			$this->view->block( self::BLOCK_NAME, $block_data );

			return ob_get_clean();
		}

		/**
		 * Parse post's content to extract headings.
		 *
		 * @param string $content   Post content.
		 * @param array  $headings  Headings attributes.
		 * @param array  $links     Links attributes.
		 *
		 * @return array
		 */
		private function parse_post_content( $content, $headings, $links ) {

			$heading1 = $headings[0];
			$heading2 = $headings[1];
			$link1    = $links[0];
			$link2    = $links[1];
			$menu     = [];

			if ( $this->is_allowed_tag( $heading1 ) ) {

				$heading_tag = "h$heading1";
				$dom         = new \DOMDocument();
				$dom->loadHtml( $content );
				$xpath = new \DOMXPath( $dom );

				// Find the heading with tag name $heading_tag.
				$nodes = $xpath->query( "//$heading_tag" );

				foreach ( $nodes as $node ) {

					$parent = $this->create_menu_object( $node, $heading_tag, $link1 );
					$menu[] = $parent;

					if ( ! $this->is_allowed_tag( $heading2 ) ) {
						continue;
					}

					$heading_tag2 = "h$heading2";
					while ( $node = $node->nextSibling ) {

						// If we get to the last DIV, stop.
						if ( $node->nodeName === $heading_tag2 ) {
							$child              = $this->create_menu_object( $node, $heading_tag2, $link2 );
							$parent->children[] = $child;
						}

						// Break if we get to next heading.
						if ( $node->nodeName === $heading_tag ) {
							break;
						}
					}
				}
			}

			return $menu;
		}

		/**
		 * Create a std object representing a node/heading.
		 *
		 * @param \DOMElement $node  Dom element.
		 * @param string      $type  Type/name of the tag.
		 * @param string      $link  String that defines if the menu object should contain an anchor tag.
		 *
		 * @return \stdClass
		 */
		private function create_menu_object( $node, $type, $link ) {
			$node_value         = $node->nodeValue;
			$menu_obj           = new \stdClass();
			$menu_obj->text     = utf8_decode($node_value);
			$menu_obj->hash     = md5( $node_value );
			$menu_obj->type     = $type;
			$menu_obj->link     = filter_var( $link, FILTER_VALIDATE_BOOLEAN );
			$menu_obj->id       = sanitize_title( iconv( "UTF-8", "ISO-8859-1//TRANSLIT", utf8_decode( $node_value ) ) );
			$menu_obj->children = [];

			return $menu_obj;
		}

		/**
		 * Decide if header should be allowed.
		 *
		 * @param string $heading Heading's number.
		 *
		 * @return bool
		 */
		private function is_allowed_tag( $heading ) {
			$valid_heading_values = [ 1, 2, 3, 4, 5, 6 ];
			$heading              = intval( $heading );
			if ( ! empty( $heading ) &&
				in_array( $heading, $valid_heading_values, true ) ) {
				return true;
			}

			return false;
		}
	}
}

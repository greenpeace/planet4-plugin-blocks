<?php
/**
 * Shortcode replacer command
 *
 * @package P4BKS
 */

namespace P4BKS\Command;

use WP_Query;

/**
 * Class for updating old shortcodes to their new equivalents
 *
 * The following blocks are replaced with the Gallery block:
 *   Carousel
 *   Three columns
 *
 * The following blocks are replaced with the Columns block:
 *   Two columns
 *   Static four column
 *   Take action tasks
 */
class ShortcodeReplacer {
	/**
	 * @var bool
	 */
	protected $initialised = false;

	/**
	 * Removes all existing shortcodes and replaces them with only the ones we're interested in
	 */
	protected function init() {
		if ( ! $this->initialised ) {
			remove_all_shortcodes();

			// Gallery block.
			add_shortcode( 'shortcake_carousel', [ $this, 'convert_carousel_to_gallery' ] );
			add_shortcode( 'shortcake_content_three_column', [ $this, 'convert_three_column_to_gallery' ] );

			// Columns block.
			add_shortcode( 'shortcake_two_columns', [ $this, 'convert_two_columns_to_columns' ] );
			add_shortcode( 'shortcake_static_four_column', [ $this, 'convert_four_columns_to_columns' ] );
			add_shortcode( 'shortcake_tasks', [ $this, 'convert_tasks_to_columns' ] );

			$this->initialised = true;
		}
	}

	/**
	 * Runs $this->update_text on every post's post_content, and updates the database whenever it changes
	 *
	 * @param int $post_id - if you wish to convert one post only (for testing purposes).
	 *
	 * @return int
	 * @throws \Exception - if no posts found.
	 */
	public function replace_all( $post_id ) {
		$args = [
			'post_type'   => 'any',
			'post_status' => [ 'publish', 'pending', 'draft', 'future', 'private' ],
			'nopaging'    => true,
		];

		if ( $post_id ) {
			$args['p'] = $post_id;
		}

		$query = new WP_Query( $args );
		$posts = $query->get_posts();

		if ( ! $posts ) {
			throw new \Exception( 'No posts found' );
		}

		$updated = 0;
		foreach ( $posts as $post ) {
			$converted = $this->update_text( $post->post_content );
			if ( $converted !== $post->post_content ) {
				$post->post_content = $converted;
				wp_update_post( $post );
				$updated ++;
			}
		}

		return $updated;
	}

	/**
	 * Converts any specified shortcodes in the given text
	 *
	 * @param string $text - post_content no doubt.
	 *
	 * @return string
	 */
	protected function update_text( $text ) {
		$this->init();

		return do_shortcode( $text );
	}

	/**
	 * Block name conversion:
	 *   shortcake_carousel -> shortcake_gallery
	 *
	 * Attribute conversions:
	 *   carousel_block_title -> gallery_block_title
	 *
	 * Attributes to add:
	 *   gallery_block_style="1"
	 *
	 * Other attributes:
	 *   multiple_image - unchanged
	 *   gallery_block_description - no equivalent in old block (& optional)
	 *
	 * Examples:
	 * Old: [shortcake_carousel carousel_block_title="Title" multiple_image="354,437,357" /]
	 * New: [shortcake_gallery gallery_block_style="1" gallery_block_title="Title" multiple_image="354,437,357" /]
	 *
	 * @param string[] $attrs - shortcode attributes.
	 *
	 * @return string
	 */
	public function convert_carousel_to_gallery( $attrs ) {
		$new_attrs = [
			'gallery_block_style' => '1',
		];

		$map = [
			'carousel_block_title' => 'gallery_block_title',
		];

		$new_attrs = array_merge( $new_attrs, $this->map_attrs( $attrs, $map ) );

		return $this->make_shortcode( 'shortcake_gallery', $new_attrs );
	}

	/**
	 * Block name conversion:
	 *   shortcake_content_three_column -> shortcake_gallery
	 *
	 * Attribute conversions:
	 *   title -> gallery_block_title
	 *   description -> gallery_block_description
	 *   image_1, image_2, image_3 -> multiple_image
	 *
	 * Attributes to add:
	 *   gallery_block_style="2"
	 *
	 * Other attributes:
	 *   None
	 *
	 * Examples:
	 * Old: [shortcake_content_three_column title="Title" description="Desc" image_1="354" image_2="437" image_3="357" /]
	 * New: [shortcake_gallery gallery_block_style="2" gallery_block_title="Title" gallery_block_description="Desc" multiple_image="354,437,357" /]
	 *
	 * @param string[] $attrs - shortcode attributes.
	 *
	 * @return string
	 */
	public function convert_three_column_to_gallery( $attrs ) {
		$new_attrs = [
			'gallery_block_style' => '2',
		];

		$images = [ '', '', '' ];
		foreach ( $attrs as $key => $value ) {
			switch ( $key ) {
				case 'image_1':
					$images[0] = $value;
					break;
				case 'image_2':
					$images[1] = $value;
					break;
				case 'image_3':
					$images[2] = $value;
					break;
				case 'title':
					$new_attrs['gallery_block_title'] = $value;
					break;
				case 'description':
					$new_attrs['gallery_block_description'] = $value;
					break;
				default:
					$new_attrs[ $key ] = $value;
			}
		}
		$new_attrs['multiple_image'] = implode( ',', array_filter( $images ) );

		return $this->make_shortcode( 'shortcake_gallery', $new_attrs );
	}

	/**
	 * Block name conversion:
	 *   shortcake_two_columns -> shortcake_columns
	 *
	 * Attribute conversions:
	 *   button_text_1 -> cta_text_1
	 *   button_text_2 -> cta_text_2
	 *   button_link_1 -> link_1
	 *   button_link_2 -> link_2
	 *
	 * Attributes to add:
	 *   columns_block_style="no_image"
	 *
	 * Other attributes:
	 *   title_1 - unchanged
	 *   title_2 - unchanged
	 *   description_1 - unchanged
	 *   description_2 - unchanged
	 *   columns_title - no equivalent in old block (& optional)
	 *   columns_description - no equivalent in old block (& optional)
	 *
	 * Examples:
	 * Old: [shortcake_two_columns title_1="Title 1" description_1="Desc 1" button_text_1="Button 1" button_link_1="/" title_2="Title 2" description_2="Desc 2" button_text_2="Button 2" button_link_2="/" /]
	 * New: [shortcake_columns columns_block_style="no_image" title_1="Title 1" description_1="Desc 1" cta_text_1="Button 1" link_1="/" title_2="Title 2" description_2="Desc 2" cta_text_2="Button 2" link_2="/" /]
	 *
	 * @param string[] $attrs - shortcode attributes.
	 *
	 * @return string
	 */
	public function convert_two_columns_to_columns( $attrs ) {
		$new_attrs = [
			'columns_block_style' => 'no_image',
		];

		$map = [];
		for ( $i = 1; $i <= 2; $i ++ ) {
			$map[ 'button_text_' . $i ] = 'cta_text_' . $i;
			$map[ 'button_link_' . $i ] = 'link_' . $i;
		}

		$new_attrs = array_merge( $new_attrs, $this->map_attrs( $attrs, $map ) );

		return $this->make_shortcode( 'shortcake_columns', $new_attrs );
	}

	/**
	 * Block name conversion:
	 *   shortcake_static_four_column -> shortcake_columns
	 *
	 * Attribute conversions:
	 *   title -> columns_title
	 *   link_text_1 ... _4 -> cta_text_1 ... _4
	 *   link_url_1 ... _4 -> link_1 ... _4
	 *
	 * Attributes to add:
	 *   columns_block_style="icons"
	 *
	 * Other attributes:
	 *   columns_description - no equivalent in old block (& optional)
	 *   title_1 ... _4 - unchanged
	 *   description_1 ...  _4 - unchanged
	 *   attachment_1 ... _4 - unchanged
	 *
	 * Examples:
	 * Old: [shortcake_static_four_column title="Four columns title" attachment_1="354" title_1="Title 1" description_1="Desc 1" link_text_1="Link 1" link_url_1="/" attachment_2="437"  title_2="Title 2" description_2="Desc 2" link_text_2="Link 2" link_url_2="/" attachment_3="357" title_3="Title 3" description_3="Desc 3" link_text_3="Link 3" link_url_3="/" attachment_4="347" title_4="Title 4" description_4="Desc 4" link_text_4="Link 4" link_url_4="/" /]
	 * New: [shortcake_columns columns_block_style="icons" columns_title="Four columns title" attachment_1="354" title_1="Title 1" description_1="Desc 1" cta_text_1="Link 1" link_1="/" attachment_2="437" title_2="Title 2" description_2="Desc 2" cta_text_2="Link 2" link_2="/" attachment_3="357" title_3="Title 3" description_3="Desc 3" cta_text_3="Link 3" link_3="/" attachment_4="347" title_4="Title 4" description_4="Desc 4" cta_text_4="Link 4" link_4="/" /]
	 *
	 * @param string[] $attrs - shortcode attributes.
	 *
	 * @return string
	 */
	public function convert_four_columns_to_columns( $attrs ) {
		$new_attrs = [
			'columns_block_style' => 'icons',
		];

		$map = [
			'title' => 'columns_title',
		];
		for ( $i = 1; $i <= 4; $i ++ ) {
			$map[ 'link_text_' . $i ] = 'cta_text_' . $i;
			$map[ 'link_url_' . $i ]  = 'link_' . $i;
		}

		$new_attrs = array_merge( $new_attrs, $this->map_attrs( $attrs, $map ) );

		return $this->make_shortcode( 'shortcake_columns', $new_attrs );
	}

	/**
	 * Block name conversion:
	 *   shortcake_tasks -> shortcake_columns
	 *
	 * Attribute conversions:
	 *   tasks_title -> columns_title
	 *   tasks_description -> columns_description
	 *   button_text_1 ... _4 -> cta_text_1 ... _4
	 *   button_link_1 ... _4 -> link_1 ... _4
	 *
	 * Attributes to add:
	 *   columns_block_style="tasks"
	 *
	 * Other attributes:
	 *   title_1 ... _4 - unchanged
	 *   description_1 ...  _4 - unchanged
	 *   attachment_1 ... _4 - unchanged
	 *
	 * Examples:
	 * Old: [shortcake_tasks tasks_title="Tasks title" tasks_description="Tasks description" title_1="Title 1" description_1="Desc 1" attachment_1="354" button_link_1="/" button_text_1="Button 1" title_2="Title 2" description_2="Desc 2" attachment_2="437" button_link_2="/" button_text_2="Link 2" title_3="Title 3" description_3="Desc 3" attachment_3="357" button_link_3="/" button_text_3="Link 3" title_4="Title 4" description_4="Desc 4" attachment_4="347" button_link_4="/" button_text_4="Link 4" /]
	 * New: [shortcake_columns columns_block_style="tasks" columns_title="Tasks title" columns_description="Tasks description" title_1="Title 1" description_1="Desc 1" attachment_1="354" link_1="/" cta_text_1="Link 1" title_2="Title 2" description_2="Desc 2" attachment_2="437" link_2="/" cta_text_2="Link 2" title_3="Title 3" description_3="Desc 3" attachment_3="357" link_3="/" cta_text_3="Link 3" title_4="Title 4" description_4="Desc 4" attachment_4="347" link_4="/" cta_text_4="Link 4" /]
	 *
	 * @param string[] $attrs - shortcode attributes.
	 *
	 * @return string
	 */
	public function convert_tasks_to_columns( $attrs ) {
		$new_attrs = [
			'columns_block_style' => 'tasks',
		];

		$map = [
			'tasks_title'       => 'columns_title',
			'tasks_description' => 'columns_description',
		];
		for ( $i = 1; $i <= 4; $i ++ ) {
			$map[ 'button_text_' . $i ] = 'cta_text_' . $i;
			$map[ 'button_link_' . $i ] = 'link_' . $i;
		}

		$new_attrs = array_merge( $new_attrs, $this->map_attrs( $attrs, $map ) );

		return $this->make_shortcode( 'shortcake_columns', $new_attrs );
	}

	/**
	 * Generates a new shortcode with the given name and attributes
	 *
	 * @param string   $name  - shortcode name.
	 * @param string[] $attrs - shortcode attributes.
	 *
	 * @return string
	 */
	protected function make_shortcode( $name, $attrs ) {
		$attr_strings = [];
		foreach ( $attrs as $key => $value ) {
			$attr_strings[] = " {$key}=\"{$value}\"";
		}

		return '[' . $name . implode( '', $attr_strings ) . ' /]';
	}

	/**
	 * Copies the array, replacing any keys that exist in the map with their mapped key
	 *
	 * @param string[] $attrs - shortcode attributes.
	 * @param string[] $map - mapping of old attribute keys to new ones.
	 *
	 * @return string[]
	 */
	protected function map_attrs( $attrs, $map ) {
		$new_attrs = [];

		foreach ( $attrs as $key => $value ) {
			if ( array_key_exists( $key, $map ) ) {
				$key = $map[ $key ];
			}
			$new_attrs[ $key ] = $value;
		}

		return $new_attrs;
	}
}

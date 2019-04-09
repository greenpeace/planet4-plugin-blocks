<?php
/**
* Blocks Usage class
*
* @package P4BKS\Controllers\Menu
* @since 1.40.0
*/

namespace P4BKS\Controllers\Menu;

if ( ! class_exists( 'Blocks_Usage_Controller' ) ) {

	/**
	 * Class Blocks_Usage_Controller
	 */
	class Blocks_Usage_Controller extends Controller {

		/**
		 * Create menu/submenu entry.
		 */
		public function create_admin_menu() {

			$current_user = wp_get_current_user();

			if ( in_array( 'administrator', $current_user->roles, true ) && current_user_can( 'manage_options' ) ) {
				add_submenu_page(
					P4BKS_PLUGIN_SLUG_NAME,
					__( 'Usage', 'planet4-blocks-backend' ),
					__( 'Usage', 'planet4-blocks-backend' ),
					'manage_options',
					'plugin_blocks_report',
					[ $this, 'plugin_blocks_report' ]
				);
			}
			add_action( 'rest_api_init', [ $this, 'plugin_blocks_report_register_rest_route' ] );
		}

		/**
		 * Filters array elements on being a shortcake shortcode
		 *
		 * @param string $shortcode The shortcode.
		 * @return bool
		 */
		public function is_shortcake( $shortcode ) {
			$found = strpos( $shortcode, 'shortcake' );
			if ( false !== $found ) {
				return true;
			}
		}

		/**
		 * Finds blocks usage in pages/posts.
		 */
		public function plugin_blocks_report() {
			global $wpdb, $shortcode_tags;

			// Array filtering on shortcake shortcodes.
			$blocks = array_filter( array_keys( $shortcode_tags ), [ $this, 'is_shortcake' ] );
			sort( $blocks );

			// phpcs:disable
			foreach ( $blocks as $block ) {
				$block = substr( $block, 10 );
				$shortcode = '%[shortcake_' . $wpdb->esc_like( $block ) . ' %';
				$sql       = $wpdb->prepare(
					"SELECT ID, post_title
					FROM `wp_posts` 
					WHERE post_status = 'publish' 
					AND `post_content` LIKE %s
					ORDER BY post_title",
					$shortcode );

				$results = $wpdb->get_results( $sql );

				// Confusion between old and new covers.
				if ( 'covers' === $block ) {
					$block = 'Take Action Covers - Old block';
				}

				echo '<hr>';
				echo '<h2>' . ucfirst( str_replace( '_', ' ', $block ) ) . '</h2>';

				foreach ( $results as $result ) {
					echo '<a href="post.php?post=' . $result->ID . '&action=edit" >' . $result->post_title . '</a>';
					echo '<br>';
				}
			}

			// Add to the report a breakdown of different styles for carousel Header
			$sql     = "SELECT ID, post_title
						FROM ". $wpdb->prefix . "posts 
						WHERE post_status = 'publish'
				      	AND `post_content` REGEXP 'shortcake_carousel_header.*full-width-classic'";
			$results = $wpdb->get_results( $sql );
			echo '<hr>';
			echo '<h2>Carousel Header Full Width Classic style</h2>';
			foreach ( $results as $result ) {
				echo '<a href="post.php?post=' . $result->ID . '&action=edit" >' . $result->post_title . '</a>';
				echo '<br>';
			}

			// Add to the report a breakdown of different styles for carousel Header
			// Given that the default (if no style is defined) is the Slide to Gray, include in the query
			// everything that is not Full Width Classic.
			$sql     = "SELECT ID, post_title
						FROM ". $wpdb->prefix . "posts 
						WHERE post_status = 'publish'
				      	AND `post_content` REGEXP 'shortcake_carousel_header'
						AND ID NOT IN (SELECT ID
											FROM ". $wpdb->prefix . "posts 
											WHERE post_status = 'publish'
											AND `post_content` REGEXP 'shortcake_carousel_header.*full-width-classic')";
			$results = $wpdb->get_results( $sql );
			echo '<hr>';
			echo '<h2>Carousel Header Zoom and Slide to Grey</h2>';
			foreach ( $results as $result ) {
				echo '<a href="post.php?post=' . $result->ID . '&action=edit" >' . $result->post_title . '</a>';
				echo '<br>';
			}

			// phpcs:enable
		}

		/**
		 * Finds blocks usage in pages/posts.
		 */
		public function plugin_blocks_report_json() {
			global $wpdb, $shortcode_tags;

			$cache_key = 'plugin-blocks-report';
			$report    = wp_cache_get( $cache_key );

			if ( ! $report ) {
				// Array filtering on shortcake shortcodes.
				$blocks = array_filter( array_keys( $shortcode_tags ), 'is_shortcake' );

				$report = [];

				// phpcs:disable
				foreach ( $blocks as $block ) {

					$block     = substr( $block, 10 );
					$shortcode = '%[shortcake_' . $wpdb->esc_like( $block ) . ' %';
					$sql       = $wpdb->prepare(
						"SELECT count(ID) AS cnt
				FROM `wp_posts` 
				WHERE post_status = 'publish' 
				AND `post_content` LIKE %s", $shortcode );
					$results = $wpdb->get_var( $sql );

					$report[ ucfirst( str_replace( '_', ' ', $block ) ) ] = $results;

				}

				// Add to the report a breakdown of different styles for carousel Header
				$sql = "SELECT count(ID) AS cnt
						FROM ". $wpdb->prefix . "posts 
						WHERE post_status = 'publish'
				      	AND `post_content` REGEXP 'shortcake_carousel_header'
						AND ID NOT IN (SELECT ID
											FROM ". $wpdb->prefix . "posts 
											WHERE post_status = 'publish'
											AND `post_content` REGEXP 'shortcake_carousel_header.*full-width-classic')";
				$cnt = $wpdb->get_var( $sql );
				$report['CarouselHeader-Zoom-And-Slide'] = $cnt;
				$sql = "SELECT count(ID) AS cnt
						FROM ". $wpdb->prefix . "posts 
						WHERE post_status = 'publish'
				      	AND `post_content` REGEXP 'shortcake_carousel_header.*full-width-classic'";
				$cnt = $wpdb->get_var( $sql );
				$report['CarouselHeader-Full-Width-Classic'] = $cnt;

				wp_cache_add( $cache_key, $report, '', 3600 );

			}
			return $report;

			// phpcs:enable
		}


		/**
		 * Register API route for report of blocks usage in pages/posts.
		 */
		public function plugin_blocks_report_register_rest_route() {
			register_rest_route(
				'plugin_blocks/v1',
				'/plugin_blocks_report/',
				[
					'methods'  => 'GET',
					'callback' => [ $this, 'plugin_blocks_report_json' ],
				]
			);
		}

		/**
		 * Validates the settings input.
		 *
		 * @param array $settings The associative array with the settings that are registered for the plugin.
		 *
		 * @return bool
		 */
		public function validate( $settings ) : bool {
			$has_errors = false;
			return ! $has_errors;
		}

		/**
		 * Sanitizes the settings input.
		 *
		 * @param array $settings The associative array with the settings that are registered for the plugin.
		 */
		public function sanitize( &$settings ) {
			if ( $settings ) {
				foreach ( $settings as $name => $setting ) {
					$settings[ $name ] = sanitize_text_field( $setting );
				}
			}
		}
	}
}

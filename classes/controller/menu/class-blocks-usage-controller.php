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
		}

		/**
		 * Filters array elements on being a shortcake shortcode
		 *
		 * @param string $shortcode The shortcode.
		 * @return bool
		 */
		public function is_shortcake( $shortcode ) : bool {
			$found = strpos( $shortcode, 'shortcake' );
			return false !== $found ? true : false;
		}

		/**
		 * Finds blocks usage in pages/posts.
		 */
		public function plugin_blocks_report() {
			global $wpdb, $shortcode_tags;
			$wpdb_prefix = $wpdb->prefix;

			// Array filtering on shortcake shortcodes.
			$blocks = array_filter( array_keys( $shortcode_tags ), [ $this, 'is_shortcake' ] );
			sort( $blocks );

			// phpcs:disable
			foreach ( $blocks as $block ) {
				$block     = substr( $block, 10 );
				$shortcode = '%[shortcake_' . $wpdb->esc_like( $block ) . ' %';
				$sql       = $wpdb->prepare(
					"SELECT ID, post_title
					FROM `wp_posts` 
					WHERE post_status = 'publish' 
					AND `post_content` LIKE %s
					ORDER BY post_title",
					$shortcode );

				$results = $wpdb->get_results( $sql );
				if ( !$results ) { continue; }

				// Confusion between old and new covers.
				if ( 'covers' === $block ) {
					$block = 'Take Action Covers - Old block';
				}

				echo '<hr>';
				echo '<h2>' . ucfirst( str_replace( '_', ' ', $block ) ) . '</h2>';
				echo '<table>';
				echo '<tr style="text-align: left"><th>ID</th><th>Title</th></tr>';
				foreach ( $results as $result ) {
					echo '<tr><td>'.$result->ID .'</td>';
					echo  '<td><a href="post.php?post=' . $result->ID . '&action=edit" >' . $result->post_title . '</a></td></tr>';
				}
				echo '</table>';
			}

			// Add to the report a breakdown of different styles for carousel Header
			$sql = 'SELECT ID, post_title
                    FROM %1$s 
                    WHERE post_status = \'publish\'
                        AND `post_content` REGEXP \'shortcake_carousel_header.*full-width-classic\'';

			$prepared_sql = $wpdb->prepare( $sql, $wpdb->posts );
			$results      = $wpdb->get_results( $prepared_sql );
			echo '<hr>';
			echo '<h2>Carousel Header Full Width Classic style</h2>';
			echo '<table><tr style="text-align: left"><th>ID</th><th>Title</th></tr>';
			foreach ( $results as $result ) {
				echo '<tr><td>'.$result->ID .'</td>';
				echo  '<td><a href="post.php?post=' . $result->ID . '&action=edit" >' . $result->post_title . '</a></td></tr>';
			}
			echo '</table>';

			// Add to the report a breakdown of different styles for carousel Header
			// Given that the default (if no style is defined) is the Slide to Gray, include in the query
			// everything that is not Full Width Classic.
			$sql = 'SELECT ID, post_title
                    FROM %1$s 
                    WHERE post_status = \'publish\'
                        AND `post_content` REGEXP \'shortcake_carousel_header\'
                        AND ID NOT IN (SELECT ID
                            FROM %2$s 
                            WHERE post_status = \'publish\'
                            AND `post_content` REGEXP \'shortcake_carousel_header.*full-width-classic\')';

			$prepared_sql = $wpdb->prepare( $sql, [ $wpdb->posts, $wpdb->posts ] );
			$results      = $wpdb->get_results( $prepared_sql );
			echo '<hr>';
			echo '<h2>Carousel Header Zoom and Slide to Grey</h2>';
			echo '<table><tr style="text-align: left"><th>ID</th><th>Title</th></tr>';
			foreach ( $results as $result ) {
				echo '<tr><td>'.$result->ID .'</td>';
				echo  '<td><a href="post.php?post=' . $result->ID . '&action=edit" >' . $result->post_title . '</a></td></tr>';
			}
			echo '</table>';


			// Add to the report a breakdown of which tags are using a redirect page and which do not
			// The first query shows the ones that do not use a redirect page
			$sql = '( SELECT term.name, tt.term_id 
							FROM %1$sterm_taxonomy AS tt, 
								 %2$sterms AS term, 
								 %3$stermmeta AS tm 
							WHERE tt.`taxonomy`= \'post_tag\' 
							AND term.term_id = tt.term_id 
							AND tm.term_id=tt.term_id 
							AND tm.meta_key=\'redirect_page\' 
							AND tm.meta_value =\'\' )
						UNION 
						( SELECT term.name, tt.term_id 
							FROM %4$sterm_taxonomy AS tt, 
								 %5$sterms AS term, 
								 %6$stermmeta AS tm
							WHERE tt.`taxonomy`=\'post_tag\' 
							AND term.term_id = tt.term_id 
							AND tm.term_id=tt.term_id 
							AND tm.term_id NOT IN (SELECT tt.term_id 
													FROM %7$sterm_taxonomy AS tt, 
														 %8$sterms AS term, 
														 %9$stermmeta AS tm 
													WHERE tt.`taxonomy`=\'post_tag\' 
													AND term.term_id = tt.term_id 
													AND tm.term_id=tt.term_id 
													AND tm.meta_key=\'redirect_page\')
							GROUP BY term.name, tt.term_id )';
			$prepared_sql = $wpdb->prepare(
				$sql,
				[
					$wpdb_prefix,
					$wpdb_prefix,
					$wpdb_prefix,
					$wpdb_prefix,
					$wpdb_prefix,
					$wpdb_prefix,
					$wpdb_prefix,
					$wpdb_prefix,
					$wpdb_prefix
				]
			);
			$results = $wpdb->get_results( $prepared_sql );
			echo '<hr>';
			echo '<h2>Tags without redirection page</h2>';
			echo '<table><tr style="text-align: left"><th>ID</th><th>Title</th></tr>';
			foreach ( $results as $result ) {
				echo '<tr><td>'.$result->term_id .'</td>';
				echo '<td><a href="term.php?taxonomy=post_tag&tag_ID=' . $result->term_id . '" >' . $result->name . '</a></td></tr>';
			}
			echo '</table>';

			// Add to the report a breakdown of which tags are using a redirect page and which do not
			// The second query shows the ones that do use a redirect page
			$sql     = 'SELECT term.name, tm.meta_value, tt.term_id
						FROM %1$sterm_taxonomy AS tt, 
							 %2$sterms AS term, 
							 %3$stermmeta AS tm
						WHERE tt.`taxonomy`=\'post_tag\' 
						AND term.term_id = tt.term_id
						AND tm.term_id=tt.term_id
						AND tm.meta_key=\'redirect_page\'
						AND tm.meta_value !=\'\'';
			$prepared_sql = $wpdb->prepare(
				$sql,
				[
					$wpdb_prefix,
					$wpdb_prefix,
					$wpdb_prefix,
				]
			);
			$results = $wpdb->get_results( $prepared_sql );
			echo '<hr>';
			echo '<h2>Tags that use a redirection page</h2>';
			echo '<table><tr style="text-align: left"><th>ID</th><th>Title</th></tr>';
			foreach ( $results as $result ) {
				echo '<tr><td>'.$result->term_id .'</td>';
				echo '<td><a href="term.php?taxonomy=post_tag&tag_ID=' . $result->term_id . '" >' . $result->name . '</a></td></tr>';
			}
			echo '</table>';


			// phpcs:enable
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

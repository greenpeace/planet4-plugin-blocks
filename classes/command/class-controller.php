<?php
/**
 * P4 Commands Controller class
 *
 * @package P4BKS
 * @since 1.40.0
 */

namespace P4BKS\Command;

use WP_CLI;

if ( defined( 'WP_CLI' ) && WP_CLI && ! class_exists( 'Controller' ) ) {

	/**
	 * In this class every method we add becomes a WP_CLI sub-command for `wp p4-blocks`.
	 *
	 * @package P4BKS\Command
	 */
	class Controller {

		/**
		 * Sub command that replaces old block shortcodes with new ones.
		 *
		 * @param array $args Sub-command parameters.
		 *
		 * @throws WP_CLI\ExitException The thrown exception.
		 */
		public function replace_shortcodes( $args ) {
			// Supply a post ID as first argument to update a single, specific post.
			$post_id = $args[0] ?? null;

			try {
				WP_CLI::log( 'Replacing shortcodes...' );

				$updater = new ShortcodeReplacer();
				$updated = $updater->replace_all( $post_id );

				if ( $post_id ) {
					if ( $updated ) {
						WP_CLI::success( "Replaced shortcodes in post $post_id" );
					} else {
						WP_CLI::log( "No shortcodes replaced in post $post_id" );
					}
				} else {
					WP_CLI::success( "Replaced shortcodes in $updated posts" );
				}
			} catch ( \Error $e ) {
				WP_CLI::error( $e->getMessage() );
			} catch ( \Exception $e ) {
				WP_CLI::log( 'Exception: ' . $e->getMessage() );
			}
		}

		// Add here new sub-commands e.g. wp p4-blocks new_sub_command.
		// public function new_sub_command() {}.
	}
}

<?php
/**
 * View class
 *
 * @package P4BKS
 * @since 0.1.0
 */

namespace P4BKS\Views;

use Timber\Timber;

if ( ! class_exists( 'View' ) ) {

	/**
	 * Class View
	 *
	 * @package P4BKS\Views
	 */
	class View {

		/** @var string $template_dir The path to the template files. */
		private $template_dir = P4BKS_INCLUDES_DIR;


		/**
		 * Creates the plugin's View object.
		 */
		public function __construct() {}

		/**
		 * Compile and return a template file.
		 *
		 * @param array|string $template_name The file name of the template to render.
		 * @param array        $data The data to pass to the template.
		 * @param string       $relevant_dir The path to a subdirectory where the template is located (relative to $template_dir).
		 *
		 * @return bool|string The returned output
		 */
		public function get_template( $template_name, $data, $relevant_dir = 'blocks/' ) {
			Timber::$locations = $this->template_dir;
			return Timber::compile( [ $relevant_dir . $template_name . '.twig' ], $data );
		}

		/**
		 * Uses the appropriate templating engine to render a template file.
		 *
		 * @param array|string $template_name The file name of the template to render.
		 * @param array        $data The data to pass to the template.
		 * @param string       $relevant_dir The path to a subdirectory where the template is located (relative to $template_dir).
		 */
		private function view_template( $template_name, $data, $relevant_dir = '' ) {
			Timber::$locations = $this->template_dir;
			Timber::render( [ $relevant_dir . $template_name . '.twig' ], $data );
		}

		/**
		 * Render the settings page of the plugin.
		 *
		 * @param array $data All the data needed to render the template.
		 */
		public function settings( $data ) {
			$this->view_template( __FUNCTION__, $data );
		}

		/**
		 * Uses the appropriate templating engine to render a template file.
		 *
		 * @param array|string $template_name The file name of the template to render.
		 * @param array        $data The data to pass to the template.
		 * @param string       $template_ext The extension of the template (php, twig, ...).
		 * @param string       $relevant_dir The path to a subdirectory where the template is located (relative to $template_dir).
		 */
		public function block( $template_name, $data, $template_ext = 'twig', $relevant_dir = 'blocks/' ) {

			if ( 'twig' === $template_ext ) {
				Timber::$locations = $this->template_dir;
				Timber::render( [ $relevant_dir . $template_name . '.' . $template_ext ], $data );
			} else {
				include_once $this->template_dir . $relevant_dir . $template_name . '.' . $template_ext;
			}
		}
	}
}

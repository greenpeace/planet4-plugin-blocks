<?php
/**
 * WordPress factory wrapper for p4 custom taxonomy
 *
 * @package P4BKS
 */

if ( ! class_exists( 'P4_CustomTaxonomyTest' ) ) {
	/**
	 * Class P4_CustomTaxonomyTest
	 *
	 * @package Planet4_Plugin_Blocks
	 */
	class P4_CustomTaxonomyTest extends WP_UnitTest_Factory_For_Term {

		/** @var string $taxonomy */
		private $taxonomy;

		/**
		 * P4_CustomTaxonomyTest constructor.
		 *
		 * @param null   $factory  WordPress unit test factory.
		 * @param string $taxonomy Taxonomy slug.
		 */
		public function __construct( $factory = null, $taxonomy = 'p4-page-type' ) {
			parent::__construct( $factory );
			$this->taxonomy = $taxonomy;

			$this->default_generation_definitions = [
				'name'        => new WP_UnitTest_Generator_Sequence( 'Term %s' ),
				'taxonomy'    => $this->taxonomy,
				'description' => new WP_UnitTest_Generator_Sequence( 'Term description %s' ),
			];

		}
	}
}

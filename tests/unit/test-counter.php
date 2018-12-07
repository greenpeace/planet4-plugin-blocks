<?php
/**
 * PHP unit test of content four column block
 *
 * @package P4BKS
 */

use P4BKS\Controllers\Blocks\Counter_Controller;
use P4BKS\Views\View as View;

require_once __DIR__ . '/../p4-unittestcase.php';

if ( ! class_exists( 'P4_CounterTest' ) ) {

	/**
	 * Class P4_ContentFourColumnTest
	 *
	 * @package Planet4_Plugin_Blocks
	 */
	class P4_CounterTest extends P4_UnitTestCase {

		/** @var $block Counter_Controller */
		protected $block;

		/**
		 * This method sets up the test.
		 */
		public function setUp() {
			parent::setUp();

			$this->block = new Counter_Controller( new View() );
		}

		/**
		 * Test that the block handles missing input values.
		 */
		public function test_missing_values() {

			$fields = [
				'completed' => '',
				'target'    => '',
				'text'      => '',
			];
			$data   = $this->block->prepare_data( $fields, '', '' );

			$this->assertEquals( $data['fields']['completed'], '' );
			$this->assertEquals( $data['fields']['target'], '' );
			$this->assertEquals( $data['fields']['percent'], 0 );
		}

		/**
		 * Test that the values are substituted into the text.
		 */
		public function test_text_substitution() {

			$fields = [
				'completed' => '9',
				'target'    => '10',
				'text'      => 'Completed %completed% of %target%, only %remaining% to go!',
			];
			$data   = $this->block->prepare_data( $fields, '', '' );

			$this->assertEquals( $data['fields']['text'], 'Completed 9 of 10, only 1 to go!' );
		}

		/**
		 * Test that the completed percentage is computed and rounded.
		 */
		public function test_percentage_calculation() {

			$fields = [
				'completed' => '6',
				'target'    => '11',
				'text'      => '',
			];
			$data   = $this->block->prepare_data( $fields, '', '' );

			$this->assertEquals( $data['fields']['percent'], 55 );
		}

	}
}

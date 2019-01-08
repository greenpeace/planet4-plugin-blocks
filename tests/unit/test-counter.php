<?php
/**
 * PHP unit test of counter block
 *
 * @package P4BKS
 */

use P4BKS\Controllers\Blocks\Counter_Controller;
use P4BKS\Views\View as View;

require_once __DIR__ . '/../p4-unittestcase.php';

if ( ! class_exists( 'P4_CounterTest' ) ) {

	/**
	 * Class P4_CounterTest
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

			$this->assertEquals( '', $data['fields']['completed'] );
			$this->assertEquals( '', $data['fields']['target'] );
			$this->assertEquals( 0, $data['fields']['percent'] );
		}

		/**
		 * Test that the values are substituted into the text.
		 */
		public function test_text_substitution() {

			$fields = [
				'completed' => '999',
				'target'    => '1000',
				'text'      => 'Completed %completed% of %target%, only %remaining% to go!',
			];
			$data   = $this->block->prepare_data( $fields, '', '' );

			$this->assertEquals( 'Completed 999 of 1,000, only 1 to go!', $data['fields']['text'] );
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

			$this->assertEquals( 55, $data['fields']['percent'] );
		}

	}
}

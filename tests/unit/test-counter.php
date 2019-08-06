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

			$this->assertEquals( '0', $data['fields']['completed'] );
			$this->assertEquals( '0', $data['fields']['target'] );
			$this->assertEquals( 0, $data['fields']['percent'] );
		}

		/**
		 * Test that the remaining value is calculated correctly.
		 */
		public function test_remaining_calculation() {

			$fields = [
				'completed' => 999,
				'target'    => 1000,
			];
			$data   = $this->block->prepare_data( $fields, '', '' );

			$this->assertEquals( '1', $data['fields']['remaining'] );

			$fields = [
				'completed' => 999,
				'target'    => 100,
			];
			$data   = $this->block->prepare_data( $fields, '', '' );

			$this->assertEquals( '0', $data['fields']['remaining'] );
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

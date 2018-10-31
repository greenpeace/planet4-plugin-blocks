<?php
/**
 * PHP unit test of carousel block
 *
 * @package P4BKS
 */

use P4BKS\Controllers\Blocks\Carousel_Controller as Carousel;
use P4BKS\Views\View as View;

require_once __DIR__ . '/../p4-unittestcase.php';

if ( ! class_exists( 'P4_CarouselTest' ) ) {

	/**
	 * Class P4_CarouselTest
	 *
	 * @package Planet4_Plugin_Blocks
	 */
	class P4_CarouselTest extends P4_UnitTestCase {

		const ATTACHMENTS_COUNT = 3;

		/** @var $block Carousel */
		protected $block;
		/** @var array $post_ids */
		protected $post_ids = [];

		/**
		 * This method sets up the test.
		 */
		public function setUp() {
			parent::setUp();

			$this->block    = new Carousel( new View() );
			$dummy_posts    = $this->get_dummy_posts();
			$this->post_ids = $this->factory->attachment->create_many( self::ATTACHMENTS_COUNT, $dummy_posts );
		}

		/**
		 * Test that the block retrieves all the available Attachments with post_type image/jpg.
		 */
		public function test_attachments_count() {

			$fields = [
				'multiple_image' => implode( ',', $this->post_ids ),
			];
			$data   = $this->block->prepare_data( $fields );

			try {
				$this->assertEquals( self::ATTACHMENTS_COUNT, count( $data['images'] ) );
			} catch ( \Exception $e ) {
				$this->fail( '->Did not find as many Attachments as expected.' );
			}
		}

		/**
		 * Get data which will be used to create dummy attachments for the test.
		 *
		 * @return array
		 */
		private function get_dummy_posts() : array {
			return [
				'post_author'    => 1,
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'post_mime_type' => 'image/jpg',
			];
		}
	}
}

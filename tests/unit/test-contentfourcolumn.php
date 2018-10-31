<?php
/**
 * PHP unit test of content four column block
 *
 * @package P4BKS
 */

use P4BKS\Controllers\Blocks\ContentFourColumn_Controller as ContentFourColumn;
use P4BKS\Views\View as View;

require_once __DIR__ . '/../p4-unittestcase.php';

if ( ! class_exists( 'P4_ContentFourColumnTest' ) ) {

	/**
	 * Class P4_ContentFourColumnTest
	 *
	 * @package Planet4_Plugin_Blocks
	 */
	class P4_ContentFourColumnTest extends P4_UnitTestCase {

		const POSTS_COUNT                = 6;
		const POSTS_WITH_TAG_COUNT       = 3;   // Should be smaller or equal to POSTS_COUNT.
		const POSTS_WITH_FIRST_TAG_COUNT = 2;   // Should be smaller or equal to POSTS_WITH_TAG_COUNT.

		/** @var $block ContentFourColumn */
		protected $block;
		/** @var array $post_ids */
		protected $post_ids = [];
		/** @var array $tag_names */
		public $tag_names = [
			'test_tag_1',
			'test_tag_2',
		];

		/**
		 * This method sets up the test.
		 */
		public function setUp() {
			parent::setUp();

			$this->block    = new ContentFourColumn( new View() );
			$dummy_posts    = $this->get_dummy_posts();
			$this->post_ids = $this->factory->post->create_many( self::POSTS_COUNT, $dummy_posts );
		}

		/**
		 * Test that the block retrieves all the available Posts with specific tags.
		 */
		public function test_posts_count() {

			$tag_ids = [];
			if ( $this->tag_names ) {
				foreach ( $this->tag_names as $tag_name ) {
					$tag_ids[] = $this->factory->term->create(
						[
							'name' => $tag_name,
						]
					);
				}
			}

			if ( $this->post_ids ) {
				$tag_names_count = count( $this->tag_names );
				for ( $i = 0; $i < self::POSTS_WITH_TAG_COUNT; $i++ ) {
					$res = $this->factory->term->add_post_terms( $this->post_ids[ $i ], $this->tag_names[ $i % $tag_names_count ], 'post_tag' );

					try {
						$this->assertNotWPError( $res );
						$this->assertNotFalse( $res );
					} catch ( \Exception $e ) {
						$this->fail( sprintf( '->Unable to add term to post with id %d.', $this->post_ids[ $i ] ) );
					}
				}
			}

			// Test the number of the posts that have a tag.
			$fields = [
				'select_tag' => implode( ',', $tag_ids ),
			];
			$data   = $this->block->prepare_data( $fields );
			$found  = count( $data['posts'] );

			try {
				$this->assertEquals( self::POSTS_WITH_TAG_COUNT, $found );
			} catch ( \Exception $e ) {
				$this->fail(
					sprintf(
						'->Expected to find %d Posts with these tags, but found %d.',
						self::POSTS_WITH_TAG_COUNT,
						$found
					)
				);
			}

			// Test the number of the posts that have the first tag.
			$fields = [
				'select_tag' => $tag_ids[0],
			];
			$data   = $this->block->prepare_data( $fields );
			$found  = count( $data['posts'] );

			try {
				$this->assertEquals( self::POSTS_WITH_FIRST_TAG_COUNT, $found );
			} catch ( \Exception $e ) {
				$this->fail(
					sprintf(
						'->Expected to find %d Posts with these tags, but found %d.',
						self::POSTS_WITH_FIRST_TAG_COUNT,
						$found
					)
				);
			}
		}

		/**
		 * Get data which will be used to create dummy attachments for the test.
		 *
		 * @return array
		 */
		private function get_dummy_posts(): array {
			return [
				'post_author' => 1,
				'post_title'  => 'this is a post',
				'post_type'   => 'post',
				'post_status' => 'publish',
			];
		}
	}
}

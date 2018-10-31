<?php
/**
 * The PHP unit test of article block
 *
 * @package P4BKS
 */

use P4BKS\Controllers\Blocks\Articles_Controller as Articles;
use P4BKS\Views\View as View;

require_once __DIR__ . '/../p4-unittestcase.php';

if ( ! class_exists( 'P4_ArticlesTest' ) ) {

	/**
	 * Class P4_ArticlesTest
	 *
	 * @package Planet4_Plugin_Blocks
	 */
	class P4_ArticlesTest extends P4_UnitTestCase {

		const PRESS_RELEASE_COUNT = 2;
		const PUBLICATION_COUNT   = 1;
		const STORY_COUNT         = 4;

		/**
		 * The block object.
		 *
		 * @var $block Articles
		 */
		protected $block;

		/**
		 * This method sets up the test.
		 */
		public function setUp() {
			parent::setUp();
			$this->block = new Articles( new View() );
		}

		/**
		 * Test that the block retrieves all the available Posts with 'press-release' as p4 page type.
		 */
		public function test_press_release_count() {
			$dummy_posts       = $this->get_dummy_posts();
			$press_release_ids = $this->factory->post->create_many( self::PRESS_RELEASE_COUNT, $dummy_posts['press-release'] );

			if ( $press_release_ids ) {
				foreach ( $press_release_ids as $id ) {
					$res = $this->factory->term->add_post_terms( $id, 'press-release', 'p4-page-type' );
					try {
						$this->assertNotWPError( $res );
						$this->assertNotFalse( $res );
					} catch ( \Exception $e ) {
						$this->fail( sprintf( '->Unable to add term to post with id %d.', $id ) );
					}
				}
			}

			$fields = [
				'article_count'              => self::PRESS_RELEASE_COUNT,
				'p4_page_type_press-release' => true,
			];
			$data   = $this->block->prepare_data( $fields );

			try {
				$this->assertEquals( self::PRESS_RELEASE_COUNT, count( $data['recent_posts'] ) );
			} catch ( \Exception $e ) {
				$this->fail( '->Did not find as many Press Release Posts as expected.' );
			}
		}

		/**
		 * Test that the block retrieves all the available Posts with 'publication' as p4 page type.
		 */
		public function test_publication_count() {
			$dummy_posts     = $this->get_dummy_posts();
			$publication_ids = $this->factory->post->create_many( self::PUBLICATION_COUNT, $dummy_posts['publication'] );

			if ( $publication_ids ) {
				foreach ( $publication_ids as $id ) {
					$res = $this->factory->term->add_post_terms( $id, 'publication', 'p4-page-type' );
					try {
						$this->assertNotWPError( $res );
						$this->assertNotFalse( $res );
					} catch ( \Exception $e ) {
						$this->fail( sprintf( '->Unable to add term to post with id %d.', $id ) );
					}
				}
			}

			$fields = [
				'article_count'            => self::PUBLICATION_COUNT,
				'p4_page_type_publication' => true,
			];
			$data   = $this->block->prepare_data( $fields );

			try {
				$this->assertEquals( self::PUBLICATION_COUNT, count( $data['recent_posts'] ) );
			} catch ( \Exception $e ) {
				$this->fail( '->Did not find as many Publication Posts as expected.' );
			}
		}

		/**
		 * Test that the block retrieves all the available Posts with 'story' as p4 page type.
		 */
		public function test_story_count() {
			$dummy_posts = $this->get_dummy_posts();
			$story_ids   = $this->factory->post->create_many( self::STORY_COUNT, $dummy_posts['story'] );

			if ( $story_ids ) {
				foreach ( $story_ids as $id ) {
					$res = $this->factory->term->add_post_terms( $id, 'story', 'p4-page-type' );
					try {
						$this->assertNotWPError( $res );
						$this->assertNotFalse( $res );
					} catch ( \Exception $e ) {
						$this->fail( sprintf( '->Unable to add term to post with id %d.', $id ) );
					}
				}
			}

			$fields = [
				'article_count'      => self::STORY_COUNT,
				'p4_page_type_story' => true,
			];
			$data   = $this->block->prepare_data( $fields );

			try {
				$this->assertEquals( self::STORY_COUNT, count( $data['recent_posts'] ) );
			} catch ( \Exception $e ) {
				$this->fail( '->Did not find as many Story Posts as expected.' );
			}
		}

		/**
		 * Get data which will be used to create dummy posts for the test.
		 *
		 * @return array
		 */
		private function get_dummy_posts() : array {
			return [
				'press-release' => [
					'post_author' => 1,
					'post_title'  => 'this is a press release',
					'post_status' => 'publish',
				],
				'publication'   => [
					'post_author' => 1,
					'post_title'  => 'this is a publication',
					'post_status' => 'publish',
				],
				'story'         => [
					'post_author' => 1,
					'post_title'  => 'this is a story',
					'post_status' => 'publish',
				],
			];
		}
	}
}

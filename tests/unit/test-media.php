<?php
/**
 * PHP unit test of content four column block
 *
 * @package P4BKS
 */

use P4BKS\Controllers\Blocks\Media_Controller;
use P4BKS\Views\View as View;

require_once __DIR__ . '/../p4-unittestcase.php';

if ( ! class_exists( 'P4_MediaTest' ) ) {

	/**
	 * Class P4_ContentFourColumnTest
	 *
	 * @package Planet4_Plugin_Blocks
	 */
	class P4_MediaTest extends P4_UnitTestCase {

		/** @var Media_Controller */
		protected $block;

		/**
		 * This method sets up the test
		 */
		public function setUp() {
			parent::setUp();

			$this->block = new Media_Controller( new View() );
		}

		/**
		 * Test YouTube input formats
		 */
		public function test_youtube() {
			add_filter( 'pre_http_request', [ $this, 'mock_oembed_response' ] );

			// Youtube ID.
			$data = $this->block->prepare_data(
				[
					'video_title' => 'Foo',
					'youtube_id'  => 'hW9ScsV6QJ0',
				],
				'',
				''
			);
			$this->assertEquals( 'video', $data['fields']['type'] );
			$this->assertEquals( '__embed__', $data['fields']['embed_html'] );

			// Youtube URL.
			$data = $this->block->prepare_data(
				[
					'video_title' => 'Foo',
					'youtube_id'  => 'https://www.youtube.com/watch?v=hW9ScsV6QJ0',
				],
				'',
				''
			);
			$this->assertEquals( 'video', $data['fields']['type'] );
			$this->assertEquals( '__embed__', $data['fields']['embed_html'] );

			remove_filter( 'pre_http_request', [ $this, 'mock_oembed_response' ] );
		}

		/**
		 * Test Vimeo URL formats
		 */
		public function test_vimeo() {
			add_filter( 'pre_http_request', [ $this, 'mock_oembed_response' ] );

			// Vimeo URL.
			$data = $this->block->prepare_data(
				[
					'video_title' => 'Foo',
					'youtube_id'  => 'https://vimeo.com/112010467',
				],
				'',
				''
			);
			$this->assertEquals( 'video', $data['fields']['type'] );
			$this->assertEquals( '__embed__', $data['fields']['embed_html'] );

			remove_filter( 'pre_http_request', [ $this, 'mock_oembed_response' ] );
		}

		/**
		 * Test SoundCloud URL formats
		 */
		public function test_soundcloud() {
			add_filter( 'pre_http_request', [ $this, 'mock_oembed_response' ] );

			// Soundcloud track URL.
			$data = $this->block->prepare_data(
				[
					'video_title' => 'Foo',
					'youtube_id'  => 'https://soundcloud.com/greenpeace-canada/ep31-the-great-bear-rainforest-spirit-bears-scientists-at-the-movies',
				],
				'',
				''
			);
			$this->assertEquals( 'audio', $data['fields']['type'] );
			$this->assertEquals( '__embed__', $data['fields']['embed_html'] );

			remove_filter( 'pre_http_request', [ $this, 'mock_oembed_response' ] );
		}

		/**
		 * Test bare video URL
		 */
		public function test_video() {

			$data = $this->block->prepare_data(
				[
					'video_title'      => 'Foo',
					'youtube_id'       => 'https://example.org/video.mp4',
					'video_poster_img' => [ '' ],
				],
				'',
				''
			);
			$this->assertRegExp( '/<video.*video.mp4/', $data['fields']['embed_html'] );
			$this->assertEquals( 'video', $data['fields']['type'] );
		}

		/**
		 * Test bare audio file URL
		 */
		public function test_audio() {
			// MP3.
			$data = $this->block->prepare_data(
				[
					'video_title' => 'Foo',
					'youtube_id'  => 'https://example.org/sound.mp3',
				],
				'',
				''
			);
			$this->assertRegExp( '/<audio.*sound.mp3/', $data['fields']['embed_html'] );
			$this->assertEquals( 'audio', $data['fields']['type'] );

			// WAV.
			$data = $this->block->prepare_data(
				[
					'video_title' => 'Foo',
					'youtube_id'  => 'https://example.org/sound.wav',
				],
				'',
				''
			);
			$this->assertRegExp( '/<audio.*sound.wav/', $data['fields']['embed_html'] );
			$this->assertEquals( 'audio', $data['fields']['type'] );
		}

		/**
		 * Test unrecognised embed URL
		 */
		public function test_unrecognized() {
			add_filter( 'pre_http_request', [ $this, 'mock_oembed_failure' ] );

			$data = $this->block->prepare_data(
				[
					'video_title' => 'Foo',
					'youtube_id'  => 'https://example.com/blarg',
				],
				'',
				''
			);
			$this->assertEquals( false, $data['fields']['embed_html'] );
			$this->assertEquals( 'video', $data['fields']['type'] );

			remove_filter( 'pre_http_request', [ $this, 'mock_oembed_failure' ] );
		}

		/**
		 * Return dummy HTTP response body for oembed request
		 *
		 * @return array
		 */
		public function mock_oembed_response() {
			return [ 'body' => '{"version":1.0,"type":"rich","provider_name":"Mock","provider_url":"http://example.com","height":400,"width":"100%","title":"Title","description":"","thumbnail_url":"","html":"__embed__","author_name":"","author_url":""}' ];
		}

		/**
		 * Return dummy HTTP failure for oembed request
		 *
		 * @return WP_Error
		 */
		public function mock_oembed_failure() {
			return new WP_Error( 404, 'Not found' );
		}

	}
}

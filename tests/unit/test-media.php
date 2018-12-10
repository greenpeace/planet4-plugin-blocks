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

			// Youtube ID.
			$data = $this->block->prepare_data( [ 'youtube_id' => 'hW9ScsV6QJ0' ], '', '' );
			$this->assertEquals( 'youtube', $data['fields']['type'] );
			$this->assertEquals( 'hW9ScsV6QJ0', $data['fields']['media_id'] );

			// Youtube URL.
			$data = $this->block->prepare_data( [ 'youtube_id' => 'https://www.youtube.com/watch?v=hW9ScsV6QJ0' ], '', '' );
			$this->assertEquals( 'youtube', $data['fields']['type'] );
			$this->assertEquals( 'hW9ScsV6QJ0', $data['fields']['media_id'] );

			// Youtube short URL.
			$data = $this->block->prepare_data( [ 'youtube_id' => 'https://youtu.be/hW9ScsV6QJ0' ], '', '' );
			$this->assertEquals( 'youtube', $data['fields']['type'] );
			$this->assertEquals( 'hW9ScsV6QJ0', $data['fields']['media_id'] );
		}

		/**
		 * Test Vimeo URL formats
		 */
		public function test_vimeo() {

			// Vimeo URL.
			$data = $this->block->prepare_data( [ 'youtube_id' => 'https://vimeo.com/112010467' ], '', '' );
			$this->assertEquals( '112010467', $data['fields']['media_id'] );
			$this->assertEquals( 'vimeo', $data['fields']['type'] );

			// Vimeo channel URL.
			$data = $this->block->prepare_data( [ 'youtube_id' => 'https://vimeo.com/channels/staffpicks/303752260' ], '', '' );
			$this->assertEquals( 'vimeo', $data['fields']['type'] );
			$this->assertEquals( '303752260', $data['fields']['media_id'] );
		}

		/**
		 * Test SoundCloud URL formats
		 */
		public function test_soundcloud() {
			add_filter( 'pre_http_request', [ $this, 'mock_soundcloud_oembed_request' ] );

			// Soundcloud track URL.
			$data = $this->block->prepare_data( [ 'youtube_id' => 'https://soundcloud.com/greenpeace-canada/ep31-the-great-bear-rainforest-spirit-bears-scientists-at-the-movies' ], '', '' );
			$this->assertEquals( 'https://w.soundcloud.com/player/?visual=true&url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F247912018&show_artwork=true', $data['fields']['media_id'] );
			$this->assertEquals( 'soundcloud', $data['fields']['type'] );

			remove_filter( 'pre_http_request', [ $this, 'mock_soundcloud_oembed_request' ] );
		}

		/**
		 * Test bare video URL
		 */
		public function test_video() {

			$data = $this->block->prepare_data(
				[
					'youtube_id'       => 'https://soundcloudexample.org/video.mp4',
					'video_poster_img' => '',
				],
				'',
				''
			);
			$this->assertEquals( 'https://soundcloudexample.org/video.mp4', $data['fields']['media_id'] );
			$this->assertEquals( 'video', $data['fields']['type'] );
			$this->assertEquals( '', $data['fields']['video_poster_img_src'] );
		}

		/**
		 * Test bare audio file URL
		 */
		public function test_audio() {
			// MP3.
			$data = $this->block->prepare_data( [ 'youtube_id' => 'https://soundcloudexample.org/sound.mp3' ], '', '' );
			$this->assertEquals( 'https://soundcloudexample.org/sound.mp3', $data['fields']['media_id'] );
			$this->assertEquals( 'audio', $data['fields']['type'] );

			// WAV.
			$data = $this->block->prepare_data( [ 'youtube_id' => 'https://soundcloudexample.org/sound.wav' ], '', '' );
			$this->assertEquals( 'https://soundcloudexample.org/sound.wav', $data['fields']['media_id'] );
			$this->assertEquals( 'audio', $data['fields']['type'] );
		}

		/**
		 * Return saved HTTP response body for Soundcloud oembed request
		 *
		 * @return array
		 */
		public function mock_soundcloud_oembed_request() {
			return [ 'body' => '{"version":1.0,"type":"rich","provider_name":"SoundCloud","provider_url":"http://soundcloud.com","height":400,"width":"100%","title":"Ep.31: The Great Bear Rainforest, Spirit Bears \u0026 Scientists At the Movies by Greenpeace Podcast","description":"Eduardo Sousa walks us through an incredible, historical agreement on the Pacific Coast of Canada to protect a rainforest the size of Belgium. Bonus: What exactly is a Spirit Bear? \n\nAndrew Norton answers the questions you never knew you had on the new podcast: #CompletelyOptionalKnowledge. This story: What pisses off scientists the most in the movies?\n\nMUSIC @ 01:04 : Skyline by Gentle Fire Studio","thumbnail_url":"http://i1.sndcdn.com/artworks-000147830042-0mifih-t500x500.jpg","html":"\u003Ciframe width=\"100%\" height=\"400\" scrolling=\"no\" frameborder=\"no\" src=\"https://w.soundcloud.com/player/?visual=true\u0026url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F247912018\u0026show_artwork=true\"\u003E\u003C/iframe\u003E","author_name":"Greenpeace Podcast","author_url":"http://soundcloud.com/greenpeace-canada"}' ];
		}

	}
}

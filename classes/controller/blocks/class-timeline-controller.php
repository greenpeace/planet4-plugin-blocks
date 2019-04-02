<?php
/**
 * Timeline block class
 *
 * @package P4BKS
 * @since 1.38.0
 */

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'Timeline_Controller' ) ) {

	/**
	 * Class Timeline_Controller
	 *
	 * Uses TimelineJS library (v3) to render timelines, using Google Sheets as the
	 * data source.
	 *
	 * See https://timeline.knightlab.com/
	 *
	 * @package P4BKS\Controllers\Blocks
	 */
	class Timeline_Controller extends Controller {
		/**
		 * @var int - needed to allow multiple timelines in the same post.
		 */
		protected static $id = 1;

		const BLOCK_NAME = 'timeline';

		const TIMELINEJS_VERSION = '3.6.3';

		// These appear to be the supported languages as of TimelineJS version 3.6.3.
		const LANGUAGES = [
			'af'      => 'Afrikaans',
			'ar'      => 'Arabic',
			'hy'      => 'Armenian',
			'eu'      => 'Basque',
			'be'      => 'Belarusian',
			'bg'      => 'Bulgarian',
			'ca'      => 'Catalan',
			'zh-cn'   => 'Chinese',
			'hr'      => 'Croatian / Hrvatski',
			'cz'      => 'Czech',
			'da'      => 'Danish',
			'nl'      => 'Dutch',
			'en'      => 'English',
			'en-24hr' => 'English (24-hour time)',
			'eo'      => 'Esperanto',
			'et'      => 'Estonian',
			'fo'      => 'Faroese',
			'fa'      => 'Farsi',
			'fi'      => 'Finnish',
			'fr'      => 'French',
			'fy'      => 'Frisian',
			'gl'      => 'Galician',
			'ka'      => 'Georgian',
			'de'      => 'German / Deutsch',
			'el'      => 'Greek',
			'he'      => 'Hebrew',
			'hi'      => 'Hindi',
			'hu'      => 'Hungarian',
			'is'      => 'Icelandic',
			'id'      => 'Indonesian',
			'ga'      => 'Irish',
			'it'      => 'Italian',
			'ja'      => 'Japanese',
			'ko'      => 'Korean',
			'lv'      => 'Latvian',
			'lt'      => 'Lithuanian',
			'lb'      => 'Luxembourgish',
			'ms'      => 'Malay',
			'my'      => 'Myanmar',
			'ne'      => 'Nepali',
			'no'      => 'Norwegian',
			'pl'      => 'Polish',
			'pt'      => 'Portuguese',
			'pt-br'   => 'Portuguese (Brazilian)',
			'ro'      => 'Romanian',
			'rm'      => 'Romansh',
			'ru'      => 'Russian',
			'sr-cy'   => 'Serbian - Cyrillic',
			'sr'      => 'Serbian - Latin',
			'si'      => 'Sinhalese',
			'sk'      => 'Slovak',
			'sl'      => 'Slovenian',
			'es'      => 'Spanish',
			'sv'      => 'Swedish',
			'tl'      => 'Tagalog',
			'ta'      => 'Tamil',
			'zh-tw'   => 'Taiwanese',
			'te'      => 'Telugu',
			'th'      => 'Thai',
			'tr'      => 'Turkish',
			'uk'      => 'Ukrainian',
			'ur'      => 'Urdu',
		];

		/**
		 * Hooks all the needed functions to load the block
		 */
		public function load() {
			parent::load();

			add_action(
				'wp_enqueue_scripts',
				function () {
					global $post;

					if ( isset( $post->post_content ) ) {
						$has_timeline = strpos( $post->post_content, 'shortcake_' . self::BLOCK_NAME ) !== false;

						// Load css only if timeline shortcode appears in post content, or the post hasn't been saved yet
						// so we don't know (so as not to break the preview iframe before the post is saved).
						if ( $has_timeline || 'auto-draft' === $post->post_status ) {
							$css = 'https://cdn.knightlab.com/libs/timeline3/' . self::TIMELINEJS_VERSION . '/css/timeline.css';
							wp_enqueue_style( 'timelinejs', $css, [], self::TIMELINEJS_VERSION );
						}
					}
				}
			);
		}

		/**
		 * Shortcode UI setup
		 */
		public function prepare_fields() {
			$default_language_code  = $this->get_default_language();
			$default_language_label = self::LANGUAGES[ $default_language_code ];

			$language_options = [
				[
					'value' => $default_language_code,
					'label' => $default_language_label,
				],
			];

			foreach ( self::LANGUAGES as $language_code => $language_name ) {
				if ( $language_code === $default_language_code ) {
					continue;
				}

				$language_options[] = [
					'value' => $language_code,
					'label' => $language_name,
				];
			}

			$url_desc  = __(
				'Enter the URL of the Google Sheets spreadsheet containing your timeline data.',
				'planet4-blocks-backend'
			);
			$url_desc .= '<br><br><a href="https://timeline.knightlab.com/#make" target="_blank" rel="noopener noreferrer">';
			$url_desc .= __(
				'See the TimelineJS website for a template GSheet.',
				'planet4-blocks-backend'
			);
			$url_desc .= '</a><br>';
			$url_desc .= __(
				'Copy this, add your own timeline data, and publish to the web.',
				'planet4-blocks-backend'
			);
			$url_desc .= '<br>';
			$url_desc .= __(
				"Once you have done so, use the URL from your address bar (not the one provided in Google's 'publish to web' dialog).",
				'planet4-blocks-backend'
			);

			$fields = [
				[
					'label'       => __( 'Google Sheets URL', 'planet4-blocks-backend' ),
					'attr'        => 'google_sheets_url',
					'type'        => 'text',
					'description' => $url_desc,
					'meta'        => [
						'placeholder' => __( 'Enter URL', 'planet4-blocks-backend' ),
					],
				],
				[
					'label'   => __( 'Language', 'planet4-blocks-backend' ),
					'attr'    => 'language',
					'type'    => 'select',
					'options' => $language_options,
				],
				[
					'label'   => __( 'Timeline navigation position', 'planet4-blocks-backend' ),
					'attr'    => 'timenav_position',
					'type'    => 'select',
					'options' => [
						[
							'value' => 'bottom',
							'label' => __( 'Bottom', 'planet4-blocks-backend' ),
						],
						[
							'value' => 'top',
							'label' => __( 'Top', 'planet4-blocks-backend' ),
						],
					],
				],
				[
					'label'       => __( 'Start at end', 'planet4-blocks-backend' ),
					'attr'        => 'start_at_end',
					'type'        => 'checkbox',
					'description' => __( 'Begin at the end of the timeline', 'planet4-blocks-backend' ),
				],
			];

			$img_src = esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/timeline.png' );

			$shortcode_ui_args = [
				'label'         => __( 'Timeline', 'planet4-blocks-backend' ),
				'listItemImage' => '<img src="' . $img_src . '" />',
				'attrs'         => $fields,
				'post_type'     => P4BKS_ALLOWED_PAGETYPE,
			];

			shortcode_ui_register_for_shortcode( 'shortcake_' . self::BLOCK_NAME, $shortcode_ui_args );
		}

		/**
		 * Prepares data for view - and also adds JS to instantiate timeline.
		 *
		 * @param array  $fields This is the array of fields of this block.
		 * @param string $content This is the post content.
		 * @param string $shortcode_tag The shortcode tag of this block.
		 *
		 * @return array
		 */
		public function prepare_data( $fields, $content, $shortcode_tag ): array {
			$timeline_id = 'timeline-' . self::$id;
			self::$id ++;

			$fields = wp_parse_args(
				$fields,
				[
					'google_sheets_url' => '',
					'timenav_position'  => 'bottom',
					'start_at_end'      => false,
					'language'          => $this->get_default_language(),
				]
			);

			$url = esc_url( $fields['google_sheets_url'] );

			$options = wp_json_encode(
				[
					'timenav_position' => sanitize_text_field( $fields['timenav_position'] ),
					'start_at_end'     => boolval( $fields['start_at_end'] ),
					'language'         => sanitize_text_field( $fields['language'] ),
				]
			);

			$js = 'https://cdn.knightlab.com/libs/timeline3/' . self::TIMELINEJS_VERSION . '/js/timeline-min.js';
			wp_enqueue_script( 'timelinejs', $js, [], self::TIMELINEJS_VERSION, true );
			wp_add_inline_script( 'timelinejs', "new TL.Timeline('$timeline_id', '$url', $options);" );

			return [
				'timeline_id' => $timeline_id,
			];
		}

		/**
		 * Google Sheets URLs contain a hash parameter, 'gid', which identifies the particular tab/sheet of the
		 * spreadsheet document that is open, e.g. '#gid=0' for the first tab.
		 *
		 * However, the hash breaks the preview, and TimelineJS ignores it anyway - it always uses the first tab/sheet.
		 *
		 * @param array  $fields         Associative array of shortcode paramaters.
		 * @param string $content        The content of the shortcode block for content wrapper shortcodes only.
		 * @param string $shortcode_tag  The shortcode tag of the block.
		 *
		 * @return string
		 */
		public function prepare_template_preview_iframe( $fields, $content, $shortcode_tag ) {
			if ( ! empty( $fields['google_sheets_url'] ) ) {
				$fields['google_sheets_url'] = explode( '#', $fields['google_sheets_url'] )[0];
			}

			return parent::prepare_template_preview_iframe( $fields, $content, $shortcode_tag );
		}

		/**
		 * Attempts to match the site's locale to a TimelineJS-supported language, to be used
		 * as the default option for the language select field.
		 *
		 * TimelineJS doesn't support many language variants, so the likes of fr-be (Belgian
		 * French), en-gb (British English) or es-mx (Mexican Spanish)
		 * would be matched to fr, en and es respectively.
		 *
		 * Defaults to English if can't find a match.
		 *
		 * @return string
		 */
		protected function get_default_language() {
			$locale = strtolower( str_replace( '_', '-', get_locale() ) );

			if ( isset( self::LANGUAGES[ $locale ] ) ) {
				$lang = $locale;
			} elseif ( substr( $locale, 2, 1 ) === '-' ) {
				$prefix = substr( $locale, 0, 2 );

				if ( isset( self::LANGUAGES[ $prefix ] ) ) {
					$lang = $prefix;
				}
			}

			return $lang ?? 'en';
		}
	}
}

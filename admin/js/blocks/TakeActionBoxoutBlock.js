/* exported TakeActionBoxoutBlock */

function TakeActionBoxoutBlock() {
  const me = this;

  /**
   * Hook disables custom fields when value from dropdown is selected
   * or disables dropdown if any of the custom fields are filled out
   */
  me.edit_custom_filed_hook = function () {
    if ( $( '[name="custom_title"]' ).val() ||
			$( '[name="custom_excerpt"]' ).val() ||
			$( '[name="custom_link"]' ).val() ||
			$( '[name="custom_link_text"]' ).val() ||
			$( '[name="tag_ids"]' ).val() ||
			$( '.shortcake-attachment-preview' ).length > 0 ||
      ( $( '[name="custom_link_new_tab"]' )[0] && $( '[name="custom_link_new_tab"]' )[0].checked ) ) {
      $( '.shortcode-ui-post-select' ).prop( 'disabled', true );
    } else if ( $( '.shortcode-ui-post-select' ).val() ) {
      $( '[name="custom_title"]' ).prop( 'disabled', true );
      $( '[name="custom_excerpt"]' ).prop( 'disabled', true );
      $( '[name="custom_link"]' ).prop( 'disabled', true );
      $( '[name="custom_link_text"]' ).prop( 'disabled', true );
      $( '[name="tag_ids"]' ).prop( 'disabled', true );
      $( '#background_image' ).prop( 'disabled', true );
      $( '[name="custom_link_new_tab"]' ).prop( 'disabled', true );
      $( '.shortcake-attachment-select' ).prop( 'disabled', true );
    } else {
      $( '.shortcode-ui-post-select' ).removeAttr( 'disabled' );
      $( '[name="custom_title"]' ).removeAttr( 'disabled' );
      $( '[name="custom_excerpt"]' ).removeAttr( 'disabled' );
      $( '[name="custom_link"]' ).removeAttr( 'disabled' );
      $( '[name="custom_link_text"]' ).removeAttr( 'disabled' );
      $( '[name="tag_ids"]' ).removeAttr( 'disabled' );
      $( '#background_image' ).removeAttr( 'disabled' );
      $( '[name="custom_link_new_tab"]' ).removeAttr( 'disabled' );
      $( '.shortcake-attachment-select' ).removeAttr( 'disabled' );
    }
  };
}
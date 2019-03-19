function SocialMediaBlock(p4BlocksUI) { // eslint-disable-line no-unused-vars
  var me = this;

  me.initialize_fields = function () {
    me.set_default_embed_type();
    me.toggle_facebook_page_options();
  };

  me.embed_type_change_hook = function () {
    me.toggle_facebook_page_options();
  };

  /**
   * If no value, default to oembed
   */
  me.set_default_embed_type = function () {
    if (!$('input[name=embed_type]:checked').val()) {
      $('input[name=embed_type][value=oembed]').prop('checked', true);
    }
  };

  /**
   * Show/hide Facebook page options according to embed_type
   */
  me.toggle_facebook_page_options = function () {
    var $facebook_page_options = $('.shortcode-ui-attribute-facebook_page_tab');
    var embed_type = $('input[name=embed_type]:checked').val();

    if ('facebook_page' === embed_type) {
      $facebook_page_options.show();
    } else {
      $facebook_page_options.hide();
    }
  };
}
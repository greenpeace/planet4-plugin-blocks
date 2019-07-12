/* exported ArticlesBlock */

// Define hook functions for articles block fields to be used when creating/editing an articles block.
function ArticlesBlock(p4BlocksUI) {
  const me = this;

  /**
   * Disable/Enable posts select box based on post types and tags select boxes.
   */
  me.page_types_change_hook = function () {
    const posts_value = $('select[id^=\'shortcode-ui-post_types\']').val();
    const tags = $('select[id^=\'shortcode-ui-tags\']').val();
    if (null === posts_value && null === tags) {
      $('select[id^=\'shortcode-ui-posts\']').prop('disabled', false);
    } else {
      $('select[id^=\'shortcode-ui-posts\']').val(null).trigger('change.select2');
      $('select[id^=\'shortcode-ui-posts\']').prop('disabled', 'disabled');
    }
  };

  /**
   * Disable/Enable p4 page types checkboxes based on posts select box value.
   */
  me.posts_select_change_hook = function () {
    const posts_value = $('select[id^=\'shortcode-ui-posts\']').val();
    if (null === posts_value) {
      $('select[id^=\'shortcode-ui-tags\']').prop('disabled', false);
      $('select[id^=\'shortcode-ui-post_types\']').prop('disabled', false);
      $('input[name^=\'ignore_categories\']').prop('disabled', false);
    } else {
      $('select[id^=\'shortcode-ui-post_types\']').val(null).trigger('change.select2');
      $('select[id^=\'shortcode-ui-post_types\']').prop('disabled', 'disabled');
      $('select[id^=\'shortcode-ui-tags\']').val(null).trigger('change.select2');
      $('select[id^=\'shortcode-ui-tags\']').prop('disabled', 'disabled');
      $('input[name^=\'ignore_categories\']').prop('disabled', 'disabled');
    }
  };

  me.read_more_change_hook = function (changed, collection) {
    const view = p4BlocksUI.find_view(collection, 'read_more_link');
    if ('undefined' !== view) {
      if (typeof changed.value !== 'undefined') {
        let url    = '';
        const format = /%[0-9a-f]/i;
        if ( format.test( changed.value ) ) {
          url = changed.value;
        } else {
          url = encodeURI(changed.value);
        }
        if ('undefined' !== url) {
          view.model.set('value', url);
          $('*[id^=\'shortcode-ui-read_more_link-\']').val(url);
        }
      }
    }
  };

  /**
   * Disable/enable fields of an articles block when rendering a preexisting articles block.
   */
  me.initialize_view_fields = function () {
    const posts = $('select[id^=\'shortcode-ui-posts\']').val();
    const tags = $('select[id^=\'shortcode-ui-tags\']').val();
    const post_types = $('select[id^=\'shortcode-ui-post_types\']').val();

    if (null !== posts) {
      $('select[id^=\'shortcode-ui-post_types\']').prop('disabled', 'disabled');
      $('select[id^=\'shortcode-ui-tags\']').prop('disabled', 'disabled');

    } else {
      if (null !== tags || null !== post_types) {
        $('select[id^=\'shortcode-ui-posts\']').prop('disabled', 'disabled');
      }
    }
  };
}

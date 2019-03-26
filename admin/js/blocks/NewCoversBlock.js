// Define hook functions for newcovers block fields to be used when creating/editing a newcovers block.
function NewCoversBlock(p4BlocksUI) { // eslint-disable-line no-unused-vars
  var me = this;

  /**
   * Cover type field change hook.
   */
  me.type_of_cover_change_hook = function () {
    var cover_type = $('input[name=cover_type]:checked').val();
    if ('undefined' === cover_type) {
      return;
    }

    if ('1' === cover_type) {
      $('select[id^=\'shortcode-ui-post_types\']').prop('disabled', 'disabled');
      $('select[id^=\'shortcode-ui-posts\']').prop('disabled', false);
      $('select[id^=\'shortcode-ui-posts\']').val(null).trigger('change');
      $('select[id^=\'shortcode-ui-tags\']').val(null).trigger('change');
    } else if ('2' === cover_type) {
      $('select[id^=\'shortcode-ui-post_types\']').prop('disabled', 'disabled');
      $('select[id^=\'shortcode-ui-posts\']').prop('disabled', 'disabled');
      $('select[id^=\'shortcode-ui-posts\']').val(null).trigger('change');
      $('select[id^=\'shortcode-ui-tags\']').val(null).trigger('change');
    } else if ('3' === cover_type) {
      $('select[id^=\'shortcode-ui-post_types\']').prop('disabled', false);
      $('select[id^=\'shortcode-ui-posts\']').prop('disabled', false);
      $('select[id^=\'shortcode-ui-posts\']').val(null).trigger('change');
      $('select[id^=\'shortcode-ui-tags\']').val(null).trigger('change');
    }
  };

  /**
   * Disable/enable fields of a newcovers block when rendering a preexisting newcovers block.
   */
  me.initialize_view_fields = function () {
    var cover_type = $('input[name=cover_type]:checked').val();
    var posts = $('select[id^=\'shortcode-ui-posts\']').val();
    var tags = $('select[id^=\'shortcode-ui-tags\']').val();
    var post_types = $('select[id^=\'shortcode-ui-post_types\']').val();
    if ('undefined' === cover_type) {
      return;
    }

    if ('1' === cover_type) {
      $('select[id^=\'shortcode-ui-post_types\']').prop('disabled', 'disabled');
      $('select[id^=\'shortcode-ui-post_types\']').val(null).trigger('change');

      if (null !== tags || null !== post_types) {
        $('select[id^=\'shortcode-ui-posts\']').prop('disabled', 'disabled');
        $('select[id^=\'shortcode-ui-posts\']').val(null).trigger('change');
      }
      if (null !== posts) {
        $('select[id^=\'shortcode-ui-tags\']').prop('disabled', 'disabled');
        $('select[id^=\'shortcode-ui-tags\']').val(null).trigger('change');
      }
    } else if ('2' === cover_type) {
      $('select[id^=\'shortcode-ui-post_types\']').prop('disabled', 'disabled');
      $('select[id^=\'shortcode-ui-post_types\']').val(null).trigger('change');
      $('select[id^=\'shortcode-ui-posts\']').prop('disabled', 'disabled');
      $('select[id^=\'shortcode-ui-posts\']').val(null).trigger('change');
    } else if ('3' === cover_type) {
      if (null !== tags || null !== post_types) {
        $('select[id^=\'shortcode-ui-posts\']').prop('disabled', 'disabled');
        $('select[id^=\'shortcode-ui-posts\']').val(null).trigger('change');
      }
      if (null !== posts) {
        $('select[id^=\'shortcode-ui-tags\']').prop('disabled', 'disabled');
        $('select[id^=\'shortcode-ui-tags\']').val(null).trigger('change');
        $('select[id^=\'shortcode-ui-post_types\']').prop('disabled', 'disabled');
        $('select[id^=\'shortcode-ui-post_types\']').val(null).trigger('change');
      }
    }
  };

  /**
   * Post types select box change hook.
   */
  me.post_types_change_hook = function () {
    var cover_type = $('input[name=cover_type]:checked').val();
    var tags = $('select[id^=\'shortcode-ui-tags\']').val();
    var post_types = $('select[id^=\'shortcode-ui-post_types\']').val();
    var posts = $('select[id^=\'shortcode-ui-posts\']').val();
    if ('undefined' === cover_type) {
      return;
    }

    if ('3' === cover_type) {
      if (null !== post_types) {
        $('select[id^=\'shortcode-ui-tags\']').prop('disabled', false);
        $('select[id^=\'shortcode-ui-post_types\']').prop('disabled', false);
        $('select[id^=\'shortcode-ui-posts\']').val(null).trigger('change');
        $('select[id^=\'shortcode-ui-posts\']').prop('disabled', 'disabled');

      } else if (null === tags && null === posts && null === post_types) {
        $('select[id^=\'shortcode-ui-tags\']').prop('disabled', false);
        $('select[id^=\'shortcode-ui-post_types\']').prop('disabled', false);
        $('select[id^=\'shortcode-ui-posts\']').prop('disabled', false);
      }
    }
  };

  /**
   * Post select box change hook.
   */
  me.posts_select_change_hook = function () {
    var cover_type = $('input[name=cover_type]:checked').val();
    var posts = $('select[id^=\'shortcode-ui-posts\']').val();
    if ('undefined' === cover_type) {
      return;
    }

    if (posts !== null) {
      if ('1' === cover_type) {
        $('select[id^=\'shortcode-ui-tags\']').val(null).trigger('change');
        $('select[id^=\'shortcode-ui-tags\']').prop('disabled', 'disabled');
      } else if ('3' === cover_type) {
        $('select[id^=\'shortcode-ui-tags\']').val(null).trigger('change');
        $('select[id^=\'shortcode-ui-tags\']').prop('disabled', 'disabled');
        $('select[id^=\'shortcode-ui-post_types\']').val(null).trigger('change');
        $('select[id^=\'shortcode-ui-post_types\']').prop('disabled', 'disabled');
      }
    } else {
      if ('1' === cover_type) {
        $('select[id^=\'shortcode-ui-tags\']').prop('disabled', false);
      } else if ('3' === cover_type) {
        $('select[id^=\'shortcode-ui-tags\']').prop('disabled', false);
        $('select[id^=\'shortcode-ui-post_types\']').prop('disabled', false);
      }
    }
  };

  /**
   * Tags select box change hook.
   */
  me.tags_change_hook = function () {
    var cover_type = $('input[name=cover_type]:checked').val();
    var posts = $('select[id^=\'shortcode-ui-posts\']').val();
    var tags = $('select[id^=\'shortcode-ui-tags\']').val();
    var post_types = $('select[id^=\'shortcode-ui-post_types\']').val();
    if ('undefined' === cover_type) {
      return;
    }

    if ('1' === cover_type) {
      if (null !== tags || null !== post_types) {
        $('select[id^=\'shortcode-ui-posts\']').prop('disabled', 'disabled');
        $('select[id^=\'shortcode-ui-posts\']').val(null).trigger('change');
        $('select[id^=\'shortcode-ui-post_types\']').prop('disabled', 'disabled');
      } else if (null === tags && null === posts) {
        $('select[id^=\'shortcode-ui-tags\']').prop('disabled', false);
        $('select[id^=\'shortcode-ui-posts\']').prop('disabled', false);
      }
    } else if ('2' === cover_type) {
      $('select[id^=\'shortcode-ui-posts\']').prop('disabled', 'disabled');
      $('select[id^=\'shortcode-ui-posts\']').val(null).trigger('change');
      $('select[id^=\'shortcode-ui-tags\']').prop('disabled', false);
      $('select[id^=\'shortcode-ui-post_types\']').val(null).trigger('change');
      $('select[id^=\'shortcode-ui-post_types\']').prop('disabled', 'disabled');
    } else if ('3' === cover_type) {
      if (null !== tags) {
        $('select[id^=\'shortcode-ui-tags\']').prop('disabled', false);
        $('select[id^=\'shortcode-ui-post_types\']').prop('disabled', false);
        $('select[id^=\'shortcode-ui-posts\']').val(null).trigger('change');
        $('select[id^=\'shortcode-ui-posts\']').prop('disabled', 'disabled');

      } else if (null === tags && null === posts && null === post_types) {
        $('select[id^=\'shortcode-ui-posts\']').prop('disabled', false);
      }
    }
  };
}
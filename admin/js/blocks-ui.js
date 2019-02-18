// Override shortcake's editAttributeSelect2Field backbone view to manipulate select2 instance.
// Modified versions of render and preselect functions of that view.
if ('undefined' !== sui.views) {

  /**
   * Load the values to be preselected before initializing field
   *
   * @param $field jQuery object reference to the <select> field
   * @param object ajaxData object containing ajax action, nonce, and shortcode & model data
   * @param string includeField how to specify the current selection, ie 'post__in'
   */
  sui.views.editAttributeSelect2Field.prototype.preselect = function ($field) {
    var _preselected = String(this.getValue());

    if (_preselected.length) {
      var request = {
        include: _preselected,
        shortcode: this.shortcode.get('shortcode_tag'),
        attr: this.model.get('attr')
      };

      if ('shortcake_newcovers' === this.shortcode.get('shortcode_tag') && 'posts' === this.model.get('attr')) {
        this.ajaxData.action = 'planet4_blocks_post_field';
      }
      return $.get(ajaxurl, $.extend(request, this.ajaxData),
        function (response) {
          _.each(response.data.items, function (item) {
            $('<option>')
              .attr('value', item.id)
              .text(item.text)
              .prop('selected', 'selected')
              .appendTo($field);
          });
        }
      );
    }
    return null;
  };

  /**
   * Abstract field for all ajax Select2-powered field views
   *
   * Adds useful helpers that are shared between all of the fields which use
   * Select2 as their UI.
   *
   */
  sui.views.editAttributeSelect2Field.prototype.render = function () {
    var self = this,
      defaults = {multiple: false};

    for (var arg in defaults) {
      if (!this.model.get(arg)) {
        this.model.set(arg, defaults[arg]);
      }
    }

    var data = this.model.toJSON();
    data.id = 'shortcode-ui-' + this.model.get('attr') + '-' + this.model.cid;

    this.$el.html(this.template(data));

    var $field = this.$el.find(this.selector);

    if (this.shortcode.get('ajax_requests') === undefined) {
      this.shortcode.set('ajax_requests', []);
    }
    var request = this.preselect($field);
    if (null !== request) {
      var requests = this.shortcode.get('ajax_requests');
      if (Array.isArray(requests)) {
        requests.push(request);
        this.shortcode.set('ajax_requests', requests);
      }
    }

    var select2_options = this.model.get('meta').select2_options;

    var default_options = {
      multiple: this.model.get('multiple'),
      dropdownParent: this.$el,
      allowClear: true,

      ajax: {
        url: ajaxurl,
        dataType: 'json',
        delay: 250,
        data: function (params) {
          // Overriding ajax data function for specific shortcode attribute ('shortcake_newcovers' - 'posts')
          if ('shortcake_newcovers' === self.shortcode.get('shortcode_tag') && 'posts' === self.model.get('attr')) {
            self.ajaxData.action = 'planet4_blocks_post_field';
            return $.extend({
              type: function () {
                return $('input[name=cover_type]:checked').val()
              },
              s: params.term, // search term
              page: params.page,
              shortcode: self.shortcode.get('shortcode_tag'),
              attr: self.model.get('attr'),
              action: 'wp_ajax_planet4_blocks_post_field'
            }, self.ajaxData);
          } else {
            return $.extend({
              s: params.term, // search term
              page: params.page,
              shortcode: self.shortcode.get('shortcode_tag'),
              attr: self.model.get('attr'),
            }, self.ajaxData);
          }
        },
        processResults: function (response, params) {
          var data = response.data;
          params.page = params.page || 1;
          if (!response.success || 'undefined' === typeof response.data) {
            return {results: []};
          }
          return {
            results: data.items,
            pagination: {
              more: (params.page * data.items_per_page) < data.found_items
            }
          };
        },
        cache: true
      },

      escapeMarkup: function (markup) {
        return markup;
      },
      minimumInputLength: 0,
      templateResult: this.templateResult,
      templateSelection: this.templateSelection,
    };

    var soptions = Object.assign({}, default_options, select2_options);

    var that = this;
    _.defer(function () {
      var $fieldSelect2 = $field[shortcodeUIData.select2_handle](soptions);
      if (that.model.get('multiple')) {
        that.sortable($field);
      }
    }, that, $field);

    return this;
  };
}


jQuery(function ($) {
  'use strict';

  if ('undefined' !== typeof (wp.shortcake)) {

    shortcodeUIFieldData.p4_select = {
      encode: false,
      template: "shortcode-ui-field-p4-select",
      view: "editAttributeHeading"
    };
    shortcodeUIFieldData.p4_checkbox = {
      encode: false,
      template: "shortcode-ui-field-p4-checkbox",
      view: "editAttributeHeading"
    };
    shortcodeUIFieldData.p4_radio = {
      encode: false,
      template: "shortcode-ui-field-p4-radio",
      view: "editAttributeHeading"
    };

    // break submenu attribute groups into rows
    var addTags = function () {
      $(".shortcode-ui-attribute-heading2").parent().before('<p></p>');
      $(".shortcode-ui-attribute-heading3").parent().before('<p></p>');
    }
    wp.shortcake.hooks.addAction('shortcode-ui.render_edit', addTags);
    wp.shortcake.hooks.addAction('shortcode-ui.render_new', addTags);
  }
});

// Define a p4_blocks object that holds functions used during rendering backend blocks' views.
var p4_blocks = {

  hooks_defined: false,

  initialize_view_fields: function (block_name) {
    switch (block_name) {
      case 'articles':
        p4_blocks.articles.initialize_view_fields();
        break;
      case 'newcovers':
        p4_blocks.newcovers.initialize_view_fields();
        break;
    }
  },

  find_view: function (collection, name) {
    return _.find(
      collection,
      function (viewModel) {
        return name === viewModel.model.get('attr');
      }
    );
  },

  // Define hook functions for newcovers block fields to be used when creating/editing a newcovers block.
  newcovers: {
    /**
     * Cover type field change hook.
     */
    type_of_cover_change_hook: function () {

      var cover_type = $('input[name=cover_type]:checked').val();
      if ('undefined' === cover_type) {
        return;
      }

      if ('1' === cover_type) {
        $("select[id^='shortcode-ui-post_types']").prop('disabled', 'disabled');
        $("select[id^='shortcode-ui-posts']").prop('disabled', false);
        $("select[id^='shortcode-ui-posts']").val(null).trigger('change');
        $("select[id^='shortcode-ui-tags']").val(null).trigger('change');
      } else if ('2' === cover_type) {
        $("select[id^='shortcode-ui-post_types']").prop('disabled', 'disabled');
        $("select[id^='shortcode-ui-posts']").prop('disabled', 'disabled');
        $("select[id^='shortcode-ui-posts']").val(null).trigger('change');
        $("select[id^='shortcode-ui-tags']").val(null).trigger('change');
      } else if ('3' === cover_type) {
        $("select[id^='shortcode-ui-post_types']").prop('disabled', false);
        $("select[id^='shortcode-ui-posts']").prop('disabled', false);
        $("select[id^='shortcode-ui-posts']").val(null).trigger('change');
        $("select[id^='shortcode-ui-tags']").val(null).trigger('change');
      }
    },


    /**
     * Disable/enable fields of a newcovers block when rendering a preexisting newcovers block.
     */
    initialize_view_fields: function () {

      var cover_type = $('input[name=cover_type]:checked').val();
      var posts = $("select[id^='shortcode-ui-posts']").val();
      var tags = $("select[id^='shortcode-ui-tags']").val();
      var post_types = $("select[id^='shortcode-ui-post_types']").val();
      if ('undefined' === cover_type) {
        return;
      }

      if ('1' === cover_type) {
        $("select[id^='shortcode-ui-post_types']").prop('disabled', 'disabled');
        $("select[id^='shortcode-ui-post_types']").val(null).trigger('change');

        if (null !== tags || null !== post_types) {
          $("select[id^='shortcode-ui-posts']").prop('disabled', 'disabled');
          $("select[id^='shortcode-ui-posts']").val(null).trigger('change');
        }
        if (null !== posts) {
          $("select[id^='shortcode-ui-tags']").prop('disabled', 'disabled');
          $("select[id^='shortcode-ui-tags']").val(null).trigger('change');
        }
      } else if ('2' === cover_type) {
        $("select[id^='shortcode-ui-post_types']").prop('disabled', 'disabled');
        $("select[id^='shortcode-ui-post_types']").val(null).trigger('change');
        $("select[id^='shortcode-ui-posts']").prop('disabled', 'disabled');
        $("select[id^='shortcode-ui-posts']").val(null).trigger('change');
      } else if ('3' === cover_type) {
        if (null !== tags || null !== post_types) {
          $("select[id^='shortcode-ui-posts']").prop('disabled', 'disabled');
          $("select[id^='shortcode-ui-posts']").val(null).trigger('change');
        }
        if (null !== posts) {
          $("select[id^='shortcode-ui-tags']").prop('disabled', 'disabled');
          $("select[id^='shortcode-ui-tags']").val(null).trigger('change');
          $("select[id^='shortcode-ui-post_types']").prop('disabled', 'disabled');
          $("select[id^='shortcode-ui-post_types']").val(null).trigger('change');
        }
      }
    },

    /**
     * Post types select box change hook.
     */
    post_types_change_hook: function () {

      var cover_type = $('input[name=cover_type]:checked').val();
      var tags = $("select[id^='shortcode-ui-tags']").val();
      var post_types = $("select[id^='shortcode-ui-post_types']").val();
      var posts = $("select[id^='shortcode-ui-posts']").val();
      if ('undefined' === cover_type) {
        return;
      }

      if ('3' === cover_type) {
        if (null !== post_types) {
          $("select[id^='shortcode-ui-tags']").prop('disabled', false);
          $("select[id^='shortcode-ui-post_types']").prop('disabled', false);
          $("select[id^='shortcode-ui-posts']").val(null).trigger('change');
          $("select[id^='shortcode-ui-posts']").prop('disabled', 'disabled');

        } else if (null === tags && null === posts && null === post_types) {
          $("select[id^='shortcode-ui-tags']").prop('disabled', false);
          $("select[id^='shortcode-ui-post_types']").prop('disabled', false);
          $("select[id^='shortcode-ui-posts']").prop('disabled', false);
        }
      }

    },

    /**
     * Post select box change hook.
     */
    posts_select_change_hook: function () {

      var cover_type = $('input[name=cover_type]:checked').val();
      var posts = $("select[id^='shortcode-ui-posts']").val();
      if ('undefined' === cover_type) {
        return;
      }

      if (posts !== null) {
        if ('1' === cover_type) {
          $("select[id^='shortcode-ui-tags']").val(null).trigger('change');
          $("select[id^='shortcode-ui-tags']").prop('disabled', 'disabled');
        } else if ('3' === cover_type) {
          $("select[id^='shortcode-ui-tags']").val(null).trigger('change');
          $("select[id^='shortcode-ui-tags']").prop('disabled', 'disabled');
          $("select[id^='shortcode-ui-post_types']").val(null).trigger('change');
          $("select[id^='shortcode-ui-post_types']").prop('disabled', 'disabled');
        }
      } else {
        if ('1' === cover_type) {
          $("select[id^='shortcode-ui-tags']").prop('disabled', false);
        } else if ('3' === cover_type) {
          $("select[id^='shortcode-ui-tags']").prop('disabled', false);
          $("select[id^='shortcode-ui-post_types']").prop('disabled', false);
        }
      }
    },

    /**
     * Tags select box change hook.
     */
    tags_change_hook: function () {

      var cover_type = $('input[name=cover_type]:checked').val();
      var posts = $("select[id^='shortcode-ui-posts']").val();
      var tags = $("select[id^='shortcode-ui-tags']").val();
      var post_types = $("select[id^='shortcode-ui-post_types']").val();
      if ('undefined' === cover_type) {
        return;
      }

      if ('1' === cover_type) {
        if (null !== tags || null !== post_types) {
          $("select[id^='shortcode-ui-posts']").prop('disabled', 'disabled');
          $("select[id^='shortcode-ui-posts']").val(null).trigger('change');
          $("select[id^='shortcode-ui-post_types']").prop('disabled', 'disabled');
        } else if (null === tags && null === posts) {
          $("select[id^='shortcode-ui-tags']").prop('disabled', false);
          $("select[id^='shortcode-ui-posts']").prop('disabled', false);
        }
      } else if ('2' === cover_type) {
        $("select[id^='shortcode-ui-posts']").prop('disabled', 'disabled');
        $("select[id^='shortcode-ui-posts']").val(null).trigger('change');
        $("select[id^='shortcode-ui-tags']").prop('disabled', false);
        $("select[id^='shortcode-ui-post_types']").val(null).trigger('change');
        $("select[id^='shortcode-ui-post_types']").prop('disabled', 'disabled');
      } else if ('3' === cover_type) {
        if (null !== tags) {
          $("select[id^='shortcode-ui-tags']").prop('disabled', false);
          $("select[id^='shortcode-ui-post_types']").prop('disabled', false);
          $("select[id^='shortcode-ui-posts']").val(null).trigger('change');
          $("select[id^='shortcode-ui-posts']").prop('disabled', 'disabled');

        } else if (null === tags && null === posts && null === post_types) {
          $("select[id^='shortcode-ui-posts']").prop('disabled', false);
        }
      }
    },

  },

  // Define hook functions for articles block fields to be used when creating/editing an articles block.
  articles: {
    /**
     * Disable/Enable posts select box based on post types and tags select boxes.
     */
    page_types_change_hook: function () {

      var posts_value = $("select[id^='shortcode-ui-post_types']").val();
      var tags = $("select[id^='shortcode-ui-tags']").val();
      if (null === posts_value && null === tags) {
        $("select[id^='shortcode-ui-posts']").prop('disabled', false);
      } else {
        $("select[id^='shortcode-ui-posts']").val(null).trigger('change.select2');
        $("select[id^='shortcode-ui-posts']").prop('disabled', 'disabled');
      }
    },

    /**
     * Disable/Enable p4 page types checkboxes based on posts select box value.
     */
    posts_select_change_hook: function () {

      var posts_value = $("select[id^='shortcode-ui-posts']").val();
      if (null === posts_value) {
        $("select[id^='shortcode-ui-tags']").prop('disabled', false);
        $("select[id^='shortcode-ui-post_types']").prop('disabled', false);
        $("input[name^='ignore_categories']").prop('disabled', false);
      } else {
        $("select[id^='shortcode-ui-post_types']").val(null).trigger('change.select2');
        $("select[id^='shortcode-ui-post_types']").prop('disabled', 'disabled');
        $("select[id^='shortcode-ui-tags']").val(null).trigger('change.select2');
        $("select[id^='shortcode-ui-tags']").prop('disabled', 'disabled');
        $("input[name^='ignore_categories']").prop('disabled', 'disabled');
      }
    },

    read_more_change_hook: function (changed, collection, shortcode) {

      var view = p4_blocks.find_view(collection, 'read_more_link');
      if ('undefined' !== view) {
        if (typeof changed.value !== 'undefined') {
          var url    = '';
          var format = /%[0-9a-f]/i;
          if ( format.test( changed.value ) ) {
            url = changed.value;
          } else {
            url = encodeURI(changed.value);
          }
          if ('undefined' !== url) {
            view.model.set('value', url);
            $("*[id^='shortcode-ui-read_more_link-']").val(url);
          }
        }
      }
    },

    /**
     * Disable/enable fields of an articles block when rendering a preexisting articles block.
     */
    initialize_view_fields: function () {

      var posts = $("select[id^='shortcode-ui-posts']").val();
      var tags = $("select[id^='shortcode-ui-tags']").val();
      var post_types = $("select[id^='shortcode-ui-post_types']").val();

      if (null !== posts) {
        $("select[id^='shortcode-ui-post_types']").prop('disabled', 'disabled');
        $("select[id^='shortcode-ui-tags']").prop('disabled', 'disabled');

      } else {
        if (null !== tags || null !== post_types) {
          $("select[id^='shortcode-ui-posts']").prop('disabled', 'disabled');
        }
      }
    }
  },

  social_media: {
    initialize_fields: function () {
      p4_blocks.social_media.set_default_embed_type();
      p4_blocks.social_media.toggle_facebook_page_options();
    },

    embed_type_change_hook: function () {
      p4_blocks.social_media.toggle_facebook_page_options();
    },

    /**
     * If no value, default to oembed
     */
    set_default_embed_type: function () {
      if (!$('input[name=embed_type]:checked').val()) {
        $('input[name=embed_type][value=oembed]').prop('checked', true);
      }
    },

    /**
     * Show/hide Facebook page options according to embed_type
     */
    toggle_facebook_page_options: function () {
      var $facebook_page_options = $('.shortcode-ui-attribute-facebook_page_tab');
      var embed_type = $('input[name=embed_type]:checked').val();

      if ('facebook_page' === embed_type) {
        $facebook_page_options.show();
      } else {
        $facebook_page_options.hide();
      }
    }
  },

  columns: {

    /**
     * Called when a new columns block is rendered in the backend.

     * @param shortcode Shortcake backbone model.
     */
    render_new: function (shortcode) {

      var $shortcode_div = $('.shortcode-ui-edit-shortcake_columns');
      $shortcode_div.append('<div data-row="0"><button class="button button-small shortcake-columns-add-column">Add Column</button>'
        + '<button class="button button-small shortcake-columns-remove-column" disabled="disabled">Remove Column</button></div>');
      this.hide_all_columns();

      this.add_click_event_handlers();
    },

    /**
     * Called when en existing columns block is rendered in the backend.

     * @param shortcode Shortcake backbone model.
     */
    render_edit: function (shortcode) {

      var $shortcode_div = $('.shortcode-ui-edit-shortcake_columns');
      $shortcode_div.append('<div data-row="0"><button class="button button-small shortcake-columns-add-column" data-row="1 data-action="add">Add Column</button>'
        + '<button class="button button-small shortcake-columns-remove-column">Remove Column</button></div>');
      var row = 0;

      [1, 2, 3, 4].forEach(function (index) {
        var input_values = $('.field-block').filter($('div[class$=\'_' + index + '\']')).children().filter($('input, textarea')).map(function (idx, elem) {
          return $(elem).val();
        }).get().join('');

        if ('' !== input_values) {
          row = index;
        }
      });

      $('.shortcake-columns-add-column').parent().data('row', row);
      for (var i = row+1; i <= 4; i++) {
        $('.field-block').filter($('div[class$=\'_' + i + '\']')).hide();
      }
      if (row === 4) {
        $('.shortcake-columns-add-column').attr('disabled', 'disabled');
      }

      this.toggle_images();
      this.add_click_event_handlers();
    },

    /**
     * Add click event handlers for add/remove buttons in columns block.
     */
    add_click_event_handlers: function () {

      var columns = this;
      // Add click event handlers for the elements.
      $('.shortcake-columns-add-column').on('click', function (event) {
        event.preventDefault();
        var $element = $(event.currentTarget);
        var row = $element.parent().data('row');

        if (row < 5) {
          columns.show_column(++row);
          $element.parent().data('row', row);
          $('.shortcake-columns-remove-column').removeAttr('disabled');
          if (row === 4) {
            $element.attr('disabled', 'disabled');
          }
        }
      });

      $('.shortcake-columns-remove-column').on('click', function (event) {
        event.preventDefault();
        var $element = $(event.currentTarget);
        var row = $element.parent().data('row');

        if (row >= 0) {
          columns.hide_column(row--);
          $element.parent().data('row', row);
          $('.shortcake-columns-add-column').removeAttr('disabled');
          if (row === 0) {
            $element.attr('disabled', 'disabled');
          }
        }
      });

      $('input[name=columns_block_style]').off('click').on('click', function() {
        p4_blocks.columns.toggle_images();
      });
    },

    /**
     * Hide a columns block row and reset the values of it's fields.
     *
     * @param row
     */
    hide_column: function (row) {
      var $column = $('.field-block').filter($('div[class$=\'_' + row + '\']'));
      // Clear all text, textarea fields for this row/column.
      $column.
        children().
        filter($('input, textarea')).each(function (index, element) {
          $(element).val('').trigger('input');
        });
      // Clear image attachment if set in this row/column.
      $column.
        find($('.attachment-previews .remove')).each(function (index, element) {
          $(element).click();
        });
      // Hide column's fields.
      $column.hide(300);
    },

    /**
     * Hide all columns block rows.
     *
     * @param row
     */
    hide_all_columns: function () {
      [1,2,3,4].forEach(function (row) {
        $( '.field-block' ).filter( $( 'div[class$=\'_'+row+'\']' ) ).hide();
      });
    },

    /**
     * Show a columns block row and scroll to bottom.
     *
     * @param row
     */
    show_column: function (row) {
      $('.field-block').filter($('div[class$=\'_' + row + '\']')).show(300, function () {
        p4_blocks.columns.toggle_images();
        $('.media-frame-content').animate({
          scrollTop: $('.shortcode-ui-content').prop('scrollHeight'),
        }, 300);
      });
    },

    /**
     * Show/hide images inputs depending on column block style.
     */
    toggle_images: function() {
      [1, 2, 3, 4].forEach(function(row) {
        var column_is_visible = $('.field-block').filter($('div[class$=\'title_' + row + '\']')).is(':visible');
        var block_style_allows_images = 'no_image' != $('input[name=columns_block_style]:checked').val();
        $('.shortcode-ui-attribute-attachment_'+ row).toggle(column_is_visible && block_style_allows_images);
      });
    }
  },

  carousel_header: {
    render_edit: function () {
      p4_blocks.carousel_header.toggle_subheaders();
      p4_blocks.carousel_header.set_maxlength();

      $('input[name=block_style]').off('click').on('click', function() {
        p4_blocks.carousel_header.toggle_subheaders();
        p4_blocks.carousel_header.set_maxlength();
      });
    },

    toggle_subheaders: function() {
      var selected_block_style = $('input[name=block_style]:checked').val();
      $('input[data-subheader=true]').closest('.field-block').toggle('full-width-classic' != selected_block_style);
    },

    set_maxlength: function() {
      var selected_block_style = $('input[name=block_style]:checked').val();
      if (selected_block_style == 'full-width-classic') {
        $('input[name^=\'header_\']').attr('maxlength', 32);
        $('textarea[name^=\'description_\']').attr('maxlength', 200);
      } else {
        $('input[name^=\'header_\']').attr('maxlength', 40);
        $('textarea[name^=\'description_\']').removeAttr('maxlength');
      }
    }
  }
};

// Define shortcake hooks for blocks fields and blocks views.
if ('undefined' !== typeof (wp.shortcake)) {

  /**
   * Attach shortcake hooks for block fields.
   */
  function attach_hooks() {

    if (!p4_blocks.hooks_defined) {
      p4_blocks.hooks_defined = true;
      wp.shortcake.hooks.addAction('shortcake_newcovers.cover_type', p4_blocks.newcovers.type_of_cover_change_hook);
      wp.shortcake.hooks.addAction('shortcake_newcovers.tags', p4_blocks.newcovers.tags_change_hook);
      wp.shortcake.hooks.addAction('shortcake_newcovers.post_types', p4_blocks.newcovers.post_types_change_hook);
      wp.shortcake.hooks.addAction('shortcake_newcovers.posts', p4_blocks.newcovers.posts_select_change_hook);

      wp.shortcake.hooks.addAction('shortcake_articles.posts', p4_blocks.articles.posts_select_change_hook);
      wp.shortcake.hooks.addAction('shortcake_articles.post_types', p4_blocks.articles.page_types_change_hook);
      wp.shortcake.hooks.addAction('shortcake_articles.tags', p4_blocks.articles.page_types_change_hook);
      wp.shortcake.hooks.addAction('shortcake_articles.read_more_link', p4_blocks.articles.read_more_change_hook);

      wp.shortcake.hooks.addAction('shortcake_social_media.embed_type', p4_blocks.social_media.embed_type_change_hook);
    }

    // There may be multiple social media embeds on a page; fields need initializing separately for each one.
    p4_blocks.social_media.initialize_fields();
  }

  // Attach hooks when rendering a new p4 block.
  wp.shortcake.hooks.addAction('shortcode-ui.render_new', function (shortcode) {
    attach_hooks();

    var shortcode_tag = shortcode.get('shortcode_tag');
    if ('shortcake_columns' === shortcode_tag) {
      p4_blocks.columns.render_new(shortcode);
    }

    if ('shortcake_carousel_header' === shortcode_tag) {
      p4_blocks.carousel_header.render_edit();
    }
  });

  // Trigger hooks when shortcode renders an existing p4 block.
  wp.shortcake.hooks.addAction('shortcode-ui.render_edit', function (shortcode) {
    attach_hooks();

    var shortcode_tag = shortcode.get('shortcode_tag');
    var block_name = shortcode_tag.replace('shortcake_', '');
    if (['shortcake_articles', 'shortcake_newcovers'].includes(shortcode_tag)) {

      var requests = shortcode.get('ajax_requests');

      if (null !== requests) {

        // Block ui / shortcake block view until all fields are populated.
        var $block_div = $('.shortcode-ui-edit-' + shortcode_tag);
        $block_div.addClass('not-clickable');
        $block_div.prev().prepend('<span class="spinner is-active" id="bl_loader"></span>' +
          '<span id="bl_loading_span">Populating block\'s fields..</span>');
        $block_div.animate({opacity: 0.5});

        // Add a hook to unblock shortcake block's view when all ajax requests have been completed.
        Promise.all(requests).then(function (values) {
          $block_div.animate({opacity: 1});
          $block_div.removeClass('not-clickable');
          $('#bl_loader').removeClass('is-active');
          $('#bl_loading_span').remove();
          shortcode.unset('ajax_requests');
          p4_blocks.initialize_view_fields(block_name);
        });
      }
    }

    if ('shortcake_columns' === shortcode_tag) {
      p4_blocks.columns.render_edit(shortcode);
    }

    if ('shortcake_carousel_header' === shortcode_tag) {
      p4_blocks.carousel_header.render_edit();
    }
  });
}

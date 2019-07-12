/* exported ColumnsBlock */

function ColumnsBlock() {
  const me = this;

  /**
   * Called when a new columns block is rendered in the backend.
   * @param shortcode Shortcake backbone model.
   */
  me.render_new = function () {
    const $shortcode_div = $('.shortcode-ui-edit-shortcake_columns');
    $shortcode_div.append('<div data-row="0"><button class="button button-small shortcake-columns-add-column">Add Column</button>'
      + '<button class="button button-small shortcake-columns-remove-column" disabled="disabled">Remove Column</button></div>');
    this.hide_all_columns();

    this.add_click_event_handlers();
  };

  /**
   * Called when en existing columns block is rendered in the backend.
   * @param shortcode Shortcake backbone model.
   */
  me.render_edit = function () {
    const $shortcode_div = $('.shortcode-ui-edit-shortcake_columns');
    $shortcode_div.append('<div data-row="0"><button class="button button-small shortcake-columns-add-column" data-row="1 data-action="add">Add Column</button>'
      + '<button class="button button-small shortcake-columns-remove-column">Remove Column</button></div>');
    let row = 0;

    [1, 2, 3, 4].forEach(function (index) {
      const input_values = $('.field-block').filter($('div[class$=\'_' + index + '\']')).children().filter($('input, textarea')).map(function (idx, elem) {
        return $(elem).val();
      }).get().join('');

      if ('' !== input_values) {
        row = index;
      }
    });

    $('.shortcake-columns-add-column').parent().data('row', row);
    for (let i = row+1; i <= 4; i++) {
      $('.field-block').filter($('div[class$=\'_' + i + '\']')).hide();
    }
    if (row === 4) {
      $('.shortcake-columns-add-column').attr('disabled', 'disabled');
    }

    this.toggle_images();
    this.add_click_event_handlers();
  };

  /**
   * Add click event handlers for add/remove buttons in columns block.
   */
  me.add_click_event_handlers = function () {
    const columns = this;
    // Add click event handlers for the elements.
    $('.shortcake-columns-add-column').on('click', function (event) {
      event.preventDefault();
      const $element = $(event.currentTarget);
      let row = $element.parent().data('row');

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
      const $element = $(event.currentTarget);
      let row = $element.parent().data('row');

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
      me.toggle_images();
    });
  };

  /**
   * Hide a columns block row and reset the values of it's fields.
   *
   * @param row
   */
  me.hide_column = function (row) {
    const $column = $('.field-block').filter($('div[class$=\'_' + row + '\']'));
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
  };

  /**
   * Hide all columns block rows.
   *
   * @param row
   */
  me.hide_all_columns = function () {
    [1,2,3,4].forEach(function (row) {
      $( '.field-block' ).filter( $( 'div[class$=\'_'+row+'\']' ) ).hide();
    });
  };

  /**
   * Show a columns block row and scroll to bottom.
   *
   * @param row
   */
  me.show_column = function (row) {
    $('.field-block').filter($('div[class$=\'_' + row + '\']')).show(300, function () {
      me.toggle_images();
      $('.media-frame-content').animate({
        scrollTop: $('.shortcode-ui-content').prop('scrollHeight'),
      }, 300);
    });
  };

  /**
   * Show/hide images inputs depending on column block style.
   */
  me.toggle_images = function() {
    [1, 2, 3, 4].forEach(function(row) {
      const column_is_visible = $('.field-block').filter($('div[class$=\'title_' + row + '\']')).is(':visible');
      const block_style_allows_images = 'no_image' != $('input[name=columns_block_style]:checked').val();
      $('.shortcode-ui-attribute-attachment_'+ row).toggle(column_is_visible && block_style_allows_images);
    });
  };
}

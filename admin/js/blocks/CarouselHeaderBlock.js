function CarouselHeaderBlock(p4BlocksUI) { // eslint-disable-line no-unused-vars
  var me = this;

  me.render_edit = function () {
    me.toggle_subheaders();
    me.set_maxlength();

    $('input[name=block_style]').off('click').on('click', function() {
      me.toggle_subheaders();
      me.set_maxlength();
    });
  };

  me.toggle_subheaders = function() {
    var selected_block_style = $('input[name=block_style]:checked').val();
    $('input[data-subheader=true]').closest('.field-block').toggle('full-width-classic' != selected_block_style);
  };

  me.add_maxlength_with_counter = function(element, maxLength) {
    $(element).attr('maxlength', maxLength);
    if (!$(element).next('div').length) {
      var maxLengthCounter = '<div class="max-length-counter">0/' + maxLength + '</div>';
      $(maxLengthCounter).insertAfter(element);
    }
    $(element).off('input').on('input', function() {
      var charCount = $(element).val().length;
      $(element).next('div.max-length-counter').html(charCount + '/' + maxLength);
    });
  };

  me.set_maxlength = function() {
    var me = this;
    var selected_block_style = $('input[name=block_style]:checked').val();

    if (selected_block_style == 'full-width-classic') {
      $('input[name^=\'header_\']').each(function() {
        me.add_maxlength_with_counter(this, 32);
      });
      $('textarea[name^=\'description_\']').each(function() {
        me.add_maxlength_with_counter(this, 200);
      });
      $('input[name^=\'link_text_\']').each(function() {
        me.add_maxlength_with_counter(this, 24);
      });
    } else {
      $('input[name^=\'header_\']').attr('maxlength', 40);
      $('textarea[name^=\'description_\']').removeAttr('maxlength');
      $('input[name^=\'link_text_\']').removeAttr('maxlength');
      $('div.maxLengthCounter').remove();
    }
  };
}
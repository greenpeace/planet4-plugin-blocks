jQuery(function ($) {
  'use strict';

  // Check header heights to align them all vertically to the max one
  function align_column_headers() {
    if ($(window).width() > 768) {
      $.each($('.columns-block'), function() {
        let max = 0;
        $.each($(this).find('.column-wrap h3'), function() {
          let height = $(this).height(); // eslint-disable-line
          max = (height > max) ? height : max;
        });
        $.each($(this).find('.column-wrap h3'), function() {
          $(this).css('min-height', max);
        });
      });
    } else {
      $('.columns-block .column-wrap h3').css('min-height', 'auto');
    }
  }

  window.addEventListener('resize', align_column_headers);
  window.addEventListener('load', align_column_headers);
});

// Force wide blocks outside the container

$(document).ready(function() {
  'use strict';

  const $wideblocks = $('.block-wide');

  function force_wide_blocks() {
    $wideblocks.each(function() {
      const left = $(this).offset().left;

      if (left > 0) {
        const margin = -left;

        if ($('html').attr('dir') === 'rtl') {
          $(this).css('margin-left', 'auto');
          $(this).css('margin-right', margin + 'px');
        } else {
          $(this).css('margin-left', margin + 'px');
        }
      }
    });
  }

  if ($wideblocks.length > 0) {
    force_wide_blocks();
    $(window).on('resize', force_wide_blocks);
  } else {
    $('.block-wide').attr('style','margin: 0px !important;padding-left: 0px !important;padding-right: 0px !important');
    $('iframe').attr('style','left: 0');
  }
});

jQuery(function ($) {
  'use strict';

  $('.notice.is-dismissible').animate({'margin-left' : '+=20', 'opacity' : '+=0.9'}, 800);

  setTimeout(function() {
    $('.notice.is-dismissible').fadeOut(2000, function () {
      $(this).remove();
    });
  }, 3800);
} );

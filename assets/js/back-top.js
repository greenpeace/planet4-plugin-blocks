$(document).ready(function() {
  'use strict';

  const $backtop = $('.back-top');
  const $submenu = $('.submenu-block');

  if ($submenu.length > 0) {
    $(window).scroll(function () {
      if ($(this).scrollTop() > 400) {
        $backtop.fadeIn();
        if ($('.cookie-block:visible').length > 0) {
          $backtop.css('bottom', '120px');
        } else {
          $backtop.css('bottom', '50px');
        }
      } else {
        $backtop.fadeOut();
      }
    });

    $backtop.click(function () {
      $('body, html').animate({
        scrollTop: 0
      }, 800);
      return false;
    });
  }
});

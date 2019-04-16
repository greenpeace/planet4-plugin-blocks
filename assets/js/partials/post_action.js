// Force the Cover card to follow scroll

$(document).ready(function() {
  'use strict';

  var $sidebar = $('.post-content').find('> #action-card');
  var offset = $sidebar.offset();
  var topPadding = 100;

  function scroll_action_card() {
    if ($(window).width() > 992) {
      let absPosition = $('.post-details > :last-child').offset().top - $sidebar.outerHeight() - topPadding;

      if ($(window).scrollTop() > offset.top && $(window).scrollTop() < absPosition) {
        $sidebar.stop().animate({
          marginTop: $(window).scrollTop() - offset.top + topPadding
        });
      }
      if ($(window).scrollTop() < offset.top) {
        $sidebar.stop().animate({
          marginTop: 0
        });
      }
    } else {
      $sidebar.css('margin-top', 0);
    }
  }

  if ($sidebar.length > 0) {
    window.addEventListener('scroll', scroll_action_card);
    window.addEventListener('resize', scroll_action_card);
  }
});

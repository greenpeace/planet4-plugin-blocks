// Underline headlines on thumbnail hover.

$(document).ready(function() {
  'use strict';

  $('.article-listing').off('mouseenter').on('mouseenter', '.article-list-item-image',
    function() {
      $('.article-list-item-headline', $(this).parent()).addClass('article-hover');
    }
  ).off('mouseleave').on('mouseleave', '.article-list-item-image',
    function() {
      $('.article-list-item-headline', $(this).parent()).removeClass('article-hover');
    }
  );

  $('.four-column-content-symbol').hover(
    function() {
      $('h4', $(this).parent()).addClass('four-column-hover');
    }, function() {
      $('h4', $(this).parent()).removeClass('four-column-hover');
    }
  );
});

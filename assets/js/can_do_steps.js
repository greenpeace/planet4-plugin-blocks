$(document).ready(function () {
  'use strict';

  $('.can-do-steps .col').hover(function () {
    $(this).find('.step-number').toggleClass('active');
  });
});

/* global createCookie, readCookie */

function CookiesBlock(p4BlocksUI) { // eslint-disable-line no-unused-vars
  var me = this;

  me.cookie = readCookie('greenpeace');

  me.setNoTrackCookie = function() {
    if ($('#necessary_cookies').is(':checked') || $('#all_cookies').is(':checked')) {
      // Remove the 'no_track' cookie, if user accept the cookies consent.
      createCookie('no_track', 'true', -1);
    } else {
      // If user manually disables all trackings, set a 'no_track' cookie.
      createCookie('no_track', 'true', 20*365);
    }
  };

  if ('1' === me.cookie) {
    $('#necessary_cookies').prop('checked', true);
  } else if ('2' === me.cookie) {
    $('#necessary_cookies').prop('checked', true);
    $('#all_cookies').prop('checked', true);
  }

  // Add change event for necessary cookies checkbox.
  $('#necessary_cookies').on('change', function () {
    if ($('#necessary_cookies').is(':checked')) {
      createCookie('greenpeace', '1', 365);

      // the .cookie-notice element belongs to the P4 Master Theme
      $('.cookie-notice').slideUp('slow');
    } else {
      $('#all_cookies').prop('checked', false);
      createCookie('greenpeace', '0', -1);
      $('.cookie-notice').show();
    }
    me.setNoTrackCookie();
  });

  // Add change event for all cookies checkbox.
  $('#all_cookies').on('change', function () {
    if ($('#all_cookies').is(':checked')) {
      $('#necessary_cookies').prop('checked', true);
      createCookie('greenpeace', '2', 365);
      $('.cookie-notice').slideUp('slow');
    } else {
      if ($('#necessary_cookies').is(':checked')) {
        createCookie('greenpeace', '1', 365);
      } else {
        createCookie('greenpeace', '0', -1);
        $('.cookie-notice').show();
      }
    }
    me.setNoTrackCookie();
  });
}
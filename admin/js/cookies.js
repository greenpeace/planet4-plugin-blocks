/* global createCookie, readCookie */
jQuery(document).ready(function () {

    var cookie = readCookie('greenpeace');
    if ('1' === cookie) {
        $('#necessary_cookies').prop('checked', true);
    } else if ('2' === cookie) {
        $('#necessary_cookies').prop('checked', true);
        $('#all_cookies').prop('checked', true);
    }

    // Add change event for necessary cookies checkbox.
    $('#necessary_cookies').on('change', function () {
        if ($('#necessary_cookies').is(':checked')) {
            createCookie('greenpeace', '1', 365);
            $('.cookie-block').slideUp('slow');
        } else {
            $('#all_cookies').prop('checked', false);
            createCookie('greenpeace', '0', -1);
            $('.cookie-block').show();
        }
    });

    // Add change event for all cookies checkbox.
    $('#all_cookies').on('change', function () {
        if ($('#all_cookies').is(':checked')) {
            $('#necessary_cookies').prop('checked', true);
            createCookie('greenpeace', '2', 365);
            $('.cookie-block').slideUp('slow');
        } else {
            if ($('#necessary_cookies').is(':checked')) {
                createCookie('greenpeace', '1', 365);
            } else {
                createCookie('greenpeace', '0', -1);
                $('.cookie-block').show();
            }
        }
    });
});
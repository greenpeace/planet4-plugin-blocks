$(document).ready(function () {
	'use strict';

	// Parse submenu object passed to a variable from server-side.
	if ('undefined' === submenu || ! Array.isArray(submenu)) {
		submenu = [];
	}

	for (var i = 0; i < submenu.length; i++) {
		var menu = submenu[i];

		if ('undefined' === menu.id || 'undefined' === menu.type || 'undefined' === menu.link) {
			continue;
		}
		var type = menu.type;

		// Iterate over headings and create an anchor tag for this heading.
		if (menu.link) {

			var $headings = $('body ' + type);

			for (var j = 0; j < $headings.length; j++) {
				var $heading = $($headings[j]);
				if ($heading.text() === menu.text) {
					$heading.prepend('<a id="' + menu.id + '"></a>');
				}
			}
		}

		if ('undefined' === menu.children || !Array.isArray(menu.children)) {
			continue;
		}

		for (var k = 0; k < menu.children.length; k++) {
			var child = menu.children[k];
			var child_type = child.type;
			var $headings2 = $('body ' + child_type);

			for (var l = 0; l < $headings2.length; l++) {

				var $heading2 = $($headings2[l]);
				if ($heading2.text() === child.text) {
					$heading2.prepend('<a id="' + child.id + '"></a>');
				}
			}
		}
	}

	// Add click event for submenu links.
    $('.submenu-link').click(function (event) {
        event.preventDefault();
        var link = $.attr(this, 'href');
        $('html, body').animate({
            scrollTop: $(link).offset().top - 100
        }, 2000, function () {
			var position = $(window).scrollTop();
			window.location.hash = link;
			$(window).scrollTop(position);
		});

        return false;
    });
});